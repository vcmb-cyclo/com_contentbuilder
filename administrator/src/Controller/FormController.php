<?php
/**
 * @package     ContentBuilder
 * @author      Markus Bopp
 * @link        https://breezingforms.vcmb.fr
 * @copyright   Copyright (C) 2026 by XDA+GIL
 * @license     GNU/GPL
 */

namespace CB\Component\Contentbuilder\Administrator\Controller;

// no direct access
\defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\FormController as CoreFormController;
use Joomla\CMS\Router\Route;

final class FormController extends CoreFormController
{
    /**
     * Vue item et vue liste utilisées par les redirects du core
     */
    protected $view_item = 'form';
    protected $view_list = 'forms';

    /**
     * Tu peux laisser le core gérer add/edit/apply/save/save2new/cancel/remove/publish/unpublish/orderup/orderdown/saveorder.
     * Mais tu as des actions custom "list*" (listpublish, listorderup, etc.).
     * On les conserve ici en appelant tes méthodes de modèle.
     */

    public function listorderup(): void
    {
        $model = $this->getModel('Form');
        $model->listMove(-1);

        // Après une action mutative : redirect (PRG), pas display()
        $this->setRedirect(
            Route::_('index.php?option=com_contentbuilder&view=form&layout=edit&cid[]=' . $this->input->getInt('id', 0), false)
        );
    }

    public function listorderdown(): void
    {
        $model = $this->getModel('Form');
        $model->listMove(1);

        $this->setRedirect(
            Route::_('index.php?option=com_contentbuilder&view=form&layout=edit&cid[]=' . $this->input->getInt('id', 0), false)
        );
    }

    public function listsaveorder(): void
    {
        $model = $this->getModel('Form');
        $model->listSaveOrder();

        $this->setRedirect(
            Route::_('index.php?option=com_contentbuilder&view=form&layout=edit&cid[]=' . $this->input->getInt('id', 0), false)
        );
    }

    public function listpublish(): void
    {
        $model = $this->getModel('Form');
        $model->setListPublished();

        $this->setRedirect(
            Route::_('index.php?option=com_contentbuilder&view=form&layout=edit&cid[]=' . $this->input->getInt('id', 0), false),
            Text::_('COM_CONTENTBUILDER_PUBLISHED')
        );
    }

    public function listunpublish(): void
    {
        $model = $this->getModel('Form');
        $model->setListUnpublished();

        $this->setRedirect(
            Route::_('index.php?option=com_contentbuilder&view=form&layout=edit&cid[]=' . $this->input->getInt('id', 0), false),
            Text::_('COM_CONTENTBUILDER_UNPUBLISHED')
        );
    }

    public function linkable(): void
    {
        $model = $this->getModel('Form');
        $model->setListLinkable();

        $this->setRedirect(
            Route::_('index.php?option=com_contentbuilder&view=form&layout=edit&cid[]=' . $this->input->getInt('id', 0), false)
        );
    }

    public function not_linkable(): void
    {
        $model = $this->getModel('Form');
        $model->setListNotLinkable();

        $this->setRedirect(
            Route::_('index.php?option=com_contentbuilder&view=form&layout=edit&cid[]=' . $this->input->getInt('id', 0), false)
        );
    }

    public function editable(): void
    {
        $model = $this->getModel('Form');
        $model->setListEditable();

        $this->setRedirect(
            Route::_('index.php?option=com_contentbuilder&view=form&layout=edit&cid[]=' . $this->input->getInt('id', 0), false)
        );
    }

    public function not_editable(): void
    {
        $model = $this->getModel('Form');
        $model->setListNotEditable();

        $this->setRedirect(
            Route::_('index.php?option=com_contentbuilder&view=form&layout=edit&cid[]=' . $this->input->getInt('id', 0), false)
        );
    }

    public function list_include(): void
    {
        $model = $this->getModel('Form');
        $model->setListListInclude();

        $this->setRedirect(
            Route::_('index.php?option=com_contentbuilder&view=form&layout=edit&cid[]=' . $this->input->getInt('id', 0), false)
        );
    }

    public function no_list_include(): void
    {
        $model = $this->getModel('Form');
        $model->setListNoListInclude();

        $this->setRedirect(
            Route::_('index.php?option=com_contentbuilder&view=form&layout=edit&cid[]=' . $this->input->getInt('id', 0), false)
        );
    }

    public function search_include(): void
    {
        $model = $this->getModel('Form');
        $model->setListSearchInclude();

        $this->setRedirect(
            Route::_('index.php?option=com_contentbuilder&view=form&layout=edit&cid[]=' . $this->input->getInt('id', 0), false)
        );
    }

    public function no_search_include(): void
    {
        $model = $this->getModel('Form');
        $model->setListNoSearchInclude();

        $this->setRedirect(
            Route::_('index.php?option=com_contentbuilder&view=form&layout=edit&cid[]=' . $this->input->getInt('id', 0), false)
        );
    }

    public function editable_include(): void
    {
        $this->editable();
    }
}
