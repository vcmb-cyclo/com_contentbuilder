<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Component\AdminTools\Site\Dispatcher;

defined('_JEXEC') or die;

use Akeeba\Component\AdminTools\Administrator\Dispatcher\Dispatcher as AdminDispatcher;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Document\HtmlDocument;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use RuntimeException;

class Dispatcher extends AdminDispatcher
{
	protected $viewMap = [
		'block'       => 'Block',
		'filescanner' => 'FileScanner',
	];

	protected function onBeforeDispatch()
	{
		// Basic setup
		$this->loadLanguage();
		$this->loadVersion();

		// Am I showing the Block view?
		$isBlockView = $this->isBlockView();

		// Apply view, controller, task etc
		$this->applyViewAndController();

		// NB! This has to go AFTER applying the view and controller to go through the necessary mapping.
		$isFileScannerView = $this->isFileScannerView();

		// If it's none of the known views we display Joomla's component not found error
		if (!$isBlockView && !$isFileScannerView)
		{
			throw new RuntimeException(Text::_('JLIB_APPLICATION_ERROR_COMPONENT_NOT_FOUND'), 404);
		}
	}

	private function isBlockView(): bool
	{
		// Were we explicitly requested to show the Block view?
		if (!$this->app->getSession()->get('com_admintools.block', false))
		{
			return false;
		}

		// Reset the custom block view so that the next request will display correctly.
		$this->app->getSession()->set('com_admintools.block', false);

		// Modify the input object to show the correct view
		$this->input->set('option', 'com_admintools');
		$this->input->set('view', 'Block');
		$this->input->set('task', 'main');
		$this->input->set('format', 'html');
		$this->input->set('controller', null);
		$this->input->set('layout', null);
		$this->input->set('tmpl', 'component');

		// Make sure we have an HTML document in the application. If not, FORCE IT.
		if(!($this->app->getDocument() instanceof HtmlDocument))
		{
			$htmlDocument = new HtmlDocument();
			$this->app->loadDocument($htmlDocument);

			if (isset(Factory::$document))
			{
				Factory::$document = $htmlDocument;
			}
		}

		// Set the status to 403.
		$response = $this->app->getResponse()->withStatus(403);
		$this->app->setResponse($response);

		return true;
	}

	private function isFileScannerView(): bool
	{
		$view = $this->input->getCmd('view', null);
		$task = $this->input->getCmd('task', 'main');
		$key  = $this->input->get('key', '', 'raw');

		$cParams              = ComponentHelper::getParams('com_admintools');
		$validKey             = $cParams->get('frontend_secret_word', '');
		$isFileScannerEnabled = $cParams->get('frontend_enable', 0) != 0;

		$inScannerView = (strtolower($view) == 'filescanner') && ($format = 'raw') && $isFileScannerEnabled && !empty($validKey) && ($validKey == $key);

		if (!$inScannerView)
		{
			return false;
		}

		$this->input->set('view', 'FileScanner');
		$this->input->set('task', $task);
		$this->input->set('format', 'raw');
		$this->input->set('controller', 'filescanner');
		$this->input->set('layout', null);
		$this->input->set('tmpl', null);

		return true;
	}

}