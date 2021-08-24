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

namespace Sonata\HelpersBundle\Form\Type;

use Sonata\Form\Type\ImmutableArrayType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ImmutableTabsType.
 *
 * @author Benoit de Jacobet <benoit.de-jacobet@ekino.com>
 */
class ImmutableTabsType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setAllowedTypes('tabs', 'string[]')->setRequired('tabs');
    }

    public function getParent(): string
    {
        return ImmutableArrayType::class;
    }

    /**
     * @param array<non-empty-array> $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $tabKeys = $options['keys'];

        // Tabs
        foreach ($options['tabs'] as $tabCode => $tab) {
            $builder->add($tabCode, ImmutableArrayType::class, [
                'keys'  => $tabKeys,
                'label' => false,
            ]);
        }
    }

    /**
     * @param FormInterface<array>   $form
     * @param array<non-empty-array> $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['tabs'] = $options['tabs'];
    }

    public function getName(): string
    {
        return $this->getBlockPrefix();
    }

    public function getBlockPrefix(): string
    {
        return 'sonata_immutable_tabs_type';
    }
}
