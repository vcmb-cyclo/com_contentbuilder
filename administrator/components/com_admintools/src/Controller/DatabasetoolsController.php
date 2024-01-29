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
use Akeeba\Component\AdminTools\Administrator\Mixin\ControllerRegisterTasksTrait;
use Akeeba\Component\AdminTools\Administrator\Mixin\ControllerReusableModelsTrait;
use Akeeba\Component\AdminTools\Administrator\Model\DatabasetoolsModel;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Router\Route;
use Joomla\Input\Input;

class DatabasetoolsController extends BaseController
{
	use ControllerCustomACLTrait;
	use ControllerEventsTrait;
	use ControllerRegisterTasksTrait;
	use ControllerReusableModelsTrait;

	public function __construct($config = [], MVCFactoryInterface $factory = null, ?CMSApplication $app = null, ?Input $input = null)
	{
		parent::__construct($config, $factory, $app, $input);

		$this->registerControllerTasks('main');
	}

	public function optimize()
	{
		$this->main();
	}

	public function main()
	{
		/** @var DatabasetoolsModel $model */
		$model = $this->getModel();
		$from  = $this->input->getString('from', null);

		$tables    = (array) $model->findTables();
		$lastTable = $model->repairAndOptimise($from);
		$percent   = 100;

		if (!empty($lastTable))
		{
			$lastTableID = array_search($lastTable, $tables);
			$percent     = round(100 * ($lastTableID + 1) / count($tables));
			$percent     = min(max($percent, 1), 100);
		}

		$model->setState('lasttable', $lastTable);
		$model->setState('percent', $percent);

		$this->display(false);
	}

	public function purgesessions()
	{
		/** @var DatabasetoolsModel $model */
		$model = $this->getModel();
		$model->garbageCollectSessions();
		$model->purgeSessions();

		$redirectionUrl = Route::_('index.php?option=com_admintools', false);
		$this->setRedirect($redirectionUrl, Text::_('COM_ADMINTOOLS_DATABASETOOLS_LBL_PURGESESSIONS_COMPLETE'), 'success');
	}
}