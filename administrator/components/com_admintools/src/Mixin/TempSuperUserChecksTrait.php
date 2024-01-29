<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Component\AdminTools\Administrator\Mixin;

defined('_JEXEC') or die;

use Akeeba\Component\AdminTools\Administrator\Table\TempsuperuserTable;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use RuntimeException;

trait TempSuperUserChecksTrait
{
	/**
	 * Asserts that I am not trying to modify my own user.
	 *
	 * If you do not specify the user ID being edited / created we'll figure it out from the request using the model.
	 *
	 * @param   int|null  $editingID  The ID of the user being edited.
	 *
	 * @since   5.3.0
	 */
	protected function assertNotMyself(?int $editingID)
	{
		if (empty($editingID))
		{
			return;
		}

		$app = $this->app ?? Factory::getApplication();

		if ($editingID == $app->getIdentity()->id)
		{
			throw new RuntimeException(Text::sprintf('COM_ADMINTOOLS_TEMPSUPERUSERS_ERR_CANTEDITSELF'), 403);
		}

	}

	/**
	 * Asserts that I am not a temporary Super User myself
	 *
	 * @since   5.3.0
	 */
	protected function assertNotTemporary()
	{
		/** @var TempsuperuserTable $table */
		$table = $this->getModel('Tempsuperuser', 'Administrator')->getTable();

		$app = $this->app ?? Factory::getApplication();

		if ($table->load($app->getIdentity()->id))
		{
			throw new RuntimeException(Text::sprintf('COM_ADMINTOOLS_TEMPSUPERUSERS_ERR_UNAVAILABLETOTEMP'), 403);
		}
	}
}