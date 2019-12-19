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

namespace Sonata\HelpersBundle\Tests\DependencyInjection;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sonata\HelpersBundle\Block\BlockFilter\BlockFilter;
use Sonata\HelpersBundle\DependencyInjection\SonataHelpersExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author Benoit Mazière <benoit.maziere@ekino.com>
 * @author Christian Kollross <christian.kollross@ekino.com>
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
        $container = $this->createPartialMock(ContainerBuilder::class,
            ['findDefinition', 'getExtensionConfig', 'hasParameter', 'setParameter']
        );

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

        $blockFilterDefinition = $this->createMockDefinition();
        $blockFilterDefinition->expects($this->exactly(2))->method('replaceArgument')
            ->withConsecutive(
                [0, ['alpha' => 'bravo']],
                [1, ['charlie' => ['categories' => [], 'only_pages' => []]]]
            )->willReturnSelf();
        $container->expects($this->at(3))->method('findDefinition')
            ->with(BlockFilter::class)
            ->willReturn($blockFilterDefinition);

        $container->expects($this->at(4))->method('hasParameter')
            ->with('sonata.page.admin.page.controller')
            ->willReturn(true);

        $container->expects($this->at(5))->method('setParameter')
            ->with('sonata.page.admin.page.controller', 'SonataHelpersBundle:PageAdmin');

        $this->extension->load([[
            'sonata_media_private_file_provider' => [
                'url_prefix'         => 'foo',
                'storage_path'       => 'bar',
                'allowed_extensions' => ['foz'],
                'allowed_mime_types' => ['baz'],
            ],
            'compose_container' => [
                'enabled'      => true,
                'categories'   => ['alpha' => 'bravo'],
                'block_config' => ['charlie' => []],
            ],
        ]], $container);
    }

    /**
     * Assert prepend sonata helpers configuration with sonata media one.
     *
     * @dataProvider prependDataProvider
     */
    public function testPrepend(array $sonataMediaConfig, array $expected): void
    {
        $container = $this->createPartialMock(ContainerBuilder::class, ['getExtensionConfig', 'prependExtensionConfig']);

        $container->expects($this->at(0))->method('getExtensionConfig')
            ->with('sonata_helpers')
            ->willReturn([[]]);
        $container->expects($this->at(1))->method('getExtensionConfig')
            ->with('sonata_media')
            ->willReturn([$sonataMediaConfig]);
        $container->expects($this->once())->method('prependExtensionConfig')
            ->with('sonata_helpers', $expected);

        $this->extension->prepend($container);
    }

    public function prependDataProvider(): \Generator
    {
        yield 'With providers' => [
            ['providers' => [
                'file' => [
                    'allowed_extensions' => ['foo'],
                    'allowed_mime_types' => ['bar'],
                ],
            ]],
            ['sonata_media_private_file_provider' => [
                'allowed_extensions' => ['foo'],
                'allowed_mime_types' => ['bar'],
            ]],
        ];
        yield 'Without providers' => [
            [],
            [
                'sonata_media_private_file_provider' => [
                    'allowed_extensions' => [],
                    'allowed_mime_types' => [],
                ],
            ],
        ];
    }

    private function createMockDefinition(): MockObject
    {
        return $this->createMock(Definition::class);
    }
}
