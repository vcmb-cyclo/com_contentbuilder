<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Component\AdminTools\Administrator\Provider;

use Akeeba\Component\AdminTools\Administrator\Service\CacheCleaner as CacheCleanerService;
use Joomla\CMS\Cache\CacheControllerFactoryInterface;
use Joomla\CMS\Factory;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

class CacheCleaner implements ServiceProviderInterface
{
	public function register(Container $container)
	{
		$container->set(
			CacheCleanerService::class,
			function (Container $container) {
				$app = Factory::getApplication();

				return new CacheCleanerService(
					$app,
					$container->get(CacheControllerFactoryInterface::class)
				);
			}
		);
	}
}