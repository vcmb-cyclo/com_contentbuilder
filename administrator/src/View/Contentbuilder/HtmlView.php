<?php
/**
 * @package     ContentBuilder
 * @author      Xavier DANO
 * @link        https://breezingforms.vcmb.fr
 * @license     GNU/GPL
*/

namespace CB\Component\Contentbuilder\Administrator\View\Contentbuilder;

\defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView  as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Uri\Uri;

class HtmlView extends BaseHtmlView
{
    public function display($tpl = null)
    {
        // 1️⃣ Récupération du WebAssetManager
        $document = $this->getDocument();
        $wa = $document->getWebAssetManager();

        // 2️⃣ Enregistrement + chargement du CSS
        $wa->registerAndUseStyle(
            'com_contentbuilder.admin',
            'com_contentbuilder/admin.css',
            [],
            ['media' => 'all']
        );

        $document->addStyleDeclaration(
            ".icon-48-logo_left { background-image: url(" .
            Uri::root(true) . "/administrator/components/com_contentbuilder/views/logo_left.png); }"
        );

        ToolbarHelper::title(Text::_('COM_CONTENTBUILDER_ABOUT') . '</span>', 'logo_left.png');

        // 3️⃣ Affichage du layout
        parent::display($tpl);
    }
}
