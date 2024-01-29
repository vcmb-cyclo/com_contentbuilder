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
use Joomla\CMS\MVC\Controller\FormController;

class TempsuperuserController extends FormController
{
	use ControllerEventsTrait;
	use ControllerCustomACLTrait;
	use TempSuperUserChecksTrait;

	protected $text_prefix = 'COM_ADMINTOOLS_TEMPSUPERUSER';

	protected function allowEdit($data = [], $key = 'id')
	{
		$this->assertNotTemporary();

		$pk = $data[$key] ?? null;

		$this->assertNotMyself($pk);

		return parent::allowEdit($data, $key);
	}

	protected function allowAdd($data = [])
	{
		$this->assertNotTemporary();

		return parent::allowAdd($data);
	}

	protected function allowSave($data, $key = 'id')
	{
		$this->assertNotTemporary();

		$pk = $data[$key] ?? null;

		$this->assertNotMyself($pk);

		return parent::allowSave($data, $key);
	}


}