<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Component\AdminTools\Administrator\Mixin;

defined('_JEXEC') or die;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Factory;
use Akeeba\Plugin\System\AdminTools\Feature\DoNoCreateNewAdmins;

trait TableNoSuperUsersCheckFlagsTrait
{
	/**
	 * Temporarily disable Admin Tools security checks for creating / editing Super Users.
	 *
	 * * The "Backend Edit Admin User" feature will prevent creating / editing temporary Super Users. Before doing
	 *   anything with the temporary user records we set a special flag to disable it for the next request.
	 * * The "Monitor Super User accounts" will automatically disable our newly created temporary Super User. We work
	 *   around it by setting the special session flag which tells this feature to replace the Super Users list.
	 *
	 * @param   bool  $noChecks
	 */
	public function setNoCheckFlags($noChecks = true)
	{
		// Workaround for "Backend Edit Admin User"
		if (class_exists(DoNoCreateNewAdmins::class) && method_exists(DoNoCreateNewAdmins::class, 'setTempDisableFlag'))
		{
			DoNoCreateNewAdmins::setTempDisableFlag($noChecks);
		}

		// Workaround for "Monitor Super User accounts"
		/** @var CMSApplication $app */
		$app     = Factory::getApplication();
		$session = $app->getSession();
		$session->set('com_admintools.superuserslist.createnew', null);

		if ($noChecks)
		{
			$session->set('com_admintools.superuserslist.createnew', true);
		}
	}

}