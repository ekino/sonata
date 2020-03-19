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

namespace Sonata\HelpersBundle\DependencyInjection\Compiler;

use Lexik\Bundle\MaintenanceBundle\Command\DriverLockCommand as LexikDriverLockCommand;
use Lexik\Bundle\MaintenanceBundle\Command\DriverUnlockCommand as LexikDriverUnlockCommand;
use Sonata\HelpersBundle\Command\DriverLockCommand;
use Sonata\HelpersBundle\Command\DriverUnlockCommand;
use Sonata\HelpersBundle\Drivers\DatabaseDriver;
use Sonata\HelpersBundle\EventListener\MaintenanceListener;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class LexikMaintenanceCompilerPass.
 *
 * @author Quentin Belot <quentin.belot@ekino.com>
 */
class LexikMaintenanceCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $container
            ->findDefinition(LexikDriverLockCommand::class)
            ->setClass(DriverLockCommand::class)
            ->setArgument(0, new Reference('sonata.page.manager.site'));

        $container
            ->findDefinition(LexikDriverUnlockCommand::class)
            ->setClass(DriverUnlockCommand::class)
            ->setArgument(0, new Reference('sonata.page.manager.site'));

        $this->defineDatabaseDriver($container);
        $this->defineMaintenanceListener($container);
    }

    private function defineDatabaseDriver(ContainerBuilder $container): void
    {
        $definition = $container
            ->findDefinition('lexik_maintenance.driver.database')
            ->setClass(DatabaseDriver::class);

        $arguments = $definition->getArguments();
        array_unshift($arguments, new Reference('sonata.page.manager.site'));
        $definition->setArguments($arguments);
    }

    private function defineMaintenanceListener(ContainerBuilder $container): void
    {
        $definition = $container
            ->findDefinition('lexik_maintenance.listener')
            ->setClass(MaintenanceListener::class)
        ;

        $arguments = $definition->getArguments();
        array_unshift($arguments, new Reference('sonata.page.site.selector'));
        $definition->setArguments($arguments);
    }
}
