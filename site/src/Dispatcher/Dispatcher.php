<?php

namespace CB\Component\Contentbuilder\Site\Dispatcher;

\defined('_JEXEC') or die;

use Joomla\CMS\Dispatcher\ComponentDispatcher;
use Joomla\CMS\Factory;

class Dispatcher extends ComponentDispatcher
{
    public function dispatch()
    {
        $app   = Factory::getApplication();
        $input = $app->input;

        $view = $input->getCmd('view', '');
        $task = $input->getCmd('task', '');

        // IMPORTANT : ici on est AVANT le choix du controller
        if ($view === 'list' && $task === '') {
            $input->set('task', 'list.display');
        }

        parent::dispatch();
    }
}
