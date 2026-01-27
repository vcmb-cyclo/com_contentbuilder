<?php
/**
 * @package     ContentBuilder
 * @author      Markus Bopp
 * @link        https://breezingforms.vcmb.fr
 * @license     GNU/GPL
*/

namespace CB\Component\Contentbuilder\Site\Controller;

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use CB\Component\Contentbuilder\Administrator\CBRequest;
use CB\Component\Contentbuilder\Administrator\Helper\ContentbuilderLegacyHelper;

class ListController extends BaseController
{
    public function display($cachable = false, $urlparams = [])
    {
        $app   = Factory::getApplication();

        // Si tu gardes le suffixe pour compat legacy :
        //$frontend = Factory::getApplication()->isClient('site');
        $suffix = '_fe';

        // 1) d'abord depuis l'URL
        $formId   = $this->input->getInt('id', 0);
        $recordId = $this->input->getInt('record_id', 0);

        // 2) sinon depuis les params du menu actif
        if (!$formId) {
            $menu = $app->getMenu()->getActive();
            if ($menu) {
                $formId = (int) $menu->getParams()->get('form_id', 0);
            }
        }

        // Synchroniser Joomla Input + CBRequest (legacy)
        $this->input->set('id', $formId);
        Factory::getApplication()->input->set('id', $formId);

        if ($recordId) {
            $this->input->set('record_id', $recordId);
            Factory::getApplication()->input->set('record_id', $recordId);
        }

        // Contexte CB correct pour cette page
        Factory::getApplication()->input->set('view', 'list');

        // Permissions
        ContentbuilderLegacyHelper::setPermissions($formId, $recordId, $suffix);
        ContentbuilderLegacyHelper::checkPermissions(
            'listaccess',
            Text::_('COM_CONTENTBUILDER_PERMISSIONS_LISTACCESS_NOT_ALLOWED'),
            $suffix
        );

        // Piloter le rendu via l'input Joomla
        $layout = $this->input->getCmd('layout', null);
        $this->input->set('layout', ($layout === 'latest') ? null : $layout);

        return parent::display($cachable, $urlparams);
    }
}

