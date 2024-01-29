<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') || die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

/** @var    $this   \Akeeba\Component\AdminTools\Administrator\View\Emergencyoffline\HtmlView */
?>
<div class="card card-body">
	<?php if (!$this->offline): ?>
		<p class="alert alert-info">
			<?= Text::_('COM_ADMINTOOLS_EMERGENCYOFFLINE_LBL_PREAPPLY') ?>
		</p>
		<form action="<?= Route::_('index.php?option=com_admintools&view=Emergencyoffline') ?>" name="adminForm" id="adminForm" method="post">
			<input type="hidden" name="task" value="offline" />
			<?= HTMLHelper::_('form.token') ?>
			<button type="submit" class="btn btn-danger btn-lg w-100 mt-3 mb-5">
				<span class="fa fa-power-off"></span>
				<?= Text::_('COM_ADMINTOOLS_EMERGENCYOFFLINE_LBL_SETOFFLINE') ?>
			</button>
		</form>
		<p>
			<?= Text::_('COM_ADMINTOOLS_EMERGENCYOFFLINE_LBL_PREAPPLYMANUAL') ?>
		</p>
		<pre><?= $this->htaccess; ?></pre>
	<?php else: ?>
		<p class="alert alert-info">
			<?=Text::_('COM_ADMINTOOLS_EMERGENCYOFFLINE_LBL_PREUNAPPLY'); ?>
		</p>
		<div class="my-4 text-center">
			<form action="<?= Route::_('index.php?option=com_admintools&view=Emergencyoffline') ?>" name="adminForm" id="adminForm" method="post">
				<input type="hidden" name="task" value="online" />
				<?= HTMLHelper::_('form.token') ?>
				<button type="submit" class="btn btn-success btn-lg w-100 mb-3">
					<span class="fa fa-plane"></span>
					<?= Text::_('COM_ADMINTOOLS_EMERGENCYOFFLINE_LBL_UNAPPLY'); ?>
				</button>
			</form>

			<form action="<?= Route::_('index.php?option=com_admintools&view=Emergencyoffline') ?>" name="adminForm" id="adminForm" method="post">
				<input type="hidden" name="task" value="offline" />
				<?= HTMLHelper::_('form.token') ?>
				<button type="submit" class="btn btn-outline-danger btn w-50 mb-3">
					<span class="fa fa-power-off"></span>
					<?= Text::_('COM_ADMINTOOLS_EMERGENCYOFFLINE_LBL_SETOFFLINE') ?>
				</button>
			</form>
		</div>
		<p>
			<?=Text::_('COM_ADMINTOOLS_EMERGENCYOFFLINE_LBL_PREUNAPPLYMANUAL'); ?>
		</p>
	<?php endif; ?>
</div>
