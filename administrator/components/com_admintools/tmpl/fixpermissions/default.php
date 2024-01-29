<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') || die;

use Joomla\CMS\Language\Text;

/** @var $this Akeeba\Component\AdminTools\Administrator\View\Fixpermissions\HtmlView */

?>
<div class="card">
	<h3 class="card-header h1 bg-primary text-white">
		<?= Text::_($this->more ?
			'COM_ADMINTOOLS_FIXPERMISSIONS_LBL_INPROGRESS' :
			'COM_ADMINTOOLS_FIXPERMISSIONS_LBL_DONE'
		); ?>
	</h3>
	<div class="card-body">
		<div class="progress">
			<div aria-valuemax="100" aria-valuemin="0" aria-valuenow="<?= $this->percentage ?>"
				 class="progress-bar" role="progressbar"
				 style="width: <?= $this->percentage ?>%"></div>
		</div>
		<?php if (!$this->more): ?>
			<div class="alert alert-info" id="admintools-fixpermissions-autoclose">
				<p>
					<span class="icon-info-circle" aria-hidden="true"></span>
					<span class="visually-hidden"><?= Text::_('INFO'); ?></span>
					<?= Text::_('COM_ADMINTOOLS_COMMON_LBL_AUTOCLOSEIN3S') ?>
				</p>
			</div>
		<?php endif; ?>
	</div>

    <form action="index.php" name="admintoolsForm" id="admintoolsForm" method="get">
        <input type="hidden" name="option" value="com_admintools" />
        <input type="hidden" name="view" value="Fixpermissions" />
        <input type="hidden" name="task" value="run" />
        <input type="hidden" name="tmpl" value="component" />
    </form>
</div>
