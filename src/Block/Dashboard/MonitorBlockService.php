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
use Sonata\BlockBundle\Block\Service\AbstractAdminBlockService;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class MonitorBlockService.
 *
 * @author Quentin Belot <quentin.belot@ekino.com>
 */
class MonitorBlockService extends AbstractAdminBlockService
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
     *
     * @param string          $name
     * @param EngineInterface $templating
     * @param PathHelper      $pathHelper
     * @param string          $liipMonitorDefaultGroup
     */
    public function __construct(
        string $name,
        EngineInterface $templating,
        PathHelper $pathHelper,
        string $liipMonitorDefaultGroup
    ) {
        parent::__construct($name, $templating);

        $this->pathHelper              = $pathHelper;
        $this->liipMonitorDefaultGroup = $liipMonitorDefaultGroup;
    }

    /**
     * {@inheritdoc}
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'template' => '@SonataHelpers/Block/dashboard/monitor.html.twig',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $urls = $this->pathHelper->getRoutesJs([
            'liip_monitor_run_all_checks'   => ['group' => $this->liipMonitorDefaultGroup],
            'liip_monitor_run_single_check' => ['checkId' => 'replaceme', 'group' => $this->liipMonitorDefaultGroup],
        ]);

        $javascripts = $this->pathHelper->getScriptTags([
            'bundles/liipmonitor/javascript/ember-0.9.5.min.js',
            'bundles/liipmonitor/javascript/app.js',
        ]);

        return $this->renderResponse($blockContext->getTemplate(), [
            'settings'    => $blockContext->getSettings(),
            'urls'        => $urls,
            'javascripts' => $javascripts,
        ], $response);
    }
}
