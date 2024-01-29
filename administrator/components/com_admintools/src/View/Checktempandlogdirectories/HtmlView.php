<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Component\AdminTools\Administrator\View\Checktempandlogdirectories;

defined('_JEXEC') or die;

use Akeeba\Component\AdminTools\Administrator\Mixin\ViewTaskBasedEventsTrait;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

class HtmlView extends BaseHtmlView
{
	use ViewTaskBasedEventsTrait;

	protected function onBeforeMain()
	{
		$this->document->getWebAssetManager()
			->useScript('com_admintools.check_tmp_log');

		Text::script('COM_ADMINTOOLS_CHECKTEMPANDLOGDIRECTORIES_LBL_CHECKCOMPLETED', true);
		Text::script('COM_ADMINTOOLS_CHECKTEMPANDLOGDIRECTORIES_LBL_CHECKFAILED', true);
	}
}