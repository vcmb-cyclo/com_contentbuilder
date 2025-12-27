<?php

/**
 * @package     Extension
 * @author      Xavier DANO
 * @link        
 * @copyright   Copyright (C) 2025 by XDA+GIL
 * @license     GNU/GPL
 */

// admin/src/Controller/TestController.php
// Contrôleur de test qui appelle la vue de Test (admin/src/View/Test/HtmlView.php)
namespace CB\Component\Contentbuilder\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\Database\DatabaseInterface;

class TestController extends BaseController
{
    protected $db;
    protected $factory; // ne pas typer, BaseController a déjà $factory

    public function __construct($config = [], $db, $factory)
    {
        parent::__construct($config);
        $this->db = $db;
        $this->factory = $factory;
    }

    public function display($cachable = false, $urlparams = [])
    {
        // Crée la vue via MVCFactory
        $view = $this->factory->createView('Test', 'Html');

        // Injecte la DB
        $view->setDatabase($this->db);

        // Définit le layout
        $view->setLayout('default');

        // Affiche le template
        $view->display();
    }
}
