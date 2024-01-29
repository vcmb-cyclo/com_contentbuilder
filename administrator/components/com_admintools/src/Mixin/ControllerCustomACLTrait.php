<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Component\AdminTools\Administrator\Mixin;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\User\User;
use RuntimeException;

trait ControllerCustomACLTrait
{
	protected function onBeforeExecute(&$task)
	{
		$this->akeebaBackupACLCheck($this->getName(), $this->task);
	}

	/**
	 * Checks if the currently logged in user has the required ACL privileges to access the current view. If not, a
	 * RuntimeException is thrown.
	 *
	 * @return  void
	 */
	protected function akeebaBackupACLCheck($view, $task)
	{
		// Akeeba Backup-specific ACL checks. All views not listed here are limited by the akeeba.configure privilege.
		$viewACLMap = [
			'Emergencyoffline'           => 'admintools.security',
			'Mainpassword'               => 'admintools.security',
			'Adminpassword'              => 'admintools.security',
			'ConfigMaker'                => 'admintools.security',
			'Htaccessmaker'              => 'admintools.security',
			'Webconfigmaker'             => 'admintools.security',
			'Nginxconfmaker'             => 'admintools.security',
			'Webapplicationfirewall'     => 'admintools.security',
			'Configurewaf'               => 'admintools.security',
			'Wafexceptions'              => 'admintools.security',
			'Wafdenylists'               => 'admintools.security',
			'Adminallowlists'            => 'admintools.security',
			'Disallowlists'              => 'admintools.security',
			'Badwords'                   => 'admintools.security',
			'Blockedrequestslog'         => 'admintools.security',
			'Autobannedaddresses'        => 'admintools.security',
			'Ipautobanhistories'         => 'admintools.security',
			'Unblockip'                  => 'admintools.security',
			'Scanalerts'                 => 'admintools.security',
			'Scanner'                    => 'admintools.security',
			'Scans'                      => 'admintools.security',
			'Configurepermissions'       => 'admintools.maintenance',
			'Fixpermissions'             => 'admintools.maintenance',
			'Tempsuperusers'             => 'admintools.security',
			'Seoandlinktools'            => 'admintools.utils',
			'Cleantempdirectory'         => 'admintools.maintenance',
			'Checktempandlogdirectories' => 'admintools.maintenance',
			'Databasetools'              => 'admintools.maintenance',
			'Urlredirections'            => 'admintools.maintenance',
			'Exportimport'               => 'admintools.security',
			'Quickstart'                 => 'admintools.security',
			'Schedulinginformation'      => 'admintools.security',
		];

		$view = strtolower($view ?? 'controlpanel');
		$task = strtolower($task ?? 'main');

		// Default
		$privilege = 'admintools.utils';

		// Just the view was found
		if (array_key_exists($view, $viewACLMap))
		{
			$privilege = $viewACLMap[$view];
		}

		// The view AND task was found
		if (array_key_exists($view . '.' . $task, $viewACLMap))
		{
			$privilege = $viewACLMap[$view . '.' . $task];
		}

		// If an empty privilege is defined do not perform any ACL checks
		if (empty($privilege))
		{
			return;
		}

		$user = Factory::getApplication()->getIdentity() ?? (new User());

		if (!$user->authorise($privilege, 'com_admintools'))
		{
			throw new RuntimeException(Text::_('JERROR_ALERTNOAUTHOR'), 403);
		}
	}

}