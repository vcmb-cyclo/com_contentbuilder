<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Plugin\Actionlog\AdminTools\Extension;

defined('_JEXEC') or die;

use Akeeba\Component\AdminTools\Administrator\Controller\DatabasetoolsController;
use Akeeba\Component\AdminTools\Administrator\Model\DatabasetoolsModel;
use Akeeba\Component\AdminTools\Administrator\Model\ScanalertsModel;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\User\User;
use Joomla\Component\Actionlogs\Administrator\Plugin\ActionLogPlugin;
use Joomla\Event\Event;
use Joomla\Event\SubscriberInterface;
use Joomla\String\Inflector;
use ReflectionMethod;

class AdminTools extends ActionLogPlugin implements SubscriberInterface
{
	/**
	 * Load the language file on instantiation.
	 *
	 * @var    boolean
	 * @since  3.1
	 */
	protected $autoloadLanguage = true;

	private $defaultExtension = 'com_admintools';

	/**
	 * Returns an array of events this subscriber will listen to.
	 *
	 * @return  array
	 *
	 * @since   9.0.0
	 */
	public static function getSubscribedEvents(): array
	{
		// Only subscribe events if the component is installed and enabled
		if (!ComponentHelper::isEnabled('com_akeebabackup'))
		{
			return [];
		}

		// Register all public onSomething methods as event handlers
		$events   = [];
		$refClass = new \ReflectionClass(__CLASS__);
		$methods  = $refClass->getMethods(ReflectionMethod::IS_PUBLIC);

		foreach ($methods as $method)
		{
			$name = $method->getName();

			if (substr($name, 0, 2) != 'on')
			{
				continue;
			}

			$events[$name] = $name;
		}

		return $events;
	}

	public function onComAdmintoolsAdminallowlistControllerAfterApply($event)
	{
		$this->logCRUDSave($event, 'id', 'ip', 'COM_ADMINTOOLS_LOGS_WHITELISTEDADDRESSES_EDIT_2');
	}

	public function onComAdmintoolsAdminallowlistControllerAfterSave($event)
	{
		$this->logCRUDSave($event, 'id', 'ip', 'COM_ADMINTOOLS_LOGS_WHITELISTEDADDRESSES_EDIT_2');
	}

	public function onComAdmintoolsAdminallowlistControllerAfterSave2new($event)
	{
		$this->logCRUDSave($event, 'id', 'ip', 'COM_ADMINTOOLS_LOGS_WHITELISTEDADDRESSES_EDIT_2');
	}

	public function onComAdmintoolsAdminallowlistsControllerBeforeDelete($event)
	{
		$this->logCRUDAction($event, 'ip', 'COM_ADMINTOOLS_LOGS_WHITELISTEDADDRESSES_DELETE');
	}

	public function onComAdmintoolsAdminpasswordControllerBeforeProtect($event)
	{
		$this->logUserAction('', 'COM_ADMINTOOLS_LOGS_ADMINPASSWORD_ENABLE', 'com_admintools');
	}

	public function onComAdmintoolsAdminpasswordControllerBeforeUnprotect($event)
	{
		$this->logUserAction('', 'COM_ADMINTOOLS_LOGS_ADMINPASSWORD_DISABLE', 'com_admintools');
	}

	public function onComAdmintoolsAutobannedaddressesControllerBeforeDelete($event)
	{
		$this->logCRUDAction($event, 'ip', 'COM_ADMINTOOLS_LOGS_AUTOBANNEDADDRESSES_DELETE');
	}

	public function onComAdmintoolsBadwordControllerAfterApply($event)
	{
		$this->logCRUDSave($event, 'id', 'word', 'COM_ADMINTOOLS_LOGS_BADWORDS_EDIT_2');
	}

	public function onComAdmintoolsBadwordControllerAfterSave($event)
	{
		$this->logCRUDSave($event, 'id', 'word', 'COM_ADMINTOOLS_LOGS_BADWORDS_EDIT_2');
	}

