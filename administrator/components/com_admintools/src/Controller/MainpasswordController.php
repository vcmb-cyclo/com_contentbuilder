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
use Akeeba\Component\AdminTools\Administrator\Model\MainpasswordModel;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;

class MainpasswordController extends BaseController
{
	use ControllerEventsTrait;
	use ControllerCustomACLTrait;
	use ControllerRegisterTasksTrait;
	use ControllerReusableModelsTrait;

	public function main()
	{
		$this->display(false);
	}

	public function apply()
	{
		$this->checkToken();

		$masterpw = $this->input->getRaw('mainpw', '');
		$views    = $this->input->getCmd('views', []);
		$views    = is_array($views) ? $views : [];

		$restrictedViews = array_keys(array_filter($views, function ($locked) {
			return $locked == 1;
		}));

		/** @var MainpasswordModel $model */
		$model = $this->getModel();
		$model->saveSettings($masterpw, $restrictedViews);

		$message = Text::_('COM_ADMINTOOLS_MAINPASSWORD_LBL_SAVED');
		$this->setMessage($message, 'success');

		$redirectUrl = Route::_('index.php?option=com_admintools&view=Mainpassword', false);
		$this->setRedirect($redirectUrl);
	}

	public function save()
	{
		$this->apply();

		$redirectUrl = Route::_('index.php?option=com_admintools', false);
		$this->setRedirect($redirectUrl);
	}
}