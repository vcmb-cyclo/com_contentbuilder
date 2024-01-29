<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Extension\PluginInterface;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Event\DispatcherInterface;
use Akeeba\Plugin\System\AdminTools\Extension\AdminTools;

return new class implements ServiceProviderInterface {
	/**
	 * Registers the service provider with a DI container.
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  void
	 *
	 * @since   9.0.0
	 */
	public function register(Container $container)
	{
		$container->registerServiceProvider(new MVCFactory('Akeeba\\Component\\AdminTools'));

		$container->set(
			PluginInterface::class,
			function (Container $container) {
				$config     = (array) PluginHelper::getPlugin('system', 'admintools');
				$dispatcher = $container->get(DispatcherInterface::class);

				$plugin = new AdminTools($dispatcher, $config);

				$plugin->setApplication(Factory::getApplication());
				$plugin->setDatabase($container->get(DatabaseInterface::class));

				$plugin->initalisePlugin();

				return $plugin;
			}
		);
	}
};
