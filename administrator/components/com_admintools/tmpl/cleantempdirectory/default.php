<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') || die;

use Joomla\CMS\Language\Text;

/** @var $this Akeeba\AdminTools\Admin\View\Cleantempdirectory\Html */

?>
<div class="card">
	<h3 class="card-header <?= $this->more ? 'bg-primary' : 'bg-success' ?> text-white">
		<?= Text::_($this->more ? 'COM_ADMINTOOLS_CLEANTEMPDIRECTORY_LBL_CLEANTMPINPROGRESS'
			: 'COM_ADMINTOOLS_CLEANTEMPDIRECTORY_LBL_CLEANTMPDONE') ?>
	</h3>
	<div class="card-body">
		<div class="progress my-4">
			<div aria-valuemax="100" aria-valuemin="0" aria-valuenow="<?= (int) $this->percentage ?>"
				 class="progress-bar" role="progressbar"
				 style="width: <?= (int) $this->percentage ?>%"></div>
		</div>

		<?php if (!($this->more)): ?>
			<div class="alert alert-info mb-3" id="admintools-cleantmp-autoclose">
				<p><?= Text::_('COM_ADMINTOOLS_COMMON_LBL_AUTOCLOSEIN3S') ?></p>
			</div>
		<?php endif; ?>
	</div>
</div>