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
use Joomla\CMS\MVC\Controller\FormController;

class AllowlistController extends FormController
{
	use ControllerEventsTrait;
	use ControllerCustomACLTrait;

	protected $text_prefix = 'COM_ADMINTOOLS_ALLOWLIST';
}