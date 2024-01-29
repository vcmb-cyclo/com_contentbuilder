<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

/**
 * Forked from the joomla.form.field.list-fancy-select layout
 *
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   (C) 2018 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

extract($displayData);

/**
 * Layout variables
 * -----------------
 * @var   string   $autocomplete    Autocomplete attribute for the field.
 * @var   boolean  $autofocus       Is autofocus enabled?
 * @var   string   $class           Classes for the input.
 * @var   string   $description     Description of the field.
 * @var   boolean  $disabled        Is this field disabled?
 * @var   string   $group           Group the field belongs to. <fields> section in form XML.
 * @var   boolean  $hidden          Is this field hidden in the form?
 * @var   string   $hint            Placeholder for the field.
 * @var   string   $id              DOM id of the field.
 * @var   string   $label           Label of the field.
 * @var   string   $labelclass      Classes to apply to the label.
 * @var   boolean  $multiple        Does this field support multiple values?
 * @var   string   $name            Name of the input field.
 * @var   string   $onchange        Onchange attribute for the field.
 * @var   string   $onclick         Onclick attribute for the field.
 * @var   string   $pattern         Pattern (Reg Ex) of value of the form field.
 * @var   boolean  $readonly        Is this field read only?
 * @var   boolean  $repeat          Allows extensions to duplicate elements.
 * @var   boolean  $required        Is this field required?
 * @var   integer  $size            Size attribute of the input.
 * @var   boolean  $spellcheck      Spellcheck state for the form field.
 * @var   string   $validate        Validation rules to apply.
 * @var   string   $value           Value attribute of the field.
 * @var   array    $options         Options available for this field.
 * @var   string   $dataAttribute   Miscellaneous data attributes preprocessed for HTML output
 * @var   array    $dataAttributes  Miscellaneous data attribute for eg, data-*
 */

/**
 * Fancy selects are supposed to work against lists of options given as arrays of the shape ['key1' => 'value1', ...].
 * However, I don't have any preset options; the user can enter whatever they please. So how do I tell the fancy select
 * to render arbitrary options? By creating them out of the existing values, of course!
 */
$value = is_string($value) ? array_map('trim', explode(',', $value)) : $value;

$options = array_merge($options ?: [], array_combine($value ?: [], $value ?: []));
asort($options);

$html = array();
$attr = '';

// Initialize the field attributes.
$attr .= !empty($size) ? ' size="' . $size . '"' : '';
$attr .= $multiple ? ' multiple' : '';
$attr .= $autofocus ? ' autofocus' : '';
$attr .= $onchange ? ' onchange="' . $onchange . '"' : '';
$attr .= $dataAttribute;

// To avoid user's confusion, readonly="readonly" should imply disabled="disabled".
if ($readonly || $disabled)
{
	$attr .= ' disabled="disabled"';
}

/**
 * This is the other change compared to Joomla's original layout. Joomla's fancy selects are typically used to select
 * between pre-arranged values. We want the user to enter arbitrary text. We do that by using the allow-custom attribute
 * and setting new-item-prefix="" so that new items don't have any prefix which might get in the way.
 */
$attr2  = 'allow-custom new-item-prefix="" ';
$attr2 .= !empty($class) ? ' class="' . $class . '"' : '';
$attr2 .= ' placeholder="' . $this->escape($hint ?: Text::_('JGLOBAL_TYPE_OR_SELECT_SOME_OPTIONS')) . '" ';

if ($required)
{
	$attr  .= ' required class="required"';
	$attr2 .= ' required';
}

// Create a read-only list (no name) with hidden input(s) to store the value(s).
if ($readonly)
{
	$html[] = HTMLHelper::_('select.genericlist', $options, '', trim($attr), 'value', 'text', $value, $id);

	// E.g. form field type tag sends $this->value as array
	if ($multiple && is_array($value))
	{
		if (!count($value))
		{
			$value[] = '';
		}

		foreach ($value as $val)
		{
			$html[] = '<input type="hidden" name="' . $name . '" value="' . htmlspecialchars($val, ENT_COMPAT, 'UTF-8') . '">';
		}
	}
	else
	{
		$html[] = '<input type="hidden" name="' . $name . '" value="' . htmlspecialchars($value, ENT_COMPAT, 'UTF-8') . '">';
	}
}
else
// Create a regular list.
{
	$html[] = HTMLHelper::_('select.genericlist', $options, $name, trim($attr), 'value', 'text', $value, $id);
}

Text::script('JGLOBAL_SELECT_NO_RESULTS_MATCH');
Text::script('JGLOBAL_SELECT_PRESS_TO_SELECT');

Factory::getApplication()->getDocument()->getWebAssetManager()
	->usePreset('choicesjs')
	->useScript('webcomponent.field-fancy-select');

?>

<joomla-field-fancy-select <?php echo $attr2; ?>><?php	echo implode($html); ?></joomla-field-fancy-select>
