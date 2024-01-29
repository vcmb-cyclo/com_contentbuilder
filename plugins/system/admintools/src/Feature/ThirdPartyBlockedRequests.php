<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Plugin\System\AdminTools\Feature;

defined('_JEXEC') || die;

class ThirdPartyBlockedRequests extends Base
{
	/**
	 * Log a security exception coming from a third party application. It's
	 * supposed to be used by 3PD to log security exceptions in Admin Tools'
	 * log.
	 *
	 * @param   string   $reason     The blocking reason to show to the administrator. MANDATORY.
	 * @param   string   $message    The message to show to the user being blocked. MANDATORY.
	 * @param   array    $extraInfo  Any extra information to record to the log file (hash array).
	 * @param   boolean  $autoban    OBSOLETE. No longer used.
	 *
	 * @return  void
	 */
	public function onAdminToolsThirdpartyException($reason, $message, $extraInfo = [], $autoban = false)
	{
		if (empty($message))
		{
			return;
		}

		// Block the request
		$this->exceptionsHandler->blockRequest('external', $message, $extraInfo, $reason);
	}
}
