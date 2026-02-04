/**
 * Copyright © tons of limes <https://github.com/tons-of-limes>. All rights reserved.
 */

define([
    'underscore'
], function (_) {
    'use strict';

    /**
     * Get default sort order.
     *
     * As position in array * 100 to have ability to insert items between.
     */
    function getDefaultSortOrder(component, components) {
        const position = components.indexOf(component);

        return position * 100;
    }

    /**
     * Get sort order of component inside components list.
     */
    function getSortOrder(component, components) {
        if (!_.isNumber(component.regionSortOrder)) {
            return getDefaultSortOrder(component, components);
        }

        return component.regionSortOrder;
    }

    /**
     * Sort components list.
     */
    function sortComponents(components) {
        return _.sortBy(components, function (component) {
            return getSortOrder(component, components);
        });
    }

    /**
     * Mixin uiCollection `getRegion` method with sort strategy.
     *
     * Use `regionSortOrder` property for component to sort items inside region.
     */
    return function (target) {
        return target.extend({
            getRegion: function (name) {
                const components = this._super(name);
                const sortedComponents = sortComponents(components());

                // update initial value
                components(sortedComponents);

                return components;
            }
        });
    };
});
