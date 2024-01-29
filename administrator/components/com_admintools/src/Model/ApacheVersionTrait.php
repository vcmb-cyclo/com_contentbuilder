<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

/**
 * @package     Akeeba\Component\AdminTools\Administrator\Model
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */

namespace Akeeba\Component\AdminTools\Administrator\Model;


defined('_JEXEC') or die;

trait ApacheVersionTrait
{
	/**
	 * Guesses and returns the Apache version family.
	 *
	 * @return  string  1.1, 1.3, 2.0, 2.2, 2.5 or 0.0 (if no match)
	 */
	private function apacheVersion()
	{
		// Do we already have a specific version stored?
		if (!empty($this->serverVersion ?? null))
		{
			return $this->serverVersion;
		}

		// Get the server string
		$serverString = $_SERVER['SERVER_SOFTWARE'];

		// Not defined? Assume Apache 2.0.
		if (empty($serverString))
		{
			return '2.0';
		}

		// LiteSpeed? Fake it.
		if (strtoupper(substr($serverString, 0, 9)) == 'LITESPEED')
		{
			return '2.0';
		}

		// Not Apache? Return 0.0
		if (strtoupper(substr($serverString, 0, 6)) !== 'APACHE')
		{
			return '0.0';
		}

		// No slash after Apache? Assume 2.5
		if (strlen($serverString) < 7)
		{
			return '2.5';
		}

		if (substr($serverString, 6, 1) != '/')
		{
			return '2.5';
		}

		// Strip part past the version string
		$serverString = substr($serverString, 7);

		$v = substr($serverString, 0, 3);
		switch ($v)
		{
			case '1.3':
			case '2.0':
			case '2.2':
			case '2.4':
			case '2.5':
				return $v;

				break;

			default:
				if (version_compare($v, '1.3', 'lt'))
				{
					return '1.1';
				}
				else
				{
					return '2.2';
				}

				break;
		}
	}
}