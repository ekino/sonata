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

namespace Sonata\SonataHelpersBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class InternalExternalLink.
 *
 * @author  Benoit de Jacobet <benoit.de-jacobet@ekino.com>
 */
class InternalExternalLink extends Constraint
{
    public $message = 'internal_external_link.error';
}
