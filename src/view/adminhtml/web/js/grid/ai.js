/**
 * Copyright © tons of limes <https://github.com/tons-of-limes>. All rights reserved.
 */

/**
 * @api
 */
define([
    'mage/translate',
    'uiElement',
    'jquery',
    './../bookmark/applyCurrentState',
    './../util/request',
    './../util/notification',
], function (
    $t,
    Element,
    $,
    applyCurrentState,
    requestUtil,
    notificationUtil
) {
    'use strict';

    return Element.extend({
        defaults: {
            template: 'TonsOfLimes_AdminGridAI/grid/ai',
            placeholder: $t('Search by AI'),
            label: $t('AI'),
            value: '',
            buildStateUrlByQuery: '',
            isActive: false,
            editMode: true,
            focused: false,
            tracks: {
                isActive: true,
                editMode: true,
                value: true,
                inputValue: true
            },
            imports: {
                inputValue: 'value'
            },
            listens: {
                isActive: 'onActiveChange',
            },
            modules: {
                columnsComponent: 'ns = ${ $.ns }, componentType = columns',
                bookmarksComponent: 'ns = ${ $.ns }, componentType = bookmark',
            },
        },

        /**
         * @inheritDoc
         */
        initObservable: function () {
            this._super()
                .observe(['focused']);

            return this;
        },

        /**
         * Check is feature available.
         *
         * @returns {Boolean}
         */
        isFeatureAvailable: function () {
            return this.columnsComponent() && this.bookmarksComponent();
        },

        /**
         * Enable edit mode.
         */
        edit: function () {
            this.setEditMode(true);
        },

        /**
         * Switch off edit mode.
         */
        setEditMode: function (isEditMode) {
            this.editMode = isEditMode;
        },

        /**
         * On activation handler.
         *
         * @param {Boolean} isActive
         */
        onActiveChange: function (isActive) {
            if (!isActive) {
                return;
            }

            this.focused(true);
        },

        /**
         * Clears search.
         *
         * @returns {Search} Chainable.
         */
        clear: function () {
            this.value = '';

            return this;
        },

        /**
         * Click To ScrollTop.
         */
        scrollTo: function ($data) {
            $('html, body').animate({
                scrollTop: 0
            }, 'slow', function () {
                $data.focused = false;
                $data.focused = true;
            });
        },

        /**
         * Resets input value to the last applied state.
         *
         * @returns {Search} Chainable.
         */
        cancel: function () {
            this.inputValue = this.value;

            return this;
        },

        /**
         * Applies search query.
         *
         * @param {String} [value=inputValue]
         * @returns Promise<void>
         */
        apply: async function (value) {
            value = value || this.inputValue;
            this.value = this.inputValue = value.trim();
            this.setEditMode(false);

            await this.applyAIQuery(this.value);
        },

        /**
         * Apply AI query to the grid.
         *
         * @param {String} query
         * @returns {Promise<void>}
         */
        applyAIQuery: async function (query) {
            const columns = this.columnsComponent();
            const bookmarks = this.bookmarksComponent();

            if (!(columns && bookmarks)) {
                return;
            }

            columns.showLoader();

            try {
                const state = await this.buildStateByQuery(query);

                applyCurrentState(bookmarks, state);
            } catch (e) {
                const errorMessage = $t('Error on search by AI. %1').replace('%1', e.message);
                notificationUtil.error(errorMessage);
            } finally {
                columns.hideLoader();
            }
        },

        /**
         * Build state by query service.
         *
         * @param {String} query
         * @returns {Promise<{sorting}|{columns}|{filters}|Object>}
         */
        buildStateByQuery: async function (query) {
            const data = await requestUtil.post(this.buildStateUrlByQuery, {
                query
            });

            // result validation
            if (_.isUndefined(data?.filters) || _.isUndefined(data?.columns) || _.isUndefined(data?.sorting)) {
                throw new Error('Invalid response data');
            }

            return data;
        }
    });
});
