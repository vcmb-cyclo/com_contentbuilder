<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Component\AdminTools\Administrator\Table;

defined('_JEXEC') or die;

use Joomla\Database\DatabaseDriver;
use Joomla\Event\DispatcherInterface;

/**
 * @property int    $id
 * @property string $ip
 * @property string $reason
 * @property string $until
 */
class IpautobanhistoryTable extends AbstractTable
{
	public function __construct(DatabaseDriver $db, DispatcherInterface $dispatcher = null)
	{
		parent::__construct('#__admintools_ipautobanhistory', 'id', $db, $dispatcher);
	}

}