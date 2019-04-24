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

namespace Sonata\HelpersBundle\TestHelpers\Common;

/**
 * Class ClassTestHelper.
 *
 * @author Sylvain Rascar <sylvain.rascar@ekino.com>
 */
class ClassTestHelper
{
    /**
     * @param string $class
     * @param int    $id
     * @param string $propertyName
     *
     * @return mixed
     */
    public static function getInstanceWithId($class, $id, $propertyName = 'id')
    {
        $item               = new $class();
        $reflection         = new \ReflectionClass($item);
        $reflectionProperty = $reflection->getProperty($propertyName);
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($item, $id);

        return $item;
    }
}
