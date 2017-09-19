/**
 * Copyright © 2017 Kev WIS All rights reserved.
 *
 */
define(
    [
        'Kevwis_Atos/js/view/payment/method-renderer/abstract'
    ],
    function (Component) {
        'use strict';
        return Component.extend({
            defaults: {
                code: 'atos_standard',
                template: 'Kevwis_Atos/payment/method/standard'
            },

            /**
             *
             * @returns {boolean}
             */
            validate: function() {

                if ($('input[name="payment[cc_type]"]:checked').length < 1) {
                    return false;
                }

                return true;
            }
        });
    }
);
