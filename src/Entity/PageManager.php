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

use Sonata\PageBundle\Entity\PageManager as BasePageManager;
use Sonata\PageBundle\Model\Page;
use Sonata\PageBundle\Model\SiteInterface;

/**
 * Class PageManager.
 *
 * @author Benoit de Jacobet <benoit.de-jacobet@ekino.com>
 */
class PageManager extends BasePageManager implements PageManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getAvailablePages(SiteInterface $site = null)
    {
        $qb = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('page')
            ->from(Page::class, 'page')
            ->andWhere('page.requestMethod LIKE :request_method', 'page.routeName NOT LIKE :fos_user')
            ->andWhere('page.routeName NOT LIKE :sonata_cache', 'page.url IS NOT NULL')
            ->andWhere('page.routeName NOT LIKE :internal_page')
            ->setParameters([
                'request_method' => '%GET%',
                'fos_user'       => 'fos_user%',
                'sonata_cache'   => 'sonata_cache%',
                'internal_page'  => '_page_internal%',
            ])
        ;

        if (null !== $site) {
            $qb->andWhere('page.site = '.$site->getId());
        }

        return $qb->getQuery();
    }
}
