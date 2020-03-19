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

use Lexik\Bundle\MaintenanceBundle\Command\DriverUnlockCommand as BaseDriverUnlockCommand;
use Sonata\HelpersBundle\Drivers\DriverSiteInterface;
use Sonata\PageBundle\Model\SiteInterface;
use Sonata\PageBundle\Model\SiteManagerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class DriverUnlockCommand.
 *
 * @author Quentin Belot <quentin.belot@ekino.com>
 */
class DriverUnlockCommand extends BaseDriverUnlockCommand
{
    /**
     * @var SiteManagerInterface
     */
    private $siteManager;

    /**
     * DriverUnlockCommand constructor.
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
        if (!$this->confirmUnlock($input, $output)) {
            return 1;
        }

        $driver = $this->getContainer()->get('lexik_maintenance.driver.factory')->getDriver();

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

            $output->writeln('<info>'.$driver->getMessageUnlock($driver->unlock()).'</info>');
        }
    }
}
