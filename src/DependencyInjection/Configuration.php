<?php

namespace M6Web\Bundle\XRequestUidBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see
 * {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('m6web_xrequestuid');

        $rootNode
            ->children()
                ->arrayNode('services')
                    ->prototype('scalar')->end()
                ->end()
                ->scalarNode('request_uid_header_name')->defaultValue('X-Request-Uid')->end()
                ->scalarNode('request_parent_uid_header_name')->defaultValue('X-Request-Parent-Uid')->end()
                ->scalarNode('uniqId_service')->end();

        return $treeBuilder;
    }
}
