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

/** @var \Akeeba\Component\AdminTools\Administrator\View\Tempsuperuser\HtmlView $this */

?>
<form action="<?php echo Route::_('index.php?option=com_admintools&view=tempsuperuser&layout=edit'); ?>"
      aria-label="<?= Text::_('COM_ADMINTOOLS_TITLE_TEMPSUPERUSER_ADD', true) ?>"
      class="form-validate" id="tempsuperuser-form" method="post" name="adminForm">

	<div class="card mb-2">
		<div class="card-header">
			<h3><?= Text::_('COM_ADMINTOOLS_TEMPSUPERUSERS_FIELD_EXPIRATION') ?></h3>
		</div>
		<div class="card-body">
			<?= $this->form->getField('expiration')->renderField(); ?>
		</div>
	</div>

	<div class="card mb-2">
		<div class="card-header">
			<h3><?= Text::_('COM_ADMINTOOLS_TEMPSUPERUSERS_LBL_USERINFO') ?></h3>
		</div>
		<div class="card-body">
			<?= $this->form->getField('name')->renderField(); ?>
			<?= $this->form->getField('username')->renderField(); ?>
			<?= $this->form->getField('password')->renderField(); ?>
			<?= $this->form->getField('password2')->renderField(); ?>
			<?= $this->form->getField('email')->renderField(); ?>
			<?= $this->form->getField('groups')->renderField(); ?>
		</div>
	</div>
	<input type="hidden" name="task" value="">
	<?php echo HTMLHelper::_('form.token'); ?>
</form>