	public function onComAdmintoolsBadwordControllerAfterSave2new($event)
	{
		$this->logCRUDSave($event, 'id', 'word', 'COM_ADMINTOOLS_LOGS_BADWORDS_EDIT_2');
	}

	public function onComAdmintoolsBadwordsControllerBeforeDelete($event)
	{
		$this->logCRUDAction($event, 'word', 'COM_ADMINTOOLS_LOGS_BADWORDS_DELETE');
	}

	public function onComAdmintoolsBlockedrequestslogControllerBeforeBan($event)
	{
		$this->logCRUDSave($event, 'id', 'ip', 'COM_ADMINTOOLS_LOGS_SECURITYEXCEPTIONS_BAN_2');
	}

	public function onComAdmintoolsBlockedrequestslogControllerBeforeDelete($event)
	{
		$this->logCRUDAction($event, 'ip', 'COM_ADMINTOOLS_LOGS_SECURITYEXCEPTIONS_DELETE');
	}

	public function onComAdmintoolsBlockedrequestslogControllerBeforeUnban($event)
	{
		$this->logCRUDSave($event, 'id', 'ip', 'COM_ADMINTOOLS_LOGS_SECURITYEXCEPTIONS_UNBAN_2');
	}

	public function onComAdmintoolsChecktempandlogdirectoriesControllerBeforeCheck($event)
	{
		$this->logUserAction('', 'COM_ADMINTOOLS_LOGS_CHECKTEMPANDLOGDIRECTORIES_RUN', 'com_admintools');
	}

	public function onComAdmintoolsCleantempdirectoryControllerBeforeMain($event)
	{
		$this->logUserAction('', 'COM_ADMINTOOLS_LOGS_CLEANTEMPDIRECTORY_RUN', 'com_admintools');
	}

	public function onComAdmintoolsConfigurepermissionsControllerAfterSavedefaults($event)
	{
		$this->logUserAction('', 'COM_ADMINTOOLS_LOGS_CONFIGUREFIXPERMISSIONS_DEFAULTS', 'com_admintools');
	}

	public function onComAdmintoolsConfigurepermissionsControllerBeforeSaveapplyperms($event)
	{
		$this->logUserAction('', 'COM_ADMINTOOLS_LOGS_CONFIGUREFIXPERMISSIONS_SAVEAPPLYPERMS', 'com_admintools');
	}

	public function onComAdmintoolsConfigurepermissionsControllerBeforeSaveperms($event)
	{
		$this->logUserAction('', 'COM_ADMINTOOLS_LOGS_CONFIGUREFIXPERMISSIONS_SAVEPERMS', 'com_admintools');
	}

	public function onComAdmintoolsConfigurewafControllerAfterApply($event)
	{
		$this->logUserAction('', 'COM_ADMINTOOLS_LOGS_CONFIGUREWAF_EDIT', 'com_admintools');
	}

	public function onComAdmintoolsConfigurewafControllerAfterSave($event)
	{
		$this->logUserAction('', 'COM_ADMINTOOLS_LOGS_CONFIGUREWAF_EDIT', 'com_admintools');
	}

	/**
	 * @param   DatabasetoolsController  $controller
	 */
	public function onComAdmintoolsDatabasetoolsControllerAfterOptimize($event)
	{
		$arguments = array_values($event->getArguments());
		/** @var BaseController $controller */
		$controller = $arguments[0];
		/** @var DatabasetoolsModel $model */
		$model   = $controller->getModel();
		$percent = $model->getState('percent', 0);

		if ($percent >= 100)
		{
			$this->logUserAction('', 'COM_ADMINTOOLS_LOGS_DATABASETOOLS_REPAIR', 'com_admintools');
		}
	}

