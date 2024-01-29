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
use Akeeba\Component\AdminTools\Administrator\Mixin\TempSuperUserChecksTrait;
use Joomla\CMS\MVC\Controller\AdminController;

class TempsuperusersController extends AdminController
{
	use ControllerEventsTrait;
	use ControllerCustomACLTrait;
	use TempSuperUserChecksTrait;

	protected $text_prefix = 'COM_ADMINTOOLS_TEMPSUPERUSERS';

	public function getModel($name = 'Tempsuperuser', $prefix = 'Administrator', $config = ['ignore_request' => true])
	{
		return parent::getModel($name, $prefix, $config);
	}

	protected function onBeforeExecute(&$task)
	{
		$this->assertNotTemporary();
	}
}