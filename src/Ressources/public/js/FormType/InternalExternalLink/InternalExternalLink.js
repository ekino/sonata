/*
 * This file is part of the Sonata for Ekino project.
 *
 * (c) 2018 - Ekino
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

import InternalExternalLinkType from './InternalExternalLinkType';

/**
 * Initialize link behavior on all suitable children of the given node
 *
 * @param {Element} node
 */
export default function (node) {
    if (!node.querySelectorAll) {
        return;
    }

    const links = node.querySelectorAll('.LinkWidget');
    links.forEach((link) => {
        const internalExternalLinkType = new InternalExternalLinkType(link);
        internalExternalLinkType.initialize();
    });
}
