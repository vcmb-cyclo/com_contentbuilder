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
use Akeeba\Component\AdminTools\Administrator\Model\SeoandlinktoolsModel;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;

class SeoandlinktoolsController extends BaseController
{
	use ControllerEventsTrait;
	use ControllerCustomACLTrait;
	use ControllerRegisterTasksTrait;
	use ControllerReusableModelsTrait;

	public function main()
	{
		$this->display(false);
	}

	public function save()
	{
		$this->checkToken();

		/** @var SeoandlinktoolsModel $model */
		$model = $this->getModel();

		$data = $this->input->getArray();

		$model->saveConfig($data);


		$msg = Text::_('COM_ADMINTOOLS_SEOANDLINKTOOLS_LBL_CONFIGSAVED');
		$this->setMessage($msg, 'success');

		$redirectUrl = Route::_('index.php?option=com_admintools&view=Controlpanel', false);
		$this->setRedirect($redirectUrl);
	}

	public function apply()
	{
		$this->save();

		$redirectUrl = Route::_('index.php?option=com_admintools&view=Seoandlinktools', false);
		$this->setRedirect($redirectUrl);
	}
}