<?php

/*
 *
 * This file is part of the Sonata for Ekino project.
 *
 * (c) 2018 - Ekino
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace Sonata\SonataHelpersBundle\Tests\Form\DataTransformer;

use PHPUnit\Framework\TestCase;
use Sonata\PageBundle\Model\Page;
use Sonata\PageBundle\Model\PageInterface;
use Sonata\SonataHelpersBundle\Form\DataTransformer\PageDataTransformer;
use Sonata\SonataHelpersBundle\Entity\PageManagerInterface;

/**
 * Class PageDataTransformerTest.
 *
 * @author Benoit de Jacobet <benoit.de-jacobet@ekino.com>
 */
class PageDataTransformerTest extends TestCase
{
    /**
     * Test transform.
     */
    public function testTransform()
    {
        $pageManager = $this->createMock(PageManagerInterface::class);

        $page = new Page();

        $pageManager->expects($this->once())
            ->method('find')
            ->will($this->returnValue($page));

        $data = ['level1' => ['page' => 1, 'level2' => 'foo']];
        $expected = ['level1' => ['page' => $page, 'level2' => 'foo']];

        $pageDataTransformer = new PageDataTransformer($pageManager, ['page']);
        $this->assertSame($expected, $pageDataTransformer->transform($data));
    }

    /**
     * Test reverseTransform.
     */
    public function testReverseTransform()
    {
        $pageManager = $this->createMock(PageManagerInterface::class);
        $page = $this->createMock(PageInterface::class);

        $page->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(1));

        $data = ['level1' => ['page' => $page, 'level2' => 'foo']];
        $expected = ['level1' => ['page' => 1, 'level2' => 'foo']];

        $pageDataTransformer = new PageDataTransformer($pageManager, ['page']);
        $this->assertSame($expected, $pageDataTransformer->reverseTransform($data));
    }
}
