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

use Doctrine\Inflector\Inflector;
use Doctrine\Inflector\WordInflector;
use Knp\Menu\FactoryInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
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
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class AdminTestCase.
 *
 * @author Sylvain Rascar <sylvain.rascar@ekino.com>
 */
abstract class AdminTestCase extends TestCase
{
    /**
     * @var MockObject
     */
    protected $modelManagerMock;

    /**
     * @var MockObject
     */
    protected $formContractorMock;

    /**
     * @var MockObject
     */
    protected $showBuilderMock;

    /**
     * @var MockObject
     */
    protected $listBuilderMock;

    /**
     * @var MockObject
     */
    protected $datagridBuilderMock;

    /**
     * @var MockObject
     */
    protected $translatorMock;

    /**
     * @var MockObject
     */
    protected $poolMock;

    /**
     * @var MockObject
     */
    protected $routeGeneratorMock;

    /**
     * @var MockObject
     */
    protected $validatorMock;

    /**
     * @var MockObject
     */
    protected $securityHandlerMock;

    /**
     * @var MockObject
     */
    protected $menuFactoryMock;

    /**
     * @var MockObject
     */
    protected $routeBuilderMock;

    /**
     * @var MockObject
     */
    protected $labelTranslatorStrategyMock;

    /**
     * Default dummy variables.
     *
     * @var string
     */
    protected $dummyAdminId    = 'sonata.admin';

    /**
     * @var string
     */
    protected $dummyController = 'SonataAdminBundle:CRUD';

    /**
     * Create mocks of all admin default services
     * and assign them to a property of the class.
     */
    protected function mockDefaultServices(AdminInterface $admin): void
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
     *
     * @param array<array-key,array> $defaultServices
     */
    private function applyDefaults(AdminInterface $admin, array $defaultServices): void
    {
        $inflector = new Inflector($this->createMock(WordInflector::class), $this->createMock(WordInflector::class));
        // Add services to the admin
        foreach ($defaultServices as $service) {
            $method = 'set'.$inflector->classify($service[0]);
            $admin->{$method}($this->{$service[1]});
        }
    }
}
