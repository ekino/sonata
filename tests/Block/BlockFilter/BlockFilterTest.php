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

namespace Sonata\HelpersBundle\Tests\Block\BlockFilter;

/*
 * This file is part of the ekino/sonata project.
 *
 * (c) Ekino
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use PHPUnit\Framework\TestCase;
use Sonata\HelpersBundle\Block\BlockFilter\BlockFilter;
use Sonata\PageBundle\Model\PageInterface;

/**
 * Class BlockFilterTest.
 *
 * @author Christian Kollross <christian.kollross@ekino.com>
 */
class BlockFilterTest extends TestCase
{
    /**
     * @var PageInterface
     */
    private $page;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->page = $this->createMock(PageInterface::class);

        $this->page->method('getRouteName')->willReturn('SomeRoute');
    }

    /**
     * @dataProvider getCategoriesDataProvider
     *
     * @param array<non-empty-array> $categories
     */
    public function testGetCategories(array $categories): void
    {
        $blockFilter = new BlockFilter($categories, []);
        $this->assertEquals($categories, $blockFilter->getCategories());
    }

    /**
     * @return \Generator<non-empty-array>
     */
    public function getCategoriesDataProvider(): \Generator
    {
        yield 'Empty' => [[]];
        yield 'Minimal' => [['Alpha']];
        yield 'Several' => [['Alpha', 'Bravo', 'Charlie', 'Delta', 'Echo']];
    }

    /**
     * @dataProvider filterDataProvider
     *
     * @param string[]               $expected
     * @param array<non-empty-array> $categories
     * @param array<non-empty-array> $blockConfig
     * @param array<non-empty-array> $blocks
     */
    public function testFilter(array $expected, array $categories, array $blockConfig, array $blocks): void
    {
        $blockFilter = new BlockFilter($categories, $blockConfig);

        $this->assertEquals($expected, $blockFilter->filter($blocks, $this->page));
    }

    /**
     * @return \Generator<non-empty-array>
     */
    public function filterDataProvider(): \Generator
    {
        $defaultCategories  = ['foo' => 'Foo', 'bar' => 'Bar'];
        $defaultBlocks      = ['Alpha' => null, 'Bravo' => null, 'Charlie' => null, 'Delta' => null];
        $defaultBlockConfig = [
            'Bravo' => [
                'only_pages' => ['SomeOtherRoute'],
            ],
            'Charlie' => [
                'only_pages' => ['SomeCompletelyDifferentRoute'],
            ],
            'Delta' => [
                'only_pages' => ['SomeRoute'],
            ],
        ];

        yield 'Everything empty' => [[], [], [], []];
        yield 'Default blocks' => [$defaultBlocks, [], [], $defaultBlocks];
        yield 'Default blocks and categories' => [$defaultBlocks, $defaultCategories, [], $defaultBlocks];
        yield 'Default blocks, blockConfig and categories' => [
            ['Alpha' => null, 'Delta' => null],
            $defaultCategories, $defaultBlockConfig, $defaultBlocks,
        ];
    }

    /**
     * @dataProvider getBlockCategoriesDataProvider
     *
     * @param string[]               $expected
     * @param array<non-empty-array> $categories
     * @param array<non-empty-array> $blockConfig
     */
    public function testGetBlockCategories(array $expected, string $code, array $categories, array $blockConfig): void
    {
        $blockFilter = new BlockFilter($categories, $blockConfig);

        $this->assertEquals($expected, $blockFilter->getBlockCategories($code));
    }

    /**
     * @return \Generator<non-empty-array>
     */
    public function getBlockCategoriesDataProvider(): \Generator
    {
        $defaultCategories  = ['' => 'Default', 'foo' => 'Foo', 'bar' => 'Bar'];
        $defaultBlockConfig = [
            'Bravo' => [
                'categories' => ['foo', 'bar'],
            ],
            'Charlie' => [
                'categories' => ['foo'],
            ],
            'Delta' => [
                'categories' => ['bar'],
            ],
        ];

        yield 'Everything empty' => [[0 => null], '', ['' => 'Default'], []];
        yield 'Default' => [[0 => 'foo', 1 => 'bar'], 'Bravo', $defaultCategories, $defaultBlockConfig];
        yield 'Default' => [[0 => 'bar'], 'Delta', $defaultCategories, $defaultBlockConfig];
        yield 'Default' => [[0 => null], 'Alpha', $defaultCategories, $defaultBlockConfig];
    }

    /**
     * Expect an Exception if the default category has not been configured.
     */
    public function testGetBlockCategoriesDefaultMissing(): void
    {
        $blockFilter = new BlockFilter([], []);

        $this->expectExceptionMessage('Default category "" is not configured');
        $blockFilter->getBlockCategories('foo');
    }
}
