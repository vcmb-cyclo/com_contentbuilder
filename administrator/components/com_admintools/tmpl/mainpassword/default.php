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

/** @var $this \Akeeba\Component\AdminTools\Administrator\View\Mainpassword\HtmlView */
?>
<form action="<?= Route::_('index.php?option=com_admintools&view=Mainpassword') ?>"
	  id="adminForm" method="post" name="adminForm">

	<div class="card mb-2">
		<h3 class="card-header bg-primary text-white">
			<?=Text::_('COM_ADMINTOOLS_MAINPASSWORD_LBL_PASSWORD') ?>
		</h3>

		<div class="card-body">
			<div class="row mb-3">
				<label for="mainpw" class="col-sm-3 col-form-label">
					<?=Text::_('COM_ADMINTOOLS_MAINPASSWORD_LBL_PWPROMPT'); ?>
				</label>

				<div class="col-sm-9">
					<input class="form-control" id="mainpw" name="mainpw" type="password"
						   value="<?=$this->escape($this->mainPassword); ?>" />
				</div>
			</div>
		</div>
	</div>

	<div class="card">
		<h3 class="card-header bg-primary text-white">
			<?= Text::_('COM_ADMINTOOLS_MAINPASSWORD_LBL_PROTVIEWS') ?>
		</h3>

		<div class="card-body">
			<div class="row mb-3 border-bottom pb-2">
				<label class="col-sm-3 col-form-label fst-italic">
					<?= Text::_('COM_ADMINTOOLS_MAINPASSWORD_LBL_QUICKSELECT') ?>&nbsp
				</label>
				<div class="col-sm-9">
					<button class="btn btn-primary admintoolsMPMassSelect"
							data-newstate="1">
						<span class="fa fa-check"></span>
						<?= Text::_('COM_ADMINTOOLS_MAINPASSWORD_LBL_ALL'); ?>
					</button>
					<button class="btn btn-dark admintoolsMPMassSelect"
							data-newstate="0">
						<span class="fa fa-times"></span>
						<?=Text::_('COM_ADMINTOOLS_MAINPASSWORD_LBL_NONE'); ?>
					</button>
				</div>
			</div>
			<?php foreach($this->items as $view => $x): ?>
				<?php [$locked, $langKey] = $x; ?>
				<div class="row mb-3">
					<label for="views[<?=$this->escape($view) ?>]" class="col-sm-3 col-form-label">
						<?= Text::_($langKey) ?>
					</label>

					<div class="col-sm-9">
						<?= HTMLHelper::_('admintools.booleanList', 'views[' . $view . ']', (bool) $locked, Text::_($langKey), 'admintools-mainpassword-view-' . strtolower($view)) ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>

	<input type="hidden" name="task" value="" />
	<?= HTMLHelper::_('form.token') ?>
</form>
