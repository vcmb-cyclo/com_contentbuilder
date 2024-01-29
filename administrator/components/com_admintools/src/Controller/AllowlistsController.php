<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Component\AdminTools\Administrator\Controller;

defined('_JEXEC') or die;

use Akeeba\Component\AdminTools\Administrator\Mixin\ControllerCopyTrait;
use Akeeba\Component\AdminTools\Administrator\Mixin\ControllerCustomACLTrait;
use Akeeba\Component\AdminTools\Administrator\Mixin\ControllerEventsTrait;
use Akeeba\Component\AdminTools\Administrator\Mixin\ControllerRegisterTasksTrait;
use Akeeba\Component\AdminTools\Administrator\Mixin\ControllerReusableModelsTrait;
use Joomla\CMS\MVC\Controller\AdminController;

class AllowlistsController extends AdminController
{
	use ControllerEventsTrait;
	use ControllerCustomACLTrait;
	use ControllerCopyTrait;
	use ControllerReusableModelsTrait;
	use ControllerRegisterTasksTrait;

	protected $text_prefix = 'COM_ADMINTOOLS_ALLOWLISTS';

	public function getModel($name = 'Allowlist', $prefix = 'Administrator', $config = ['ignore_request' => true])
	{
		return parent::getModel($name, $prefix, $config);
	}

	public function display($cachable = false, $urlparams = [])
	{
		$view        = $this->getView();
		$cpanelModel = $this->getModel('Controlpanel', 'Administrator', ['ignore_request' => true]);

		$view->setModel($cpanelModel, false);

		return parent::display($cachable, $urlparams);
	}

}