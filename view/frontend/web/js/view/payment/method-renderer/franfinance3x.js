/**
 * Copyright © 2017 Kev WIS All rights reserved.
 *
 */
define(
    [
        'Kevwis_Atos/js/view/payment/method-renderer/abstract'
    ],
    function (Component, quote) {
        'use strict';
        return Component.extend({
            defaults: {
                code: 'atos_franfinance3x',
                template: 'Kevwis_Atos/payment/method/franfinance3x'
            }
        });
    }
);
