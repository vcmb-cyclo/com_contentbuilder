<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Component\AdminTools\Administrator\Rule;

defined('_JEXEC') or die;

use Joomla\CMS\Form\Form;
use Joomla\CMS\Form\FormRule;
use Joomla\Registry\Registry;

class AdminPwRule extends FormRule
{
    /**
     * The regular expression to use in testing a form field value.
     *
     * @var    string
     * @since  1.7.0
     */
    protected $regex = '^[A-Za-z0-9][A-Za-z0-9_.-]{3,63}';

    /**
     * The regular expression modifiers to use when testing a form field value.
     *
     * @var    string
     * @since  1.7.0
     */
    protected $modifiers = '';

    public function test(\SimpleXMLElement $element, $value, $group = null, Registry $input = null, Form $form = null)
    {
        // Allow empty values to disable it
        if (!$value)
        {
            return true;
        }

        return parent::test($element, $value, $group, $input, $form);
    }
}