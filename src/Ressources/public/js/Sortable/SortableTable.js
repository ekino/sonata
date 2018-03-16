/**
 * This file is part of the ekino/sonata project.
 *
 * (c) Ekino
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import { Sortable } from '@shopify/draggable';
import Request      from 'superagent';
import Logger       from './../Utils/Logger';

const initializedClass = 'sortable-table-initialized';
const sortableClass    = 'sortable-table';

class SortableTable {
    /**
     * @param {HTMLElement} element
     */
    constructor (element) {
        this.element          = element;
        this.idsByPositions   = [];
        this.urlSavePositions = element.dataset.urlSavePositions;
    }

    initialize () {
        // Ensure this element has not yet been initialized
        if (this.element.classList.contains(initializedClass)) {
            return;
        }

        this.element.classList.add(initializedClass, sortableClass);

        this.element.querySelectorAll('.sonata-ba-sortable-row').forEach((row) => {
            this.idsByPositions.push(row.dataset.id);
        });

        const sortable = new Sortable(this.element, {
            draggable: 'tr',
            mirror: {
                constrainDimensions: true,
            },
        });

        Logger.groupCollapsed('Drag&drop table element');
        Logger.debug(this.element);
        Logger.debug(this.idsByPositions);
        Logger.groupEnd();

        sortable.on('sortable:stop', this.sortableStopHandler.bind(this));
    }

    /**
     *
     * @param sortableStopEvent
     */
    sortableStopHandler (sortableStopEvent) {
        const sourceId = sortableStopEvent.data.dragEvent.data.source.dataset.id;
        const oldIndex = sortableStopEvent.data.oldIndex;
        const newIndex = sortableStopEvent.data.newIndex;

        if (oldIndex !== newIndex) {
            // Update position of each row between oldIndex and newIndex
            const diffIndex = newIndex - oldIndex;
            if (diffIndex > 0) {
                for (let i = oldIndex; i < newIndex; i++) {
                    this.idsByPositions[i] = this.idsByPositions[i + 1];
                }
            } else {
                for (let i = oldIndex; i > newIndex; i--) {
                    this.idsByPositions[i] = this.idsByPositions[i - 1];
                }
            }
            this.idsByPositions[newIndex] = sourceId;

            // Ajax call to save positions
            Request.post(this.urlSavePositions)
            // .set('Content-Type', 'application/json')
                .type('form')
                .send({ idsByPositions: JSON.stringify(this.idsByPositions) })
                .then((reponse) => {
                    Logger.debug('Drag&drop ajax call success');
                })
                .catch((error) => {
                    Logger.error(`Drag&drop ajax call error : ${error.message}`);
                    Logger.error(error.response);
                })
            ;

            Logger.groupCollapsed('Drag&drop draggable fin');
            Logger.debug(`source id : ${sourceId}`);
            Logger.debug(`old index : ${oldIndex}`);
            Logger.debug(`new index : ${newIndex}`);
            Logger.debug(this.idsByPositions);
            Logger.groupEnd();
        }
    }
}

export default SortableTable;
