/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
define(
    [
        'jquery',
        'Klarna_Kp/js/model/config'
    ],
    function ($, config) {
        'use strict';

        return function () {
            $.mage.redirect(config.redirectUrl);
        };
    }
);
