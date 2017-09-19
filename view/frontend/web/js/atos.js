/**
 * Copyright © 2017 Kev WIS All rights reserved.
 *
 */
define([
    'jquery',
    'jquery/ui'
], function($) {
    $.widget('atos.redirect', {
        _create: function() {

            this.element.on('click', function(e){

            });

            if (this.options !== undefined) {
                if (this.options.data !== undefined) {
                    if (this.options.data.cc_type !== undefined) {

                        if ($( "input[name='" + this.options.data.cc_type + "']", this.element).length > 0) {
                            $( "input[name='" + this.options.data.cc_type + "']", this.element).click();
                        }
                    }
                }
            }

        }
    });

    return $.soon.atos;
});