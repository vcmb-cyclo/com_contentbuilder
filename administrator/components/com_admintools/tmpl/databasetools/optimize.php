<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') || die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

/** @var $this \Akeeba\Component\AdminTools\Administrator\View\Databasetools\HtmlView */

?>
<div class="card">
	<h3 class="card-header bg-primary text-white">
		<?= Text::_(empty($this->table) ?
			'COM_ADMINTOOLS_DATABASETOOLS_LBL_OPTIMIZEDB_COMPLETE' :
			'COM_ADMINTOOLS_DATABASETOOLS_LBL_OPTIMIZEDB_INPROGRESS') ?>
	</h3>
	<div class="card-body">
		<form action="<?= Route::_('index.php?option=com_admintools&view=Databasetools&task=optimize&tmpl=component', false) ?>" method="post" name="adminForm" id="adminForm">
			<input type="hidden" name="from" value="<?=$this->escape($this->table); ?>" />

			<div class="progress">
				<div aria-valuemax="100" aria-valuemin="0" aria-valuenow="<?= $this->percent ?>"
					 class="progress-bar" role="progressbar"
					 style="width: <?= $this->percent ?>%"></div>
			</div>

			<?php if (empty($this->table) || ($this->percent == 100)): ?>
				<div class="alert alert-info" id="admintools-databasetools-autoclose">
					<p><?= Text::_('COM_ADMINTOOLS_COMMON_LBL_AUTOCLOSEIN3S'); ?></p>
				</div>
			<?php endif ?>
		</form>
	</div>
</div>