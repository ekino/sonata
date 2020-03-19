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

namespace Sonata\HelpersBundle\Tests\Drivers\Query;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;
use Sonata\HelpersBundle\Drivers\Query\DefaultQuery;
use Sonata\HelpersBundle\Tests\TestHelperTrait;
use Sonata\PageBundle\Model\SiteManagerInterface;

/**
 * Class DefaultQueryTest.
 *
 * @author Quentin Belot <quentin.belot@ekino.com>
 */
class DefaultQueryTest extends TestCase
{
    use TestHelperTrait;

    /**
     * @dataProvider initDbProvider
     */
    public function testInitDb(bool $exist): void
    {
        $siteManager = $this->createMock(SiteManagerInterface::class);
        $em          = $this->createMock(EntityManager::class);
        $connection  = $this->createMock(Connection::class);
        $pdoDriver   = new DefaultQuery($em, $siteManager);

        if (!$exist) {
            $em->expects($this->once())->method('getConnection')->willReturn($connection);
        } else {
            $this->setPrivatePropertyValue($pdoDriver, 'db', $connection);
        }

        $this->assertSame($connection, $pdoDriver->initDb());
    }

    public function initDbProvider(): \Generator
    {
        yield 'not_exist' => [false];
        yield 'exist'     => [true];
    }

    public function testDeleteQuery(): void
    {
        $siteManager = $this->createMock(SiteManagerInterface::class);
        $em          = $this->createMock(EntityManager::class);
        $connection  = $this->createMock(Connection::class);
        $pdoDriver   = $this->getMockBuilder(DefaultQuery::class)
            ->setConstructorArgs([$em, $siteManager])
            ->setMethods(['exec'])
            ->getMock()
        ;

        $siteManager->expects($this->once())->method('getTableName')->willReturn('foo');

        $pdoDriver->expects($this->once())->method('exec')->with(
            $connection, sprintf('UPDATE %s SET maintenance_ttl = NULL WHERE id = :siteId', 'foo'),
            [
                ':siteId' => 1,
            ]
        );

        $this->setPrivatePropertyValue($pdoDriver, 'siteId', 1);

        $pdoDriver->deleteQuery($connection);
    }

    /**
     * @throws \ReflectionException
     *
     * @dataProvider selectQueryProvider
     */
    public function testSelectQuery(?array $data, ?array $expect): void
    {
        $siteManager = $this->createMock(SiteManagerInterface::class);
        $em          = $this->createMock(EntityManager::class);
        $connection  = $this->createMock(Connection::class);
        $pdoDriver   = $this->getMockBuilder(DefaultQuery::class)
            ->setConstructorArgs([$em, $siteManager])
            ->setMethods(['fetch'])
            ->getMock()
        ;

        $siteManager->expects($this->once())->method('getTableName')->willReturn('foo');

        $pdoDriver->expects($this->once())->method('fetch')->with(
            $connection, sprintf('SELECT maintenance_ttl AS ttl FROM %s WHERE id = :siteId', 'foo'),
            [
                ':siteId' => 1,
            ]
        )->willReturn($data);

        $this->setPrivatePropertyValue($pdoDriver, 'siteId', 1);

        $this->assertSame($expect, $pdoDriver->selectQuery($connection));
    }

    public function selectQueryProvider(): \Generator
    {
        yield 'no_data'    => [null, null];
        yield 'empty_data' => [[], null];
        yield 'data'       => [
            [
                [
                    'ttl' => 'foo',
                ],
            ],
            [
                [
                    'ttl' => 'foo',
                ],
            ],
        ];
    }

    public function testInsertQuery(): void
    {
        $siteManager = $this->createMock(SiteManagerInterface::class);
        $em          = $this->createMock(EntityManager::class);
        $connection  = $this->createMock(Connection::class);
        $pdoDriver   = $this->getMockBuilder(DefaultQuery::class)
            ->setConstructorArgs([$em, $siteManager])
            ->setMethods(['exec'])
            ->getMock()
        ;

        $siteManager->expects($this->once())->method('getTableName')->willReturn('foo');

        $pdoDriver->expects($this->once())->method('exec')->with(
            $connection, sprintf('UPDATE %s SET maintenance_ttl = :ttl WHERE id = :siteId', 'foo'),
            [
                ':ttl'    => 'bar',
                ':siteId' => 1,
            ]
        );

        $this->setPrivatePropertyValue($pdoDriver, 'siteId', 1);

        $pdoDriver->insertQuery('bar', $connection);
    }

    public function testGetSiteId(): void
    {
        $siteManager = $this->createMock(SiteManagerInterface::class);
        $em          = $this->createMock(EntityManager::class);
        $pdoDriver   = new DefaultQuery($em, $siteManager);

        $this->setPrivatePropertyValue($pdoDriver, 'siteId', 1);

        $this->assertSame(1, $pdoDriver->getSiteId());
    }

    public function testSetSiteId(): void
    {
        $siteManager = $this->createMock(SiteManagerInterface::class);
        $em          = $this->createMock(EntityManager::class);
        $pdoDriver   = new DefaultQuery($em, $siteManager);

        $pdoDriver->setSiteId(1);

        $this->assertSame(1, $this->getPrivatePropertyValue($pdoDriver, 'siteId'));
    }
}
