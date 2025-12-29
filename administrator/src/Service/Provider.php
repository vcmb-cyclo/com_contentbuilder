<?php

/**
 * @package     Extension
 * @author      Xavier DANO
 * @link        
 * @copyright   Copyright (C) 2025 by XDA+GIL
 * @license     GNU/GPL
 */

namespace CB\Component\Contentbuilder\Administrator\Service;

defined('_JEXEC') or die;

use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use CB\Component\Contentbuilder\Administrator\Extension\ContentbuilderComponent;
use Joomla\CMS\Factory;


return new class implements ServiceProviderInterface {

    public function register(Container $container): void
    {
        // MVC moderne disponible pour les nouvelles parties
        $container->registerServiceProvider(
            new MVCFactory('\\CB\\Component\\Contentbuilder\\Administrator', $container)
        );

        Factory::getApplication()->enqueueMessage('MVCFactory enregistré', 'success');

        // Dispatcher moderne (sans Extension Component)
        $container->registerServiceProvider(
            new ComponentDispatcherFactory('com_contentbuilder')
        );

        
        // $container->set(
        //    ComponentInterface::class,
        //   fn () => new ContentbuilderComponent()
        //);


    }
};