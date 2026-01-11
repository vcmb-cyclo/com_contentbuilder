<?php
/**
 * @package     ContentBuilder
 * @author      Markus Bopp / XDA+GIL
 * @copyright   Copyright (C) 2026 by XDA+GIL
 * @license     GNU/GPL
 */

namespace CB\Component\Contentbuilder\Administrator\View\Storages;

\defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Toolbar\ToolbarHelper;
use CB\Component\Contentbuilder\Administrator\View\Contentbuilder\HtmlView as BaseHtmlView;

/**
 * Vue Storages pour ContentBuilder
 */
class HtmlView extends BaseHtmlView
{
    protected $items;
    protected $pagination;
    protected $state;
    protected $lists;
    protected $ordering;

    /**
     * Méthode d'affichage de la vue
     *
     * @param   string  $tpl  Nom du template alternatif
     * @return  void
     */
    public function display($tpl = null)
    {
        // Récupération des données du modèle
        $this->items      = (array) ($this->getModel()->getItems() ?? []);
        $this->pagination = $this->getModel()->getPagination();
        $this->state      = $this->getModel()->getState();

        // Préparation des filtres et tris (Joomla standard)
        $this->lists['order_Dir'] = (string) $this->state->get('list.direction', 'ASC');
        $this->lists['order']     = (string) $this->state->get('list.ordering', 'a.ordering');

        // Si tu as un filtre published standard
        $this->lists['state']     = HTMLHelper::_('grid.state', (string) $this->state->get('filter.state', ''));

        // Ton flag ordering (ton template compare à "ordering" mais toi tu utilises souvent "a.ordering")
        $this->ordering = ($this->lists['order'] === 'a.ordering' || $this->lists['order'] === 'ordering');

        // Vérification des erreurs
        if (count($errors = $this->get('Errors')))
        {
            throw new \Exception(implode('<br>', $errors), 500);
        }

        // Barre d'outils
        $this->addToolbar();

        // Ajout du CSS personnalisé (méthode propre)
        $this->addStylesheet();
        $this->addToolbarIcon();

        HTMLHelper::_('behavior.keepalive');
        parent::display($tpl);
    }

    /**
     * Ajoute la barre d'outils
     */
    protected function addToolbar()
    {
        ToolbarHelper::title(
            Text::_('COM_CONTENTBUILDER_STORAGES'),
            'contentbuilder icon-contentbuilder'  // classe CSS personnalisée
        );

        ToolbarHelper::addNew('storage.add');
        ToolbarHelper::editList('storage.edit');
        ToolbarHelper::deleteList('JGLOBAL_CONFIRM_DELETE', 'storage.delete');        
        ToolbarHelper::preferences('com_contentbuilder');
    }

    /**
     * Ajoute le CSS personnalisé
     */
    protected function addStylesheet()
    {
        // Chargement d'un CSS fixe pour bluestork si nécessaire (sinon à supprimer)
        // $document = Factory::getDocument();
        // $document->addStyleSheet(Uri::root(true) . '/media/com_ontentbuilder/css/bluestork.fix.css');
    }

    /**
     * Ajoute l'icône personnalisée pour le titre de la barre d'outils
     */
    protected function addToolbarIcon()
    {
        // Récupération du WebAssetManager (méthode moderne)
        $wa = $this->getDocument()->getWebAssetManager();
        $wa->useStyle('com_contentbuilder.admin-toolbar'); // à déclarer dans joomla.asset.json

        // Optionnel : si vous avez aussi bluestork.fix.css
        // $wa->useStyle('com_contentbuilder.bluestork-fix');
    }
}