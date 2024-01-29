<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Component\AdminTools\Administrator\View\Allowlist;

defined('_JEXEC') or die;

use Akeeba\Component\AdminTools\Administrator\Model\AllowlistModel;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

class HtmlView extends BaseHtmlView
{
	/**
	 * The Form object
	 *
	 * @var    Form
	 * @since  1.5
	 */
	protected $form;

	/**
	 * The active item
	 *
	 * @var    object
	 * @since  1.5
	 */
	protected $item;

	/**
	 * The model state
	 *
	 * @var    object
	 * @since  1.5
	 */
	protected $state;

	public function display($tpl = null): void
	{
		/** @var AllowlistModel $model */
		$model       = $this->getModel();
		$this->form  = $model->getForm();
		$this->item  = $model->getItem();
		$this->state = $model->getState();

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new GenericDataException(implode("\n", $errors), 500);
		}

		$this->addToolbar();

		parent::display($tpl);
	}

	protected function addToolbar(): void
	{
		Factory::getApplication()->input->set('hidemainmenu', true);

		$isNew = empty($this->item->id);

		ToolbarHelper::title(Text::_('COM_ADMINTOOLS_TITLE_ALLOWLIST_' . ($isNew ? 'ADD' : 'EDIT')), 'icon-admintools');

		ToolbarHelper::apply('allowlist.apply');

		$toolbarButtons = [];

		// If not checked out, can save the item.
		$toolbarButtons[] = ['save', 'allowlist.save'];
		$toolbarButtons[] = ['save2new', 'allowlist.save2new'];

		ToolbarHelper::saveGroup(
			$toolbarButtons,
			'btn-success'
		);

		ToolbarHelper::cancel('allowlist.cancel', $isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE');

		ToolbarHelper::inlinehelp();

		ToolbarHelper::help(null, false, 'https://www.akeeba.com/documentation/admin-tools-joomla/waf-ip-allowlist.html');
	}
}