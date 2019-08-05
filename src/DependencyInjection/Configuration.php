<?php

declare(strict_types=1);

/*
 * This file is part of the ekino/sonata project.
 *
 * (c) Ekino
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\HelpersBundle\DependencyInjection;

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
        // Keep compatibility with symfony/config < 4.2
        if (!method_exists(TreeBuilder::class, 'getRootNode')) {
            $treeBuilder = new TreeBuilder();
            $rootNode    = $treeBuilder->root('sonata_helpers');
        } else {
            $treeBuilder = new TreeBuilder('sonata_helpers');
            $rootNode    = $treeBuilder->getRootNode();
        }

        $rootNode
            ->children()
                ->arrayNode('sonata_media_private_file_provider')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('url_prefix')->cannotBeEmpty()->defaultValue('/admin/private')->end()
                        ->scalarNode('storage_path')->cannotBeEmpty()->defaultValue('%kernel.project_dir%/data/media')->end()
                        ->arrayNode('allowed_extensions')->cannotBeEmpty()->prototype('scalar')->end()->defaultValue([])->end()
                        ->arrayNode('allowed_mime_types')->cannotBeEmpty()->prototype('scalar')->end()->defaultValue([])->end()
                    ->end()
                ->end()
                ->arrayNode('compose_container')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')->defaultFalse()->end()
                        ->arrayNode('categories')->prototype('scalar')->end()->end()
                        ->arrayNode('block_config')
                            ->arrayPrototype()
                                ->children()
                                    ->arrayNode('only_pages')->prototype('scalar')->end()->end()
                                    ->arrayNode('categories')->prototype('scalar')->end()->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
