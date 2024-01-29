<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') || die;

// Protect from unauthorized access
use Joomla\CMS\Language\Text;

/** @var  Akeeba\Component\AdminTools\Administrator\View\Controlpanel\HtmlView $this */

$graphDayFrom = gmdate('Y-m-d', time() - 30 * 24 * 3600);
?>

<div class="card mb-3">
	<h3 class="card-header">
		<?=Text::_('COM_ADMINTOOLS_CONTROLPANEL_DASHBOARD_EXCEPTIONS'); ?>
	</h3>

	<div class="card-body">
		<div class="d-flex align-items-center">
			<div class="me-2">
				<label class="visually-hidden">
					<?=Text::_('COM_ADMINTOOLS_CONTROLPANEL_DASHBOARD_FROMDATE'); ?>
				</label>

				<input type="date"
					   value="<?= $this->escape($graphDayFrom) ?>"
					   name="admintools_graph_datepicker"
					   id="admintools_graph_datepicker"
					   pattern="\d{4}-\d{2}-\d{2}"
				>
			</div>

			<div>
				<button class="btn btn-dark" id="admintools_graph_reload">
					<?=Text::_('COM_ADMINTOOLS_CONTROLPANEL_DASHBOARD_RELOADGRAPHS'); ?>
				</button>
			</div>
		</div>

		<div class="akeeba-graph">
			<span id="akthrobber" class="akion-load-a"></span>
			<canvas id="admintoolsExceptionsLineChart" width="400" height="200"></canvas>

			<div id="admintoolsExceptionsLineChartNoData" style="display:none" class="alert alert-success small">
				<p><?=Text::_('COM_ADMINTOOLS_CONTROLPANEL_DASHBOARD_STATS_NODATA'); ?></p>
			</div>
		</div>
	</div>
</div>

<div class="card mb-3">
	<h3 class="card-header">
		<?=Text::_('COM_ADMINTOOLS_CONTROLPANEL_DASHBOARD_EXCEPTSTATS'); ?>
	</h3>
	<div class="card-body">
		<div class="akeeba-graph">
			<span id="akthrobber2" class="akion-load-a"></span>
			<canvas id="admintoolsExceptionsPieChart" width="400" height="200"></canvas>

			<div id="admintoolsExceptionsPieChartNoData" style="display:none" class="alert alert-success small">
				<p><?=Text::_('COM_ADMINTOOLS_CONTROLPANEL_DASHBOARD_STATS_NODATA'); ?></p>
			</div>
		</div>
	</div>
</div>