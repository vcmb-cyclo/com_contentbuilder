<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Plugin\System\AdminTools\Utility;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseDriver;
use Joomla\Database\DatabaseInterface;

abstract class Cache
{
	private static $cache = [];

	/**
	 * Returns the (cached) copy of all records in an Admin Tools table.
	 *
	 * This minimises the amount of database queries, allowing the plugin to work much faster
	 *
	 * @param   string  $key  The type of records to load: adminiplist, badwords, ipautoban, ipblock, redirects,
	 *                        wafblacklists, wafexceptions
	 *
	 * @return  array  A list of records in a shape that's convenient for handling by the plugin
	 */
	public static function getCache(string $key): array
	{
		if (isset(self::$cache[$key]))
		{
			return self::$cache[$key];
		}

		try
		{
			switch ($key)
			{
				case 'adminiplist':
					self::$cache[$key] = self::getAdminIPList();
					break;

				case 'badwords':
					self::$cache[$key] = self::getBadwords();
					break;

				case 'ipautoban':
					self::$cache[$key] = self::getIPAutoBan();
					break;

				case 'ipallow':
					self::$cache[$key] = self::getIPAllow();
					break;

				case 'ipblock':
					self::$cache[$key] = self::getIPBlock();
					break;

				case 'redirects':
					self::$cache[$key] = self::getRedirects();
					break;

				case 'wafblacklists':
					self::$cache[$key] = self::getWAFBlacklist();
					break;

				case 'wafexceptions':
					self::$cache[$key] = self::getWafexceptions();
					break;

				default:
					self::$cache[$key] = [];
					break;
			}
		}
		catch (\Exception $e)
		{
			self::$cache[$key] = [];
		}

		return self::$cache[$key];
	}

	public static function resetCache(string $key): void
	{
		if (isset(self::$cache[$key]))
		{
			unset (self::$cache[$key]);
		}
	}

	private static function getDbo(): DatabaseDriver
	{
		return Factory::getContainer()->get(DatabaseInterface::class);
	}

	private static function getAdminIPList(): array
	{
		$db    = self::getDbo();
		$query = $db->getQuery(true)
			->select($db->quoteName('ip'))
			->from($db->quoteName('#__admintools_adminiplist'));

		return $db->setQuery($query)->loadColumn() ?: [];
	}

	private static function getBadwords(): array
	{
		$db    = self::getDbo();
		$query = $db->getQuery(true)
			->select($db->quoteName('word'))
			->from($db->quoteName('#__admintools_badwords'));

		return $db->setQuery($query)->loadColumn() ?: [];
	}

	private static function getIPAutoBan(): array
	{
		$db    = self::getDbo();
		$query = $db->getQuery(true)
			->select('*')
			->from($db->quoteName('#__admintools_ipautoban'));

		return $db->setQuery($query)->loadAssocList('ip') ?: [];
	}

	private static function getIPBlock(): array
	{
		$db    = self::getDbo();
		$query = $db->getQuery(true)
			->select($db->quoteName('ip'))
			->from($db->quoteName('#__admintools_ipblock'));

		return $db->setQuery($query)->loadColumn() ?: [];
	}

	private static function getIPAllow(): array
	{
		$db    = self::getDbo();
		$query = $db->getQuery(true)
			->select($db->quoteName('ip'))
			->from($db->quoteName('#__admintools_ipallow'));

		return $db->setQuery($query)->loadColumn() ?: [];
	}

	private static function getRedirects(): array
	{
		$db    = self::getDbo();
		$query = $db->getQuery(true)
			->select([
				$db->quoteName('source'),
				$db->quoteName('dest'),
				$db->quoteName('keepurlparams'),
			])
			->from($db->quoteName('#__admintools_redirects'))
			->where($db->quoteName('published') . ' = 1')
			->order($db->quoteName('ordering') . ' ASC');

		return $db->setQuery($query)->loadAssocList('dest') ?: [];
	}

	private static function getWAFBlacklist(): array
	{
		$db    = self::getDbo();
		$query = $db->getQuery(true)
			->select([
				$db->quoteName('option'),
				$db->quoteName('view'),
				$db->quoteName('task'),
				$db->quoteName('query'),
				$db->quoteName('query_type'),
				$db->quoteName('query_content'),
				$db->quoteName('verb'),
				$db->quoteName('application'),
			])
			->from($db->quoteName('#__admintools_wafblacklists'))
			->where($db->quoteName('enabled') . ' = 1');

		return $db->setQuery($query)->loadAssocList() ?: [];
	}

	private static function getWafexceptions(): array
	{
		$db    = self::getDbo();
		$query = $db->getQuery(true)
			->select([
				$db->quoteName('option'),
				$db->quoteName('view'),
				$db->quoteName('query'),
			])
			->from($db->quoteName('#__admintools_wafexceptions'));

		return $db->setQuery($query)->loadAssocList() ?: [];
	}

}