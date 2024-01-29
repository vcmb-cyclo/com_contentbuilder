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
class AllowlistsModel extends ListModel
{
	public function __construct($config = [], MVCFactoryInterface $factory = null)
	{
		$config['filter_fields'] = $config['filter_fields'] ?? [];
		$config['filter_fields'] = $config['filter_fields'] ?: [
			'search',
			'id',
			'ip',
			'description',
		];

		parent::__construct($config, $factory);
	}

	protected function populateState($ordering = 'id', $direction = 'desc')
	{
		$app = Factory::getApplication();

		// If we're under CLI there's nothing to populate
		if ($app->isClient('cli'))
		{
			return;
		}

		$search = $app->getUserStateFromRequest($this->context . 'filter.search', 'filter_search', '', 'string');
		$this->setState('filter.search', $search);

		parent::populateState($ordering, $direction);
	}

	protected function getStoreId($id = '')
	{
		$id .= ':' . $this->getState('filter.search');

		return parent::getStoreId($id);
	}

	protected function getListQuery()
	{
		$db    = $this->getDatabase();
		$query = $db->getQuery(true)
			->select('*')
			->from($db->quoteName('#__admintools_ipallow'));

		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			if (substr($search, 0, 3) === 'id:')
			{
				$id = (int) substr($search, 3);

				$query->where($db->quoteName('id') . ' = :id')
					->bind(':id', $id, ParameterType::INTEGER);
			}
			if (substr($search, 0, 3) === 'ip:')
			{
				$ip = '%' . substr($search, 3) . '%';

				$query->where($db->quoteName('ip') . ' = :ip')
					->bind(':ip', $ip, ParameterType::STRING);
			}
			else
			{
				$search = '%' . $search . '%';

				$query->where($db->quoteName('description') . ' LIKE :search', 'OR')
					->where($db->quoteName('ip') . ' LIKE :search2', 'OR')
					->bind(':search', $search, ParameterType::STRING)
					->bind(':search2', $search, ParameterType::STRING);
			}
		}

		// List ordering clause
		$orderCol  = $this->state->get('list.ordering', 'id');
		$orderDirn = $this->state->get('list.direction', 'desc');
		$ordering  = $db->escape($orderCol) . ' ' . $db->escape($orderDirn);

		$query->order($ordering);

		return $query;
	}
}