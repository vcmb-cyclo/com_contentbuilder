

<?php
/**
 * @package     ContentBuilder
 * @author      Xavier DANO
 * @link        https://breezingforms.vcmb.fr
 * @copyright   Copyright (C) 2026 by XDA+GIL
 * @license     GNU/GPL
 */

// no direct access
\defined('_JEXEC') or die;

use Joomla\CMS\Dispatcher\ComponentDispatcher;

$dispatcher = ComponentDispatcher::getInstance('com_contentbuilder');
$dispatcher->dispatch();
