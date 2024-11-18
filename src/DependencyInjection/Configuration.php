<?php

namespace Tug\HttpCacheBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('tug_http_cache');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('allowed_param_names')
                    ->scalarPrototype()->end()
                ->end()
                ->arrayNode('ignored_param_names')
                    ->scalarPrototype()->end()
                ->end()
                ->arrayNode('routes')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('name')
                                ->defaultNull()
                            ->end()
                            ->arrayNode('allowed_query_names')
                                ->scalarPrototype()->end()
                            ->end()
                            ->arrayNode('allowed_param_names')
                                ->scalarPrototype()->end()
                            ->end()
                            ->arrayNode('ignored_param_names')
                                ->scalarPrototype()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
