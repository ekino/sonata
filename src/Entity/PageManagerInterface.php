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

namespace Sonata\SonataHelpersBundle\Entity;

use Sonata\PageBundle\Model\PageManagerInterface as BasePageManagerInterface;
use Sonata\PageBundle\Model\SiteInterface;

/**
 * Interface PageDataTransformer.
 *
 * @author Benoit de Jacobet <benoit.de-jacobet@ekino.com>
 */
interface PageManagerInterface extends BasePageManagerInterface
{
    /**
     * Return a query build with all pages for the $site.
     *
     * @param SiteInterface $site
     *
     * @return \Doctrine\ORM\Query
     */
    public function getAvailablePages(SiteInterface $site);
}
