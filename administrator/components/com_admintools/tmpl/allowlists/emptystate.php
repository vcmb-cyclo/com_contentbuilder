<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Layout\LayoutHelper;

$displayData = [
	'textPrefix' => 'COM_ADMINTOOLS_ALLOWLISTS',
	'formURL'    => 'index.php?option=com_admintools&view=allowlists',
	//'helpURL'    => 'https://docs.joomla.org/Special:MyLanguage/Adding_a_new_article',
	'icon'       => 'fa fa-door-open',
];

$user = Factory::getApplication()->getIdentity();

if ($user->authorise('core.create', 'com_admintools'))
{
	$displayData['createURL'] = 'index.php?option=com_admintools&task=allowlist.add';
}

echo LayoutHelper::render('joomla.content.emptystate', $displayData);
