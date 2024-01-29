<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Component\AdminTools\Administrator\Mixin;

defined('_JEXEC') or die;

use Akeeba\Component\AdminTools\Administrator\Helper\TemplateEmails;
use Akeeba\Component\AdminTools\Administrator\Model\ConfigurewafModel;
use Exception;
use Joomla\CMS\Language\Text;

trait SendTroubleshootingEmailTrait
{
	/**
	 * Sends a preemptive troubleshooting email to the user before taking an action which might lock them out.
	 *
	 * @param   string  $controllerName
	 *
	 * @return  void
	 */
	protected function sendTroubelshootingEmail($controllerName)
	{
		// Is sending this email blocked in the WAF configuration?
		try
		{
			/** @var ConfigurewafModel $configModel */
			$configModel = $this->getModel('Configurewaf', 'Administrator', ['ignore_request' => true]);
		}
		catch (Exception $e)
		{
			$configModel = false;
		}

		// The Core version does not have the Configurewaf model and must therefore not send such emails.
		if ($configModel === false)
		{
			return;
		}

		$wafConfig = $configModel->getConfig();
		$sendEmail = $wafConfig['troubleshooteremail'] ?? 1;

		if (!$sendEmail)
		{
			return;
		}

		// Can't send email if I don't about this controller
		$actionKey = 'COM_ADMINTOOLS_EMAIL_TROUBLESHOOTING_ACTION_' . $controllerName;

		if (Text::_($actionKey) == $actionKey)
		{
			return;
		}

		// Send the email
		try
		{
			$user     = $this->app->getIdentity();
			$siteName = $this->app->get('sitename');

			TemplateEmails::updateTemplate('com_admintools.troubleshooting');
			TemplateEmails::sendMail('com_admintools.troubleshooting', [
				'USER'                      => $user->name,
				'SITENAME'                  => $siteName,
				'ACTION'                    => Text::_($actionKey),
				'TROUBLESHOOTING_URLS'      => "-  https://akee.ba/lockedout4\n" .
					"-  https://akee.ba/500htaccess4\n" .
					"-  https://akee.ba/adminpassword4\n" .
					"-  https://akee.ba/403edituser4",
				'TROUBLESHOOTING_URLS_HTML' => "<ul><li>https://akee.ba/lockedout4</li>" .
					"<li>https://akee.ba/500htaccess4</li>" .
					"<li>https://akee.ba/adminpassword4</li>" .
					"<li>https://akee.ba/403edituser4</li></ul>",
			], $user, null, true);
		}
		catch (Exception $e)
		{
			/**
			 * The email sending will return an exception if the From or To email are invalid, the email sending
			 * configuration of the site is wrong, the sending timed out etc. We will add the error to Joomla's stack so
			 * that the user knows email sending did fail. Unlike the system plugin which absorbs these errors, here we
			 * are guaranteed to run as a privileged, backend user who can either fix email sending or get hold of an
			 * administrator who can.
			 */
			$this->app->enqueueMessage($e->getMessage(), 'warning');
		}
	}
}