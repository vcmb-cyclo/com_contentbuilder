<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Component\AdminTools\Administrator\View\Fixpermissions;

defined('_JEXEC') or die;

use Akeeba\Component\AdminTools\Administrator\Mixin\ViewTaskBasedEventsTrait;
use Akeeba\Component\AdminTools\Administrator\Model\FixpermissionsModel;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

class HtmlView extends BaseHtmlView
{
	use ViewTaskBasedEventsTrait;

	/**
	 * Do we need to perform more steps?
	 *
	 * @var  bool
	 */
	public $more;

	/**
	 * Percent complete
	 *
	 * @var  int
	 */
	public $percentage;

	protected function onBeforeMain()
	{
		$this->fixPermissions();
	}

	protected function onBeforeRun()
	{
		$this->fixPermissions();
	}

	private function fixPermissions()
	{
		/** @var FixpermissionsModel $model */
		$model = $this->getModel();
		$state = $model->getState('scanstate', false);

		$total = $model->totalFolders;
		$done  = $model->doneFolders;

		$percent = 100;
		$more    = false;

		ToolbarHelper::back('JTOOLBAR_BACK', 'index.php?option=com_admintools');

		if ($state)
		{
			if ($total > 0)
			{
				$percent = min(max(round(100 * $done / $total), 1), 100);
			}

			$more = true;
		}

		$this->more       = $more;
		$this->percentage = $percent;

		$this->setLayout('default');

		$this->document->getWebAssetManager()
			->useScript('com_admintools.fix_permissions');
	}
}