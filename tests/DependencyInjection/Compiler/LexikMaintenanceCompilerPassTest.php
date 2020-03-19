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

namespace Sonata\HelpersBundle\Tests\DependencyInjection\Compiler;

use Lexik\Bundle\MaintenanceBundle\Command\DriverLockCommand as LexikDriverLockCommand;
use Lexik\Bundle\MaintenanceBundle\Command\DriverUnlockCommand as LexikDriverUnlockCommand;
use PHPUnit\Framework\TestCase;
use Sonata\HelpersBundle\Command\DriverLockCommand;
use Sonata\HelpersBundle\Command\DriverUnlockCommand;
use Sonata\HelpersBundle\DependencyInjection\Compiler\LexikMaintenanceCompilerPass;
use Sonata\HelpersBundle\Drivers\DatabaseDriver;
use Sonata\HelpersBundle\EventListener\MaintenanceListener;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class LexikMaintenanceCompilerPassTest.
 *
 * @author Quentin Belot <quentin.belot@ekino.com>
 */
class LexikMaintenanceCompilerPassTest extends TestCase
{
    public function testProcess(): void
    {
        $container = new ContainerBuilder();
        $container->register(LexikDriverLockCommand::class);
        $container->register(LexikDriverUnlockCommand::class);
        $container->register('lexik_maintenance.driver.database');
        $container->register('lexik_maintenance.listener');

        $this->process($container);

        $this->assertSame(DriverLockCommand::class, $container->findDefinition(LexikDriverLockCommand::class)->getClass());
        $this->assertEquals(new Reference('sonata.page.manager.site'), $container->findDefinition(LexikDriverLockCommand::class)->getArgument(0));

        $this->assertSame(DriverUnlockCommand::class, $container->findDefinition(LexikDriverUnlockCommand::class)->getClass());
        $this->assertEquals(new Reference('sonata.page.manager.site'), $container->findDefinition(LexikDriverUnlockCommand::class)->getArgument(0));

        $this->assertSame(DatabaseDriver::class, $container->findDefinition('lexik_maintenance.driver.database')->getClass());
        $this->assertEquals(new Reference('sonata.page.manager.site'), $container->findDefinition('lexik_maintenance.driver.database')->getArgument(0));

        $this->assertSame(MaintenanceListener::class, $container->findDefinition('lexik_maintenance.listener')->getClass());
        $this->assertEquals(new Reference('sonata.page.site.selector'), $container->findDefinition('lexik_maintenance.listener')->getArgument(0));
    }

    private function process(ContainerBuilder $container): void
    {
        (new LexikMaintenanceCompilerPass())->process($container);
    }
}
