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

namespace Sonata\HelpersBundle\TestHelpers\Admin;

use Doctrine\Common\Inflector\Inflector;
use Knp\Menu\FactoryInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Admin\Pool;
use Sonata\AdminBundle\Builder\DatagridBuilderInterface;
use Sonata\AdminBundle\Builder\FormContractorInterface;
use Sonata\AdminBundle\Builder\ListBuilderInterface;
use Sonata\AdminBundle\Builder\RouteBuilderInterface;
use Sonata\AdminBundle\Builder\ShowBuilderInterface;
use Sonata\AdminBundle\Route\RouteGeneratorInterface;
use Sonata\AdminBundle\Security\Handler\SecurityHandlerInterface;
use Sonata\AdminBundle\Translator\LabelTranslatorStrategyInterface;
use Sonata\DoctrineORMAdminBundle\Model\ModelManager;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class AdminTestCase.
 *
 * @author Sylvain Rascar <sylvain.rascar@ekino.com>
 */
abstract class AdminTestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ModelManager|MockObject
     */
    protected $modelManagerMock;

    /**
     * @var FormContractorInterface|MockObject
     */
    protected $formContractorMock;

    /**
     * @var ShowBuilderInterface|MockObject
     */
    protected $showBuilderMock;

    /**
     * @var ListBuilderInterface|MockObject
     */
    protected $listBuilderMock;

    /**
     * @var DatagridBuilderInterface|MockObject
     */
    protected $datagridBuilderMock;

    /**
     * @var TranslatorInterface|MockObject
     */
    protected $translatorMock;

    /**
     * @var Pool|MockObject
     */
    protected $poolMock;

    /**
     * @var RouteGeneratorInterface|MockObject
     */
    protected $routeGeneratorMock;

    /**
     * @var ValidatorInterface|MockObject
     */
    protected $validatorMock;

    /**
     * @var SecurityHandlerInterface|MockObject
     */
    protected $securityHandlerMock;

    /**
     * @var FactoryInterface|MockObject
     */
    protected $menuFactoryMock;

    /**
     * @var RouteBuilderInterface|MockObject
     */
    protected $routeBuilderMock;

    /**
     * @var LabelTranslatorStrategyInterface|MockObject
     */
    protected $labelTranslatorStrategyMock;

    /**
     * Default dummy variables.
     *
     * @var string
     */
    protected $dummyAdminId    = 'sonata.admin';

    protected $dummyController = 'SonataAdminBundle:CRUD';

    /**
     * Create mocks of all admin default services
     * and assign them to a property of the class.
     */
    protected function mockDefaultServices(AdminInterface $admin)
    {
        // Each element is composed of: original_service_key, propertyName, class
        $defaultServices = [
            ['model_manager', 'modelManagerMock', ModelManager::class],
            ['form_contractor', 'formContractorMock', FormContractorInterface::class],
            ['show_builder', 'showBuilderMock', ShowBuilderInterface::class],
            ['list_builder', 'listBuilderMock', ListBuilderInterface::class],
            ['datagrid_builder', 'datagridBuilderMock', DatagridBuilderInterface::class],
            ['translator', 'translatorMock', TranslatorInterface::class],
            ['configuration_pool', 'poolMock', Pool::class],
            ['route_generator', 'routeGeneratorMock', RouteGeneratorInterface::class],
            ['validator', 'validatorMock', ValidatorInterface::class],
            ['security_handler', 'securityHandlerMock', SecurityHandlerInterface::class],
            ['menu_factory', 'menuFactoryMock', FactoryInterface::class],
            ['route_builder', 'routeBuilderMock', RouteBuilderInterface::class],
            ['label_translator_strategy', 'labelTranslatorStrategyMock', LabelTranslatorStrategyInterface::class],
        ];

        // Generate all mocks
        foreach ($defaultServices as $service) {
            $this->{$service[1]} = $this->createMock($service[2]);
        }

        $this->applyDefaults($admin, $defaultServices);
    }

    /**
     * Use this function to instantiate all default services as mocks in the Admin.
     */
    private function applyDefaults(AdminInterface $admin, array $defaultServices)
    {
        // Add services to the admin
        foreach ($defaultServices as $service) {
            $method = 'set'.Inflector::classify($service[0]);
            $admin->{$method}($this->{$service[1]});
        }
    }
}