	public function onComAdmintoolsDatabasetoolsControllerBeforePurgesessions($event)
	{
		$this->logUserAction('', 'COM_ADMINTOOLS_LOGS_DATABASETOOLS_PURGESESSIONS', 'com_admintools');
	}

	public function onComAdmintoolsEmergencyofflineControllerBeforeOffline($event)
	{
		$this->logUserAction('', 'COM_ADMINTOOLS_LOGS_EMERGENCYOFFLINE_ENABLE', 'com_admintools');
	}

	public function onComAdmintoolsEmergencyofflineControllerBeforeOnline($event)
	{
		$this->logUserAction('', 'COM_ADMINTOOLS_LOGS_EMERGENCYOFFLINE_DISABLE', 'com_admintools');
	}

	public function onComAdmintoolsExportimportControllerBeforeDoexport($event)
	{
		$this->logUserAction('', 'COM_ADMINTOOLS_LOGS_IMPORANDEXPORT_EXPORT', 'com_admintools');
	}

	public function onComAdmintoolsExportimportControllerBeforeDoimport($event)
	{
		$this->logUserAction('', 'COM_ADMINTOOLS_LOGS_IMPORANDEXPORT_IMPORT', 'com_admintools');
	}

	public function onComAdmintoolsFixpermissionsControllerBeforeMain($event)
	{
		$this->logUserAction('', 'COM_ADMINTOOLS_LOGS_FIXPERMISSIONS_RUN', 'com_admintools');
	}

	public function onComAdmintoolsHtaccessmakerControllerAfterApply($event)
	{
		$this->logUserAction('', 'COM_ADMINTOOLS_LOGS_HTACCESSMAKER_EDIT', 'com_admintools');
	}

	public function onComAdmintoolsHtaccessmakerControllerAfterSave($event)
	{
		$this->logUserAction('', 'COM_ADMINTOOLS_LOGS_HTACCESSMAKER_EDIT', 'com_admintools');
	}

	public function onComAdmintoolsIPDenyListControllerAfterApply($event)
	{
		$this->logCRUDSave($event, 'id', 'ip', 'COM_ADMINTOOLS_LOGS_BLACKLISTEDADDRESSES_EDIT_2');
	}

	/* Start of CRUD tasks */

	public function onComAdmintoolsIPDenyListControllerAfterSave($event)
	{
		$this->logCRUDSave($event, 'id', 'ip', 'COM_ADMINTOOLS_LOGS_BLACKLISTEDADDRESSES_EDIT_2');
	}

	public function onComAdmintoolsIPDenyListControllerAfterSave2new($event)
	{
		$this->logCRUDSave($event, 'id', 'ip', 'COM_ADMINTOOLS_LOGS_BLACKLISTEDADDRESSES_EDIT_2');
	}

	public function onComAdmintoolsIPDenyListsControllerBeforeDelete($event)
	{
		$this->logCRUDAction($event, 'ip', 'COM_ADMINTOOLS_LOGS_BLACKLISTEDADDRESSES_DELETE');
	}

	public function onComAdmintoolsIpautobanhistoriesControllerBeforeDelete($event)
	{
		$this->logCRUDAction($event, 'ip', 'COM_ADMINTOOLS_LOGS_IPAUTOBANHISTORIES_DELETE');
	}

	public function onComAdmintoolsMainpasswordControllerAfterApply($event)
	{
		$this->logUserAction('', 'COM_ADMINTOOLS_LOGS_MASTERPASSWORD_EDIT', 'com_admintools');
	}

	public function onComAdmintoolsMainpasswordControllerAfterSave($event)
	{
		$this->logUserAction('', 'COM_ADMINTOOLS_LOGS_MASTERPASSWORD_EDIT', 'com_admintools');
	}

	public function onComAdmintoolsNginxconfmakerControllerAfterApply($event)
	{
		$this->logUserAction('', 'COM_ADMINTOOLS_LOGS_NGINXCONFMAKER_EDIT', 'com_admintools');
	}

