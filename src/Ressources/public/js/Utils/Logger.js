/*
 * This file is part of the Sonata for Ekino project.
 *
 * (c) 2018 - Ekino
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

const useLogger = window.location.pathname.match(/app_dev.php/g);

export default {
    log (message, level = 'info') {
        if (!useLogger) {
            return;
        }

        if (level === 'error') {
            console.error(message);
        } else if (level === 'warn') {
            console.warn(message);
        } else if (level === 'info') {
            console.info(message);
        } else if (level === 'group') {
            console.group(message);
        } else if (level === 'groupCollapsed') {
            console.groupCollapsed(message);
        } else if (level === 'groupEnd') {
            console.groupEnd();
        } else {
            console.log(message);
        }
    },

    debug (message) {
        this.log(message, 'debug');
    },

    info (message) {
        this.log(message, 'info');
    },

    warn (message) {
        this.log(message, 'warn');
    },

    error (message) {
        this.log(message, 'error');
    },

    group (name) {
        this.log(name, 'group');
    },

    groupCollapsed (name) {
        this.log(name, 'groupCollapsed');
    },

    groupEnd () {
        this.log('', 'groupEnd');
    },
}
