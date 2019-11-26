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

namespace Sonata\HelpersBundle;

use Sonata\HelpersBundle\DependencyInjection\Compiler\LexikMaintenanceCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class SonataHelpersBundle.
 *
 * @author Benoit Mazière <benoit.maziere@ekino.com>
 */
class SonataHelpersBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new LexikMaintenanceCompilerPass());
    }
}
