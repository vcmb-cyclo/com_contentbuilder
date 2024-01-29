<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') || die;

use Joomla\CMS\Extension\PluginInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Event\DispatcherInterface;
use Akeeba\Plugin\Actionlog\AdminTools\Extension\AdminTools;

// Make sure that Joomla has registered the namespace for the plugin
if (!class_exists(AdminTools::class))
{
	JLoader::registerNamespace('\Akeeba\Plugin\Actionlog\AdminTools', realpath(__DIR__ . '/../src'));
}

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
		$container->set(
			PluginInterface::class,
			function (Container $container) {
				$config  = (array) PluginHelper::getPlugin('actionlog', 'admintools');
				$subject = $container->get(DispatcherInterface::class);

				$plugin = new AdminTools($subject, $config);

				$plugin->setApplication(Factory::getApplication());

				return $plugin;
			}
		);
	}
};
