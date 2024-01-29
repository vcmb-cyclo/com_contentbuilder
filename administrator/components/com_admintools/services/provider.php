<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') || die;

use Akeeba\Component\AdminTools\Administrator\Extension\AdminToolsComponent;
use Akeeba\Component\AdminTools\Administrator\Provider\CacheCleaner;
use Akeeba\Component\AdminTools\Administrator\Provider\ComponentParameters;
use Joomla\CMS\Component\Router\RouterFactoryInterface;
use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;
use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\CMS\Extension\Service\Provider\RouterFactory;
use Joomla\CMS\HTML\Registry;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

/**
 * The banners service provider.
 *
 * @since  9.0.0
 */
return new class implements ServiceProviderInterface {
	/**
	 * Registers the service provider with a DI container.
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  void
	 *
	 * @since   7.0.0
	 */
	public function register(Container $container)
	{
		$container->registerServiceProvider(new MVCFactory('Akeeba\\Component\\AdminTools'));
		$container->registerServiceProvider(new ComponentDispatcherFactory('Akeeba\\Component\\AdminTools'));
		$container->registerServiceProvider(new RouterFactory('\\Akeeba\\Component\\AdminTools'));
		$container->registerServiceProvider(new CacheCleaner());
		$container->registerServiceProvider(new ComponentParameters('com_admintools'));

		$container->set(
			ComponentInterface::class,
			function (Container $container) {
				if (!class_exists(\Akeeba\UsageStats\Collector\StatsCollector::class))
				{
					try
					{
						require_once JPATH_ADMINISTRATOR . '/components/com_admintools/vendor/autoload.php';
					}
					catch (Throwable $e)
					{
						// Well, we tried…
					}
				}

				$component = new AdminToolsComponent($container->get(ComponentDispatcherFactoryInterface::class));

				$component->setRegistry($container->get(Registry::class));
				$component->setMVCFactory($container->get(MVCFactoryInterface::class));
				$component->setRouterFactory($container->get(RouterFactoryInterface::class));

				return $component;
			}
		);
	}
};
