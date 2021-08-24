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

namespace Sonata\HelpersBundle\Controller;

use Sonata\BlockBundle\Block\BlockServiceManager;
use Sonata\BlockBundle\Block\Service\BlockServiceInterface;
use Sonata\HelpersBundle\Block\BlockFilter\BlockFilter;
use Sonata\PageBundle\Admin\BlockAdmin;
use Sonata\PageBundle\Controller\PageAdminController as BasePageAdminController;
use Sonata\PageBundle\Model\Block;
use Sonata\PageBundle\Model\Template;
use Sonata\PageBundle\Page\TemplateManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Class PageAdminController.
 *
 * @author Vincent Laurier <vincent.laurier@ekino.com>
 * @author Romain Mouillard <romain.mouillard@ekino.com>
 * @author Ramesses Bonhof Keny <ramesses-bonhof.keny@ekino.com>
 * @author Christian Kollross <christian.kollross@ekino.com>
 */
class PageAdminController extends BasePageAdminController
{
    /**
     * @var BlockFilter
     */
    private $blockFilter;

    /**
     * @var BlockAdmin
     */
    private $blockAdmin;

    /**
     * @var BlockServiceManager
     */
    private $blockServiceManager;

    /**
     * @var TemplateManagerInterface
     */
    private $templateManager;

    /**
     * PageAdminController constructor.
     */
    public function __construct(
        BlockFilter $blockFilter,
        BlockAdmin $blockAdmin,
        BlockServiceManager $blockServiceManager,
        TemplateManagerInterface $templateManager
    ) {
        $this->blockFilter         = $blockFilter;
        $this->blockAdmin          = $blockAdmin;
        $this->blockServiceManager = $blockServiceManager;
        $this->templateManager     = $templateManager;
    }

    /**
     * @throws AccessDeniedException
     * @throws NotFoundHttpException
     */
    public function composeContainerShowAction(?Request $request = null): Response
    {
        if (false === $this->blockAdmin->isGranted('LIST')) {
            throw new AccessDeniedException();
        }

        if (null == $request) {
            $request = $this->getRequest();
        }

        $id    = $request->attributes->get($this->admin->getIdParameter());
        $block = $this->blockAdmin->getObject($id);

        if (!$block instanceof Block) {
            throw new NotFoundHttpException(sprintf('Unable to find the block with id : %s', $id));
        }

        $blockServices = $this->blockServiceManager->getServicesByContext('sonata_page_bundle', false);
        $page          = $block->getPage();

        // Filter service using the template configuration
        if (null !== $page) {
            $page = $block->getPage();
            if (null !== $page->getTemplateCode()) {
                $template  = $this->templateManager->get($page->getTemplateCode());
                if ($template instanceof Template) {
                    $container = $template->getContainer($block->getSetting('code'));

                    if (isset($container['blocks']) && \count($container['blocks']) > 0) {
                        foreach ($blockServices as $code => $service) {
                            if (\in_array($code, $container['blocks'])) {
                                continue;
                            }

                            unset($blockServices[$code]);
                        }
                    }

                    // We're filtering remaining block services according to page
                    $blockServices = $this->blockFilter->filter($blockServices, $page);
                }
            }
        }

        $blocksByCategory = $this->getBlocksByCategory($blockServices);

        // @Todo: Use template registry
        return $this->renderWithExtraParams('@SonataHelpers/PageAdmin/compose_container_show.html.twig', [
            'blockCategories'  => $this->blockFilter->getCategories(),
            'blocksByCategory' => $blocksByCategory,
            'blockServices'    => $blockServices,
            'container'        => $block,
            'page'             => $block->getPage(),
        ]);
    }

    /**
     * Return blocks grouped by categories.
     *
     * @param array<BlockServiceInterface> $blockServices
     *
     * @return array<array-key, BlockServiceInterface[]>
     */
    protected function getBlocksByCategory(array $blockServices): array
    {
        $blocksByCategory = [];

        foreach ($blockServices as $code => $blockService) {
            $categories = $this->blockFilter->getBlockCategories($code);

            foreach ($categories as $category) {
                if (!isset($blocksByCategory[$category])) {
                    $blocksByCategory[$category] = [];
                }

                $blocksByCategory[$category][$code] = $blockService;
            }
        }

        return $blocksByCategory;
    }
}
