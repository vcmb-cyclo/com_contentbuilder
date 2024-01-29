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
 * @property string $path
 * @property int    $scan_id
 * @property string $diff
 * @property int    $threat_score
 * @property int    $acknowledged
 */
class ScanalertTable extends AbstractTable
{
	public function __construct(DatabaseDriver $db, DispatcherInterface $dispatcher = null)
	{
		parent::__construct('#__admintools_scanalerts', 'id', $db, $dispatcher);

		$this->setColumnAlias('published', 'acknowledged');
	}

}