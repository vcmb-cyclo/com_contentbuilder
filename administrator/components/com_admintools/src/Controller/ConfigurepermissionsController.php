<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Component\AdminTools\Administrator\Controller;

defined('_JEXEC') or die;

use Akeeba\Component\AdminTools\Administrator\Mixin\ControllerCustomACLTrait;
use Akeeba\Component\AdminTools\Administrator\Mixin\ControllerEventsTrait;
use Akeeba\Component\AdminTools\Administrator\Mixin\ControllerRegisterTasksTrait;
use Akeeba\Component\AdminTools\Administrator\Mixin\ControllerReusableModelsTrait;
use Akeeba\Component\AdminTools\Administrator\Model\ConfigurepermissionsModel;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Router\Route;
use Joomla\Input\Input;

class ConfigurepermissionsController extends BaseController
{
	use ControllerEventsTrait;
	use ControllerCustomACLTrait;
	use ControllerRegisterTasksTrait;
	use ControllerReusableModelsTrait;

	public function __construct($config = [], MVCFactoryInterface $factory = null, ?CMSApplication $app = null, ?Input $input = null)
	{
		parent::__construct($config, $factory, $app, $input);

		$this->registerControllerTasks('main');
	}

	public function main($cachable = false, $urlparams = [])
	{
		$path = $this->input->getRaw('path', '');

		/** @var ConfigurepermissionsModel $model */
		$model = $this->getModel();
		$model->setState('path', $path);
		$model->applyPath();

		return $this->display($cachable, $urlparams);
	}

	public function savedefaults()
	{
		$this->checkToken();

		/** @var ConfigurepermissionsModel $model */
		$model = $this->getModel();
		$model->setState('dirperms', $this->input->getCmd('dirperms', '0755'));
		$model->setState('fileperms', $this->input->getCmd('fileperms', '0644'));
		$model->setState('perms_show_hidden', $this->input->getInt('perms_show_hidden', 0));
		$model->saveDefaults();

		$message = Text::_('COM_ADMINTOOLS_CONFIGUREPERMISSIONS_LBL_DEFAULTSSAVED');
		$this->setMessage($message, 'success');

		$returnUrl = Route::_('index.php?option=com_admintools&view=Configurepermissions', false);
		$this->setRedirect($returnUrl);
	}

	/**
	 * Saves the custom permissions and reloads the current view
	 */
	public function saveperms()
	{
		$this->checkToken();

		$this->saveCustomPermissions();

		$message = Text::_('COM_ADMINTOOLS_CONFIGUREPERMISSIONS_LBL_CUSTOMSAVED');
		$this->setMessage($message, 'success');

		$path      = $this->input->getRaw('path', '');
		$returnUrl = Route::_('index.php?option=com_admintools&view=Configurepermissions&path=' . urlencode($path), false);
		$this->setRedirect($returnUrl);
	}

	/**
	 * Saves the custom permissions, applies them and reloads the current view
	 */
	public function saveapplyperms()
	{
		// CSRF prevention
		$this->checkToken();

		$this->saveCustomPermissions(true);

		$message = Text::_('COM_ADMINTOOLS_CONFIGUREPERMISSIONS_LBL_CUSTOMSAVEDAPPLIED');
		$this->setMessage($message, 'success');

		$path      = $this->input->getRaw('path', '');
		$returnUrl = Route::_('index.php?option=com_admintools&view=Configurepermissions&path=' . urlencode($path), false);
		$this->setRedirect($returnUrl);
	}

	private function saveCustomPermissions($apply = false)
	{
		$path = $this->input->getRaw('path', '');

		/** @var ConfigurepermissionsModel $model */
		$model = $this->getModel();
		$model->setState('path', $path);
		$model->applyPath();

		$folders = $this->input->getRaw('folders', []);
		$files   = $this->input->getRaw('files', []);

		$model->setState('folders', $folders);
		$model->setState('files', $files);

		$model->savePermissions($apply);
	}
}