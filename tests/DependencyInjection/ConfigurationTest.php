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

use PHPUnit\Framework\TestCase;
use Sonata\HelpersBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;

/**
 * @author Vincent Laurier <vincent.laurier@ekino.com>
 */
class ConfigurationTest extends TestCase
{
    /**
     * @param string[] $expected
     * @dataProvider getConfig
     */
    public function testDefaultConfig(array $expected): void
    {
        $processor = new Processor();
        $config    = $processor->processConfiguration(new Configuration(), []);

        $this->assertEquals($expected, $config);
    }

    /**
     * @return \Generator<array>
     */
    public function getConfig(): \Generator
    {
        yield 'Test default configuration' => [
            [
                'sonata_media_private_file_provider' => [
                    'url_prefix'         => '/admin/private',
                    'storage_path'       => '%kernel.project_dir%/data/media',
                    'allowed_extensions' => [],
                    'allowed_mime_types' => [],
                ],
                'compose_container' => [
                    'enabled'      => false,
                    'categories'   => [],
                    'block_config' => [],
                ],
            ],
        ];
    }
}