	public function onComAdmintoolsNginxconfmakerControllerAfterSave($event)
	{
		$this->logUserAction('', 'COM_ADMINTOOLS_LOGS_NGINXCONFMAKER_EDIT', 'com_admintools');
	}

	public function onComAdmintoolsQuickstartControllerAfterCommit($event)
	{
		$this->logUserAction('', 'COM_ADMINTOOLS_LOGS_QUICKSTART_SAVE', 'com_admintools');
	}

	public function onComAdmintoolsScanalertsControllerAfterMarkallsafe($event)
	{
		$scan_id = $this->getApplication()->input->getInt('scan_id', 0);

		if (empty($scan_id))
		{
			return;
		}

		$this->logUserAction($scan_id, 'COM_ADMINTOOLS_LOGS_SCANALERTS_MARKEDALLSAFE', 'com_admintools');
	}

	public function onComAdmintoolsScanalertsControllerAfterPublish($event)
	{
		$arguments = array_values($event->getArguments());
		/** @var BaseController $controller */
		$controller = $arguments[0];
		/** @var ScanalertsModel $model */
		$model = $controller->getModel();
		$ids   = $this->getIDsFromRequest();

		if (!$ids)
		{
			return;
		}

		$table = $model->getTable('Scanalert', 'Administrator');

		foreach ($ids as $id)
		{
			if (!$table->load($id))
			{
				continue;
			}

			$this->logUserAction($table->path, 'COM_ADMINTOOLS_LOGS_SCANALERTS_MARKEDSAFE', 'com_admintools');
		}
	}

	public function onComAdmintoolsScanalertsControllerAfterUnpublish($event)
	{
		$arguments = array_values($event->getArguments());
		/** @var BaseController $controller */
		$controller = $arguments[0];
		/** @var ScanalertsModel $model */
		$model = $controller->getModel();
		$ids   = $this->getIDsFromRequest();

		if (!$ids)
		{
			return;
		}

		$table = $model->getTable('Scanalert', 'Administrator');

		foreach ($ids as $id)
		{
			if (!$table->load($id))
			{
				continue;
			}

			$this->logUserAction($table->path, 'COM_ADMINTOOLS_LOGS_SCANALERTS_MARKEDUNSAFE', 'com_admintools');
		}
	}

	public function onComAdmintoolsScansControllerBeforeStartscan($event)
	{
		$this->logUserAction('', 'COM_ADMINTOOLS_LOGS_SCANS_RUN', 'com_admintools');
	}

	public function onComAdmintoolsSeoandlinktoolsControllerAfterApply($event)
	{
		$this->logUserAction('', 'COM_ADMINTOOLS_LOGS_SEOANDLINKTOOLS_EDIT', 'com_admintools');
	}

	public function onComAdmintoolsSeoandlinktoolsControllerAfterSave($event)
	{
		$this->logUserAction('', 'COM_ADMINTOOLS_LOGS_SEOANDLINKTOOLS_EDIT', 'com_admintools');
	}

	public function onComAdmintoolsUrlredirectionControllerAfterApply($event)
	{
		$this->logCRUDSave($event, 'id', 'dest', 'COM_ADMINTOOLS_LOGS_REDIRECTIONS_EDIT_2');
	}

	public function onComAdmintoolsUrlredirectionControllerAfterSave($event)
	{
		$this->logCRUDSave($event, 'id', 'dest', 'COM_ADMINTOOLS_LOGS_REDIRECTIONS_EDIT_2');
	}

	public function onComAdmintoolsUrlredirectionControllerAfterSave2new($event)
	{
		$this->logCRUDSave($event, 'id', 'dest', 'COM_ADMINTOOLS_LOGS_REDIRECTIONS_EDIT_2');
	}

	public function onComAdmintoolsUrlredirectionsControllerAfterPublish($event)
	{
		$this->logCRUDAction($event, 'dest', 'COM_ADMINTOOLS_LOGS_REDIRECTIONS_PUBLISH_2');
	}

