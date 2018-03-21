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

namespace Sonata\SonataHelpersBundle\Validator\Constraints;

use Sonata\PageBundle\Model\PageInterface;
use Sonata\SonataHelpersBundle\Form\DataTransformer\PageDataTransformer;
use Sonata\SonataHelpersBundle\Entity\PageManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class InternalExternalLinkValidator.
 *
 * @author  Benoit de Jacobet <benoit.de-jacobet@ekino.com>
 */
class InternalExternalLinkValidator extends ConstraintValidator
{
    /**
     * @var PageManagerInterface
     */
    private $pageManager;

    /**
     * @param PageManagerInterface $pageManager
     */
    public function __construct(PageManagerInterface $pageManager)
    {
        $this->pageManager = $pageManager;
    }

    /**
     * Validate InternalExternalLink data.
     *
     * @param mixed      $values
     * @param Constraint $constraint
     */
    public function validate($values, Constraint $constraint)
    {
        switch ($values['linkType']) {
            case 'none':
                break;
            case 'link':
                if (!$values['link']) {
                    $this->context->buildViolation($constraint->message)
                        ->setParameter('{{ error }}', 'Il faut saisir une url externe')
                        ->atPath('[link]')
                        ->addViolation();
                }
                break;
            case 'page':
                if (!$values['page']) {
                    $this->context->buildViolation($constraint->message)
                        ->setParameter('{{ error }}', 'Il faut choisir une page interne')
                        ->atPath('[page]')
                        ->addViolation();
                } else {
                    $pageDataTransformer = new PageDataTransformer($this->pageManager, ['page']);
                    $values = $pageDataTransformer->transform($values);

                    // Check page exists
                    if ($values['page'] instanceof PageInterface) {
                        $this->validatePageParams($values, $values['page'], $constraint);
                    } else {
                        $this->context->buildViolation($constraint->message)
                            ->setParameter('{{ error }}', 'Cette page interne n\'existe pas')
                            ->atPath('[page]')
                            ->addViolation();
                    }
                }
                break;
            default:
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ error }}', 'Il faut choisir un type de lien')
                    ->atPath('[linkType]')
                    ->addViolation();
                break;
        }
    }

    /**
     * Validate page params.
     *
     * @param array         $values
     * @param PageInterface $page
     * @param Constraint    $constraint
     */
    private function validatePageParams($values, PageInterface $page, Constraint $constraint)
    {
        if (false !== preg_match_all("/(\{\w+\})/", $page->getUrl(), $placeholders, PREG_SET_ORDER)) {
            foreach ($placeholders as $placeholder) {
                $placeholder = substr($placeholder[0], 1, -1); // Remove {} around placeholder name
                if (!$values['params'] || (isset($values['params'][$placeholder]) && null === $values['params'][$placeholder])) {
                    $this->context->buildViolation($constraint->message)
                        ->setParameter('{{ error }}', sprintf('Il faut saisir la valeur du paramètre "%s"', $placeholder))
                        ->addViolation();
                }
            }
        }
    }
}
