/**
 * Copyright © tons of limes <https://github.com/tons-of-limes>. All rights reserved.
 */

/**
 * @api
 */
define([
    'underscore'
], function (_) {
    'use strict';

    /**
     * Apply visible columns state to bookmarks component.
     *
     * @param {Object} bookmarks - Bookmarks component instance
     * @param {Array<String>} visibleColumns - Array of visible column names
     */
    function applyVisibleColumnsState(bookmarks, visibleColumns) {
        const registeredColumns = bookmarks.get('current.columns');

        if (!_.isObject(registeredColumns)) {
            return;
        }

        _.each(registeredColumns, function (item, name) {
            const isVisible = visibleColumns.find(column => column === name);

            bookmarks.set(`current.columns.${name}.visible`, isVisible);
        });
    }

    /**
     * Apply sorting state to bookmarks component.
     *
     * @param {Object} bookmarks - Bookmarks component instance
     * @param {Object} sorting - Sorting object
     */
    function applySortingState(bookmarks, sorting) {
        const registeredColumns = bookmarks.get('current.columns');

        if (!_.isObject(registeredColumns)) {
            return;
        }

        const {
            direction,
            field
        } = sorting;

        _.each(registeredColumns, function (item, name) {
            bookmarks.set(`current.columns.${name}.sorting`, false);
        });

        bookmarks.set(`current.columns.${field}.sorting`, direction);
    }

    /**
     * Apply filters state to bookmarks component.
     *
     * @param {Object} bookmarks - Bookmarks component instance
     * @param {Array<Object>} filters - Array of filter objects
     */
    function applyFiltersState(bookmarks, filters) {
        const applied = {
            placeholder: true,
        };
        let search = null;

        filters.forEach(function (item) {
            if (item.code === 'fulltext') {
                search = item.value;
            } else {
                applied[item.code] = item.value;
            }
        });

        bookmarks.set('current.filters.applied', applied);
        bookmarks.set('current.search.value', search);
    }

    /**
     * Apply the current state to the bookmarks component.
     *
     * @param {Object} bookmarksComponent - The bookmarks component instance
     * @param {filters: Array<Object>, columns: Array<String>, sorting: Object} state - The state object containing filters, columns, and sorting
     */
    return function (bookmarksComponent, state) {
        if (!bookmarksComponent) {
            throw new Error('Bookmarks component is required to apply current state.');
        }

        const {filters, columns, sorting} = state;

        if (_.isArray(filters)) {
            applyFiltersState(bookmarksComponent, filters);
        }

        if (_.isArray(columns)) {
            applyVisibleColumnsState(bookmarksComponent, columns);
        }

        if (_.isObject(sorting)) {
            applySortingState(bookmarksComponent, sorting);
        }
    };
});
