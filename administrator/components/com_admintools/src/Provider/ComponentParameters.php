<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Component\AdminTools\Administrator\Provider;

use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

class ComponentParameters implements ServiceProviderInterface
{
	private $defaultExtension;

	public function __construct(string $defaultExtension)
	{
		$this->defaultExtension = $defaultExtension;
	}

	public function register(Container $container)
	{
		$container->set(
			\Akeeba\Component\AdminTools\Administrator\Service\ComponentParameters::class,
			function (Container $container) {
				return new \Akeeba\Component\AdminTools\Administrator\Service\ComponentParameters(
					$container->get(\Akeeba\Component\AdminTools\Administrator\Service\CacheCleaner::class),
					$this->defaultExtension
				);
			}
		);
	}
}