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

use Doctrine\Bundle\DoctrineBundle\Registry;
use Lexik\Bundle\MaintenanceBundle\Drivers\DatabaseDriver as LexikDatabaseDriver;
use Lexik\Bundle\MaintenanceBundle\Drivers\DriverTtlInterface;
use Lexik\Bundle\MaintenanceBundle\Drivers\Query\DsnQuery;
use Sonata\HelpersBundle\Drivers\Query\DefaultQuery;
use Sonata\PageBundle\Model\SiteManagerInterface;

/**
 * Class DatabaseDriver.
 *
 * @author Quentin Belot <quentin.belot@ekino.com>
 */
class DatabaseDriver extends LexikDatabaseDriver implements DriverTtlInterface, DriverSiteInterface
{
    /**
     * @var int
     */
    protected $siteId;

    /**
     * @var SiteManagerInterface
     */
    protected $siteManager;

    /**
     * DatabaseDriver constructor.
     */
    public function __construct(SiteManagerInterface $siteManager, Registry $doctrine = null)
    {
        parent::__construct($doctrine);

        $this->siteManager = $siteManager;
    }

    /**
     * {@inheritdoc}
     */
    public function setOptions($options)
    {
        $this->options = $options;

        if (isset($this->options['dsn'])) {
            $this->pdoDriver = new DsnQuery($this->options);
        } else {
            if (isset($this->options['connection'])) {
                $this->pdoDriver = new DefaultQuery($this->doctrine->getManager($this->options['connection']), $this->siteManager);
            } else {
                $this->pdoDriver = new DefaultQuery($this->doctrine->getManager(), $this->siteManager);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function createLock()
    {
        $this->pdoDriver->setSiteId($this->siteId);

        return parent::createLock();
    }

    /**
     * {@inheritdoc}
     */
    protected function createUnlock()
    {
        $this->pdoDriver->setSiteId($this->siteId);

        return parent::createUnlock();
    }

    /**
     * {@inheritdoc}
     */
    public function isExists()
    {
        $this->pdoDriver->setSiteId($this->siteId);

        return parent::isExists();
    }

    /**
     * {@inheritdoc}
     */
    public function getMessageLock($resultTest)
    {
        $key = $resultTest ? 'lexik_maintenance.success_lock_database' : 'lexik_maintenance.not_success_lock';

        return $this->translator->trans($key, [
            '%siteId%' => $this->siteId,
        ], 'SonataHelpersBundle');
    }

    /**
     * {@inheritdoc}
     */
    public function getMessageUnlock($resultTest)
    {
        $key = $resultTest ? 'lexik_maintenance.success_unlock' : 'lexik_maintenance.not_success_unlock';

        return $this->translator->trans($key, [
            '%siteId%' => $this->siteId,
        ], 'SonataHelpersBundle');
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
