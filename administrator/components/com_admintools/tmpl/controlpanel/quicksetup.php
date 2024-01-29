<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') || die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

/** @var  Akeeba\Component\AdminTools\Administrator\View\Controlpanel\HtmlView $this For type hinting in the IDE */

?>

<div class="card mb-3">
	<h3 class="card-header bg-light text-dark">
		<?=Text::_('COM_ADMINTOOLS_CONTROLPANEL_HEADER_QUICKSETUP'); ?>
	</h3>

	<div class="card-body">
		<div class="alert alert-warning text-dark small">
			<?= Text::_('COM_ADMINTOOLS_CONTROLPANEL_HEADER_QUICKSETUP_HELP'); ?>
		</div>

		<div class="akeeba-cpanel-container d-flex flex-row flex-wrap align-items-stretch">
			<a class="akeeba-cpanel-button text-center align-self-stretch btn btn-outline-warning text-dark border-0" style="width: 10em"
			   href="<?= Route::_('index.php?option=com_admintools&view=Quickstart') ?>">
				<div class="bg-warning d-block text-center p-3 h2">
					<span class="fa fa-bolt"></span>
				</div>
				<span>
					<?= Text::_('COM_ADMINTOOLS_TITLE_QUICKSTART') ?>
				</span>
			</a>
		</div>
	</div>


</div>
