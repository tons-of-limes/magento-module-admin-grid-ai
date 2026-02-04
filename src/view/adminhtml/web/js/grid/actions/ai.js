/**
 * Copyright © tons of limes <https://github.com/tons-of-limes>. All rights reserved.
 */

/**
 * @api
 */
define([
    'uiElement',
], function (
    Element,
) {
    'use strict';

    return Element.extend({
        defaults: {
            template: 'TonsOfLimes_AdminGridAI/grid/actions/ai',
            isActive: false,
            tracks: {
                isActive: true,
            },
            links: {
                isActive: '${ $.parentName }.search-by-ai:isActive',
            },
        },

        /**
         * Toggle active state.
         */
        toggle: function () {
            this.isActive = !this.isActive;
        }
    });
});
