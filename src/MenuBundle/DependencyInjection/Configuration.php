<?php

namespace Rabble\MenuBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $builder = new TreeBuilder('rabble_menu');
        $root = $builder->getRootNode();
        $root
            ->children()
                ->arrayNode('menus')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('name')
                                ->validate()
                                    ->ifTrue(
                                        function ($value) {
                                            return (bool) preg_match('/[^a-z0-9-_]/i', $value);
                                        }
                                    )
                                    ->thenInvalid('A menu name should only contain alphanumeric characters, dashes and underscores.')
                                ->end()
                                ->beforeNormalization()
                                    ->always(
                                        function ($name) {
                                            return is_scalar($name) ? strtolower($name) : $name;
                                        }
                                    )
                                ->end()
                            ->end()
                            ->integerNode('max_depth')
                                ->defaultValue(-1)
                            ->end()
                            ->arrayNode('attributes')
                                ->variablePrototype()
                            ->end()
                        ->end()
                    ->end()
                ->end()->end()
            ->end()
        ->end()
        ;

        return $builder;
    }
}
