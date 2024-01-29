<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') || die;

use Akeeba\Component\AdminTools\Administrator\Model\BlockedrequestslogsModel;
use Joomla\CMS\Language\Text;

/** @var $this \Akeeba\Component\AdminTools\Administrator\View\Controlpanel\HtmlView */

$logUrl = 'index.php?option=com_admintools&view=blockedrequestslog&datefrom=%s&dateto=%s&groupbydate=0&groupbytype=0';

/** @var BlockedrequestslogsModel $logsModel */
$logsModel = $this->getModel('Blockedrequestslogs');

?>
<div class="card mb-3">
	<h3 class="card-header">
		<?=Text::_('COM_ADMINTOOLS_CONTROLPANEL_DASHBOARD_STATS'); ?>
	</h3>

	<div class="card-body">
		<table width="100%" class="table table-striped">
			<tbody>
			<tr>
				<th class="w-75" scope="row">
					<a href="<?=sprintf($logUrl, (gmdate('Y') - 1) . '-01-01 00:00:00', (gmdate('Y') - 1) . '-12-31 23:59:59'); ?>">
						<?=Text::_('COM_ADMINTOOLS_CONTROLPANEL_DASHBOARD_STATS_LASTYEAR'); ?>
					</a>
				</th>
				<td style="text-align:right" width="25%">
					<?= $logsModel->resetState()
						->datefrom((gmdate('Y') - 1) . '-01-01 00:00:00')
						->dateto((gmdate('Y') - 1) . '-12-31 23:59:59')
						->getTotal(); ?>

				</td>
			</tr>
			<tr>
				<th class="w-75" scope="row">
					<a href="<?=sprintf($logUrl, gmdate('Y') . '-01-01', gmdate('Y') . '-12-31 23:59:59'); ?>">
						<?=Text::_('COM_ADMINTOOLS_CONTROLPANEL_DASHBOARD_STATS_THISYEAR'); ?>
					</a>
				</th>
				<td style="text-align:right">
					<?= $logsModel->resetState()
						->datefrom(gmdate('Y') . '-01-01')
						->dateto(gmdate('Y') . '-12-31 23:59:59')
						->getTotal(); ?>

				</td>
			</tr>
			<tr>
				<?php
				$y = gmdate('Y');
				$m = gmdate('m');
				if ($m == 1)
				{
					$m = 12;
					$y -= 1;
				}
				else
				{
					$m -= 1;
				}
				switch ($m)
				{
					case 1:
					case 3:
					case 5:
					case 7:
					case 8:
					case 10:
					case 12:
						$lmday = 31;
						break;
					case 4:
					case 6:
					case 9:
					case 11:
						$lmday = 30;
						break;
					case 2:
						if (!($y % 4) && ($y % 400))
						{
							$lmday = 29;
						}
						else
						{
							$lmday = 28;
						}
				}
				if ($y < 2011)
				{
					$y = 2011;
				}
				if ($m < 1)
				{
					$m = 1;
				}
				if ($lmday < 1)
				{
					$lmday = 1;
				}
				?>
				<th class="w-75" scope="row">
					<a href="<?= sprintf($logUrl, $y . '-' . $m . '-01', $y . '-' . $m . '-' . $lmday . ' 23:59:59'); ?>">
						<?=Text::_('COM_ADMINTOOLS_CONTROLPANEL_DASHBOARD_STATS_LASTMONTH'); ?>
					</a>
				</th>
				<td style="text-align:right">
					<?= $logsModel->resetState()
						->datefrom($y . '-' . $m . '-01')
						->dateto($y . '-' . $m . '-' . $lmday . ' 23:59:59')
						->getTotal(); ?>

				</td>
			</tr>
			<tr>
				<?php
				switch (gmdate('m'))
				{
					case 1:
					case 3:
					case 5:
					case 7:
					case 8:
					case 10:
					case 12:
						$lmday = 31;
						break;
					case 4:
					case 6:
					case 9:
					case 11:
						$lmday = 30;
						break;
					case 2:
						$y = gmdate('Y');
						if (!($y % 4) && ($y % 400))
						{
							$lmday = 29;
						}
						else
						{
							$lmday = 28;
						}
				}
				if ($lmday < 1)
				{
					$lmday = 28;
				}
				?>
				<th class="w-75" scope="row">
					<a href="<?=sprintf($logUrl, gmdate('Y') . '-' . gmdate('m') . '-01', gmdate('Y') . '-' . gmdate('m') . '-' . $lmday . ' 23:59:59'); ?>">
						<?=Text::_('COM_ADMINTOOLS_CONTROLPANEL_DASHBOARD_STATS_THISMONTH'); ?>
					</a>
				</th>
				<td style="text-align:right">
					<?= $logsModel->resetState()
						->datefrom(gmdate('Y') . '-' . gmdate('m') . '-01')
						->dateto(gmdate('Y') . '-' . gmdate('m') . '-' . $lmday . ' 23:59:59')
						->getTotal(); ?>

				</td>
			</tr>
			<tr>
				<th class="w-75" scope="row">
					<a href="<?=sprintf($logUrl, gmdate('Y-m-d', time() - 7 * 24 * 3600), gmdate('Y-m-d')); ?>">
						<?=Text::_('COM_ADMINTOOLS_CONTROLPANEL_DASHBOARD_STATS_LAST7DAYS'); ?>
					</a>
				</th>
				<td style="text-align:right" width="25%">
					<?=$logsModel
						->datefrom(gmdate('Y-m-d', time() - 7 * 24 * 3600))
						->dateto(gmdate('Y-m-d'))
						->getTotal(); ?>

				</td>
			</tr>
			<tr>
				<?php
				$date = new DateTime();
				$date->setDate(gmdate('Y'), gmdate('m'), gmdate('d'));
				$date->modify("-1 day");
				$yesterday = $date->format("Y-m-d");
				$date->modify("+1 day")
				?>
				<th class="w-75" scope="row">
					<a href="<?=sprintf($logUrl, $yesterday, $date->format("Y-m-d")); ?>">
						<?=Text::_('COM_ADMINTOOLS_CONTROLPANEL_DASHBOARD_STATS_YESTERDAY'); ?>
					</a>
				</th>
				<td style="text-align:right" width="25%">
					<?=$logsModel
						->datefrom($yesterday)
						->dateto($date->format("Y-m-d"))
						->getTotal(); ?>

				</td>
			</tr>
			<tr>
				<?php
				$expiry = clone $date;
				$expiry->modify('+1 day');
				?>
				<th class="w-75" scope="row">
					<a href="<?=sprintf($logUrl, $date->format("Y-m-d"), $expiry->format("Y-m-d")); ?>">
						<strong><?=Text::_('COM_ADMINTOOLS_CONTROLPANEL_DASHBOARD_STATS_TODAY'); ?></strong>
					</a>
				</th>
				<td style="text-align:right" width="25%">
					<strong>
						<?=$logsModel
							->datefrom($date->format("Y-m-d"))
							->dateto($expiry->format("Y-m-d"))
							->getTotal(); ?>

					</strong>
				</td>
			</tr>
			</tbody>
		</table>
	</div>
</div>
