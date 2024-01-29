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
 * @property string $query
 */
class WafexceptionTable extends AbstractTable
{
	public function __construct(DatabaseDriver $db, DispatcherInterface $dispatcher = null)
	{
		parent::__construct('#__admintools_wafexceptions', 'id', $db, $dispatcher);
	}

	protected function onBeforeCheck()
	{
		if (empty($this->option) && empty($this->view) && empty($this->query))
		{
			throw new RuntimeException(Text::_('COM_ADMINTOOLS_ERR_EXCEPTIONSFROMWAF_ALLNULL'));
		}
	}
}