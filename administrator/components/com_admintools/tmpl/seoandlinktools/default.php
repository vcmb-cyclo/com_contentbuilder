<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') || die;

/** @var $this \Akeeba\Component\AdminTools\Administrator\View\Seoandlinktools\HtmlView */

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

?>
<form action="<?= Route::_('index.php?option=com_admintools&view=Seoandlinktools') ?>"
	  id="adminForm" method="post" name="adminForm">

	<div class="card mb-3">
		<h3 class="card-header bg-primary text-white">
			<?= Text::_('COM_ADMINTOOLS_SEOANDLINKTOOLS_LBL_OPTGROUP_MIGRATION') ?>
		</h3>
		<div class="card-body">

			<div class="row mb-3">
				<label for="linkmigration" class="col-sm-3 col-form-label">
					<?= Text::_('COM_ADMINTOOLS_SEOANDLINKTOOLS_LBL_OPT_LINKMIGRATION') ?>
				</label>

				<div class="col-sm-9">
					<?= HTMLHelper::_('admintools.booleanList', 'linkmigration', $this->linkToolsConfig['linkmigration'], Text::_('COM_ADMINTOOLS_SEOANDLINKTOOLS_LBL_OPT_LINKMIGRATION')); ?>
				</div>
			</div>

			<div class="row mb-3">
				<label for="migratelist"
					   class="col-sm-3 col-form-label"
				>
					<?= Text::_('COM_ADMINTOOLS_SEOANDLINKTOOLS_LBL_OPT_LINKMIGRATIONLIST') ?>
				</label>

				<div class="col-sm-9">
					<textarea class="form-control" cols="55" id="migratelist" name="migratelist"
							  rows="5"><?= $this->escape($this->linkToolsConfig['migratelist']) ?></textarea>
					<p class="form-text">
						<?= Text::_('COM_ADMINTOOLS_SEOANDLINKTOOLS_LBL_OPT_LINKMIGRATIONLIST_TIP') ?>
					</p>
				</div>
			</div>

		</div>
	</div>

	<input type="hidden" name="task" value="save" />
	<?= HTMLHelper::_('form.token') ?>
</form>
