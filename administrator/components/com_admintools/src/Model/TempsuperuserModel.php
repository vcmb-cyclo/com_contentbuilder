<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Component\AdminTools\Administrator\Model;

defined('_JEXEC') or die;

use Akeeba\Component\AdminTools\Administrator\Mixin\RunPluginsTrait;
use Akeeba\Component\AdminTools\Administrator\Mixin\TableNoSuperUsersCheckFlagsTrait;
use Akeeba\Component\AdminTools\Administrator\Mixin\TempSuperUserChecksTrait;
use Exception;
use Joomla\CMS\Access\Access;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormFactoryInterface;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\User\User;
use Joomla\CMS\User\UserFactoryInterface;
use Joomla\CMS\User\UserHelper;
use RuntimeException;

#[\AllowDynamicProperties]
class TempsuperuserModel extends AdminModel
{
	use TableNoSuperUsersCheckFlagsTrait;
	use TempSuperUserChecksTrait;
	use RunPluginsTrait;

	/**
	 * Cache of user group IDs with Super User privileges
	 *
	 * @var   array
	 * @since 5.3.0
	 */
	protected $superUserGroups = [];

	public function __construct($config = [], MVCFactoryInterface $factory = null, FormFactoryInterface $formFactory = null)
	{
		parent::__construct($config, $factory, $formFactory);

		$this->_parent_table = '';
	}

