<?php

namespace Littlerobinson\QueryBuilderBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode    = $treeBuilder->root('littlerobinson_query_builder');

        $rootNode->
        children()
            ->arrayNode('database')
            ->children()
            ->scalarNode('title')->end()
            ->booleanNode('is_dev_mode')->end()
            ->scalarNode('config_path')->end()
            ->scalarNode('file_name')->end()
            ->arrayNode('params')
            ->children()
            ->scalarNode('driver')->end()
            ->scalarNode('host')->end()
            ->scalarNode('port')->end()
            ->scalarNode('user')->end()
            ->scalarNode('password')->end()
            ->scalarNode('dbname')->end()
            ->scalarNode('charset')->end()
            ->end()
            ->end()
            ->end()
            ->end()
            ->arrayNode('user')
            ->children()
            ->scalarNode('name')->end()
            ->scalarNode('type')->end()
            ->end()
            ->end()
            ->arrayNode('association')
            ->children()
            ->scalarNode('name')->end()
            ->scalarNode('type')->end()
            ->end()
            ->end()
            ->variableNode('rules')->end()
            ->variableNode('security')->end()
            ->end();


        return $treeBuilder;
    }
}
