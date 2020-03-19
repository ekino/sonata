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

namespace Sonata\HelpersBundle\Tests;

/**
 * Trait TestHelperTrait.
 *
 * @author Quentin Belot <quentin.belot@ekino.com>
 */
trait TestHelperTrait
{
    /**
     * Call protected/private method of a class.
     *
     * @param mixed  $object     Instancied object that will return on method on
     * @param string $methodName Method name to call
     * @param array  $parameters Array of parameters to pass into method
     *
     * @throws \ReflectionException
     *
     * @return mixed Method return
     */
    public function invokeMethod($object, string $methodName, array $parameters = [])
    {
        $method = new \ReflectionMethod($object, $methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    /**
     * @param mixed $object
     * @param mixed $value
     *
     * @throws \ReflectionException
     */
    public function setPrivatePropertyValue($object, string $propertyName, $value): void
    {
        $property = new \ReflectionProperty($object, $propertyName);
        $property->setAccessible(true);
        $property->setValue($object, $value);
        $property->setAccessible(false);
    }

    /**
     * @param mixed $object
     *
     * @throws \ReflectionException
     *
     * @return mixed
     */
    public function getPrivatePropertyValue($object, string $propertyName)
    {
        $property = new \ReflectionProperty($object, $propertyName);
        $property->setAccessible(true);

        $value = $property->getValue($object);
        $property->setAccessible(false);

        return $value;
    }
}
