<?php
namespace CB\Component\Contentbuilder\Site\Controller;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Factory;

class DisplayController extends BaseController
{
    public function display($cachable = false, $urlparams = [])
    {
        // Option 1: retourner un 404 propre
        throw new \Exception('Page not found', 404);

        // Option 2: rediriger vers l’admin (si c’est volontairement admin-only)
        // Factory::getApplication()->redirect('administrator/index.php?option=com_contentbuilder');
    }
}
