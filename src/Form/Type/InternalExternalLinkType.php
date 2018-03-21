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

namespace Sonata\SonataHelpersBundle\Form\Type;

use Sonata\AdminBundle\Form\Type\ChoiceFieldMaskType;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Model\ModelManagerInterface;
use Sonata\PageBundle\Model\Page;
use Sonata\SonataHelpersBundle\Form\DataTransformer\PageDataTransformer;
use Sonata\SonataHelpersBundle\Entity\PageManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class InternalExternalLinkType.
 *
 * @author Laurier Vincent <vincent.laurier@ekino.com>
 * @author Benoit de Jacobet <benoit.de-jacobet@ekino.com>
 */
class InternalExternalLinkType extends AbstractType
{
    /**
     * @var PageManagerInterface
     */
    private $pageManager;

    /**
     * @var ModelManagerInterface
     */
    private $modelManager;

    /**
     * @param PageManagerInterface  $pageManager
     * @param ModelManagerInterface $modelManager
     */
    public function __construct(
        PageManagerInterface $pageManager,
        ModelManagerInterface $modelManager
    ) {
        $this->pageManager = $pageManager;
        $this->modelManager = $modelManager;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setRequired('site');
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $choices = [
            'Choisissez le type de lien' => 'no_choice',
            'Page Interne' => 'page',
            'Lien externe' => 'link',
        ];
        if (isset($options['required']) && !$options['required']) {
            $choices['Pas de lien'] = 'none';
        }

        $builder->add('linkType', ChoiceFieldMaskType::class, [
            'choices' => $choices,
            'map' => [
                'none' => [],
                'page' => ['page', 'params'],
                'link' => ['link'],
            ],
            'placeholder' => false,
            'required' => false,
            'label' => 'Type de lien',
            'attr' => ['class' => 'LinkWidget_LinkTypeField'],
        ]);

        $builder->add('link', UrlType::class, [
            'label' => 'Lien externe',
            'required' => false,
            'attr' => [
                'class' => 'LinkWidget_ExternalLinkField',
                'placeholder' => 'https://',
            ],
        ]);

        $builder->add('page', ModelType::class, [
            'label' => 'Page interne',
            'property' => 'nameWithParams',
            'class' => Page::class,
            'model_manager' => $this->modelManager,
            'required' => true,
            'query' => $options['site'] ? $this->pageManager->getAvailablePages($options['site']) : null,
            'btn_add' => false,
            'attr' => ['class' => 'LinkWidget_PageNameField'],
        ])->addModelTransformer(new PageDataTransformer($this->pageManager, ['page']));

        $builder->add('params', HiddenType::class, ['attr' => ['class' => 'LinkWidget_PageParamsField', 'mapped' => false]]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sonata_type_internal_external_link_type';
    }
}
