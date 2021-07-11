<?php

namespace Rabble\MenuBundle\DependencyInjection;

use Rabble\MenuBundle\Menu\MenuBuilder;
use Rabble\MenuBundle\RabbleMenu\MenuDefinition;
use Rabble\MenuBundle\RabbleMenu\MenuManager;
use Rabble\MenuBundle\RabbleMenu\MenuManagerInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

class RabbleMenuExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(\dirname(__DIR__).'/Resources/config'));
        $loader->load('services.xml');

        $this->registerMenuManager($config, $container);
    }

    private function registerMenuManager(array $config, ContainerBuilder $container)
    {
        $menuManagerDef = new Definition(MenuManager::class);
        foreach ($config['menus'] as $menu) {
            $id = 'rabble_menu.'.$menu['name'];
            $menuDef = new Definition(MenuDefinition::class, [
                $menu['name'],
                $menu['max_depth'],
                $menu['attributes'],
            ]);
            $container->setDefinition($id, $menuDef);
            $menuManagerDef->addMethodCall('addMenu', [$menuDef]);

            $menuBuilderDef = new Definition(MenuBuilder::class, [
                new Reference('knp_menu.factory'),
                new Reference('rabble_menu.item_fetcher'),
                new Reference('request_stack'),
                $menu['name'],
            ]);
            $menuBuilderDef->addTag('knp_menu.menu_builder', [
                'method' => 'build',
                'alias' => $menu['name'],
            ]);
            $container->setDefinition('rabble_menu.builder.'.$menu['name'], $menuBuilderDef);
        }
        $id = 'rabble_menu.menu_manager';
        $container->setDefinition($id, $menuManagerDef);
        $container->addAliases([
            'menu_manager' => $id,
            MenuManager::class => $id,
            MenuManagerInterface::class => $id,
        ]);
    }
}
