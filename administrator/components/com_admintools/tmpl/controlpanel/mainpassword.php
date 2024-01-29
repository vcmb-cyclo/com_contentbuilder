<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') || die;

/** @var  \Akeeba\Component\AdminTools\Administrator\View\Controlpanel\HtmlView $this */

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

?>
<div class="alert alert-danger">
	<form action="<?= Route::_('index.php?option=com_admintools&view=Controlpanel&task=login') ?>" class="akeeba-form--inline"
		  id="adminForm" method="post" name="adminForm">
		<h3 class="alert-heading">
			<?=Text::_('COM_ADMINTOOLS_CONTROLPANEL_LBL_MAINPASSWORD_HEAD'); ?>
		</h3>

		<p>
			<?= Text::_('COM_ADMINTOOLS_CONTROLPANEL_LBL_MAINPASSWORD_INTRO'); ?>
		</p>

		<div class="col-sm-9 input-group">
			<label for="userpw" class="input-group-text">
				<?=Text::_('COM_ADMINTOOLS_CONTROLPANEL_LBL_MAINPASSWORD'); ?>
			</label>
			<input class="form-control" id="userpw" name="userpw" placeholder="<?=Text::_('COM_ADMINTOOLS_CONTROLPANEL_LBL_MAINPASSWORD'); ?>" type="password"
				   value="" />
			<button type="submit" class="btn btn-primary">
				<span class="fa fa-unlock"></span>
				<?= Text::_('JSUBMIT') ?>
			</button>
		</div>
	</form>
</div>
