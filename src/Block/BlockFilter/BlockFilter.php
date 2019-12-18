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

namespace Sonata\HelpersBundle\Block\BlockFilter;

use Sonata\PageBundle\Model\PageInterface;

/**
 * Class BlockFilter.
 *
 * This class can be used to filter blocks available on pages, by allowing or denying it according to the page
 *
 * @author Romain Mouillard <romain.mouillard@ekino.com>
 */
final class BlockFilter
{
    /**
     * @var array
     */
    private $blockConfig;

    /**
     * @var array
     */
    private $categories;

    /**
     * @var string|null
     */
    private $defaultCategory;

    /**
     * BlockFilter constructor.
     */
    public function __construct(array $categories = [], array $blockConfig = [])
    {
        $this->categories = $categories;

        $this->checkBlockConfig($blockConfig);
        $this->blockConfig = $blockConfig;
    }

    /**
     * Return all block categories.
     */
    public function getCategories(): array
    {
        return $this->categories;
    }

    /**
     * Filter the given block list according to the page.
     *
     * @param array         $blocks Initial blocks
     * @param PageInterface $page   Page on which blocks should be filtered
     *
     * @return array Filtered blocks
     */
    public function filter(array $blocks, PageInterface $page): array
    {
        $blocksToRemove = [];

        foreach ($blocks as $code => $block) {
            $blockFilterConfig = $this->blockConfig[$code] ?? null;

            // Skip if there is no filter instruction for this block
            if (\is_null($blockFilterConfig)) {
                continue;
            }

            $onlyPages = $blockFilterConfig['only_pages'] ?? [];

            // We must keep the block only if we're on an authorized page
            if (\count($onlyPages) > 0) {
                $keepBlock = \in_array($page->getRouteName(), $onlyPages);

                if (!$keepBlock) {
                    $blocksToRemove[] = $code;
                    continue;
                }
            }
        }

        if (\count($blocksToRemove) > 0) {
            foreach ($blocksToRemove as $code) {
                unset($blocks[$code]);
            }
        }

        return $blocks;
    }

    /**
     * Return categories of the given block.
     *
     * @return string[]
     */
    public function getBlockCategories(string $code): array
    {
        $blockFilterConfig = $this->blockConfig[$code] ?? null;

        // If no config for this block, set the default category
        if (\is_null($blockFilterConfig)) {
            return [$this->getDefaultCategory()];
        }

        $blockCategories = $blockFilterConfig['categories'] ?? [];

        // If no category defined for this block, set the default category
        if (0 === \count($blockCategories)) {
            return [$this->getDefaultCategory()];
        }

        return $blockCategories;
    }

    /**
     * @throws \InvalidArgumentException
     */
    private function getDefaultCategory(): ?string
    {
        if (!isset($this->categories[$this->defaultCategory])) {
            throw new \InvalidArgumentException(sprintf('Default category "%s" is not configured.', $this->defaultCategory));
        }

        return $this->defaultCategory;
    }

    /**
     * @throws \InvalidArgumentException
     */
    private function checkBlockConfig(array $config): void
    {
        // Check block config validity
        foreach ($config as $code => $blockConfig) {
            $blockCategories = $blockConfig['categories'] ?? [];

            foreach ($blockCategories as $blockCategory) {
                if (!isset($this->categories[$blockCategory])) {
                    throw new \InvalidArgumentException(sprintf('Category "%s" defined for block "%s" is unknown.', $blockCategory, $code));
                }
            }
        }
    }
}
