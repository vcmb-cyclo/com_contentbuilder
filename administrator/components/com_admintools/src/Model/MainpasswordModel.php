<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Component\AdminTools\Administrator\Model;

defined('_JEXEC') or die;

use Akeeba\Component\AdminTools\Administrator\Helper\Storage;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\String\Inflector;

#[\AllowDynamicProperties]
class MainpasswordModel extends BaseDatabaseModel
{
	public $views = [
		'adminpassword'              => 'COM_ADMINTOOLS_TITLE_ADMINPASSWORD',
		'badwords'                   => 'COM_ADMINTOOLS_TITLE_BADWORDS',
		'cleantempdirectory'         => 'COM_ADMINTOOLS_TITLE_CLEANTEMPDIRECTORY',
		'databasetools'              => 'COM_ADMINTOOLS_TITLE_DBTOOLS',
		'emergencyoffline'           => 'COM_ADMINTOOLS_TITLE_EMERGENCYOFFLINE',
		'fixpermissions'             => 'COM_ADMINTOOLS_TITLE_FIXPERMISSIONS',
		'configurepermissions'       => 'COM_ADMINTOOLS_TITLE_CONFIGUREPERMISSIONS',
		'htaccessmaker'              => 'COM_ADMINTOOLS_TITLE_HTACCESSMAKER',
		'nginxconfmaker'             => 'COM_ADMINTOOLS_TITLE_NGINXCONFMAKER',
		'webconfigmaker'             => 'COM_ADMINTOOLS_TITLE_WEBCONFIGMAKER',
		'ipautobanhistories'         => 'COM_ADMINTOOLS_TITLE_IPAUTOBANHISTORIES',
		'autobannedaddresses'        => 'COM_ADMINTOOLS_TITLE_AUTOBANNEDADDRESSES',
		'disallowlists'              => 'COM_ADMINTOOLS_TITLE_DISALLOWLISTS',
		'adminallowlists'            => 'COM_ADMINTOOLS_TITLE_ADMINALLOWLISTS',
		'mainpassword'               => 'COM_ADMINTOOLS_TITLE_MAINPASSWORD',
		'quickstart'                 => 'COM_ADMINTOOLS_TITLE_QUICKSTART',
		'urlredirections'            => 'COM_ADMINTOOLS_TITLE_URLREDIRECTIONS',
		'scanner'                    => 'COM_ADMINTOOLS_TITLE_SCANNER',
		'scan'                       => 'COM_ADMINTOOLS_TITLE_SCANS',
		'scanalerts'                 => 'COM_ADMINTOOLS_TITLE_SCANALERTS_MAINPASSWORD',
		'seoandlinktools'            => 'COM_ADMINTOOLS_TITLE_SEOANDLINKTOOLS',
		'webapplicationfirewall'     => 'COM_ADMINTOOLS_TITLE_WAF',
		'wafdenylists'               => 'COM_ADMINTOOLS_TITLE_WAFDENYLISTS',
		'configurewaf'               => 'COM_ADMINTOOLS_TITLE_CONFIGUREWAF',
		'wafexceptions'              => 'COM_ADMINTOOLS_TITLE_WAFEXCEPTIONS',
		'checktempandlogdirectories' => 'COM_ADMINTOOLS_TITLE_CHECKTEMPANDLOGDIRECTORIES',
		'schedulinginformation'      => 'COM_ADMINTOOLS_TITLE_SCHEDULINGINFORMATION',
		'exportimport'               => 'COM_ADMINTOOLS_TITLE_EXPORTIMPORT',
		'blockedrequestslog'         => 'COM_ADMINTOOLS_TITLE_LOG',
		'unblockip'                  => 'COM_ADMINTOOLS_TITLE_UNBLOCKIP',
		'tempsuperusers'             => 'COM_ADMINTOOLS_TITLE_TEMPSUPERUSERS',
	];

