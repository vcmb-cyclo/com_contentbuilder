<?php
/*
 * @package   stats_collector
 * @copyright Copyright (c)2023-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\UsageStats\Collector\Sender\Adapter;

use Joomla\Http\HttpFactory;

/**
 * Information Sending adapter for Joomla sites, version 4 or later
 *
 * @since  1.0.0
 */
final class JoomlaAdapter implements AdapterInterface
{
	use ServerUrlTrait;

	/**
	 * @inheritDoc
	 */
	public function isAvailable(): bool
	{
		return defined('_JEXEC')
		       && version_compare(JVERSION, '4.0.0', 'ge');
	}

	/**
	 * @inheritDoc
	 */
	public function sendStatistics(array $queryParameters): void
	{
		$factory = new HttpFactory();
		$http    = $factory->getHttp(
			[
				'follow_location' => true,
				'userAgent'       => $this->getUserAgent(),
				'timeout'         => $this->getTimeout(),
			]
		);

		$http->get($this->getUrl($queryParameters));
	}
}