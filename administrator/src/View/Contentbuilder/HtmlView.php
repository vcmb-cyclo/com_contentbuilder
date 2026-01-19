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

        $wa->addInlineStyle(
            '.icon-48-logo_icon_cb{background-image:url('
            . Uri::root(true)
            . '/media/com_contentbuilder/images/logo_icon_cb.png);background-size:contain;background-repeat:no-repeat;}'
        );

        ToolbarHelper::title(
            'ContentBuilder :: ' . Text::_('COM_CONTENTBUILDER_FORMS'),
            'logo_icon_cb'
        );


        // 3️⃣ Affichage du layout
        parent::display($tpl);
    }
}
