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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SortActionAdminControllerTrait.
 *
 * Add action to sort list to admin controller
 *
 * @author Benoit de Jacobet <benoit.de-jacobet@ekino.com>
 */
trait SortActionAdminControllerTrait
{
    /**
     * @param Request|null $request
     *
     * @return Response
     */
    public function savePositionsAction(Request $request = null): Response
    {
        $this->admin->checkAccess('list');

        $idsByPositions = $request->request->get('idsByPositions');

        if (!$idsByPositions) {
            return $this->renderJson(['status' => 'error', 'message' => 'idsByPositions is null'], 500);
        }

        // Update positions
        $idsByPositions = json_decode($idsByPositions, true);
        $sortAction     = $this->get('admin.sort_action');
        $result         = $sortAction->sortEntities($this->admin->getClass(), $idsByPositions);

        if ('error' === $result['status']) {
            return $this->renderJson($result, 500);
        }

        return $this->renderJson($result);
    }
}
