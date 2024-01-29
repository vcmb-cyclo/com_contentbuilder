<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Component\AdminTools\Administrator\Model;

defined('_JEXEC') or die;

use Akeeba\Component\AdminTools\Administrator\Mixin\ModelCopyTrait;
use Akeeba\Component\AdminTools\Administrator\Mixin\RunPluginsTrait;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Form\FormFactoryInterface;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Plugin\PluginHelper;

#[\AllowDynamicProperties]
class AllowlistModel extends AdminModel
{
	use ModelCopyTrait;
	use RunPluginsTrait;

	public function __construct($config = [], MVCFactoryInterface $factory = null, FormFactoryInterface $formFactory = null)
	{
		parent::__construct($config, $factory, $formFactory);

		$this->_parent_table = '';
	}

	/**
	 * @inheritDoc
	 */
	public function getForm($data = [], $loadData = true)
	{
		$form = $this->loadForm(
			'com_admintools.allowlist',
			'allowlist',
			[
				'control'   => 'jform',
				'load_data' => $loadData,
			]
		) ?: false;

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	protected function loadFormData()
	{
		/** @var CMSApplication $app */
		$app  = Factory::getApplication();
		$data = $app->getUserState('com_admintools.edit.allowlist.data', []);
		$pk   = (int) $this->getState($this->getName() . '.id');
		$item = ($pk ? (object) $this->getItem()->getProperties() : false) ?: [];

		$data = $data ?: $item;

		$this->preprocessData('com_admintools.allowlist', $data);

		return $data;
	}

	protected function preprocessForm(Form $form, $data, $group = 'content')
	{
		/** @var ControlpanelModel $cpanelModel */
		$cpanelModel = $this->getMVCFactory()->createModel('Controlpanel', 'Administrator', ['ignore_request' => true]);
		$myIP        = $cpanelModel->getVisitorIP();

		$form->setFieldAttribute('your_ip', 'description', sprintf("<code>%s</code>", htmlentities($myIP)));

		// Import the appropriate plugin group.
		PluginHelper::importPlugin($group);

		// Trigger the form preparation event.
		$this->triggerPluginEvent('onContentPrepareForm', [$form, $data]);
	}


}