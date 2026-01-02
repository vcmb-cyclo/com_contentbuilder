<?php
/**
 * @package     ContentBuilder
 * @author      Markus Bopp / XDA+GIL
 * @link        https://breezingforms.vcmb.fr
 * @copyright   Copyright (C) 2026 by XDA+GIL
 * @license     GNU/GPL
 */

namespace CB\Component\Contentbuilder\Administrator\Controller;

// no direct access
\defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\MVC\Controller\AdminController;

final class FormsController extends AdminController
{
    /**
     * Nom de la vue liste et item (convention Joomla 6).
     */
    protected $view_list = 'forms';
    protected $view_item = 'form';

    public function __construct($config = [])
    {
        parent::__construct($config);

        // Si tu veux absolument garder ces paramètres en session (legacy),
        // tu peux le faire proprement via $this->input.
        $session = Factory::getApplication()->getSession();

        if ($this->input->getInt('email_users', -1) !== -1) {
            $session->set('email_users', $this->input->get('email_users', 'none'), 'com_contentbuilder');
        }

        if ($this->input->getInt('email_admins', -1) !== -1) {
            $session->set('email_admins', $this->input->get('email_admins', ''), 'com_contentbuilder');
        }

        if ($this->input->getInt('slideStartOffset', -1) !== -1) {
            $session->set('slideStartOffset', $this->input->getInt('slideStartOffset', 1));
        }

        if ($this->input->getInt('tabStartOffset', -1) !== -1) {
            $session->set('tabStartOffset', $this->input->getInt('tabStartOffset', 0));
        }
    }

    /**
     * Copie (custom)
     */
    public function copy(): void
    {
        $cid = (array) $this->input->get('cid', [], 'array');

        if (!empty($cid)) {
            $model = $this->getModel('Forms');
            $model->copy();
        }

        $this->setRedirect(
            Route::_('index.php?option=com_contentbuilder&view=forms&limitstart=' . $this->input->getInt('limitstart'), false),
            Text::_('COM_CONTENTBUILDER_COPIED')
        );
    }

    /**
     * Si tu avais une raison de forcer la vue dans display(), tu peux l’enlever :
     * AdminController::display() gère déjà la vue liste.
     * (On la laisse quand même pour coller à ton comportement.)
     */
    public function display($cachable = false, $urlparams = []): void
    {
        $this->input->set('view', $this->view_list);
        parent::display($cachable, $urlparams);
    }

    /**
     * Si tu avais un vieux mapping add->edit, le core le gère déjà.
     * Donc registerTask('add','edit') n’est plus nécessaire.
     */
}
