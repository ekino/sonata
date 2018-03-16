/**
 * This file is part of the ekino/sonata project.
 *
 * (c) Ekino
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import SortableTable from './SortableTable';

/**
 * Initialize SortableList behavior on all suitable children of the given node
 *
 * @param {Element} node
 */
export default function initializeSortableList (node) {
    if (!node.querySelectorAll) {
        return;
    }

    const listNodes = node.querySelectorAll('.sonata-ba-sortable-list');
    listNodes.forEach((listNode) => {
        const sortableTable = new SortableTable(listNode);
        sortableTable.initialize();
    });
};
