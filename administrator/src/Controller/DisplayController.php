<?php

/**
 * @package     Extension
 * @author      Xavier DANO
 * @link        
 * @copyright   Copyright (C) 2025 by XDA+GIL
 * @license     GNU/GPL
 */

// admin/src/Controller/DisplayController.php

namespace CB\Component\Contentbuilder\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Factory;

class DisplayController extends BaseController
{
    public function display($cachable = false, $urlparams = [])
    {
        $view = $this->input->get('view', 'test'); // fallback sur test
        $this->input->set('view', $view);

        return parent::display($cachable, $urlparams);
    }

    /**
     * Task "test" pour appeler le TestController moderne
     */
    public function test(): void
    {
        // Récupère le container via le controller déjà instancié
        $container = $this->getContainer();

        // Récupère la DB et la MVCFactory depuis le container
        $db = $container->get(DatabaseInterface::class);
        $factory = $container->get(\Joomla\CMS\MVC\Factory\MVCFactoryInterface::class);

        // Instancie le TestController moderne
        $controller = new \CB\Component\Contentbuilder\Administrator\Controller\TestController([], $db, $factory);
        $controller->display();
    }

}
