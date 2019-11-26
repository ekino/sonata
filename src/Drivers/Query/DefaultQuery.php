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

namespace Sonata\HelpersBundle\Drivers\Query;

use Doctrine\ORM\EntityManager;
use Lexik\Bundle\MaintenanceBundle\Drivers\Query\DefaultQuery as LexikDefaultQuery;
use Sonata\PageBundle\Model\SiteManagerInterface;

/**
 * Class DefaultQuery.
 *
 * @author Quentin Belot <quentin.belot@ekino.com>
 */
class DefaultQuery extends LexikDefaultQuery
{
    /**
     * @var int
     */
    protected $siteId;

    /**
     * @var SiteManagerInterface
     */
    protected $siteManager;

    public function __construct(EntityManager $em, SiteManagerInterface $siteManager)
    {
        parent::__construct($em);

        $this->siteManager = $siteManager;
    }

    /**
     * {@inheritdoc}
     */
    public function initDb()
    {
        if (null === $this->db) {
            $this->db = $this->em->getConnection();
        }

        return $this->db;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteQuery($db)
    {
        return $this->exec(
            $db, sprintf('UPDATE %s SET maintenance_ttl = NULL WHERE id = :siteId', $this->siteManager->getTableName()),
            [
                ':siteId' => $this->siteId,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function selectQuery($db)
    {
        $data = $this->fetch($db, sprintf('SELECT maintenance_ttl AS ttl FROM %s WHERE id = :siteId', $this->siteManager->getTableName()), [
            ':siteId' => $this->siteId,
        ]);

        if (\is_array($data) && !current($data)['ttl']) {
            return null;
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function insertQuery($ttl, $db)
    {
        return $this->exec(
            $db, sprintf('UPDATE %s SET maintenance_ttl = :ttl WHERE id = :siteId', $this->siteManager->getTableName()),
            [
                ':ttl'    => $ttl,
                ':siteId' => $this->siteId,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function setSiteId(int $siteId): void
    {
        $this->siteId = $siteId;
    }

    /**
     * {@inheritdoc}
     */
    public function getSiteId(): int
    {
        return $this->siteId;
    }
}
