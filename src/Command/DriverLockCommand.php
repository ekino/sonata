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

namespace Sonata\HelpersBundle\Command;

use Lexik\Bundle\MaintenanceBundle\Command\DriverLockCommand as LexikDriverLockCommand;
use Lexik\Bundle\MaintenanceBundle\Drivers\DriverTtlInterface;
use Sonata\HelpersBundle\Drivers\DriverSiteInterface;
use Sonata\PageBundle\Model\SiteInterface;
use Sonata\PageBundle\Model\SiteManagerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class DriverLockCommand.
 *
 * @author Quentin Belot <quentin.belot@ekino.com>
 */
class DriverLockCommand extends LexikDriverLockCommand
{
    /**
     * @var SiteManagerInterface
     */
    private $siteManager;

    /**
     * DriverLockCommand constructor.
     */
    public function __construct(SiteManagerInterface $siteManager)
    {
        parent::__construct();

        $this->siteManager = $siteManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->addOption(
                'site-id',
                null,
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'Indicate id of site to lock',
                ['all']
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $driver = $this->getContainer()->get('lexik_maintenance.driver.factory')->getDriver();

        if ($input->isInteractive()) {
            if (!$this->askConfirmation('WARNING! Are you sure you wish to continue? (y/n)', $input, $output)) {
                $output->writeln('<error>Maintenance cancelled!</error>');

                return 1;
            }
        } elseif (null !== $input->getArgument('ttl')) {
            $this->ttl = $input->getArgument('ttl');
        } elseif ($driver instanceof DriverTtlInterface) {
            $this->ttl = $driver->getTtl();
        }

        // set ttl from command line if given and driver supports it
        if ($driver instanceof DriverTtlInterface) {
            $driver->setTtl($this->ttl);
        }

        if (['all'] !== $input->getOption('site-id')) {
            $sites = $input->getOption('site-id');
        } else {
            $sites = $this->siteManager->findAll();
        }

        foreach ($sites as $site) {
            $siteId = $site instanceof SiteInterface ? $site->getId() : (int) $site;

            // set id of site from command line if given and driver supports it
            if ($driver instanceof DriverSiteInterface) {
                $driver->setSiteId($siteId);
            }

            $output->writeln('<info>'.$driver->getMessageLock($driver->lock()).'</info>');
        }
    }
}
