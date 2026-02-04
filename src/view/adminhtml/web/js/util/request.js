/**
 * Copyright © tons of limes <https://github.com/tons-of-limes>. All rights reserved.
 */

/**
 * @api
 */
define([
    'jquery',
    'underscore'
], function ($, _) {
    'use strict';

    const ERROR_CODE_UNKNOWN = 'UNKNOWN';

    /**
     * Build action error from response data.
     *
     * @param responseData
     * @returns {Error}
     */
    function buildActionError(responseData) {
        const code = responseData?.error?.code || ERROR_CODE_UNKNOWN;
        const message = responseData?.error?.message || 'Unknown error';

        const error = new Error(message);
        error.code = code;

        return error;
    }

    return {
        /**
         * Make post request.
         *
         * Handles action-level errors and re-throw as error.
         *
         * @param url
         * @param data
         * @returns {Promise<Object>}
         */
        post: async function(url, data) {
            const responseData = await $.post(url, data);

            if (!responseData?.success) {
                throw buildActionError(responseData);
            }

            return responseData.data;
        }
    };
});
