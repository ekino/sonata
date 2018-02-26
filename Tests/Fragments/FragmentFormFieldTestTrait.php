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

namespace SonataHelpers\Tests\FragmentService;

use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\ArticleBundle\Entity\Fragment;
use Sonata\ArticleBundle\FragmentService\FragmentServiceInterface;
use Sonata\CoreBundle\Form\Type\ImmutableArrayType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Trait FragmentFormFieldTestTrait.
 *
 * @author Sylvain Rascar <sylvain.rascar@ekino.com>
 */
trait FragmentFormFieldTestTrait
{
    /**
     * @param FragmentServiceInterface $fragmentService The fragment service to validate
     * @param array                    $fields          The list of fields in order indexed from 0
     * @param Fragment|null            $fragment        If the fragment must contain special properties,
     *                                                  you can pass them here
     */
    protected function expectInOrder(FragmentServiceInterface $fragmentService, array $fields, Fragment $fragment = null)
    {
        /** @var FormMapper|\PHPUnit_Framework_MockObject_MockObject $formMapper */
        $formMapper = $this->createMock(FormMapper::class);
        /*
         * We use the form builder mock to navigate through successive calls to 'get'
         * in case of model transformer instantiation for example
         *
         * @var FormBuilderInterface|\PHPUnit_Framework_MockObject_MockObject $form
         */
        $formBuilder = $this->createMock(FormBuilderInterface::class);
        $fragment = $fragment ?: new Fragment();

        $formBuilder->expects($this->any())->method('get')->will($this->returnValue($formBuilder));

        $formMapper->expects($this->any())
            ->method('getAdmin')->will($this->returnValue($this->createMock(AdminInterface::class)));
        $formMapper->expects($this->any())
            ->method('create')->will($this->returnCallback(function ($name, $type) {
                return [$name, $type];
            }));
        $formMapper->expects($this->any())
            ->method('getFormBuilder')->will($this->returnValue($formBuilder));

        $formMapper->expects($this->once())->method('add')->with(
            'settings',
            ImmutableArrayType::class,
            $this->callback(function ($item) use ($fields) {
                $valid = is_array($item) && array_key_exists('keys', $item);

                foreach ($fields as $index => $field) {
                    $valid = $valid && isset($item['keys'][$index][0]) && $item['keys'][$index][0] === $field[0]
                        && isset($item['keys'][$index][1]) && $item['keys'][$index][1] === $field[1]
                    ;
                }

                return $valid;
            })
        );

        $fragmentService->buildForm($formMapper, $fragment);
    }
}
