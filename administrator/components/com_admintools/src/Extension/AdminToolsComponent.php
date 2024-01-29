<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Component\AdminTools\Administrator\Extension;

defined('_JEXEC') or die;

use Akeeba\Component\AdminTools\Administrator\Service\CacheCleaner;
use Akeeba\Component\AdminTools\Administrator\Service\ComponentParameters;
use Akeeba\Component\AdminTools\Administrator\Service\Html\AdminTools;
use Joomla\CMS\Categories\CategoryServiceInterface;
use Joomla\CMS\Categories\CategoryServiceTrait;
use Joomla\CMS\Component\Router\RouterServiceInterface;
use Joomla\CMS\Component\Router\RouterServiceTrait;
use Joomla\CMS\Extension\BootableExtensionInterface;
use Joomla\CMS\Extension\MVCComponent;
use Joomla\CMS\HTML\HTMLRegistryAwareTrait;
use Joomla\DI\Container;
use Psr\Container\ContainerInterface;

class AdminToolsComponent extends MVCComponent implements
	BootableExtensionInterface, CategoryServiceInterface, RouterServiceInterface
{
	use HTMLRegistryAwareTrait;
	use RouterServiceTrait;
	use CategoryServiceTrait;

	/**
	 * The container we were created with
	 *
	 * @var   Container
	 * @since 7.2.0
	 */
	private $container;

	/**
	 * Booting the extension. This is the function to set up the environment of the extension like
	 * registering new class loaders, etc.
	 *
	 * If required, some initial set up can be done from services of the container, eg.
	 * registering HTML services.
	 *
	 * @param   ContainerInterface  $container  The container
	 *
	 * @return  void
	 * @since   7.0.0
	 */
	public function boot(ContainerInterface $container)
	{
		$this->container = $container;

		$this->getRegistry()->register('admintools', new AdminTools());
	}

	/**
	 * Returns the Container the extension was created with.
	 *
	 * We are going to use it wherever we are not instantiated through the extension object, e.g. fields.
	 *
	 * @return  Container
	 * @since   7.2.0
	 */
	public function getContainer(): Container
	{
		return $this->container;
	}

	public function getComponentParametersService(): ComponentParameters
	{
		return $this->container->get(ComponentParameters::class);
	}

	public function getCacheCleanerService(): CacheCleaner
	{
		return $this->container->get(CacheCleaner::class);
	}
}