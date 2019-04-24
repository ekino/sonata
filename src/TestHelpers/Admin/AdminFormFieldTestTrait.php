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

namespace Sonata\HelpersBundle\TestHelpers\Admin;

use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Trait AdminFormFieldTestTrait.
 *
 * @author Sylvain Rascar <sylvain.rascar@ekino.com>
 */
trait AdminFormFieldTestTrait
{
    /**
     * @param AdminInterface $admin
     *
     * @return FormMapper|\PHPUnit_Framework_MockObject_MockObject|FormMapper
     */
    protected function mockFormMapper(AdminInterface $admin)
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

        /*
         * We create a public property fields that will store the fields added to the mapper
         * to validate configureFormFields method
         */
        $formMapper->fields = [];

        $formBuilder->expects($this->any())->method('get')->will($this->returnValue($formBuilder));

        $formMapper->expects($this->any())
            ->method('getAdmin')->will($this->returnValue($admin));
        $formMapper->expects($this->any())
            ->method('create')->will($this->returnCallback(function ($name, $type) {
                return [$name, $type];
            }));
        $formMapper->expects($this->any())
            ->method('getFormBuilder')->will($this->returnValue($formBuilder));
        // We don't care about the groups
        $formMapper->expects($this->any())
            ->method('with')->will($this->returnValue($formMapper));
        $formMapper->expects($this->any())
            ->method('end')->will($this->returnValue($formMapper));
        // We just store the generated fields in the fields attribute
        $formMapper->expects($this->any())
            ->method('add')->will($this->returnCallback(function ($name, $type, $config) use ($formMapper) {
                $formMapper->fields[] = [$name, $type, $config];

                return $formMapper;
            }));

        return $formMapper;
    }

    /**
     * @param FormMapper|\PHPUnit_Framework_MockObject_MockObject $formMapper The FormMapper mock
     * @param array                                               $fields     The of fields in the expected order
     */
    protected function expectInOrder($formMapper, array $fields)
    {
        $admin      = $formMapper->getAdmin();
        $reflection = new \ReflectionClass(\get_class($admin));
        $method     = $reflection->getMethod('configureFormFields');
        $method->setAccessible(true);

        $method->invokeArgs($admin, [$formMapper]);

        foreach ($fields as $index => $field) {
            $this->assertArrayHasKey($index, $formMapper->fields);
            // We validate at least name and type
            $this->assertSame([$field[0], $field[1]], [$formMapper->fields[$index][0], $formMapper->fields[$index][1]]);
        }
    }
}