	/**
	 * Checks if the user should be granted access to the current view,
	 * based on his Main Password setting.
	 *
	 * @param   string  $view  Optional. The string to check. Leave null to use the current view.
	 *
	 * @return  bool
	 */
	public function accessAllowed(?string $view = null): bool
	{
		/** @var CMSApplication $app */
		$app     = Factory::getApplication();
		$session = $app->getSession();

		// Is this a view protected by the Main Password feature?
		if (empty($view))
		{
			$view = $app->input->getCmd('view', 'Controlpanel');
		}

		$inflector = new Inflector();
		$altView   = $inflector->isPlural($view) ? $inflector->toSingular($view) : $inflector->toPlural($view);

		// We're working on lowercase
		$view    = strtolower($view);
		$altView = strtolower($altView);

		if (!isset($this->views[$view]) && !isset($this->views[$altView]))
		{
			return true;
		}

		// Do we have a Main Password?
		$params   = Storage::getInstance();
		$mainHash = $params->getValue('mainpassword', $params->getValue('masterpassword', ''));

		if (empty($mainHash))
		{
			return true;
		}

		// Compare the main pw with the one the user entered
		$mainHash = md5($mainHash);
		$userHash = $session->get('admintools.userpwhash', '');

		if ($userHash === $mainHash)
		{
			// The password matches, we are allowed to access everything
			return true;
		}

		// The login is invalid. If the view is locked I'll have to kick the user out.
		$lockedviews_raw = $params->getValue('lockedviews', '');

		if (empty($lockedviews_raw))
		{
			// There are no locked views.
			return true;
		}

		$lockedViews = explode(",", $lockedviews_raw);
		$lockedViews = array_map('strtolower', $lockedViews);

		if (empty($lockedViews))
		{
			// This shouldn't happen. There are no locked views.
			return true;
		}

		// Special case: view=Blockedrequestslog, task=browse, format=json (graphs) is always allowed.
		$task   = $app->input->get('task', '');
		$format = $app->input->get('format', 'html');

		if (
			(($view == 'blockedrequestslog') || ($view == 'blockedrequestslogs')) &&
			(($format == 'json') || $format == 'raw') &&
			(empty($task) || ($task == 'main') || ($task == 'default'))
		)
		{
			return true;
		}

		// Check if the view is locked.
		if (in_array($view, $lockedViews) || in_array($altView, $lockedViews))
		{
			return false;
		}

		return true;
	}

	/**
	 * Compares the user-supplied password against the main password
	 *
	 * @return  bool  True if the passwords match
	 */
	public function hasValidPassword()
	{
		$params = Storage::getInstance();

		$mainHash = $params->getValue('mainpassword', $params->getValue('masterpassword', ''));

		if (empty($mainHash))
		{
			return true;
		}

		$mainHash = md5($mainHash);
		$userHash = Factory::getApplication()->getSession()->get('admintools.userpwhash', '');

		return hash_equals($mainHash, $userHash);
	}

	/**
	 * Stores the hash of the user's password in the session
	 *
	 * @param   $passwd  string  The password supplied by the user
	 *
	 * @return  void
	 */
	public function setUserPassword($passwd)
	{
		$userHash = md5($passwd);
		Factory::getApplication()->getSession()->set('admintools.userpwhash', $userHash);
	}

	/**
	 * Saves the Main Password and the protected views list
	 *
	 * @param   string  $mainPassword    The new main password
	 * @param   array   $protectedViews  A list of the views to protect
	 *
	 * @return  void
	 */
	public function saveSettings($mainPassword, array $protectedViews)
	{
		$params = Storage::getInstance();

		// Add the new main password (also replaces the old "Master password")
		$params->setValue('masterpassword', null);
		$params->setValue('mainpassword', $mainPassword);

		// Add the protected views
		if (!empty($mainPassword) && !in_array('Mainpassword', $protectedViews))
		{
			$protectedViews[] = 'Mainpassword';
		}

		$params->setValue('lockedviews', implode(',', $protectedViews));

		$params->save();
	}

	/**
	 * Get a list of the views which can be locked down and their lockdown status
	 *
	 * @return  array
	 */
	public function &getItemList()
	{
		$lockedViews = [];

		$params = Storage::getInstance();

		$lockedViewsRaw = $params->getValue('lockedviews', '');

		if (!empty($lockedViewsRaw))
		{
			$lockedViews = explode(",", $lockedViewsRaw);
		}

		$views = [];

		foreach ($this->views as $view => $langKey)
		{
			// I will only show a view if it actually exists as a View directory or Controller file.
			$viewDir        = JPATH_ADMINISTRATOR . '/components/com_admintools/src/View/' . ucfirst($view);
			$controllerFile = JPATH_ADMINISTRATOR . '/components/com_admintools/src/Controller/' . ucfirst($view) . 'Controller.php';

			if (!@is_dir($viewDir) && !@is_file($controllerFile))
			{
				continue;
			}

			$views[$view] = [
				in_array($view, $lockedViews),
				$langKey,
			];
		}

		return $views;
	}

	/**
	 * Returns the stored main password
	 *
	 * @return  string
	 */
	public function getMainpassword()
	{
		$params = Storage::getInstance();

		return $params->getValue('mainpassword', $params->getValue('masterpassword', ''));
	}
}