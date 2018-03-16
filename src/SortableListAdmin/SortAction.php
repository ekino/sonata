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

namespace Sonata\SonataHelpersBundle\SortableListAdmin;

use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class SortAction.
 *
 * To sort entities of object type in database by the field `position`
 *
 * @author  Benoit de Jacobet <benoit.de-jacobet@ekino.com>
 */
class SortAction
{
    // Sortable class names and their database table names
    const SORTABLE_CLASS = [
        // @Todo : update with your entity
        //YourEntity::class => [
        //    'table' => 'your_entity_table',
        //    'field' => 'position',
        //],
    ];

    /**
     * @var RegistryInterface
     */
    private $doctrine;

    /**
     * ObjectPositions constructor.
     *
     * @param RegistryInterface $doctrine
     */
    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * Sort entities by update their `position` in database.
     *
     * Generate and execute a query to update all positions by id
     * "UPDATE your_entity_table SET position = (
     *      case  when id = 1 then 0
     *            when id = 2 then 1
     *            when id = 3 then 2
     *      end
     * ) WHERE id in ( 1, 2, 3)"
     *
     * @param string $className
     * @param array  $idsByPositions
     *
     * @return array
     */
    public function sortEntities($className, $idsByPositions): array
    {
        if (!$this->isSortableClass($className)) {
            return ['status' => 'error', 'message' => sprintf('%s is not a sortable class', $className)];
        }

        $queryAndParams = $this->generateUpdateQuery($className, $idsByPositions);

        try {
            $connection = $this->doctrine->getConnection();
            $connection->executeQuery($queryAndParams['query'], $queryAndParams['params'])->execute();
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }

        return ['status' => 'success'];
    }

    /**
     * Generate update query and params.
     *
     * @param string $className
     * @param array  $idsByPositions
     *
     * @return array
     */
    public function generateUpdateQuery($className, $idsByPositions): array
    {
        $queryStart = sprintf(
            'UPDATE %s SET %s = (case ',
            self::SORTABLE_CLASS[$className]['table'],
            self::SORTABLE_CLASS[$className]['field']
        );
        $queryEnd = '';

        $casesParams = [];
        $whereParams = [];
        foreach ($idsByPositions as $position => $id) {
            $queryStart .= ' when id = ? then ? ';
            if (!$queryEnd) {
                $queryEnd = ' end) WHERE id in ( ?';
            } else {
                $queryEnd .= ', ?';
            }
            $casesParams[] = (int) $id;
            $casesParams[] = $position;
            $whereParams[] = (int) $id;
        }
        $query = sprintf('%s %s)', $queryStart, $queryEnd);

        $params = array_merge($casesParams, $whereParams);

        return [
           'query' => $query,
           'params' => $params,
        ];
    }

    /**
     * Check if a class name is sortable.
     *
     * @param string $className
     *
     * @return bool
     */
    private function isSortableClass($className): bool
    {
        return array_key_exists($className, self::SORTABLE_CLASS);
    }
}
