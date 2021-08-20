<?php

/*
 * This file is part of the ekino/sonata project.
 *
 * (c) Ekino
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\HelpersBundle\Block\Dashboard;

use Liip\MonitorBundle\Helper\PathHelper;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Environment;

/**
 * Class MonitorBlockService.
 *
 * @author Quentin Belot <quentin.belot@ekino.com>
 */
class MonitorBlockService extends AbstractBlockService
{
    /**
     * @var PathHelper
     */
    private $pathHelper;

    /**
     * @var string
     */
    private $liipMonitorDefaultGroup;

    /**
     * MonitorBlockService constructor.
     */
    public function __construct(
        Environment $templating,
        PathHelper $pathHelper,
        string $liipMonitorDefaultGroup
    ) {
        parent::__construct($templating);
        $this->pathHelper              = $pathHelper;
        $this->liipMonitorDefaultGroup = $liipMonitorDefaultGroup;
    }

    /**
     * {@inheritdoc}
     */
    public function configureSettings(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'template' => '@SonataHelpers/Block/dashboard/monitor.html.twig',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null): Response
    {
        /** @var string $urls */
        $urls = $this->pathHelper->getRoutesJs([
            'liip_monitor_run_all_checks'   => ['group' => $this->liipMonitorDefaultGroup],
            'liip_monitor_run_single_check' => ['checkId' => 'replaceme', 'group' => $this->liipMonitorDefaultGroup],
        ]);

        /** @var string $javascripts */
        $javascripts = $this->pathHelper->getScriptTags([
            'bundles/liipmonitor/javascript/ember-0.9.5.min.js',
            'bundles/liipmonitor/javascript/app.js',
        ]);

        return $this->renderResponse((string) $blockContext->getTemplate(), [
            'settings'    => $blockContext->getSettings(),
            'urls'        => $urls,
            'javascripts' => $javascripts,
        ], $response);
    }
}
