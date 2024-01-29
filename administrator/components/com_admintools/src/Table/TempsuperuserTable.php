<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Component\AdminTools\Administrator\Table;

defined('_JEXEC') or die;

use Akeeba\Component\AdminTools\Administrator\Mixin\TableNoSuperUsersCheckFlagsTrait;
use DateTimeZone;
use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\User;
use Joomla\Database\DatabaseDriver;
use Joomla\Event\DispatcherInterface;
use RuntimeException;

/**
 * @property int    $user_id
 * @property string $expiration
 */
class TempsuperuserTable extends AbstractTable
{
	use TableNoSuperUsersCheckFlagsTrait;

	public function __construct(DatabaseDriver $db, DispatcherInterface $dispatcher = null)
	{
		parent::__construct('#__admintools_tempsupers', 'user_id', $db, $dispatcher);
	}

	protected function onBeforeCheck()
	{
		$app  = Factory::getApplication();
		$user = $app->getIdentity();

		// Make sure I am not editing myself
		if ($this->user_id == $user->id)
		{
			throw new RuntimeException(Text::_('COM_ADMINTOOLS_TEMPSUPERUSERS_ERR_CANTEDITSELF'), 403);
		}

		// Make sure I am not setting an expiration time in the past
		$timezone = $user->getParam('timezone', $app->get('offset', 'GMT'));
		try
		{
			$tz = new DateTimeZone($timezone);
		}
		catch (Exception $e)
		{
			$tz = new DateTimeZone('GMT');
		}

		$jNow  = clone Factory::getDate();
		$jThen = clone Factory::getDate($this->expiration, $tz);

		if ($jThen->toUnix() < $jNow->toUnix())
		{
			throw new RuntimeException(Text::_('COM_ADMINTOOLS_TEMPSUPERUSERS_ERR_EXPIRATIONINPAST'), 500);
		}

		$this->expiration = $jThen->toSql();
	}

	protected function onAfterDelete(&$result, $pk)
	{
		$userId = $this->user_id;

		if (empty($userId))
		{
			return;
		}

		$user = new User($this->getDbo());

		if (!$user->load($this->user_id))
		{
			return;
		}

		$this->setNoCheckFlags(true);
		$user->delete($this->user_id);
		$this->setNoCheckFlags(false);
	}
}