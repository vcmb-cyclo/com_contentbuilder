<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Component\AdminTools\Administrator\View\Seoandlinktools;

defined('_JEXEC') or die;

use Akeeba\Component\AdminTools\Administrator\Mixin\ViewTaskBasedEventsTrait;
use Akeeba\Component\AdminTools\Administrator\Model\SeoandlinktoolsModel;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

class HtmlView extends BaseHtmlView
{
	use ViewTaskBasedEventsTrait;

	/**
	 * The configuration for this feature
	 *
	 * @var  array
	 */
	public $linkToolsConfig;

	protected function onBeforeMain()
	{
		/** @var SeoandlinktoolsModel $model */
		$model                 = $this->getModel();
		$this->linkToolsConfig = $model->getConfig();

		ToolbarHelper::title(Text::_('COM_ADMINTOOLS_TITLE_SEOANDLINKTOOLS'), 'admintools');
		ToolbarHelper::apply();
		ToolbarHelper::save();
		ToolbarHelper::back('JTOOLBAR_BACK', 'index.php?option=com_admintools');

		ToolbarHelper::help(null, false, 'https://www.akeeba.com/documentation/admin-tools-joomla/ch02s12.html');

	}
}