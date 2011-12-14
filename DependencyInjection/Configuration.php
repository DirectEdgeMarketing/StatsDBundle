<?php

namespace SM\StatsDBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * defines host and port defaults.
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 * @author Tarjei Huse
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('sm_stats_d');
        $rootNode->children()
            ->scalarNode("port")->defaultValue(8125)->end()
            ->scalarNode("host")->defaultValue("localhost")->end()
			->scalarNode("noop")->defaultValue(false)->end()
			->scalarNode("prefix")->isRequired()->end()
            ->end();
        return $treeBuilder;
    }
}
