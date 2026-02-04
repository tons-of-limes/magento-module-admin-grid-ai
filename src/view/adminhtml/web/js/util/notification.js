/**
 * Copyright © tons of limes <https://github.com/tons-of-limes>. All rights reserved.
 */

/**
 * @api
 */
define([
    'jquery',
], function ($) {
    'use strict';

    /**
     * Add message to notification area.
     *
     * @param {String} message
     * @param {Boolean} isError
     */
    function addMessage(message, isError) {
        $('body').notification('add', {
            error: isError,
            message: message,

            /**
             * Insert method.
             *
             * @param {String} msg
             */
            insertMethod: function (msg) {
                var $wrapper = $('<div></div>').addClass('messages').html(msg);

                $('.page-main-actions', '.page-content').after($wrapper);
                $('html, body').animate({
                    scrollTop: $('.page-main-actions', '.page-content').offset().top
                });
            }
        });
    }

    /**
     * Clear all messages from notification area.
     */
    function clearMessages() {
        $('body').notification('clear');
    }

    return {
        /**
         * Add error message.
         *
         * @param {String} message
         */
        error: function (message) {
            clearMessages();
            addMessage(message, true);
        },

        /**
         * Add info message.
         *
         * @param {String} message
         */
        info: function (message) {
            clearMessages();
            addMessage(message, true);
        }
    };
});
