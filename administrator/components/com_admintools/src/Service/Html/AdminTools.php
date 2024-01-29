<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Component\AdminTools\Administrator\Service\Html;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;

/**
 * AdminTools HTML Helper
 *
 * Used in Joomla as HTMLHelper::_('admintools.something', $option, $anotherOption, ...)
 */
class AdminTools
{
	public static function formatDate($date, $local = true, $dateFormat = null)
	{
		$date = clone Factory::getDate($date, 'GMT');

		if ($local)
		{
			$app  = Factory::getApplication();
			$user = $app->getIdentity();
			$zone = $user->getParam('timezone', $app->get('offset', 'UTC'));
			$tz   = new \DateTimeZone($zone);
			$date->setTimezone($tz);
		}

		$dateFormat = $dateFormat ?: (Text::_('DATE_FORMAT_LC6') . ' T');

		return $date->format($dateFormat, $local);
	}

	public static function booleanList(string $name, bool $value, string $label, ?string $id = null)
	{
		return (new FileLayout('joomla.form.field.radio.switcher'))->render([
			'id'            => $id ?: $value,
			'name'          => $name,
			'label'         => $label,
			'value'         => $value ? 1 : 0,
			'onchange'      => '',
			'dataAttribute' => '',
			'readonly'      => false,
			'disabled'      => false,
			'class'         => '',
			'options'       => [
				HTMLHelper::_('select.option', '0', Text::_('JNO')),
				HTMLHelper::_('select.option', '1', Text::_('JYES')),
			],
		]);
	}

	public static function permissions(string $name, array $options)
	{
		$permissions = [
			0400, 0440, 0444, 0600, 0640, 0644, 0660, 0664, 0700, 0740, 0744, 0750, 0754, 0755, 0757, 0770, 0775, 0777,
		];

		$data = array_map(function ($permission) {
			$text = decoct($permission);

			return HTMLHelper::_('select.option', '0' . $text, $text);
		}, $permissions);

		if ($options['show_no_option'] ?? false)
		{
			$data[] = HTMLHelper::_('select.option', '', '---');
		}

		return HTMLHelper::_('select.genericlist', $data, $name, $options);
	}
}