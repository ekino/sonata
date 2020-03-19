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

namespace Sonata\HelpersBundle\Tests\Drivers;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Lexik\Bundle\MaintenanceBundle\Drivers\Query\DsnQuery;
use PHPUnit\Framework\TestCase;
use Sonata\HelpersBundle\Drivers\DatabaseDriver;
use Sonata\HelpersBundle\Drivers\Query\DefaultQuery;
use Sonata\HelpersBundle\Tests\TestHelperTrait;
use Sonata\PageBundle\Entity\SiteManager;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class DatabaseDriverTest.
 *
 * @author Quentin Belot <quentin.belot@ekino.com>
 */
class DatabaseDriverTest extends TestCase
{
    use TestHelperTrait;

    /**
     * @throws \ReflectionException
     *
     * @dataProvider setOptionsProvider
     */
    public function testSetOptions(array $options, string $class, bool $expectManager): void
    {
        $siteManager = $this->createMock(SiteManager::class);
        $registry    = $this->createMock(Registry::class);
        $driver      = new DatabaseDriver($siteManager, $registry);

        if ($expectManager) {
            $registry->expects($this->once())->method('getManager')->willReturn($this->createMock(EntityManager::class));
        }

        $driver->setOptions($options);

        $this->assertInstanceOf($class, $this->getPrivatePropertyValue($driver, 'pdoDriver'));
    }

    public function setOptionsProvider(): \Generator
    {
        yield 'dsn_query'                => [['dsn' => 'foo'], DsnQuery::class, false];
        yield 'default_query'            => [['connection' => 'bar'], DefaultQuery::class, true];
        yield 'default_query_by_default' => [[], DefaultQuery::class, true];
    }

    public function testCreateLock(): void
    {
        $siteManager = $this->createMock(SiteManager::class);
        $registry    = $this->createMock(Registry::class);
        $driver      = new DatabaseDriver($siteManager, $registry);

        $pdoDriver = $this->createMock(DefaultQuery::class);
        $pdoDriver->expects($this->once())->method('setSiteId')->with($this->equalTo(1));

        $this->setPrivatePropertyValue($driver, 'siteId', 1);
        $this->setPrivatePropertyValue($driver, 'pdoDriver', $pdoDriver);

        $this->invokeMethod($driver, 'createLock');
    }

    public function testCreateUnlock(): void
    {
        $siteManager = $this->createMock(SiteManager::class);
        $registry    = $this->createMock(Registry::class);
        $driver      = new DatabaseDriver($siteManager, $registry);

        $pdoDriver = $this->createMock(DefaultQuery::class);
        $pdoDriver->expects($this->once())->method('setSiteId')->with($this->equalTo(1));

        $this->setPrivatePropertyValue($driver, 'siteId', 1);
        $this->setPrivatePropertyValue($driver, 'pdoDriver', $pdoDriver);

        $this->invokeMethod($driver, 'createUnlock');
    }

    public function testIsExists(): void
    {
        $siteManager = $this->createMock(SiteManager::class);
        $registry    = $this->createMock(Registry::class);
        $driver      = new DatabaseDriver($siteManager, $registry);

        $pdoDriver = $this->createMock(DefaultQuery::class);
        $pdoDriver->expects($this->once())->method('setSiteId')->with($this->equalTo(1));

        $this->setPrivatePropertyValue($driver, 'siteId', 1);
        $this->setPrivatePropertyValue($driver, 'pdoDriver', $pdoDriver);

        $this->invokeMethod($driver, 'isExists');
    }

    /**
     * @throws \ReflectionException
     *
     * @dataProvider getMessageLockProvider
     */
    public function testGetMessageLock(bool $data, string $expect): void
    {
        $siteManager = $this->createMock(SiteManager::class);
        $registry    = $this->createMock(Registry::class);
        $driver      = new DatabaseDriver($siteManager, $registry);

        $translator = $this->createMock(TranslatorInterface::class);
        $translator->expects($this->once())->method('trans')->with($expect, [
            '%siteId%' => 1,
        ], 'SonataHelpersBundle');

        $this->setPrivatePropertyValue($driver, 'siteId', 1);
        $this->setPrivatePropertyValue($driver, 'translator', $translator);

        $driver->getMessageLock($data);
    }

    public function getMessageLockProvider(): \Generator
    {
        yield 'not_success_lock' => [false, 'lexik_maintenance.not_success_lock'];
        yield 'success_lock'     => [true, 'lexik_maintenance.success_lock_database'];
    }

    /**
     * @throws \ReflectionException
     *
     * @dataProvider getMessageUnlockProvider
     */
    public function testGetMessageUnlock(bool $data, string $expect): void
    {
        $siteManager = $this->createMock(SiteManager::class);
        $registry    = $this->createMock(Registry::class);
        $driver      = new DatabaseDriver($siteManager, $registry);

        $translator = $this->createMock(TranslatorInterface::class);
        $translator->expects($this->once())->method('trans')->with($expect, [
            '%siteId%' => 1,
        ], 'SonataHelpersBundle');

        $this->setPrivatePropertyValue($driver, 'siteId', 1);
        $this->setPrivatePropertyValue($driver, 'translator', $translator);

        $driver->getMessageUnlock($data);
    }

    public function getMessageUnlockProvider(): \Generator
    {
        yield 'not_success_unlock' => [false, 'lexik_maintenance.not_success_unlock'];
        yield 'success_unlock'     => [true, 'lexik_maintenance.success_unlock'];
    }
}