	public function onComAdmintoolsUrlredirectionsControllerAfterUnpublish($event)
	{
		$this->logCRUDAction($event, 'dest', 'COM_ADMINTOOLS_LOGS_REDIRECTIONS_UNPUBLISH_2');
	}

	public function onComAdmintoolsUrlredirectionsControllerBeforeDelete($event)
	{
		$this->logCRUDAction($event, 'dest', 'COM_ADMINTOOLS_LOGS_REDIRECTIONS_DELETE');
	}

	public function onComAdmintoolsWafdenylistControllerAfterApply($event)
	{
		$this->logCRUDSave($event, 'id', 'id', 'COM_ADMINTOOLS_LOGS_WAFBLACKLIST_EDIT_2');
	}

	public function onComAdmintoolsWafdenylistControllerAfterSave($event)
	{
		$this->logCRUDSave($event, 'id', 'id', 'COM_ADMINTOOLS_LOGS_WAFBLACKLIST_EDIT_2');
	}

	public function onComAdmintoolsWafdenylistControllerAfterSave2new($event)
	{
		$this->logCRUDSave($event, 'id', 'id', 'COM_ADMINTOOLS_LOGS_WAFBLACKLIST_EDIT_2');
	}

	public function onComAdmintoolsWafdenylistsControllerAfterDelete($event)
	{
		$this->logCRUDAction($event, 'id', 'COM_ADMINTOOLS_LOGS_WAFBLACKLIST_DELETE');
	}

	public function onComAdmintoolsWafdenylistsControllerAfterPublish($event)
	{
		$this->logCRUDAction($event, 'id', 'COM_ADMINTOOLS_LOGS_WAFBLACKLIST_PUBLISH_2');
	}

	public function onComAdmintoolsWafdenylistsControllerAfterUnpublish($event)
	{
		$this->logCRUDAction($event, 'id', 'COM_ADMINTOOLS_LOGS_WAFBLACKLIST_UNPUBLISH_2');
	}

	public function onComAdmintoolsWafexceptionControllerAfterApply($event)
	{
		$this->logCRUDSave($event, 'id', 'id', 'COM_ADMINTOOLS_LOGS_WAFEXCEPTIONS_EDIT_2');
	}

	public function onComAdmintoolsWafexceptionControllerAfterSave($event)
	{
		$this->logCRUDSave($event, 'id', 'id', 'COM_ADMINTOOLS_LOGS_WAFEXCEPTIONS_EDIT_2');
	}

	public function onComAdmintoolsWafexceptionControllerAfterSave2new($event)
	{
		$this->logCRUDSave($event, 'id', 'id', 'COM_ADMINTOOLS_LOGS_WAFEXCEPTIONS_EDIT_2');
	}

	public function onComAdmintoolsWafexceptionsControllerBeforeDelete($event)
	{
		$this->logCRUDAction($event, 'id', 'COM_ADMINTOOLS_LOGS_WAFEXCEPTIONS_DELETE');
	}

	public function onComAdmintoolsWebconfigmakerControllerAfterApply($event)
	{
		$this->logUserAction('', 'COM_ADMINTOOLS_LOGS_WEBCONFIGMAKER_EDIT', 'com_admintools');
	}

	public function onComAdmintoolsWebconfigmakerControllerAfterSave($event)
	{
		$this->logUserAction('', 'COM_ADMINTOOLS_LOGS_WEBCONFIGMAKER_EDIT', 'com_admintools');
	}

	/* End of CRUD tasks */

	/**
	 * Gets the list of IDs from the request data
	 *
	 * @return array
	 */
	private function getIDsFromRequest()
	{
		// Get the ID or list of IDs from the request or the configuration
		$cid = $this->getApplication()->input->get('cid', [], 'array');
		$id  = $this->getApplication()->input->getInt('id', 0);

		$ids = [];

		if (is_array($cid) && !empty($cid))
		{
			$ids = $cid;
		}
		elseif (!empty($id))
		{
			$ids = [$id];
		}

		return $ids;
	}

