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

/** @var    $this   \Akeeba\Component\AdminTools\Administrator\View\Configurepermissions\HtmlView */

$path = $this->currentPath . (empty($this->currentPath) ? '' : '/');

?>
<div class="card mb-2">
	<h3 class="card-header">
		<?=Text::_('COM_ADMINTOOLS_CONFIGUREPERMISSIONS_LBL_DEFAULTS'); ?>
	</h3>
	<form action="<?= Route::_('index.php?option=com_admintools&view=Configurepermissions&task=savedefaults') ?>"
		  class="card-body row row-cols-lg-auto g-3 align-items-center" id="defaultsForm" method="post"
		  name="defaultsForm">

		<div class="col-12">
			<label for="dirperms">
				<?= Text::_('COM_ADMINTOOLS_CONFIGUREPERMISSIONS_LBL_DEFDIRPERM'); ?>
			</label>
			<?= HTMLHelper::_('admintools.permissions', 'dirperms', [
				'id'          => 'dirperms',
				'list.attr'   => ['class' => 'form-select'],
				'list.select' => $this->dirperms,
			]) ?>
		</div>

		<div class="col-12">
			<label for="fileperms"><?= Text::_('COM_ADMINTOOLS_CONFIGUREPERMISSIONS_LBL_DEFFILEPERMS'); ?></label>
			<?= HTMLHelper::_('admintools.permissions', 'fileperms', [
				'id'          => 'fileperms',
				'list.attr'   => ['class' => 'form-select'],
				'list.select' => $this->fileperms,
			]) ?>
		</div>

		<div class="col-12">
			<label for="perms_show_hidden">
				<?= Text::_('COM_ADMINTOOLS_CONFIGUREPERMISSIONS_LBL_SHOW_HIDDEN'); ?>
			</label>
			<div class="input-group">
				<?= HTMLHelper::_('admintools.booleanList', 'perms_show_hidden', $this->perms_show_hidden, Text::_('COM_ADMINTOOLS_CONFIGUREPERMISSIONS_LBL_SHOW_HIDDEN')); ?>
			</div>
		</div>

		<div class="col-12">
			<button type="submit" class="btn btn-primary">
				<span class="fa fa-save"></span>
				<?= Text::_('COM_ADMINTOOLS_CONFIGUREPERMISSIONS_LBL_SAVEDEFAULTS'); ?>
			</button>
		</div>

		<?= HTMLHelper::_('form.token') ?>
	</form>
</div>

<div class="card card-body my-2">
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb m-0 p-0">
			<li class="breadcrumb-item">
				<?= Text::_('COM_ADMINTOOLS_CONFIGUREPERMISSIONS_LBL_PATH'); ?>:
				<a href="<?= Route::_('index.php?option=com_admintools&view=ConfigureFixpermissions&path=/') ?>">
					<?= Text::_('COM_ADMINTOOLS_CONFIGUREPERMISSIONS_LBL_ROOT'); ?>
				</a>
			</li>

			<?php
			$runningRelativePath = '';
			$i                   = 1;

			foreach ($this->listing['crumbs'] as $crumb):
				if (empty($crumb)) continue;
				$i++;
				$runningRelativePath = ltrim($runningRelativePath . '/' . $crumb, '/');
				$isLastItem          = $i == count($this->listing['crumbs']);
				?>
				<li class="breadcrumb-item <?= $isLastItem ? 'active' : '' ?>">
					<a href="<?= Route::_('index.php?option=com_admintools&view=ConfigureFixpermissions&path=' . urlencode($runningRelativePath)) ?>">
						<?= $this->escape($crumb); ?>
					</a>
				</li>
			<?php endforeach; ?>
		</ol>
	</nav>
</div>

