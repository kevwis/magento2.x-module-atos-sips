/**
 * Copyright © 2017 Kev WIS All rights reserved.
 *
 */
/*browser:true*/
/*global define*/
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'atos_standard',
                component: 'Kevwis_Atos/js/view/payment/method-renderer/standard'
            },
            {
                type: 'atos_several',
                component: 'Kevwis_Atos/js/view/payment/method-renderer/several'
            },
            {
                type: 'atos_euro',
                component: 'Kevwis_Atos/js/view/payment/method-renderer/euro'
            },
            {
                type: 'atos_aurore',
                component: 'Kevwis_Atos/js/view/payment/method-renderer/aurore'
            },
            {
                type: 'atos_cofidis3x',
                component: 'Kevwis_Atos/js/view/payment/method-renderer/cofidis3x'
            },
            {
                type: 'atos_franfinance3x',
                component: 'Kevwis_Atos/js/view/payment/method-renderer/franfinance3x'
            }
        );
        /** Add view logic here if needed */
        return Component.extend({});
    }
);