	/**
	 * @param           $controller
	 * @param   string  $displayKey
	 * @param   string  $translationKey
	 */
	private function logCRUDAction(Event $event, string $displayKey, string $translationKey): void
	{
		$arguments = array_values($event->getArguments());
		/** @var BaseController $controller */
		$controller = $arguments[0];
		$ids        = $this->getIDsFromRequest();
		$model      = $controller->getModel();
		$tableName  = $controller->getName();
		try
		{
			$table = $model->getTable($tableName, 'Administrator');
		}
		catch (\Exception $e)
		{
			$tableName = Inflector::singularize($tableName);
			$table     = $model->getTable($tableName, 'Administrator');
		}

		foreach ($ids as $id)
		{
			if (!$table->load($id))
			{
				continue;
			}

			$primaryKey = $table->getKeyName(false);
			$link       = sprintf(
				"index.php?option=com_admintools&view=%s&task=edit&%s=%s",
				urlencode($tableName),
				urlencode($primaryKey),
				urlencode($table->{$primaryKey}));

			$this->logUserAction([
				'title' => $table->{$displayKey},
				'link'  => $link,
			], $translationKey, 'com_admintools');
		}
	}

	/**
	 * @param           $controller
	 * @param   string  $primaryKey
	 * @param   string  $displayKey
	 * @param   string  $translationKey
	 */
	private function logCRUDSave(Event $event, string $primaryKey, string $displayKey, string $translationKey): void
	{
		$arguments = array_values($event->getArguments());
		/** @var BaseController $controller */
		$controller     = $arguments[0];
		$model          = $controller->getModel();
		$controllerName = $controller->getName();
		$tableName      = $controller->getName();
		try
		{
			$table = $model->getTable($tableName, 'Administrator');
		}
		catch (\Exception $e)
		{
			$tableName = Inflector::singularize($tableName);
			$table     = $model->getTable($tableName, 'Administrator');
		}

		if (!$table->load($this->getApplication()->input->getInt($primaryKey)))
		{
			return;
		}

		$link = sprintf(
			"index.php?option=com_admintools&view=%s&task=edit&%s=%s",
			urlencode($controllerName),
			urlencode($primaryKey),
			urlencode($table->{$primaryKey}));

		$this->logUserAction([
			'title' => $table->{$displayKey},
			'link'  => $link,
		], $translationKey, 'com_admintools');
	}

	/**
	 * Log a user action.
	 *
	 * This is a simple wrapper around self::addLog
	 *
	 * @param   string|array  $title               Language key for title or an array of additional data to record in
	 *                                             the audit log.
	 * @param   string        $messageLanguageKey  Language key describing the user action taken.
	 * @param   string|null   $context             The name of the extension being logged (default: use
	 *                                             $this->defaultExtension).
	 * @param   User|null     $user                User object taking this action (default: currently logged in user).
	 *
	 * @return  void
	 *
	 * @see     self::addLog
	 * @since   9.0.0
	 */
	private function logUserAction($title, string $messageLanguageKey, ?string $context = null, ?User $user = null): void
	{
		// Get the user if not defined
		$user = $user ?? $this->getApplication()->getIdentity();

		// No log for guests
		if (empty($user) || ($user->guest))
		{
			return;
		}

		// Default extension if none defined
		$context = $context ?? $this->defaultExtension;

		$message = [
			'username'    => $user->username,
			'accountlink' => 'index.php?option=com_users&task=user.edit&id=' . $user->id,
		];

		if (!is_array($title))
		{
			$title = [
				'title' => $title,
			];
		}

		$message = array_merge($message, $title);

		$this->addLog([$message], $messageLanguageKey, $context, $user->id);
	}
}