<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Component\AdminTools\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\ParameterType;

#[\AllowDynamicProperties]
class TempsuperusersModel extends ListModel
{
	public function __construct($config = [], MVCFactoryInterface $factory = null)
	{
		$config['filter_fields'] = $config['filter_fields'] ?? [];
		$config['filter_fields'] = $config['filter_fields'] ?: [
			'search',
			'user_id', 't.user_id',
			'expiration', 't.expiration',
			'u.username',
		];

		parent::__construct($config, $factory);
	}

	protected function populateState($ordering = 't.expiration', $direction = 'desc')
	{
		$app = Factory::getApplication();

		$search = $app->getUserStateFromRequest($this->context . 'filter.search', 'filter_search', '', 'string');
		$this->setState('filter.search', $search);

		$user_id = $app->getUserStateFromRequest($this->context . 'filter.user_id', 'filter_user_id', '', 'string');
		$this->setState('filter.user_id', ($user_id === '') ? $user_id : (int) $user_id);

		parent::populateState($ordering, $direction);
	}

	protected function getStoreId($id = '')
	{
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.user_id');

		return parent::getStoreId($id);
	}

	protected function getListQuery()
	{
		$db = $this->getDatabase();
		$query = $db->getQuery(true)
			->select([
				$db->quoteName('t') . '.*',
				$db->quoteName('u.name'),
				$db->quoteName('u.username'),
				$db->quoteName('u.email'),
				$db->quoteName('u.block'),
				$db->quoteName('u.registerDate'),
				$db->quoteName('u.lastvisitDate'),
			])
			->from($db->quoteName('#__admintools_tempsupers', 't'))
			->join('LEFT', $db->quoteName('#__users', 'u'),
				$db->quoteName('u.id') . ' = ' . $db->quoteName('t.user_id')
			);

		// Search (username or ID) and user_id filters.
		$search = $this->getState('filter.search');
		$userId = $this->getState('filter.user_id');

		// -- If search begins with 'id:' we will look for that user ID, ignoring the user_id filter.
		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$userId = (int) substr($search, 3);
			}

			if (!empty($userId))
			{
				$search = null;
			}
		}

		if (!empty($search))
		{
			$search = '%' . $search . '%';
			$query
				->where($db->quoteName('u.username') . ' LIKE :username', 'OR')
				->where($db->quoteName('u.email') . ' LIKE :email', 'OR')
				->bind(':username', $search);
		}

		if (!empty($userId))
		{
			$query->where($db->quoteName('t.user_id') . ' = :user_id')
				->bind(':user_id', $userId, ParameterType::INTEGER);
		}

		// List ordering clause
		$orderCol  = $this->state->get('list.ordering', 't.expiration');
		$orderDirn = $this->state->get('list.direction', 'DESC');
		$ordering  = $db->escape($orderCol) . ' ' . $db->escape($orderDirn);

		$query->order($ordering);

		return $query;
	}
}