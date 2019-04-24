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
        $treeBuilder = new TreeBuilder();
        $rootNode    = $treeBuilder->root('sonata_helpers');

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
            ->end()
        ;

        return $treeBuilder;
    }
}
