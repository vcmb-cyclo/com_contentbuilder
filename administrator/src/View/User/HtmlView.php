<?php
/**
 * @package     ContentBuilder
 * @author      Markus Bopp
 * @link        https://breezingforms.vcmb.fr
 * @copyright   (C) 2026 by XDA+GIL
 * @license     GNU/GPL
 */

namespace CB\Component\Contentbuilder\Administrator\View\User;

// no direct access
\defined('_JEXEC') or die('Restricted access');

use CB\Component\Contentbuilder\Administrator\View\Contentbuilder\HtmlView as BaseHtmlView;

class HtmlView extends BaseHtmlView
{
    function display($tpl = null)
    {
        // Get data from the model
        $subject = $this->get('Data');
        $this->subject = $subject;
        parent::display($tpl);
    }
}
