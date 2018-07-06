if ( undefined !== jQuery ) {
    // script dependent on jQuery
    jQuery( function( $ ) {

        var get_url = function( endpoint ) {
            return wc_cart_params.wc_ajax_url.toString().replace(
                '%%endpoint%%',
                endpoint
            );
        };

        //Cart selection handler
        function wjecf_cart_select_free_product() {
            var me = this;

            me.init = function() {
                $( document ).ajaxSuccess( me.handle_ajax_success );
                $( document ).on(
                    'change input',
                    '.wjecf-select-free-products :input',
                    me.input_changed );
            };

            me.update_cart = function() {
                //Update the cart form
                jQuery(document.body).trigger('wc_update_cart'); 
            };
            me.update_select_free_product = function() {
                $.ajax( {
                    url: get_url( 'wjecf_cart_select_free_product' ),
                    dataType: 'html',
                    success: function( response ) {
                        $( '.wjecf-fragment-cart-select-free-product' ).replaceWith( response );
                    }
                } );                             
            }

            /**
             * After an input has changed, enabled the update cart button.
             */
            me.input_changed = function( e ) {
                $( 'div.woocommerce > form input[name="update_cart"]' ).prop( 'disabled', false );
            } 

            return me;
        }
        //End Cart selection handler

        //Totalizer handler
        //A totalizer will sum up the values of all inputs that share the same 'data-wjecf-qty-totalizer' attribute
        //A (hidden) input with that name should exist 
        //The hidden input can have an 'wjecf-qty-max'-attribute to limit the sum to the given max value
        function wjecf_totalizer_handler() {
            var me = this;

            /**
             * Initializes the totalizer handler
             * @return void
             */
            me.init = function() {
                me.update_all_totalizers();
                $( document ).on(
                    'change input',
                    '*[data-wjecf-qty-totalizer]',
                    me.input_changed );                            
            }                        

             /**
              * After an input has changed, update the totalizer.
              * @param Event e 
              * @return void
              */
            me.input_changed    = function( e ) {
                me.update_totalizer( e.target.getAttribute('data-wjecf-qty-totalizer'), e.target );
            };

            /**
             * Update the values of all totalizers
             * @return void
             */
            me.update_all_totalizers = function() {
                var totalizer_ids = {};
                $( '*[data-wjecf-qty-totalizer]' ).each(function(){
                    var totalizer_id = this.getAttribute('data-wjecf-qty-totalizer')
                    totalizer_ids[totalizer_id] = totalizer_id;
                });
                for(var totalizer_id in totalizer_ids) {
                    me.update_totalizer( totalizer_id );
                }
            }

            /**
             * Update the totalizer with the given id.
             * If updated_input is given; the value will be limited to be <= max_value
             *
             * @param string totalizer_id 
             * @param object updated_input The updated DOM-element
             */
            me.update_totalizer = function( totalizer_id, updated_input = undefined ) {
                if ( undefined == totalizer_id ) return;

                var is_checkbox = function( element ) {
                    return element.type && element.type === 'checkbox';
                }

                var set_totalizer_value = function( element, value ) {
                    if ( element === undefined ) return;
                    if ( element.tagName.toLowerCase() === 'input' )
                        element.value = value;
                    else
                        element.textContent = value;
                }                            

                /**
                 * Get quantity from a DOM-element (input type="number", "checkbox" or "radio")
                 * @param object element 
                 * @return quantity
                 */
                var get_quantity = function( element ) {
                    if ( is_checkbox( element ) ) return element.checked ? 1 : 0;

                    // assume numeric input
                    return 1*$(element).val();
                }

                //Calculate total
                var total = 0;
                $( '*[data-wjecf-qty-totalizer="' + totalizer_id + '"]' ).each(function(){
                    total += get_quantity( this );
                });


                //Max value?
                var totalizer = $( '#' + totalizer_id );
                var max_quantity = totalizer.data('wjecf-qty-max');

                //Set max value for all inputs
                if ( undefined !== max_quantity ) {
                    //Limit updated_input to the max value
                    $( '*[data-wjecf-qty-totalizer="' + totalizer_id + '"]' ).each(function(){
                        var old_value = get_quantity( this );
                        //Max allowed amount for this input
                        var max_left = Math.max( 0, max_quantity - total  + get_quantity( this ) );

                        if ( this === updated_input && old_value > max_left) {
                            if ( is_checkbox( this ) ) {
                                this.checked &= max_left > 0; //uncheck if too many
                            } else {
                                $(this).val( Math.min( max_left, $(this).val() ) ); //limit the value
                            }
                            total += get_quantity( this ) - old_value;
                        }

                        // if ( ! is_checkbox( this ) ) {
                        //     $(this).attr({"max":max_left});
                        // }
                    });
                }

                set_totalizer_value( totalizer.get(0), total );
            }

            return me;
        }
        //End Totalizer handler

        // wc_cart_params is required to continue, ensure the object exists
        if ( typeof wc_cart_params !== 'undefined' ) {
            wjecf_cart_select_free_product().init();
        }

        wjecf_totalizer_handler().init();

    });
}
