<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Component\AdminTools\Administrator\Table;

defined('_JEXEC') or die;

use Exception;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseDriver;
use Joomla\Event\DispatcherInterface;

/**
 * @property int    $id
 * @property string $ip
 * @property string $description
 *
 */
class AllowlistTable extends AbstractTable
{
	public function __construct(DatabaseDriver $db, DispatcherInterface $dispatcher = null)
	{
		parent::__construct('#__admintools_ipallow', 'id', $db, $dispatcher);
	}

	protected function onBeforeCheck()
	{
		if (!$this->ip)
		{
			throw new Exception(Text::_('COM_ADMINTOOLS_ALLOWLIST_ERR_NEEDS_IP'));
		}
	}
}