<form action="<?= Route::_('index.php?option=com_admintools&view=Configurepermissions&task=saveperms') ?>"
	  class="card card-body" id="adminForm" method="post" name="adminForm">
	<input type="hidden" name="path" value="<?= $this->escape($this->currentPath); ?>"/>
	<input type="hidden" name="task" value="saveperms"/>
	<?= HTMLHelper::_('form.token') ?>

	<div class="my-2 text-center">
		<button type="submit" class="btn btn-success">
			<span class="fa fa-save"></span>
			<?= Text::_('COM_ADMINTOOLS_CONFIGUREPERMISSIONS_LBL_SAVEPERMS'); ?>
		</button>

		<button type="submit" class="btn btn-warning admintoolsSaveApplyPermissions">
			<span class="fa fa-check-double"></span>
			<?= Text::_('COM_ADMINTOOLS_CONFIGUREPERMISSIONS_LBL_SAVEAPPLYPERMS'); ?>
		</button>
	</div>

	<div class="container my-2">
		<div class="row">
			<div class="col-xl-6">
				<h4 class="h2 border-bottom">
					<?= Text::_('COM_ADMINTOOLS_CONFIGUREPERMISSIONS_LBL_FOLDERS_HEADER') ?>
				</h4>
				<table class="table table-striped">
					<thead>
					<tr>
						<th scope="col">
							<?= Text::_('COM_ADMINTOOLS_CONFIGUREPERMISSIONS_LBL_FOLDER'); ?>
						</th>
						<th scope="col">
							<?= Text::_('COM_ADMINTOOLS_CONFIGUREPERMISSIONS_LBL_OWNER'); ?>
						</th>
						<th colspan="2" scope="col">
							<?= Text::_('COM_ADMINTOOLS_CONFIGUREPERMISSIONS_LBL_PERMS'); ?>
						</th>
					</tr>
					</thead>
					<tbody>
					<?php foreach($this->listing['folders'] as $folder): ?>
						<tr>
							<td>
								<a href="<?= Route::_('index.php?option=com_admintools&view=ConfigureFixpermissions&path=' . urlencode($folder['path'])) ?>">
									<?= $this->escape($folder['item']); ?>
								</a>
							</td>
							<td>
								<?= $this->renderUGID($folder['uid'], $folder['gid']) ?>
							</td>
							<td>
								<?= $this->renderPermissions($folder['realperms']) ?>
							</td>
							<td>
								<?php if ($folder['realperms'] !== false): ?>
								<?= HTMLHelper::_('admintools.permissions', 'folders[' . $folder['path'] . ']', [
									'show_no_option' => true,
									'list.attr'      => ['class' => 'form-select'],
									'list.select'    => $folder['perms'],
								]) ?>
								<?php else: ?>
									&mdash;
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			</div>

			<div class="col-xl-6">
				<h4 class="h2 border-bottom">
					<?= Text::_('COM_ADMINTOOLS_CONFIGUREPERMISSIONS_LBL_FILES_HEADER') ?>
				</h4>
				<table class="table table-striped">
					<thead>
					<tr>
						<th scope="col">
							<?= Text::_('COM_ADMINTOOLS_CONFIGUREPERMISSIONS_LBL_FILE'); ?>
						</th>
						<th scope="col">
							<?= Text::_('COM_ADMINTOOLS_CONFIGUREPERMISSIONS_LBL_OWNER'); ?>
						</th>
						<th colspan="2" scope="col">
							<?= Text::_('COM_ADMINTOOLS_CONFIGUREPERMISSIONS_LBL_PERMS'); ?>
						</th>
					</tr>
					</thead>
					<tbody>
					<?php foreach($this->listing['files'] as $file): ?>
						<tr>
							<td>
								<?= $this->escape($file['item']) ?>
							</td>
							<td>
								<?= $this->renderUGID($file['uid'], $file['gid']) ?>
							</td>
							<td>
								<?= $this->escape($this->renderPermissions($file['realperms'])) ?>
							</td>
							<td>
								<?php if ($file['realperms'] !== false): ?>
								<?= HTMLHelper::_('admintools.permissions', 'files[' . $file['path'] . ']', [
									'show_no_option' => true,
									'list.attr'   => ['class' => 'form-select'],
									'list.select' => $file['perms'],
								]) ?>
								<?php else: ?>
								&mdash;
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>


	<div class="my-2 text-center">
		<button type="submit" class="btn btn-success">
			<span class="fa fa-save"></span>
			<?= Text::_('COM_ADMINTOOLS_CONFIGUREPERMISSIONS_LBL_SAVEPERMS'); ?>
		</button>

		<button type="submit" class="btn btn-warning admintoolsSaveApplyPermissions">
			<span class="fa fa-check-double"></span>
			<?= Text::_('COM_ADMINTOOLS_CONFIGUREPERMISSIONS_LBL_SAVEAPPLYPERMS'); ?>
		</button>
	</div>
</form>
