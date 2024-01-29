<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') || die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

/** @var  \Akeeba\Component\AdminTools\Administrator\View\Controlpanel\HtmlView $this */

?>
<div class="card mb-3">
	<h3 class="card-header bg-light text-dark">
		<?=Text::_('COM_ADMINTOOLS_CONTROLPANEL_LBL_TOOLS'); ?>
	</h3>

	<div class="akeeba-cpanel-container card-body d-flex flex-row flex-wrap align-items-stretch">
		<?php if(strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN'): ?>
			<a class="akeeba-cpanel-button text-center align-self-stretch btn btn-outline-primary border-0" style="width: 10em"
			   href="<?= Route::_('index.php?option=com_admintools&view=ConfigureFixpermissions') ?>">
				<div class="bg-primary text-white d-block text-center p-3 h2">
					<span class="fa fa-cog"></span>
				</div>
				<span>
					<?= Text::_('COM_ADMINTOOLS_TITLE_CONFIGUREPERMISSIONS') ?>
				</span>
			</a>

			<?php if($this->enable_fixperms): ?>
				<a class="akeeba-cpanel-button text-center align-self-stretch btn btn-outline-primary border-0" href="<?= Route::_('index.php?option=com_admintools&view=Fixpermissions&tmpl=component') ?>"
				   id="fixperms" style="width: 10em">
					<div class="bg-primary text-white d-block text-center p-3 h2">
						<span class="fa fa-magic"></span>
					</div>
					<span>
						<?= Text::_('COM_ADMINTOOLS_TITLE_FIXPERMISSIONS') ?>
					</span>
				</a>
			<?php endif; ?>
		<?php endif; ?>

		<a class="akeeba-cpanel-button text-center align-self-stretch btn btn-outline-success border-0" style="width: 10em"
		   href="<?= Route::_('index.php?option=com_admintools&view=Tempsuperusers') ?>">
			<div class="bg-success text-white d-block text-center p-3 h2">
				<span class="fa fa-user-clock"></span>
			</div>
			<span>
				<?= Text::_('COM_ADMINTOOLS_TITLE_TEMPSUPERUSERS') ?>
			</span>
		</a>

		<a class="akeeba-cpanel-button text-center align-self-stretch btn btn-outline-primary border-0" style="width: 10em"
		   href="<?= Route::_('index.php?option=com_admintools&view=Seoandlinktools') ?>" >
			<div class="bg-primary text-white d-block text-center p-3 h2">
				<span class="fa fa-link"></span>
			</div>
			<span>
				<?= Text::_('COM_ADMINTOOLS_TITLE_SEOANDLINKTOOLS') ?>
			</span>
		</a>

		<?php if($this->enable_cleantmp): ?>
			<a class="akeeba-cpanel-button text-center align-self-stretch btn btn-outline-warning text-dark border-0" style="width: 10em"
			   href="<?= Route::_('index.php?option=com_admintools&view=Cleantempdirectory&tmpl=component') ?>"
			   id="cleantmp"
			>
				<div class="bg-warning d-block text-center p-3 h2">
					<span class="fa fa-recycle"></span>
				</div>
				<span>
					<?= Text::_('COM_ADMINTOOLS_TITLE_CLEANTEMPDIRECTORY') ?>
				</span>
			</a>
		<?php endif; ?>

		<?php if($this->enable_tmplogcheck): ?>
			<a class="akeeba-cpanel-button text-center align-self-stretch btn btn-outline-warning text-dark border-0" style="width: 10em"
			   href="<?= Route::_('index.php?option=com_admintools&view=Checktempandlogdirectories&tmpl=component') ?>"
			   id="tmplogcheck"
			>
				<div class="bg-warning d-block text-center p-3 h2">
					<span class="fa fa-folder"></span>
				</div>
				<span>
					<?= Text::_('COM_ADMINTOOLS_TITLE_CHECKTEMPANDLOGDIRECTORIES') ?>
				</span>
			</a>
		<?php endif; ?>

		<?php if($this->enable_dbtools && $this->isMySQL): ?>
			<a class="akeeba-cpanel-button text-center align-self-stretch btn btn-outline-warning text-dark border-0" style="width: 10em"
			   href="<?= Route::_('index.php?option=com_admintools&view=Databasetools&task=optimize&tmpl=component') ?>"
			   id="optimizedb"
			>
				<div class="bg-warning d-block text-center p-3 h2">
					<span class="fa fa-screwdriver"></span>
				</div>
				<span>
					<?= Text::_('COM_ADMINTOOLS_DATABASETOOLS_LBL_OPTIMIZEDB') ?>
				</span>
			</a>
		<?php endif; ?>

		<?php if($this->enable_dbtools && $this->isMySQL): ?>
			<a class="akeeba-cpanel-button text-center align-self-stretch btn btn-outline-warning text-dark border-0" style="width: 10em"
			   href="<?= Route::_('index.php?option=com_admintools&view=Databasetools&task=purgesessions&tmpl=component') ?>"
			   id="purgesessions"
			>
				<div class="bg-warning d-block text-center p-3 h2">
					<span class="fa fa-user-md"></span>
				</div>
				<span>
					<?= Text::_('COM_ADMINTOOLS_DATABASETOOLS_LBL_PURGESESSIONS') ?>
				</span>
			</a>
		<?php endif; ?>

		<?php if($this->isPro): ?>
			<a class="akeeba-cpanel-button text-center align-self-stretch btn btn-outline-success border-0" style="width: 10em"
			   href="<?= Route::_('index.php?option=com_admintools&view=Redirections') ?>">
				<div class="bg-success text-white d-block text-center p-3 h2">
					<span class="fa fa-random"></span>
				</div>
				<span>
					<?= Text::_('COM_ADMINTOOLS_TITLE_URLREDIRECTIONS') ?>
				</span>
			</a>

			<a class="akeeba-cpanel-button text-center align-self-stretch btn btn-outline-primary border-0" style="width: 10em"
			   href="<?= Route::_('index.php?option=com_plugins&task=plugin.edit&extension_id=' . $this->pluginid) ?>">
				<div class="bg-primary text-white d-block text-center p-3 h2">
					<span class="fa fa-calendar"></span>
				</div>
				<span>
					<?= Text::_('COM_ADMINTOOLS_CONTROLPANEL_LBL_SCHEDULING') ?>
				</span>
			</a>

			<a class="akeeba-cpanel-button text-center align-self-stretch btn btn-outline-primary border-0" style="width: 10em"
			   href="<?= Route::_('index.php?option=com_admintools&view=Exportimport&task=export') ?>">
				<div class="bg-primary text-white d-block text-center p-3 h2">
					<span class="fa fa-file-download"></span>
				</div>
				<span>
					<?= Text::_('COM_ADMINTOOLS_TITLE_EXPORT_SETTINGS') ?>
				</span>
			</a>

			<a class="akeeba-cpanel-button text-center align-self-stretch btn btn-outline-primary border-0" style="width: 10em"
			   href="<?= Route::_('index.php?option=com_admintools&view=Exportimport&task=import') ?>">
				<div class="bg-primary text-white d-block text-center p-3 h2">
					<span class="fa fa-file-upload"></span>
				</div>
				<span>
					<?= Text::_('COM_ADMINTOOLS_TITLE_IMPORT_SETTINGS') ?>
				</span>
			</a>
		<?php endif; ?>
	</div>
</div>
