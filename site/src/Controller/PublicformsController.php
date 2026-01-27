<?php
/**
 * @package     ContentBuilder
 * @author      Markus Bopp
 * @link        https://breezingforms.vcmb.fr
 * @license     GNU/GPL
*/

namespace CB\Component\Contentbuilder\Site\Controller;

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\MVC\Controller\BaseController;
use CB\Component\Contentbuilder\Administrator\CBRequest;

class PublicformsController extends BaseController
{

    function display($cachable = false, $urlparams = array())
    {
        Factory::getApplication()->input->set('tmpl', Factory::getApplication()->input->getWord('tmpl',null));
        Factory::getApplication()->input->set('layout', Factory::getApplication()->input->getWord('layout',null));
        Factory::getApplication()->input->set('view', 'publicforms');

        parent::display();
    }
}
