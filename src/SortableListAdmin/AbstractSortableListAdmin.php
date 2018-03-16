<?php

/*
 * This file is part of the ekino/sonata project.
 *
 * (c) Ekino
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\HelpersBundle\SortableListAdmin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class AbstractSortableListAdmin.
 *
 * @author  Benoit de Jacobet <benoit.de-jacobet@ekino.com>
 */
abstract class AbstractSortableListAdmin extends AbstractAdmin
{
    // Sortable list can be only with one page
    protected $maxPerPage     = 500;
    protected $perPageOptions = [500];

    protected $datagridValues = [
        '_page'       => 1,
        '_sort_order' => 'ASC',
        '_sort_by'    => 'position',
    ];

    /**
     * @var RegistryInterface
     */
    protected $doctrine;

    /**
     * Set Registry.
     *
     * @param RegistryInterface $doctrine
     */
    public function setRegistry(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplate($name)
    {
        switch ($name) {
            // @Todo : update twig path with your bundle
            //case 'list':
            //    return 'YourBundle:CRUD:list_sortable.html.twig';
            //case 'outer_sortable_list_rows_list':
            //    return 'YourBundle:CRUD:list_outer_rows_sortable_list.html.twig';
            default:
                return parent::getTemplate($name);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function prePersist($object)
    {
        parent::prePersist($object);

        // Set position
        $em         = $this->doctrine->getManager();
        $connection = $this->doctrine->getConnection();
        $query      = 'SELECT MAX(position) as position FROM :tableName;';
        $stmt       = $connection->executeQuery($query, ['tableName' => $em->getClassMetadata($this->getClass())->getTableName()]);
        $result     = $stmt->fetch();

        $object->setPosition($result['position'] + 1);

        return [];
    }

    /**
     * {@inheritdoc}
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);

        $collection->add('save_positions');
    }
}
