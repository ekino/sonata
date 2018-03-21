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

namespace CanalPlus\Component\Form\Type;

use Doctrine\ORM\QueryBuilder;
use Sonata\AdminBundle\Form\Type\ChoiceFieldMaskType;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Model\ModelManagerInterface;
use Sonata\PageBundle\Model\SiteInterface;
use Sonata\SonataHelpersBundle\Form\DataTransformer\PageDataTransformer;
use Sonata\SonataHelpersBundle\Form\Type\InternalExternalLinkType;
use Sonata\SonataHelpersBundle\Entity\PageManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class InternalExternalLinkTypeTest.
 *
 * @author  Benoit de Jacobet <benoit.de-jacobet@ekino.com>
 */
class InternalExternalLinkTypeTest extends TypeTestCase
{
    /**
     * Test configureOptions.
     */
    public function testConfigureOptions()
    {
        $pageManager = $this->getMockBuilder(PageManagerInterface::class)->disableOriginalConstructor()->getMock();
        $modelManager = $this->getMockBuilder(ModelManagerInterface::class)->disableOriginalConstructor()->getMock();
        $resolver = $this->getMockBuilder(OptionsResolver::class)->disableOriginalConstructor()->getMock();

        $resolver
            ->expects($this->once())
            ->method('setRequired')
            ->with('site');

        $type = new InternalExternalLinkType($pageManager, $modelManager);
        $type->configureOptions($resolver);
    }

    /**
     * Test buildForm.
     */
    public function testBuildForm()
    {
        $pageManager = $this->getMockBuilder(PageManagerInterface::class)->disableOriginalConstructor()->getMock();
        $modelManager = $this->getMockBuilder(ModelManagerInterface::class)->disableOriginalConstructor()->getMock();
        $formBuilder = $this->getMockBuilder(FormBuilder::class)->disableOriginalConstructor()->getMock();
        $site = $this->getMockBuilder(SiteInterface::class)->disableOriginalConstructor()->getMock();
        $query = $this->getMockBuilder(QueryBuilder::class)->disableOriginalConstructor()->getMock();

        $pageDataTransformer = new PageDataTransformer($pageManager, ['page']);

        $pageManager
            ->expects($this->once())
            ->method('getAvailablePages')
            ->with($site)
            ->will($this->returnValue($query));

        $formBuilder
            ->expects($this->at(0))
            ->method('add')
            ->with(
                'linkType',
                ChoiceFieldMaskType::class,
                [
                    'choices' => [
                        'Page Interne' => 'page',
                        'Lien externe' => 'link',
                        'Pas de lien' => 'none',
                        'Choisissez le type de lien' => 'no_choice',
                    ],
                    'map' => [
                        'none' => [],
                        'page' => ['page', 'params'],
                        'link' => ['link'],
                    ],
                    'placeholder' => false,
                    'required' => false,
                    'label' => 'Type de lien',
                    'attr' => ['class' => 'LinkWidget_LinkTypeField'],
                ]
            );

        $formBuilder
            ->expects($this->at(1))
            ->method('add')
            ->with(
                'link',
                UrlType::class,
                [
                    'label' => 'Lien externe',
                    'required' => false,
                    'attr' => [
                        'class' => 'LinkWidget_ExternalLinkField',
                        'placeholder' => 'https://',
                    ],
                ]
            );

        $formBuilder
            ->expects($this->at(2))
            ->method('add')
            ->with(
                'page',
                ModelType::class,
                [
                    'label' => 'Page interne',
                    'property' => 'nameWithParams',
                    'class' => Page::class,
                    'model_manager' => $modelManager,
                    'required' => true,
                    'query' => $query,
                    'btn_add' => false,
                    'attr' => ['class' => 'LinkWidget_PageNameField'],
                ]
            )
            ->will($this->returnValue($formBuilder));

        $formBuilder
            ->expects($this->at(3))
            ->method('addModelTransformer')
            ->with($pageDataTransformer);

        $type = new InternalExternalLinkType($pageManager, $modelManager);
        $type->buildForm($formBuilder, [
            'site' => $site,
            'label' => 'Lien',
            'required' => false,
        ]);
    }

    /**
     * Test getBlockPrefix.
     */
    public function testGetBlockPrefix()
    {
        $pageManager = $this->getMockBuilder(PageManagerInterface::class)->disableOriginalConstructor()->getMock();
        $modelManager = $this->getMockBuilder(ModelManagerInterface::class)->disableOriginalConstructor()->getMock();

        $type = new InternalExternalLinkType($pageManager, $modelManager);
        $this->assertSame('awaken_type_internal_external_link_type', $type->getBlockPrefix());
    }
}
