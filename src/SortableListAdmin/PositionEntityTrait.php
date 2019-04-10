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

namespace Sonata\SonataHelpersBundle\SortableListAdmin;

/**
 * Class PositionEntityTrait.
 *
 * Add position field with getter/setter to an entity
 *
 * @author  Benoit de Jacobet <benoit.de-jacobet@ekino.com>
 */
trait PositionEntityTrait
{
    /**
     * @var int
     */
    protected $position;

    /**
     * {@inheritdoc}
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * {@inheritdoc}
     */
    public function getPosition()
    {
        return $this->position;
    }
}
