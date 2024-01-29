<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Component\AdminTools\Administrator\View\Adminpassword;

defined('_JEXEC') or die;

use Akeeba\Component\AdminTools\Administrator\Mixin\ViewLoadAnyTemplateTrait;
use Akeeba\Component\AdminTools\Administrator\Mixin\ViewTaskBasedEventsTrait;
use Akeeba\Component\AdminTools\Administrator\Model\AdminpasswordModel;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

class HtmlView extends BaseHtmlView
{
	use ViewTaskBasedEventsTrait;
	use ViewLoadAnyTemplateTrait;

	/**
	 * .htaccess username
	 *
	 * @var   string
	 */
	public $username;

	/**
	 * .htaccess password
	 *
	 * @var   string
	 */
	public $password;

	/**
	 * Should I reset custom error pages?
	 *
	 * @var   bool
	 *
	 * @since 5.3.4
	 */
	public $resetErrorPages;

	/**
	 * Protection mode
	 *
	 * @var   string
	 *
	 * @since 7.0.0
	 */
	public $mode;

	/**
	 * Is the backend locked?
	 *
	 * @var  string
	 */
	public $adminLocked;

	protected function onBeforeMain()
	{
		/** @var AdminpasswordModel $model */
		$model = $this->getModel();
		/** @var CMSApplication $app */
		$app = Factory::getApplication();

		$this->username        = $app->getUserStateFromRequest('com_admintools.adminpassword.username', 'username', '', 'raw');
		$this->password        = $app->getUserStateFromRequest('com_admintools.adminpassword.password', 'password', '', 'raw');
		$this->resetErrorPages = $app->getUserStateFromRequest('com_admintools.adminpassword.resetErrorPages', 'resetErrorPages', 1, 'int');
		$this->mode = $app->getUserStateFromRequest('com_admintools.adminpassword.mode', 'mode', 'everything', 'cmd');
		$this->adminLocked     = $model->isLocked();

		ToolbarHelper::title(sprintf(Text::_('COM_ADMINTOOLS_TITLE_ADMINPASSWORD')), 'icon-admintools');
		ToolbarHelper::back('COM_ADMINTOOLS_TITLE_CONTROLPANEL', 'index.php?option=com_admintools');

		ToolbarHelper::help(null, false, 'https://www.akeeba.com/documentation/admin-tools-joomla/admin-pw-protection.html');
	}

}