	/**
	 * @inheritDoc
	 */
	public function getForm($data = [], $loadData = true)
	{
		$pk       = (int) $this->getState($this->getName() . '.id');
		$id       = $data['user_id'] ?? $pk;
		$user     = empty($id) ? new User() : Factory::getContainer()->get(UserFactoryInterface::class)->loadUserById($id);
		$formName = (!empty($id) && ($user->id == $id)) ? 'tempsuperuser' : 'tempsuperuser_wizard';

		$form = $this->loadForm(
			'com_admintools.' . $formName,
			$formName,
			[
				'control'   => 'jform',
				'load_data' => $loadData,
			]
		) ?: false;

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	public function save($data)
	{
		try
		{
			$this->setNoCheckFlags(true);

			$isNew = empty($data['user_id'] ?? null);

			$data['user_id'] = ($data['user_id'] ?? null) ?: $this->getUserFromData($data);
		}
		catch (Exception $e)
		{
			$this->setError($e->getMessage());

			return false;
		}
		finally
		{
			$this->setNoCheckFlags(false);
		}

		try
		{
			$table   = $this->getTable();
			$context = $this->option . '.' . $this->name;
			$app     = Factory::getApplication();

			$key = $table->getKeyName();
			$pk  = (isset($data[$key])) ? $data[$key] : (int) $this->getState($this->getName() . '.id');

			if (!$isNew)
			{
				$table->load($pk);
			}

			// Bind the data.
			if (!$table->bind($data))
			{
				$this->setError($table->getError());

				return false;
			}

			// Prepare the row for saving
			$this->prepareTable($table);

			// Check the data.
			if (!$table->check())
			{
				$this->setError($table->getError());

				return false;
			}

			// Trigger the before save event.
			$result = $this->triggerPluginEvent($this->event_before_save, [$context, $table, $isNew, $data], null, $app);

			if (\in_array(false, $result, true))
			{
				$this->setError($table->getError());

				return false;
			}

			// Store the data.
			try
			{
				if ($isNew)
				{
					$this->getDatabase()->insertObject($table->getTableName(), $table, $table->getKeyName());
				}
				else
				{
					$this->getDatabase()->updateObject($table->getTableName(), $table, $table->getKeyName(), true);
				}
			}
			catch (Exception $e)
			{
				$this->setError($e->getMessage());

				return false;
			}

			if (!$table->store())
			{
				$this->setError($table->getError());

				return false;
			}

			// Clean the cache.
			$this->cleanCache();

			// Trigger the after save event.
			$this->triggerPluginEvent($this->event_after_save, [$context, $table, $isNew, $data], null, $app);
		}
		catch (Exception $e)
		{
			$this->setError($e->getMessage());

			return false;
		}

		if (isset($table->$key))
		{
			$this->setState($this->getName() . '.id', $table->$key);
		}

		$this->setState($this->getName() . '.new', $isNew);

		return true;
	}

	protected function canDelete($record)
	{
		$this->assertNotMyself($record->id);

		return parent::canDelete($record);
	}

	/**
	 * Loads the form data.
	 *
	 * This method has three modes of operation:
	 *
	 * - If there is saved form data in the session and the user_id (PK) matches we'll use that.
	 * - If we are editing an existing record we load the record.
	 * - Otherwise it's the wizard layout and we pre-fill it with some sane defaults.
	 *
	 * @return array|bool|\Joomla\CMS\Object\CMSObject|mixed
	 * @throws Exception
	 */
	protected function loadFormData()
	{
		/** @var CMSApplication $app */
		$app  = Factory::getApplication();
		$data = $app->getUserState('com_admintools.edit.tempsuperuser.data', []);
		$pk   = (int) $this->getState($this->getName() . '.id');
		$item = ($pk ? (object) $this->getItem()->getProperties() : false) ?: [];

		// There's data saved in the session and the user_id in it matches what we're editing
		if (!empty($data) && ($item->user_id ?? null) == ($data['user_id'] ?? null))
		{
			$this->preprocessData('com_admintools.tempsuperuser', $data);

			return $data;
		}

		// First, let's try loading an existing item.
		$data = $item;
		$pk   = (int) $this->getState($this->getName() . '.id');

		if ($pk > 0)
		{
			$this->preprocessData('com_admintools.tempsuperuser', $data);

			return $data;
		}

		// No existing item. I am in the Wizard view. Let's preload it with some randomized, default values.
		$jDate           = clone Factory::getDate();
		$interval        = new \DateInterval('P15D');
		$superUserGroups = $this->getSuperUserGroups() ?: [8];

		// Get a random password respecting Joomla's password restrictions
		$uParams  = ComponentHelper::getParams('com_users');
		$length   = max($uParams->get('minimum_length', 8) ?: 8, 32);
		$nInt     = $uParams->get('minimum_integers', 0) ?: 0;
		$nSymbols = $uParams->get('minimum_symbols', 0) ?: 0;
		$nUpper   = $uParams->get('minimum_uppercase', 0) ?: 0;
		$nLower   = $uParams->get('minimum_lowercase', 0) ?: 0;
		$password = $this->generatePassword($length, $nInt, $nSymbols, $nUpper, $nLower);

		return [
			'expiration' => $jDate->add($interval)->toRFC822(),
			'username'   => 'temp' . UserHelper::genRandomPassword(12),
			'password'   => $password,
			'password2'  => $password,
			'email'      => UserHelper::genRandomPassword(12) . '@example.com',
			'name'       => Text::_('COM_ADMINTOOLS_TEMPSUPERUSERS_LBL_DEFAULTNAME'),
			'groups'     => array_shift($superUserGroups),
		];
	}

	/**
	 * Generate a random password with specific restrictions
	 *
	 * @param   int       $length     Password length
	 * @param   int|null  $minInt     Minimum number of integers to include
	 * @param   int|null  $minSymbol  Minimum number of symbols to include
	 * @param   int|null  $minUpper   Minimum number of uppercase English letters to include
	 * @param   int|null  $minLower   Minimum number of lowercase English letters to include
	 *
	 * @return  string  A secure, random password
	 * @throws  Exception
	 */
	private function generatePassword(int $length = 64, ?int $minInt = 0, ?int $minSymbol = 0, ?int $minUpper = 0, ?int $minLower = 0): string
	{
		$sNumbers    = '1234567890';
		$sSymbols    = '~!@#$%^&*()_+[]{};:\'"\|,<.>/?';
		$sUpper      = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$sLower      = 'abcdefghijklmnopqrstuvwxyz';
		$sEverything = $sLower . $sUpper . $sNumbers . $sSymbols;

		$getRandomCharacters = function (int $length, string $salt = '') use ($sEverything) {
			$salt     = $salt ?: $sEverything;
			$base     = \strlen($salt);
			$makepass = '';
			$random   = random_bytes($length + 1);
			$shift    = \ord($random[0]);

			for ($i = 1; $i <= $length; ++$i)
			{
				$makepass .= $salt[($shift + \ord($random[$i])) % $base];
				$shift    += \ord($random[$i]);
			}

			return $makepass;
		};

		// The password length is at least the sum of the minimum occurrences set up
		$minLength = ($minInt ?? 0) + ($minSymbol ?? 0) + ($minUpper ?? 0) + ($minLower ?? 0);
		$length    = max($length, $minLength);
		$pass      = '';

		// If there are no requirements on minimum number of characters return a truly random password and be done with it
		if ($minLength === 0)
		{
			return $getRandomCharacters($length, $sUpper . $sLower . $sNumbers);
		}

		// Create a minimum number of integers
		if (($minInt ?? 0) > 0)
		{
			$pass .= $getRandomCharacters($minInt, $sNumbers);
		}

		// Create a minimum number of symbols
		if (($minSymbol ?? 0) > 0)
		{
			$pass .= $getRandomCharacters($minSymbol, $sSymbols);
		}

		// Create a minimum number of uppercase characters
		if (($minUpper ?? 0) > 0)
		{
			$pass .= $getRandomCharacters($minUpper, $sUpper);
		}

		// Create a minimum number of lowercase characters
		if (($minLower ?? 0) > 0)
		{
			$pass .= $getRandomCharacters($minUpper, $sLower);
		}

		// Add random characters for the remaining length of the password
		$remainingLength = $length - $minLength + 1;

		if ($remainingLength > 0)
		{
			$pass .= $getRandomCharacters($remainingLength, $sEverything);
		}

		// Shuffle the characters
		for ($i = 0; $i < strlen($sEverything) * strlen($pass); $i++)
		{
			$from        = random_int(0, $length - 1);
			$to          = random_int(0, $length - 1);
			$temp        = $pass[$to];
			$pass[$to]   = $pass[$from];
			$pass[$from] = $temp;
		}

		return $pass;
	}

	/**
	 * Returns all Joomla! user groups
	 *
	 * @return  array
	 *
	 * @since   5.3.0
	 */
	private function getSuperUserGroups()
	{
		if (!empty($this->superUserGroups))
		{
			return $this->superUserGroups;
		}

		// Get all groups
		$db    = $this->getDatabase();
		$query = $db->getQuery(true)
			->select([$db->qn('id')])
			->from($db->qn('#__usergroups'));

		$this->superUserGroups = $db->setQuery($query)->loadColumn(0);

		// This should never happen (unless your site is very dead, in which case I feel terribly sorry for you...)
		if (empty($this->superUserGroups))
		{
			$this->superUserGroups = [];
		}

		$this->superUserGroups = array_filter($this->superUserGroups, function ($group) {
			return Access::checkGroup($group, 'core.admin');
		});

		return $this->superUserGroups;
	}

	private function getUserFromData($info)
	{
		$info['block']         = 0;
		$info['sendEmail']     = 0;
		$info['lastvisitDate'] = (clone Factory::getDate())->toSql();
		$info['activation']    = '';
		$info['otpKey']        = '';
		$info['otep']          = '';
		$info['requireReset']  = 0;

		$userId = Factory::getContainer()->get(UserFactoryInterface::class)->loadUserByUsername($info['username'])->id;

		if (empty($userId))
		{
			return $this->createNewUser($info);
		}

		// Make sure I am not trying to edit myself
		if ($userId == Factory::getApplication()->getIdentity()->id)
		{
			throw new RuntimeException(Text::_('COM_ADMINTOOLS_TEMPSUPERUSERS_ERR_CANTEDITSELF'), 403);
		}

		// Get the existing user
		$user = Factory::getContainer()->get(UserFactoryInterface::class)->loadUserById($userId);

		// Make sure the user is a Super User
		if (!$user->authorise('core.admin'))
		{
			throw new RuntimeException(Text::_('COM_ADMINTOOLS_TEMPSUPERUSERS_ERR_NOTSUPER'), 500);
		}

		// Make sure the user was already blocked
		if (!$user->block)
		{
			throw new RuntimeException(Text::_('COM_ADMINTOOLS_TEMPSUPERUSERS_ERR_NOTBLOCKED'), 500);
		}

		// Apply changes to the existing user
		$user->bind($info);

		$saved = $user->save();

		if (!$saved)
		{
			throw new RuntimeException($user->getError());
		}

		return $userId;
	}

	private function createNewUser(&$info)
	{
		// Make sure $info['groups'] is defined and defines at least one Super User group
		$superUserGroups = $this->getSuperUserGroups();
		$usedSUGroups    = array_intersect($info['groups'], $superUserGroups);

		if (empty($usedSUGroups))
		{
			$this->setNoCheckFlags(false);

			throw new RuntimeException(Text::_('COM_ADMINTOOLS_TEMPSUPERUSERS_ERR_NOTASUPERUSER'), 500);
		}

		// Create a new user
		$user = new User();

		// Set the user's default language to whatever the site's current language is
		$info['params'] = [
			'language' => Factory::getApplication()->get('language'),
		];

		$user->bind($info);

		$saved = $user->save();

		if (!$saved)
		{
			$this->setNoCheckFlags(false);

			throw new RuntimeException($user->getError());
		}

		$this->addUserToSafeId($user->id);

		return $user->id;
	}

	/**
	 * Adds a new user into the list of "safe ids", otherwise at the next session load it will be disabled by the
	 * feature "Monitor Super User accounts"
	 *
	 * @param   int  $userid  ID of the new user that should be injected into the list
	 */
	private function addUserToSafeId($userid)
	{
		$db    = $this->getDatabase();
		$query = $db->getQuery(true)
			->select($db->quoteName('value'))
			->from($db->quoteName('#__admintools_storage'))
			->where($db->quoteName('key') . ' = ' . $db->quote('superuserslist'));
		$db->setQuery($query);

		try
		{
			$jsonData = $db->loadResult();
		}
		catch (Exception $e)
		{
			return;
		}

		$userList = [];

		if (!empty($jsonData))
		{
			$userList = json_decode($jsonData, true);
		}

		$userList[] = $userid;

		$data = json_encode($userList);

		$query = $db->getQuery(true)
			->delete($db->quoteName('#__admintools_storage'))
			->where($db->quoteName('key') . ' = ' . $db->quote('superuserslist'));
		$db->setQuery($query);
		$db->execute();

		$object = (object) [
			'key'   => 'superuserslist',
			'value' => $data,
		];

		$db->insertObject('#__admintools_storage', $object);
	}

}