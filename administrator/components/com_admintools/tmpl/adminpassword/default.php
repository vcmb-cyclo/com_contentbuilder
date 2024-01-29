<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;

/** @var \Akeeba\Component\AdminTools\Administrator\View\Adminpassword\HtmlView $this */

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

$modeOptions = [
		HTMLHelper::_('select.option', 'joomla', Text::_('COM_ADMINTOOLS_ADMINPASSWORD_LBL_MODE_JOOMLA')),
		HTMLHelper::_('select.option', 'php', Text::_('COM_ADMINTOOLS_ADMINPASSWORD_LBL_MODE_PHP')),
		HTMLHelper::_('select.option', 'everything', Text::_('COM_ADMINTOOLS_ADMINPASSWORD_LBL_MODE_EVERYTHING')),
];

?>
<div class="card mb-2">
	<h3 class="card-header bg-info text-white">
		<?= Text::_('COM_ADMINTOOLS_ADMINPASSWORD_LBL_HOWITWORKS') ?>
	</h3>
	<div class="card-body">
		<p class="card-text">
			<?= Text::_('COM_ADMINTOOLS_ADMINPASSWORD_LBL_INFO') ?>
		</p>
		<div class="alert alert-warning text-dark">
			<span class="fa fa-exclamation-triangle" aria-hidden="true"></span>
			<span class="visually-hidden"><?= Text::_('WARNING'); ?></span>

			<?= Text::_('COM_ADMINTOOLS_ADMINPASSWORD_LBL_WARN') ?>
		</div>
	</div>
</div>

<div class="card">
	<h3 class="card-header bg-primary text-white">
		<?= Text::_('COM_ADMINTOOLS_ADMINPASSWORD_LBL_PASSWORDPROTECTION') ?>
	</h3>
	<div class="card-body">
		<form action="<?= Route::_('index.php?option=com_admintools&view=Adminpassword&task=protect') ?>"
			  id="adminForm" method="post" name="adminForm">

			<div class="row mb-3">
				<label for="mode" class="col-sm-3 col-form-label">
					<?= Text::_('COM_ADMINTOOLS_ADMINPASSWORD_LBL_MODE') ?>
				</label>
				<div class="col-sm-9">
					<?= HTMLHelper::_('select.genericlist', $modeOptions, 'mode', [
						'id'          => 'mode',
						'list.select' => $this->mode,
						'list.attr'   => ['class' => 'form-select'],
					]) ?>

					<p class="form-text">
						<?= Text::_('COM_ADMINTOOLS_ADMINPASSWORD_LBL_MODE_HELP') ?>
					</p>
				</div>
			</div>

			<div class="row mb-3">
				<label for="resetErrorPages" class="col-sm-3 col-form-label">
					<?= Text::_('COM_ADMINTOOLS_ADMINPASSWORD_LBL_RESETERRORPAGES') ?>
				</label>
				<div class="col-sm-9">
					<?= HTMLHelper::_('admintools.booleanList', 'resetErrorPages', $this->resetErrorPages == 1, Text::_('COM_ADMINTOOLS_ADMINPASSWORD_LBL_RESETERRORPAGES')) ?>

					<p class="form-text">
						<?= Text::_('COM_ADMINTOOLS_ADMINPASSWORD_LBL_RESETERRORPAGES_HELP') ?>
					</p>
				</div>
			</div>

			<div class="row mb-3">
				<label for="username" class="col-sm-3 col-form-label">
					<?= Text::_('COM_ADMINTOOLS_ADMINPASSWORD_LBL_USERNAME') ?>
				</label>
				<div class="col-sm-9">
					<input autocomplete="off" id="username" name="username" type="text"
						   class="form-control"
						   value="<?= $this->escape($this->username) ?>" />

					<p class="form-text">
						<?= Text::_('COM_ADMINTOOLS_ADMINPASSWORD_LBL_USERNAME_HELP') ?>
					</p>
				</div>
			</div>

			<div class="row mb-3">
				<label for="password" class="col-sm-3 col-form-label">
					<?= Text::_('COM_ADMINTOOLS_ADMINPASSWORD_LBL_PASSWORD') ?>
				</label>
				<div class="col-sm-9">
					<input autocomplete="off" id="password" name="password" type="password"
						   class="form-control"
						   value="<?= $this->escape($this->password) ?>" />

					<p class="form-text">
						<?= Text::_('COM_ADMINTOOLS_ADMINPASSWORD_LBL_PASSWORD_HELP') ?>
					</p>
				</div>
			</div>

			<div class="row mb-3">
				<label for="password2" class="col-sm-3 col-form-label">
					<?= Text::_('COM_ADMINTOOLS_ADMINPASSWORD_LBL_PASSWORD2') ?>
				</label>
				<div class="col-sm-9">
					<input autocomplete="off" id="password2" name="password2" type="password"
						   class="form-control"
						   value="<?= $this->escape($this->password) ?>" />

					<p class="form-text">
						<?= Text::_('COM_ADMINTOOLS_ADMINPASSWORD_LBL_PASSWORD2_HELP') ?>
					</p>
				</div>
			</div>

			<div class="row mb-3">
				<div class="col-sm-9 offset-sm-3">
					<button type="submit" class="btn btn-success">
						<span class="fa fa-user-lock"></span>
						<?= Text::_('COM_ADMINTOOLS_ADMINPASSWORD_LBL_PROTECT') ?>
					</button>

					<?php if ($this->adminLocked): ?>
						<a class="btn btn-danger"
						   href="<?= Route::_('index.php?option=com_admintools&view=Adminpassword&task=unprotect&' . Factory::getApplication()->getFormToken() . '=1') ?>"
						>
							<span class="fa fa-unlock-alt"></span>
							<?= Text::_('COM_ADMINTOOLS_ADMINPASSWORD_LBL_UNPROTECT') ?>
						</a>
					<?php endif ?>
				</div>
			</div>

			<?= HTMLHelper::_('form.token') ?>
		</form>

	</div>
</div>

