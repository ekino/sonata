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

namespace Sonata\SonataHelpersBundle\Form\DataTransformer;

use Sonata\PageBundle\Model\PageInterface;
use Sonata\PageBundle\Model\PageManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class PageDataTransformer.
 *
 * @author Benoit de Jacobet <benoit.de-jacobet@ekino.com>
 */
class PageDataTransformer implements DataTransformerInterface
{
    /**
     * @var PageManagerInterface
     */
    protected $pageManager;

    /**
     * @var array
     */
    protected $keysToTransform;

    /**
     * @param PageManagerInterface $pageManager
     * @param array                $keysToTransform Array of key of field to transform
     */
    public function __construct(PageManagerInterface $pageManager, $keysToTransform = [])
    {
        $this->pageManager = $pageManager;
        $this->keysToTransform = $keysToTransform;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($settings)
    {
        if (is_array($settings) && !empty($settings)) {
            foreach ($settings as $key => $setting) {
                // $setting is a Page
                if (in_array($key, $this->keysToTransform, true) && null !== $setting) {
                    $settings[$key] = $this->pageManager->find($setting);
                }

                // Setting is an array of sub-settings
                if (!in_array($key, $this->keysToTransform, true) && is_array($setting) && !empty($setting)) {
                    $settings[$key] = $this->transform($setting);
                }
            }
        }

        return $settings;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($settings)
    {
        if (is_array($settings) && !empty($settings)) {
            foreach ($settings as $key => $setting) {
                // Setting is a Page
                if ($setting instanceof PageInterface) {
                    $settings[$key] = $setting->getId();
                }

                // Setting is an array of sub-settings
                if (is_array($setting) && !empty($setting)) {
                    $settings[$key] = $this->reverseTransform($setting);
                }
            }
        }

        return $settings;
    }
}
