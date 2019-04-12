<?php

/*
 * This file is part of the ekino/sonata project.
 *
 * (c) Ekino
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\HelpersBundle\Tests\DependencyInjection;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sonata\HelpersBundle\DependencyInjection\SonataHelpersExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author Benoit Mazière <benoit.maziere@ekino.com>
 */
class SonataHelpersExtensionTest extends TestCase
{
    /**
     * @var SonataHelpersExtension
     */
    private $extension;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->extension = new SonataHelpersExtension();
    }

    /**
     * Assert the sonata media private file provider is well configured.
     */
    public function testSonataMediaConfig(): void
    {
        $container = $this->createPartialMock(ContainerBuilder::class, ['findDefinition']);

        $cdnServerDefinition = $this->createMockDefinition();
        $cdnServerDefinition->expects($this->once())->method('replaceArgument')->with(0, 'foo')->willReturnSelf();
        $container->expects($this->at(0))->method('findDefinition')
            ->with('sonata_helpers.private.cdn.server')
            ->willReturn($cdnServerDefinition);

        $localAdapterDefinition = $this->createMockDefinition();
        $localAdapterDefinition->expects($this->once())->method('replaceArgument')->with(0, 'bar')->willReturnSelf();
        $container->expects($this->at(1))->method('findDefinition')
            ->with('sonata_helpers.private.adapter.local')
            ->willReturn($localAdapterDefinition);

        $privateFileProviderDefinition = $this->createMockDefinition();
        $privateFileProviderDefinition->expects($this->exactly(2))->method('replaceArgument')
            ->withConsecutive(
                [5, ['foz']],
                [6, ['baz']]
            )->willReturnSelf();
        $container->expects($this->at(2))->method('findDefinition')
            ->with('sonata_helpers.private.provider.file')
            ->willReturn($privateFileProviderDefinition);

        $this->extension->load([['sonata_media_private_file_provider' => [
            'url_prefix'         => 'foo',
            'storage_path'       => 'bar',
            'allowed_extensions' => ['foz'],
            'allowed_mime_types' => ['baz'],
        ]]], $container);
    }

    /**
     * Assert prepend sonata helpers configuration with sonata media one.
     */
    public function testPrepend(): void
    {
        $container = $this->createPartialMock(ContainerBuilder::class, ['getExtensionConfig', 'prependExtensionConfig']);

        $container->expects($this->at(0))->method('getExtensionConfig')
            ->with('sonata_helpers')
            ->willReturn([[]]);
        $container->expects($this->at(1))->method('getExtensionConfig')
            ->with('sonata_media')
            ->willReturn([['providers' => [
                'file' => [
                    'allowed_extensions' => ['foo'],
                    'allowed_mime_types' => ['bar'],
                ]
            ]]]);
        $container->expects($this->once())->method('prependExtensionConfig')
            ->with('sonata_helpers', ['sonata_media_private_file_provider' => [
                'allowed_extensions' => ['foo'],
                'allowed_mime_types' => ['bar'],
            ]]);

        $this->extension->prepend($container);
    }

    /**
     * @return MockObject
     */
    private function createMockDefinition(): MockObject
    {
        return $this->createMock(Definition::class);
    }
}
