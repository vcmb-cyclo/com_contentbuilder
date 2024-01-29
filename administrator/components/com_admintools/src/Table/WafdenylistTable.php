<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Component\AdminTools\Administrator\Table;

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseDriver;
use Joomla\Event\DispatcherInterface;
use RuntimeException;

/**
 * @property int    $id
 * @property string $option
 * @property string $view
 * @property string $task
 * @property string $query
 * @property string $query_type
 * @property string $query_content
 * @property string $verb
 * @property string $application
 * @property int    $enabled
 */
class WafdenylistTable extends AbstractTable
{
	public function __construct(DatabaseDriver $db, DispatcherInterface $dispatcher = null)
	{
		parent::__construct('#__admintools_wafblacklists', 'id', $db, $dispatcher);

		$this->setColumnAlias('published', 'enabled');
	}

	protected function onBeforeCheck()
	{
		if (empty($this->option) && empty($this->view) && empty($this->task) && empty($this->query))
		{
			throw new RuntimeException(Text::_('COM_ADMINTOOLS_WAFDENYLISTS_LBL_ERR_ALLEMPTY'));
		}
	}
}