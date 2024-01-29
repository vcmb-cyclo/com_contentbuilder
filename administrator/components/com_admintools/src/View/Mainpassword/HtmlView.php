<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Component\AdminTools\Administrator\View\Mainpassword;

defined('_JEXEC') or die;

use Akeeba\Component\AdminTools\Administrator\Mixin\ViewTaskBasedEventsTrait;
use Akeeba\Component\AdminTools\Administrator\Model\MainpasswordModel;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

class HtmlView extends BaseHtmlView
{
	use ViewTaskBasedEventsTrait;

	/**
	 * Current main password
	 *
	 * @var  string
	 */
	public $mainPassword;

	/**
	 * List of views that could be password-protected
	 *
	 * @var  array
	 */
	public $items;

	public function onBeforeMain()
	{
		/** @var MainpasswordModel $model */
		$model              = $this->getModel();
		$this->mainPassword = $model->getMainpassword();
		$this->items        = $model->getItemList();

		$this->document->getWebAssetManager()
			->useScript('com_admintools.main_password');

		ToolbarHelper::title(Text::_('COM_ADMINTOOLS_TITLE_MAINPASSWORD'), 'admintools');
		ToolbarHelper::apply();
		ToolbarHelper::save();
		ToolbarHelper::back('JTOOLBAR_BACK', 'index.php?option=com_admintools');
	}

}