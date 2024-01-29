<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Component\AdminTools\Administrator\View\Tempsuperuser;

defined('_JEXEC') or die;

use Akeeba\Component\AdminTools\Administrator\Mixin\ViewLoadAnyTemplateTrait;
use Akeeba\Component\AdminTools\Administrator\Mixin\ViewTaskBasedEventsTrait;
use Akeeba\Component\AdminTools\Administrator\Model\TempsuperuserModel;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

class HtmlView extends BaseHtmlView
{
	use ViewLoadAnyTemplateTrait;
	use ViewTaskBasedEventsTrait {
		ViewTaskBasedEventsTrait::display as smartDisplay;
	}

	/**
	 * The Form object
	 *
	 * @var    Form
	 * @since  7.0.0
	 */
	protected $form;

	/**
	 * The active item
	 *
	 * @var    object
	 * @since  7.0.0
	 */
	protected $item;

	/**
	 * The model state
	 *
	 * @var    object
	 * @since  7.0.0
	 */
	protected $state;

	public function display($tpl = null): void
	{
		/** @var TempsuperuserModel $model */
		$model       = $this->getModel();
		$this->form  = $model->getForm();
		$this->item  = $model->getItem();
		$this->state = $model->getState();

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new GenericDataException(implode("\n", $errors), 500);
		}

		$isNew = empty($this->item->user_id);

		if ($this->getLayout() == '')
		{
			$this->setLayout('edit');
		}

		if ($isNew)
		{
			$this->setLayout('wizard');
		}

		$this->addToolbar();

		$this->smartDisplay($tpl);
	}

	protected function addToolbar(): void
	{
		Factory::getApplication()->input->set('hidemainmenu', true);

		$isNew = empty($this->item->user_id);

		ToolbarHelper::title(Text::_('COM_ADMINTOOLS_TITLE_TEMPSUPERUSER_' . ($isNew ? 'ADD' : 'EDIT')), 'icon-admintools');

		ToolbarHelper::apply('tempsuperuser.apply');
		ToolbarHelper::save('tempsuperuser.save');

		ToolbarHelper::cancel('tempsuperuser.cancel', $isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE');
	}
}