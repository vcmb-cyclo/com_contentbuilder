<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Component\AdminTools\Administrator\Model;

defined('_JEXEC') or die;

use Akeeba\UsageStats\Collector\Constants\SoftwareType;
use Akeeba\UsageStats\Collector\StatsCollector;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

#[\AllowDynamicProperties]
class UsageStatisticsModel extends BaseDatabaseModel
{
	/**
	 * Send site information to the remove collection service
	 *
	 * @return  bool
	 */
	public function collectStatistics()
	{
		$params = ComponentHelper::getParams('com_admintools');

		// Is data collection turned off?
		if (!$params->get('stats_enabled', 1))
		{
			return false;
		}

		// Make sure the autoloader for our Composer dependencies is loaded.
		if (!class_exists(StatsCollector::class))
		{
			try
			{
				require_once JPATH_ADMINISTRATOR . '/components/com_admintools/vendor/autoload.php';
			}
			catch (\Throwable $e)
			{
				return false;
			}
		}

		// Usage stats collection class is undefined, we cannot continue
		if (!class_exists(StatsCollector::class, false))
		{
			return false;
		}

		if (!defined('ADMINTOOLS_VERSION'))
		{
			@include_once __DIR__ . '/../../version.php';
		}

		if (!defined('ADMINTOOLS_VERSION'))
		{
			define('ADMINTOOLS_VERSION', 'dev');
			define('ADMINTOOLS_DATE', date('Y-m-d'));
		}

		try
		{
			(new StatsCollector(
				SoftwareType::AT_JOOMLA_CORE,
				ADMINTOOLS_VERSION,
				defined('ADMINTOOLS_PRO') ? ADMINTOOLS_PRO : false
			))->conditionalSendStatistics();
		}
		catch (\Throwable $e)
		{
			return false;
		}

		return true;
	}
}