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

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class SonataHelpersExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);
        $loader        = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');

        $this->configureSonataMediaPrivateFileProvider($config['sonata_media_private_file_provider'], $container);
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        $config            = $container->getExtensionConfig($this->getAlias())[0];
        $sonataMediaConfig = $container->getExtensionConfig('sonata_media')[0];

        foreach (['allowed_extensions', 'allowed_mime_types'] as $key) {
            if (!empty($config['sonata_media_private_file_provider'][$key])) {
                continue;
            }

            $config['sonata_media_private_file_provider'][$key] = $sonataMediaConfig['providers']['file'][$key] ?? [];
        }

        $container->prependExtensionConfig('sonata_helpers', $config);
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    private function configureSonataMediaPrivateFileProvider(array $config, ContainerBuilder $container): void
    {
        $container
            ->findDefinition('sonata_helpers.private.cdn.server')
            ->replaceArgument(0, $config['url_prefix']);
        $container
            ->findDefinition('sonata_helpers.private.adapter.local')
            ->replaceArgument(0, $config['storage_path']);
        $container->findDefinition('sonata_helpers.private.provider.file')
            ->replaceArgument(5, $config['allowed_extensions'])
            ->replaceArgument(6, $config['allowed_mime_types']);
    }
}
