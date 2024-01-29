<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Component\AdminTools\Administrator\Dispatcher;

defined('_JEXEC') or die;

use Akeeba\Component\AdminTools\Administrator\Mixin\TriggerEventTrait;
use Akeeba\Component\AdminTools\Administrator\Model\MainpasswordModel;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Dispatcher\ComponentDispatcher;
use Joomla\CMS\Document\HtmlDocument;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\String\Inflector;
use Throwable;

class Dispatcher extends ComponentDispatcher
{
	use TriggerEventTrait;

	protected $defaultController = 'controlpanel';

	protected $viewMap = [
		'blacklistedaddresses'    => 'disallowlists',
		'blacklistedaddress'      => 'disallowlist',
		'configurefixpermissions' => 'configurepermissions',
		'exceptionsfromwafs'      => 'wafexceptions',
		'exceptionsfromwaf'       => 'wafexception',
		'importandexport'         => 'exportimport',
		'masterpassword'          => 'mainpassword',
		'redirections'            => 'urlredirections',
		'redirection'             => 'urlredirection',
		'securityexceptions'      => 'blockedrequestslog',
		'wafblacklistedrequests'  => 'wafdenylists',
		'wafblacklistedrequest'   => 'wafdenylist',
		'whitelistedaddresses'    => 'adminallowlists',
		'whitelistedaddress'      => 'adminallowlist',
	];

	public function dispatch()
	{
		// Check the minimum supported PHP version
		$minPHPVersion = '7.4.0';
		$softwareName  = 'Admin Tools <small>for Joomla!</small>';
		$silentResults = $this->app->isClient('site');

		if (version_compare(PHP_VERSION, $minPHPVersion, 'lt'))
		{
			die(
				sprintf(
					'%s requires PHP %s or later. Your site is running on PHP %s.',
					$softwareName,
					$minPHPVersion,
					PHP_VERSION
				)
			);
		}

		try
		{
			$this->triggerEvent('onBeforeDispatch');

			parent::dispatch();

			// This will only execute if there is no redirection set by the Controller
			$this->triggerEvent('onAfterDispatch');
		}
		catch (Throwable $e)
		{
			$title = 'Admin Tools <small>for Joomla!</small>';
			$isPro = false;

			// Frontend: forwards errors 401, 403 and 404 to Joomla
			if (in_array($e->getCode(), [401, 403, 404]) && $this->app->isClient('site'))
			{
				throw $e;
			}

			if (!(include_once JPATH_ADMINISTRATOR . '/components/com_admintools/tmpl/common/errorhandler.php'))
			{
				throw $e;
			}
		}
	}

	protected function onBeforeDispatch()
	{
		$this->loadLanguage();

		$this->applyViewAndController();

		$this->loadVersion();

		$this->loadCommonStaticMedia();

		$this->mainPasswordCheck();
	}

	protected function loadLanguage(): void
	{
		$jLang = $this->app->getLanguage();

		$jLang->load($this->option, JPATH_ADMINISTRATOR);

		if (!$this->app->isClient('administrator'))
		{
			$jLang->load($this->option, JPATH_SITE);
		}
	}

	private function loadCommonStaticMedia()
	{
		// Make sure we run under a CMS application
		if (!($this->app instanceof CMSApplication))
		{
			return;
		}

		// Make sure the document is HTML
		$document = $this->app->getDocument();

		if (!($document instanceof HtmlDocument))
		{
			return;
		}

		// Finally, load our 'common' preset
		$webAssetManager = $document->getWebAssetManager();
		$webAssetManager
			->usePreset('com_admintools.backend');

		if (version_compare(JVERSION, '4.999.999', 'gt'))
		{
			$webAssetManager
				->useStyle('com_admintools.j5dark');
		}
	}

	protected function applyViewAndController(): void
	{
		$controller = $this->input->getCmd('controller', null);
		$view       = $this->input->getCmd('view', null);
		$task       = $this->input->getCmd('task', 'main');

		if (strpos($task, '.') !== false)
		{
			// Explode the controller.task command.
			[$controller, $task] = explode('.', $task);
		}

		if (empty($controller) && empty($view))
		{
			$controller = $this->defaultController;
			$view       = $this->defaultController;
		}
		elseif (empty($controller) && !empty($view))
		{
			$view       = $this->mapView($view);
			$controller = $view;
		}
		elseif (!empty($controller) && empty($view))
		{
			$view = $controller;
		}

		$controller = strtolower($controller);
		$view       = strtolower($view);

		$this->input->set('view', $view);
		$this->input->set('controller', $controller);
		$this->input->set('task', $task);
	}

	private function mapView(string $view)
	{
		$view = strtolower($view);

		return $this->viewMap[$view] ?? $view;
	}

	private function mainPasswordCheck()
	{
		$view       = $this->input->getCmd('view');
		$view       = Inflector::singularize($view);
		$controller = Inflector::singularize($this->input->getCmd('controller', $view));

		try
		{
			/** @var MainpasswordModel $model */
			$model = $this->mvcFactory->createModel('Mainpassword', 'Administrator', ['ignore_request' => true]);
		}
		catch (\Exception $e)
		{
			return;
		}

		if ($model === false)
		{
			return;
		}

		if ($model->accessAllowed($view) && $model->accessAllowed($controller))
		{
			return;
		}

		$this->app->enqueueMessage(Text::_('COM_ADMINTOOLS_CONTROLPANEL_ERR_NOTAUTHORIZED'), 'error');
		$this->app->redirect(Route::_('index.php?option=com_admintools&view=Controlpanel', false));
	}

	protected function loadVersion()
	{
		$filePath = JPATH_ADMINISTRATOR . '/components/com_admintools/version.php';

		if (@file_exists($filePath) && is_file($filePath))
		{
			include_once $filePath;
		}

		if (!defined('ADMINTOOLS_VERSION'))
		{
			define('ADMINTOOLS_VERSION', 'dev');
		}

		if (!defined('ADMINTOOLS_DATE'))
		{
			define('ADMINTOOLS_DATE', gmdate('Y-m-d'));
		}

		if (!defined('ADMINTOOLS_PRO'))
		{
			$isPro = @file_exists(JPATH_ADMINISTRATOR . '/components/com_admintools/src/Controller/ScansController.php');

			define('ADMINTOOLS_PRO', $isPro ? '1' : '0');
		}
	}
}