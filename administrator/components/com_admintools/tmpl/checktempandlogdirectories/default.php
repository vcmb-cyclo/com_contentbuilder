<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') || die;

/** @var $this \Akeeba\Component\AdminTools\Administrator\View\Checktempandlogdirectories\HtmlView */

use Joomla\CMS\Language\Text;

?>
<div class="card">
	<h3 class="card-header bg-primary text-white" id="check-header">
		<?=Text::_('COM_ADMINTOOLS_CHECKTEMPANDLOGDIRECTORIES_LBL_CHECKINPROGRESS'); ?>
	</h3>

	<div class="card-body">
		<div class="progress my-4">
			<div aria-valuemax="100" aria-valuemin="0" aria-valuenow="0"
				 class="progress-bar" role="progressbar"
				 style="width: 0"></div>
		</div>

		<div id="message" class="my-3" style="display:none"></div>

		<div id="autoclose" class="alert alert-info mb-3" style="display:none">
			<p><?= Text::_('COM_ADMINTOOLS_COMMON_LBL_AUTOCLOSEIN3S') ?></p>
		</div>
	</div>

</div>


