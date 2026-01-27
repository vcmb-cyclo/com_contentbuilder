<?php

/**
 * @package     ContentBuilder
 * @author      Markus Bopp / XDA + GIL
 * @link        https://breezingforms.vcmb.fr
 * @copyright   Copyright (C) 2026 by XDA+GIL
 * @license     GNU/GPL
 */

// No direct access
\defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;

// Charge les scripts Joomla nécessaires (checkAll, submit, etc.)
HTMLHelper::_('behavior.core');
HTMLHelper::_('behavior.multiselect');

// Sécurité: valeurs par défaut
$order     = $this->lists['order'] ?? 'a.ordering';
$orderDir  = $this->lists['order_Dir'] ?? 'asc';
$orderDir  = strtolower($orderDir) === 'desc' ? 'desc' : 'asc';

// Les flèches d'ordering ne doivent être actives QUE si on est trié sur ordering
$saveOrder = ($order === 'a.ordering');

$n = is_countable($this->items) ? count($this->items) : 0;

// Joomla 6 native list start
$app = Factory::getApplication();
$list = (array) $app->input->get('list', [], 'array');
$listStart = isset($list['start']) ? (int) $list['start'] : 0;
$limitValue = (int) ($this->pagination->limit ?? 0);

$limitOptions = [];
for ($i = 5; $i <= 30; $i += 5) {
    $limitOptions[] = HTMLHelper::_('select.option', (string) $i);
}
$limitOptions[] = HTMLHelper::_('select.option', '50', Text::_('J50'));
$limitOptions[] = HTMLHelper::_('select.option', '100', Text::_('J100'));
$limitOptions[] = HTMLHelper::_('select.option', '0', Text::_('JALL'));

$limitSelect = HTMLHelper::_(
    'select.genericlist',
    $limitOptions,
    'list[limit]',
    'class="form-select js-select-submit-on-change active" id="list_limit" onchange="document.adminForm.submit();"',
    'value',
    'text',
    $limitValue
);

$sortLink = function (string $label, string $field) use ($order, $orderDir, $limitValue): string {
    $isActive = ($order === $field);
    $nextDir = ($isActive && $orderDir === 'asc') ? 'desc' : 'asc';
    $indicator = $isActive
        ? ($orderDir === 'asc'
            ? ' <span class="ms-1 icon-sort icon-sort-asc" aria-hidden="true"></span>'
            : ' <span class="ms-1 icon-sort icon-sort-desc" aria-hidden="true"></span>')
        : '';
    $url = Route::_(
        'index.php?option=com_contentbuilder&view=storages&list[start]=0&list[ordering]='
        . $field . '&list[direction]=' . $nextDir . '&list[limit]=' . $limitValue
    );

    return '<a href="' . $url . '">' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . $indicator . '</a>';
};
?>

<form action="<?php echo Route::_('index.php?option=com_contentbuilder&task=storages.display'); ?>"
    method="post"
    name="adminForm"
    id="adminForm">

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th class="w-1 text-nowrap"><?php echo $sortLink(Text::_('COM_CONTENTBUILDER_ID'), 'a.id'); ?></th>

                    <th class="w-1 text-center">
                        <?php echo HTMLHelper::_('grid.checkall'); ?>
                    </th>

                    <th>
                        <?php echo $sortLink(
                            Text::_('COM_CONTENTBUILDER_NAME'),
                            'a.name'
                        ); ?>
                    </th>

                    <th>
                        <?php echo $sortLink(
                            Text::_('COM_CONTENTBUILDER_STORAGE_TITLE'),
                            'a.title'
                        ); ?>
                    </th>

                    <th class="w-10 text-nowrap">
                        <?php echo $sortLink(
                            Text::_('COM_CONTENTBUILDER_ORDERBY'),
                            'a.ordering'
                        ); ?>
                    </th>

                    <th class="w-1 text-center">
                        <?php echo $sortLink(
                            Text::_('COM_CONTENTBUILDER_PUBLISHED'),
                            'a.published'
                        ); ?>
                    </th>
                </tr>
            </thead>

            <tbody>
                <?php if ($n === 0) : ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
                        </td>
                    </tr>
                <?php else : ?>
                    <?php foreach ($this->items as $i => $row) :

                        $id        = (int) ($row->id ?? 0);
                        $name      = htmlspecialchars((string) ($row->name ?? ''), ENT_QUOTES, 'UTF-8');
                        $title     = htmlspecialchars((string) ($row->title ?? ''), ENT_QUOTES, 'UTF-8');

                        // ⚠️ Vérifie ta convention : task=storage.edit (singulier) ou storages.edit (pluriel)
                        $link = Route::_('index.php?option=com_contentbuilder&task=storage.edit&id=' . $id);

                        $checked   = HTMLHelper::_('grid.id', $i, $id);
                        $published = HTMLHelper::_('jgrid.published', $row->published, $i, 'storages.', true);

                    ?>
                    <tr>
                        <td class="text-nowrap"><?php echo $id; ?></td>
                        <td class="text-center"><?php echo $checked; ?></td>

                        <td><a href="<?php echo $link; ?>"><?php echo $name; ?></a></td>
                        <td><a href="<?php echo $link; ?>"><?php echo $title; ?></a></td>

                        <td class="order text-nowrap">
                            <?php if ($saveOrder) : ?>
                                <span class="me-2">
                                    <?php echo $this->pagination->orderUpIcon($i, $saveOrder, 'storages.orderup', 'JLIB_HTML_MOVE_UP', $saveOrder); ?>
                                </span>
                                <span>
                                    <?php echo $this->pagination->orderDownIcon($i, $n, $saveOrder, 'storages.orderdown', 'JLIB_HTML_MOVE_DOWN', $saveOrder); ?>
                                </span>
                            <?php endif; ?>
                        </td>

                        <td class="text-center">
                            <?php echo $published; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>

            <tfoot>
                <tr>
                    <td colspan="6">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">

                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <?php echo $this->pagination->getPagesCounter(); ?>
                                <span><?php echo Text::_('COM_CONTENTBUILDER_DISPLAY_NUM'); ?></span>
                                <span class="d-inline-block"><?php echo $limitSelect; ?></span>
                                <span><?php echo Text::_('COM_CONTENTBUILDER_OF'); ?></span>
                                <span><?php echo (int) ($this->pagination->total ?? 0); ?></span>
                            </div>

                            <div>
                                <?php echo $this->pagination->getPagesLinks(); ?>
                            </div>

                        </div>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    <input type="hidden" name="option" value="com_contentbuilder">
    <input type="hidden" name="task" value="">
    <input type="hidden" name="list[start]" value="<?php echo (int) $listStart; ?>">
    <input type="hidden" name="boxchecked" value="0">
    <input type="hidden" name="list[ordering]" value="<?php echo htmlspecialchars($order, ENT_QUOTES, 'UTF-8'); ?>">
    <input type="hidden" name="list[direction]" value="<?php echo htmlspecialchars($orderDir, ENT_QUOTES, 'UTF-8'); ?>">

    <?php echo HTMLHelper::_('form.token'); ?>
</form>
