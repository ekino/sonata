<?php

/*
 * This file is part of the ekino/sonata project.
 *
 * (c) Ekino
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\HelpersBundle\Tests;

use Sonata\HelpersBundle\Tests\Common\TranslationTestHelper;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Trait MockBuilderTrait.
 *
 * @author Sylvain Rascar <sylvain.rascar@ekino.com>
 */
trait MockBuilderTrait
{
    /**
     * Create TranslatorInterfaceMock and add a callback method to it.
     *
     * @return TranslatorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockTranslator()
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $translator->expects($this->any())->method('trans')
            ->will($this->returnCallback([TranslationTestHelper::class, 'getTranslationString']));

        return $translator;
    }
}
