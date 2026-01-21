<?php
/**
 * @package     ContentBuilder
 * @author      Markus Bopp / XDA+GIL
 * @link        https://breezingforms.vcmb.fr
 * @copyright   Copyright (C) 2026 by XDA+GIL
 * @license     GNU/GPL
 */

// Fichier d’entrée du composant (Site) - Joomla 6 Modern Dispatcher

\defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use CB\Component\Contentbuilder\Administrator\CBRequest;

$app = Factory::getApplication();

// ----------------------------------------------------
// 1) Compat : reset des variables CBRequest.
// ----------------------------------------------------
CBRequest::setVar('cb_controller', null);
CBRequest::setVar('cb_category_id', null);
CBRequest::setVar('cb_list_filterhidden', null);
CBRequest::setVar('cb_list_orderhidden', null);
CBRequest::setVar('cb_show_author', null);
CBRequest::setVar('cb_show_top_bar', null);
CBRequest::setVar('cb_show_details_top_bar', null);
CBRequest::setVar('cb_show_bottom_bar', null);
CBRequest::setVar('cb_show_details_bottom_bar', null);
CBRequest::setVar('cb_latest', null);
CBRequest::setVar('cb_show_details_back_button', null);
CBRequest::setVar('cb_list_limit', null);
CBRequest::setVar('cb_filter_in_title', null);
CBRequest::setVar('cb_prefix_in_title', null);
CBRequest::setVar('force_menu_item_id', null);
CBRequest::setVar('cb_category_menu_filter', null);

// ----------------------------------------------------
// 2) Menu actif / Itemid.
// ----------------------------------------------------
$menu = $app->getMenu();
$item = $menu->getActive();

if (is_object($item)) {
    CBRequest::setVar('Itemid', $item->id);
}

// ----------------------------------------------------
// 3) Récup des params menu + logique "latest/details".
// ----------------------------------------------------
if (CBRequest::getInt('Itemid', 0)) {

    if (CBRequest::getVar('layout', null) !== null) {
        $app->getSession()->set(
            'com_contentbuilder.layout.' . CBRequest::getInt('Itemid', 0) . CBRequest::getVar('layout', null),
            CBRequest::getVar('layout', null)
        );
    }

    if ($app->getSession()->get(
        'com_contentbuilder.layout.' . CBRequest::getInt('Itemid', 0) . CBRequest::getVar('layout', null),
        null
    ) !== null) {
        CBRequest::setVar(
            'layout',
            $app->getSession()->get(
                'com_contentbuilder.layout.' . CBRequest::getInt('Itemid', 0) . CBRequest::getVar('layout', null),
                null
            )
        );
    }

    if (is_object($item)) {

        if ($item->getParams()->get('form_id', null) !== null) {
            CBRequest::setVar('id', $item->getParams()->get('form_id', null));
        }

        if ($item->getParams()->get('record_id', null) !== null && ($item->query['view'] ?? '') === 'details' && !isset($_REQUEST['view'])) {
            CBRequest::setVar('record_id', $item->getParams()->get('record_id', null));
            CBRequest::setVar('controller', 'details');
        }

        if ($item->getParams()->get('record_id', null) !== null && ($item->query['view'] ?? '') === 'details' && isset($_REQUEST['view'])) {
            CBRequest::setVar('record_id', $item->getParams()->get('record_id', null));
            CBRequest::setVar('controller', 'edit');
        }

        if (($item->query['view'] ?? '') === 'latest' && !isset($_REQUEST['view'])) {
            CBRequest::setVar('view', 'latest');
            CBRequest::setVar('controller', 'details');
        }

        if (($item->query['view'] ?? '') === 'latest' && isset($_REQUEST['view']) && $_REQUEST['view'] === 'edit' && isset($_REQUEST['record_id'])) {
            CBRequest::setVar('record_id', $_REQUEST['record_id']);
            CBRequest::setVar('view', 'latest');
            CBRequest::setVar('controller', 'edit');
        }

        CBRequest::setVar('cb_category_id', $item->getParams()->get('cb_category_id', null));
        CBRequest::setVar('cb_controller', $item->getParams()->get('cb_controller', null));
        CBRequest::setVar('cb_list_filterhidden', $item->getParams()->get('cb_list_filterhidden', null));
        CBRequest::setVar('cb_list_orderhidden', $item->getParams()->get('cb_list_orderhidden', null));
        CBRequest::setVar('cb_show_author', $item->getParams()->get('cb_show_author', null));
        CBRequest::setVar('cb_show_bottom_bar', $item->getParams()->get('cb_show_bottom_bar', null));
        CBRequest::setVar('cb_show_top_bar', $item->getParams()->get('cb_show_top_bar', null));
        CBRequest::setVar('cb_show_details_bottom_bar', $item->getParams()->get('cb_show_details_bottom_bar', null));
        CBRequest::setVar('cb_show_details_top_bar', $item->getParams()->get('cb_show_details_top_bar', null));
        CBRequest::setVar('cb_show_details_back_button', $item->getParams()->get('cb_show_details_back_button', null));
        CBRequest::setVar('cb_list_limit', $item->getParams()->get('cb_list_limit', 20));
        CBRequest::setVar('cb_filter_in_title', $item->getParams()->get('cb_filter_in_title', 1));
        CBRequest::setVar('cb_prefix_in_title', $item->getParams()->get('cb_prefix_in_title', 1));
        CBRequest::setVar('force_menu_item_id', $item->getParams()->get('force_menu_item_id', 0));
        CBRequest::setVar('cb_category_menu_filter', $item->getParams()->get('cb_category_menu_filter', 0));
    }
}

// ----------------------------------------------------
// 4) Compat : déduire le "controller" logique.
// ----------------------------------------------------
$controller = trim(CBRequest::getWord('controller'));

if (CBRequest::getCmd('view', '') === 'details' || (CBRequest::getCmd('view', '') === 'latest' && CBRequest::getCmd('controller', '') === '')) {
    $controller = 'details';
}

if (CBRequest::getVar('cb_controller') === 'edit') {
    $controller = 'edit';
} elseif (CBRequest::getVar('cb_controller') === 'publicforms' && CBRequest::getInt('id', 0) <= 0) {
    $controller = 'publicforms';
}

if (!$controller) {
    $controller = 'list';
}

// ----------------------------------------------------
// 5) Joomla input (IMPORTANT)
// ----------------------------------------------------
$input = $app->input;

// Si un task est explicitement fourni (ex: task=details.display), on ne touche pas.
$task = $input->getCmd('task', '');

if ($task === '') {
    // Mapping simple : controller => view
    // (à ajuster si ton routing moderne diffère)
    $input->set('view', $controller);

    // Assure un task "standard" si nécessaire
    // Beaucoup de composants supportent juste display par défaut
    $input->set('task', $controller . '.display');
}

// ----------------------------------------------------
// 6) DISPATCH MODERNE (Joomla 6)
// ----------------------------------------------------
$component = $app->bootComponent('com_contentbuilder');
$component->getDispatcher($input)->dispatch();
