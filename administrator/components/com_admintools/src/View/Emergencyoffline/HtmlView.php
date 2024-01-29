<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Component\AdminTools\Administrator\View\Emergencyoffline;

defined('_JEXEC') or die;

use Akeeba\Component\AdminTools\Administrator\Mixin\ViewTaskBasedEventsTrait;
use Akeeba\Component\AdminTools\Administrator\Model\EmergencyofflineModel;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

class HtmlView extends BaseHtmlView
{
	use ViewTaskBasedEventsTrait;

	/** @var    bool    Is the site currently offline? */
	public $offline;

	/** @var  string    Htaccess contents */
	public $htaccess;

	public function onBeforeMain()
	{
		/** @var EmergencyofflineModel $model */
		$model = $this->getModel();

		$this->offline  = $model->isOffline();
		$this->htaccess = $model->getHtaccess();

		$this->setLayout('default');

		ToolbarHelper::title(Text::_('COM_ADMINTOOLS_TITLE_EMERGENCYOFFLINE'), 'admintools');
		ToolbarHelper::back('COM_ADMINTOOLS_TITLE_CONTROLPANEL', 'index.php?option=com_admintools');
		ToolbarHelper::help(null, false, 'https://www.akeeba.com/documentation/admin-tools-joomla/emergency-offline.html');
	}
}