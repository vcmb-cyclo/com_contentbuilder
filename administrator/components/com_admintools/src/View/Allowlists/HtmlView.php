<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Component\AdminTools\Administrator\View\Allowlists;

defined('_JEXEC') or die;

use Akeeba\Component\AdminTools\Administrator\Helper\Storage;
use Akeeba\Component\AdminTools\Administrator\Mixin\ViewLoadAnyTemplateTrait;
use Akeeba\Component\AdminTools\Administrator\Mixin\ViewSystemPluginExistsTrait;
use Akeeba\Component\AdminTools\Administrator\Mixin\ViewTaskBasedEventsTrait;
use Akeeba\Component\AdminTools\Administrator\Model\AllowlistsModel;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Toolbar\Button\DropdownButton;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\Registry\Registry;

class HtmlView extends BaseHtmlView
{
	use ViewLoadAnyTemplateTrait;
	use ViewTaskBasedEventsTrait;
	use ViewSystemPluginExistsTrait;

	/**
	 * The search tools form
	 *
	 * @var    Form
	 * @since  7.0.0
	 */
	public $filterForm;

	/**
	 * The active search filters
	 *
	 * @var    array
	 * @since  7.0.0
	 */
	public $activeFilters = [];

	/**
	 * The WAF configuration parameters
	 *
	 * @var    Storage
	 * @since  7.0.0
	 */
	public $storage;

	/**
	 * Do I have too many items which could make the site sluggish?
	 *
	 * @var   bool
	 * @since 7.0.0
	 */
	public $tooMany;

	/**
	 * An array of items
	 *
	 * @var    array
	 * @since  7.0.0
	 */
	protected $items = [];

	/**
	 * The pagination object
	 *
	 * @var    Pagination
	 * @since  7.0.0
	 */
	protected $pagination;

	/**
	 * The model state
	 *
	 * @var    Registry
	 * @since  7.0.0
	 */
	protected $state;

	/**
	 * Is this view an Empty State
	 *
	 * @var   boolean
	 * @since 7.0.0
	 */
	private $isEmptyState = false;

	public function display($tpl = null)
	{
		/** @var AllowlistsModel $model */
		$model               = $this->getModel();
		$this->items         = $model->getItems();
		$this->pagination    = $model->getPagination();
		$this->state         = $model->getState();
		$this->filterForm    = $model->getFilterForm();
		$this->activeFilters = $model->getActiveFilters();
		$this->storage       = Storage::getInstance();
		$this->tooMany       = $model->getTotal() > 50;

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new GenericDataException(implode("\n", $errors), 500);
		}

		if (!\count($this->items) && $this->isEmptyState = $this->get('IsEmptyState'))
		{
			$this->setLayout('emptystate');
		}

		$this->populateSystemPluginExists();

		$this->addToolbar();

		parent::display($tpl);
	}

	private function addToolbar()
	{
		$user = Factory::getApplication()->getIdentity();

		// Get the toolbar object instance
		$toolbar = Toolbar::getInstance('toolbar');

		ToolbarHelper::title(sprintf(Text::_('COM_ADMINTOOLS_TITLE_ALLOWLISTS')), 'icon-admintools');

		$canCreate    = $user->authorise('core.create', 'com_admintools');
		$canDelete    = $user->authorise('core.delete', 'com_admintools');
		$canEditState = $user->authorise('core.edit.state', 'com_admintools');

		if ($canCreate)
		{
			$toolbar
				->addNew('allowlist.add');
		}

		if ($canDelete || $canEditState || $canCreate)
		{
			/** @var DropdownButton $dropdown */
			$dropdown = $toolbar->dropdownButton('status-group')
				->text('JTOOLBAR_CHANGE_STATUS')
				->toggleSplit(false)
				->icon('icon-ellipsis-h')
				->buttonClass('btn btn-action')
				->listCheck(true);

			$childBar = $dropdown->getChildToolbar();

			if ($canCreate)
			{
				$childBar->standardButton('copy', 'COM_ADMINTOOLS_COMMON_LBL_COPY_LABEL')
					->icon('fa fa-copy')
					->task('allowlists.copy')
					->listCheck(true);
			}

			if ($canDelete)
			{
				$childBar->delete('allowlists.delete')
					->message('JGLOBAL_CONFIRM_DELETE')
					->listCheck(true);
			}
		}

		ToolbarHelper::back('JTOOLBAR_BACK', Route::_('index.php?option=com_admintools&view=Webapplicationfirewall', false));

		ToolbarHelper::help(null, false, 'https://www.akeeba.com/documentation/admin-tools-joomla/waf-ip-allowlist.html');
	}
}