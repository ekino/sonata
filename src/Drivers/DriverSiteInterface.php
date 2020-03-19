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

namespace Sonata\HelpersBundle\Drivers;

/**
 * Interface DriverSiteInterface.
 *
 * @author Quentin Belot <quentin.belot@ekino.com>
 */
interface DriverSiteInterface
{
    /**
     * Set id of site.
     */
    public function setSiteId(int $siteId): void;

    /**
     * Return id of site.
     */
    public function getSiteId(): int;
}
