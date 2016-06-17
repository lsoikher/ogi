/**
 * myStore Custom JS Functionality
 *
 */
( function( $ ) {
    
    jQuery( document ).ready( function() {
        
        // Handle clicking the purchase button
        $( 'a#upgrade-purchase-link' ).click( function(e) {
            e.preventDefault();
            window.open( $(this).attr( 'href' ), '_blank', 'width=960,height=800,resizeable,scrollbars' );
            $( '.upgrade-order-number-info-form' ).slideDown();
            $( 'html, body' ).animate( {'scrollTop':0} );
            return false;
        });
        
        // Show/Hide the order number form
        $( '#upgrade-has-order-number' ).click( function () {
            $( '.upgrade-order-number-info-form' ).slideToggle();
            return false;
        } );
        
        // Add simple js validation for the Order Number input
        $( 'input.upgrade-submit' ).click( function (e) {
            var is_valid = true;
            var order_number = $( 'input.upgrade-text' ).val();
            
            if ( order_number == '' ) {
                
                is_valid = false;
                $( 'input.upgrade-text' ).addClass( 'upgrade-error' );
                
            }
            
            if ( is_valid == false )
                e.preventDefault();
        });
        
    });
    
    $(window).resize(function () {
        
        
        
    }).resize();
    
    $(window).load(function() {
        
        mystore_upgrade_ratings_slider();
        
    });
    
    // Upgrade Ratings Slider
    function mystore_upgrade_ratings_slider() {
        $( '.upgrade-rating-slider' ).carouFredSel({
            responsive: true,
            circular: true,
            infinite: false,
            width: 280,
            height: 'variable',
            items: {
                visible: 1,
                start: 'random',
                width: 280,
                height: 'variable'
            },
            onCreate: function(items) {
                $( '.upgrade-rating-slider-wrap' ).removeClass( 'upgrade-rating-slider-wrap-remove' );
            },
            scroll: {
                fx: 'crossfade',
                duration: 450
            },
            auto: 10000,
            prev: '.upgrade-rating-slider-prev',
            next: '.upgrade-rating-slider-next'
        });
    }
    
} )( jQuery );