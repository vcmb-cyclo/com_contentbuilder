<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Component\AdminTools\Administrator\View\Cleantempdirectory;

defined('_JEXEC') or die;

use Akeeba\Component\AdminTools\Administrator\Model\CleantempdirectoryModel;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

class HtmlView extends BaseHtmlView
{
	/**
	 * Do we have more processing to do?
	 *
	 * @var  bool
	 */
	public $more;

	/**
	 * Percentage complete, 0 to 100
	 *
	 * @var  int
	 */
	public $percentage;

	public function display($tpl = null)
	{
		/** @var CleantempdirectoryModel $model */
		$model = $this->getModel();
		$state = $model->getState('scanstate', false);

		$total   = max(1, $model->totalFolders);
		$done    = $model->doneFolders;
		$percent = 100;
		$more    = false;

		if ($state)
		{
			$more = true;

			if ($total > 0)
			{
				$percent = min(max(round(100 * $done / $total), 1), 100);
			}
		}

		$this->more       = $more;
		$this->percentage = $percent;

		$this->setLayout('default');

		$this->document->getWebAssetManager()
			->useScript('com_admintools.clean_tmp');

		parent::display($tpl);
	}

}