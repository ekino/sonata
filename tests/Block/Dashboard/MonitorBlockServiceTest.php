<?php

/*
 * This file is part of the ekino/sonata project.
 *
 * (c) Ekino
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\HelpersBundle\Tests\Block\Dashboard;

use Liip\MonitorBundle\Helper\PathHelper;
use PHPUnit\Framework\TestCase;
use Sonata\BlockBundle\Block\BlockContext;
use Sonata\HelpersBundle\Block\Dashboard\MonitorBlockService;
use Sonata\PageBundle\Entity\BaseBlock;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Environment;

/**
 * Class MonitorBlockServiceTest.
 *
 * @author Quentin Belot <quentin.belot@ekino.com>
 */
class MonitorBlockServiceTest extends TestCase
{
    /**
     * Test Execute.
     */
    public function testExecute(): void
    {
        $pathHelper     = $this->createMock(PathHelper::class);
        $templating     = $this->createMock(Environment::class);
        $service        = new MonitorBlockService($templating, $pathHelper, 'defaultGroup');
        $block          = new Block();
        $optionResolver = new OptionsResolver();

        $service->configureSettings($optionResolver);

        $blockContext = new BlockContext($block, $optionResolver->resolve());

        $this->assertArrayHasKey('template', $blockContext->getSettings());

        $this->assertSame('@SonataHelpers/Block/dashboard/monitor.html.twig', $blockContext->getSettings()['template']);

        $pathHelper->expects($this->once())
            ->method('getRoutesJs')
            ->with([
                'liip_monitor_run_all_checks' => [
                    'group' => 'defaultGroup',
                ],
                'liip_monitor_run_single_check' => [
                    'checkId' => 'replaceme',
                    'group'   => 'defaultGroup',
                ],
            ])
            ->willReturn(['an array of urls']);
        $pathHelper->expects($this->once())
            ->method('getScriptTags')
            ->with([
                'bundles/liipmonitor/javascript/ember-0.9.5.min.js',
                'bundles/liipmonitor/javascript/app.js',
            ])
            ->willReturn(['an array of js files']);

        $service->execute($blockContext);
    }
}

class Block extends BaseBlock
{
    /**
     * @var int
     */
    protected $id;

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }
}
