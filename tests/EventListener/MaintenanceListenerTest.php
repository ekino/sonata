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

namespace Sonata\HelpersBundle\Tests\EventListener;

use Lexik\Bundle\MaintenanceBundle\Drivers\DriverFactory;
use Lexik\Bundle\MaintenanceBundle\Exception\ServiceUnavailableException;
use Sonata\HelpersBundle\Drivers\DatabaseDriver;
use Sonata\HelpersBundle\EventListener\MaintenanceListener;
use Sonata\PageBundle\Model\SiteInterface;
use Sonata\PageBundle\Site\SiteSelectorInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Class MaintenanceListenerTest.
 *
 * @author Quentin Belot <quentin.belot@ekino.com>
 */
class MaintenanceListenerTest extends WebTestCase
{
    /**
     * Test method onKernelRequest.
     */
    public function testOnKernelRequest(): void
    {
        $request = Request::create('/', 'GET');

        $site           = $this->createMock(SiteInterface::class);
        $siteSelector   = $this->createMock(SiteSelectorInterface::class);
        $driverFactory  = $this->createMock(DriverFactory::class);
        $databaseDriver = $this->createMock(DatabaseDriver::class);
        $event          = $this->createMock(GetResponseEvent::class);

        $event->expects($this->once())->method('isMasterRequest')->willReturn(true);
        $event->expects($this->once())->method('getRequest')->willReturn($request);
        $siteSelector->expects($this->once())->method('retrieve')->willReturn($site);
        $site->expects($this->once())->method('getId')->willReturn(1);
        $driverFactory->expects($this->once())->method('getDriver')->willReturn($databaseDriver);
        $databaseDriver->expects($this->once())->method('setSiteId')->with(1);
        $databaseDriver->expects($this->once())->method('decide')->willReturn(false);

        $listener = new MaintenanceListener($siteSelector, $driverFactory);
        $listener->onKernelRequest($event);
    }

    /**
     * Test method onKernelRequest.
     */
    public function testOnKernelRequestException(): void
    {
        $request = Request::create('/', 'GET');

        $site           = $this->createMock(SiteInterface::class);
        $siteSelector   = $this->createMock(SiteSelectorInterface::class);
        $driverFactory  = $this->createMock(DriverFactory::class);
        $databaseDriver = $this->createMock(DatabaseDriver::class);
        $event          = $this->createMock(GetResponseEvent::class);

        $event->expects($this->once())->method('isMasterRequest')->willReturn(true);
        $event->expects($this->once())->method('getRequest')->willReturn($request);
        $siteSelector->expects($this->once())->method('retrieve')->willReturn($site);
        $site->expects($this->once())->method('getId')->willReturn(1);
        $driverFactory->expects($this->once())->method('getDriver')->willReturn($databaseDriver);
        $databaseDriver->expects($this->once())->method('setSiteId')->with(1);
        $databaseDriver->expects($this->once())->method('decide')->willReturn(true);

        $this->expectException(ServiceUnavailableException::class);

        $listener = new MaintenanceListener($siteSelector, $driverFactory);
        $listener->onKernelRequest($event);
    }
}
