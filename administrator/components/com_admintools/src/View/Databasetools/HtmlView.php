<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Component\AdminTools\Administrator\View\Databasetools;

defined('_JEXEC') or die;

use Akeeba\Component\AdminTools\Administrator\Mixin\ViewLoadAnyTemplateTrait;
use Akeeba\Component\AdminTools\Administrator\Mixin\ViewTaskBasedEventsTrait;
use Akeeba\Component\AdminTools\Administrator\Model\DatabasetoolsModel;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

class HtmlView extends BaseHtmlView
{
	use ViewLoadAnyTemplateTrait;
	use ViewTaskBasedEventsTrait;

	/**
	 * Table being processed
	 *
	 * @var  string
	 */
	public $table;

	/**
	 * Percent complete
	 *
	 * @var  int
	 */
	public $percent;

	public function display($tpl = null)
	{
		/** @var DatabasetoolsModel $model */
		$model         = $this->getModel();
		$this->table   = $model->getState('lasttable', '');
		$this->percent = $model->getState('percent', '');

		$this->setLayout('optimize');

		$this->document->getWebAssetManager()
			->useScript('com_admintools.dbtools');

		ToolbarHelper::title(Text::_('COM_ADMINTOOLS_TITLE_DBTOOLS'), 'admintools');
		ToolbarHelper::back('COM_ADMINTOOLS_TITLE_CONTROLPANEL', 'index.php?option=com_admintools');

		parent::display($tpl);
	}
}