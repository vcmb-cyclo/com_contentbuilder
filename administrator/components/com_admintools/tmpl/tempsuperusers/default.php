<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') || die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

/** @var \Akeeba\Component\AdminTools\Administrator\View\Tempsuperusers\HtmlView $this */

$user      = Factory::getApplication()->getIdentity();
$userId    = $user->get('id');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
$nullDate  = Factory::getDbo()->getNullDate();
$i         = 0;

$userLayout = new FileLayout('akeeba.admintools.common.user', JPATH_ADMINISTRATOR . '/components/com_admintools/layouts');
?>

<form action="<?= Route::_('index.php?option=com_admintools&view=tempsuperusers'); ?>"
	  method="post" name="adminForm" id="adminForm">
	<div class="row">
		<div class="col-md-12">
			<div id="j-main-container" class="j-main-container">
				<?= LayoutHelper::render('joomla.searchtools.default', ['view' => $this]) ?>
				<?php if (empty($this->items)) : ?>
					<div class="alert alert-info">
						<span class="icon-info-circle" aria-hidden="true"></span> <span
								class="visually-hidden"><?= Text::_('INFO'); ?></span>
						<?= Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
					</div>
				<?php else : ?>
					<table class="table" id="articleList">
						<caption class="visually-hidden">
							<?= Text::_('COM_ADMINTOOLS_TEMPSUPERUSERS_TABLE_CAPTION'); ?>, <span
									id="orderedBy"><?= Text::_('JGLOBAL_SORTED_BY'); ?> </span>, <span
									id="filteredBy"><?= Text::_('JGLOBAL_FILTERED_BY'); ?></span>
						</caption>
						<thead>
						<tr>
							<td class="w-1 text-center">
								<?= HTMLHelper::_('grid.checkall'); ?>
							</td>

							<th scope="col">
								<?= HTMLHelper::_('searchtools.sort', 'COM_ADMINTOOLS_TEMPSUPERUSERS_LBL_USER', 'u.username', $listDirn, $listOrder); ?>
							</th>

							<th scope="col">
								<?= HTMLHelper::_('searchtools.sort', 'COM_ADMINTOOLS_TEMPSUPERUSERS_FIELD_EXPIRATION', 't.expiration', $listDirn, $listOrder); ?>
							</th>

							<th scope="col" class="w-1 d-none d-md-table-cell">
								<?= HTMLHelper::_('searchtools.sort', 'COM_ADMINTOOLS_TEMPSUPERUSERS_FIELD_USER_ID', 't.user_id', $listDirn, $listOrder); ?>
							</th>
						</tr>
						</thead>
						<tbody>
						<?php foreach ($this->items as $item) : ?>
							<?php
							$canEdit = $user->authorise('core.edit', 'com_admintools');
							$expires = clone Factory::getDate($item->expiration);
							$expired = $expires->toUnix() <= time();
							?>
							<tr class="row<?= $i++ % 2; ?>">
								<td class="text-center">
									<?= HTMLHelper::_('grid.id', $i, $item->user_id, false, 'cid', 'cb', $item->username); ?>
								</td>

								<td>
									<?= $userLayout->render([
										'user_id'  => $item->user_id,
										'username' => $item->username,
										'name'     => $item->name,
										'email'    => $item->email,
										'showLink' => $canEdit,
									]) ?>
								</td>

								<td class="<?= $expired ? 'text-danger' : '' ?>">
									<?php if ($canEdit): ?>
										<a href="<?= Route::_('index.php?option=com_admintools&view=Tempsuperuser&task=edit&user_id=' . (int) $item->user_id) ?>">
											<?= HTMLHelper::_('admintools.formatDate', $item->expiration, true) ?>
										</a>
									<?php else: ?>
										<?= HTMLHelper::_('admintools.formatDate', $item->expiration, true) ?>
									<?php endif; ?>
								</td>

								<td class="w-1 d-none d-md-table-cell">
									<?= $item->user_id ?>
								</td>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>

					<?php // Load the pagination. ?>
					<?= $this->pagination->getListFooter(); ?>
				<?php endif; ?>

				<input type="hidden" name="task" value=""> <input type="hidden" name="boxchecked" value="0">
				<?= HTMLHelper::_('form.token'); ?>
			</div>
		</div>
	</div>
</form>