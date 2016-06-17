/**
 * myStore Customizer Custom Functionality
 *
 */
( function( $ ) {
    
    $( window ).load( function() {
        
        //Show / Hide Color selector for header setting
        var the_header_select_value = $( '#customize-control-mystore-header-layout select' ).val();
        mystore_customizer_header_check( the_header_select_value );
        
        $( '#customize-control-mystore-header-layout select' ).on( 'change', function() {
            var header_select_value = $( this ).val();
            mystore_customizer_header_check( header_select_value );
        } );
        
        function mystore_customizer_header_check( header_select_value ) {
            if ( header_select_value == 'mystore-header-layout-standard' ) {
                $( '#accordion-section-mystore-header-section #customize-control-mystore-header-bg-color' ).show();
            } else {
                $( '#accordion-section-mystore-header-section #customize-control-mystore-header-bg-color' ).hide();
            }
        }
        
        //Show / Hide Color selector for slider setting
        var the_slider_select_value = $( '#customize-control-mystore-slider-type select' ).val();
        mystore_customizer_slider_check( the_slider_select_value );
        
        $( '#customize-control-mystore-slider-type select' ).on( 'change', function() {
            var slider_select_value = $( this ).val();
            mystore_customizer_slider_check( slider_select_value );
        } );
        
        function mystore_customizer_slider_check( slider_select_value ) {
            if ( slider_select_value == 'mystore-slider-default' ) {
                $( '#accordion-section-mystore-slider-section #customize-control-mystore-meta-slider-shortcode' ).hide();
                $( '#accordion-section-mystore-slider-section #customize-control-mystore-slider-cats' ).show();
            } else if ( slider_select_value == 'mystore-meta-slider' ) {
                $( '#accordion-section-mystore-slider-section #customize-control-mystore-slider-cats' ).hide();
                $( '#accordion-section-mystore-slider-section #customize-control-mystore-meta-slider-shortcode' ).show();
            } else {
                $( '#accordion-section-mystore-slider-section #customize-control-mystore-slider-cats' ).hide();
                $( '#accordion-section-mystore-slider-section #customize-control-mystore-meta-slider-shortcode' ).hide();
            }
        }
        
        //Show / Hide Color selector for blocks layout setting
        var the_body_blocks_select_value = $( '#customize-control-mystore-page-styling select' ).val();
        mystore_customizer_page_style_check( the_body_blocks_select_value );
        
        $( '#customize-control-mystore-page-styling select' ).on( 'change', function() {
            var body_style_select_value = $( this ).val();
            mystore_customizer_page_style_check( body_style_select_value );
        } );
        
        function mystore_customizer_page_style_check( body_style_select_value ) {
            if ( body_style_select_value == 'mystore-page-styling-flat' ) {
                $( '#accordion-section-mystore-site-layout-section #customize-control-mystore-page-styling-color' ).hide();
            } else {
                $( '#accordion-section-mystore-site-layout-section #customize-control-mystore-page-styling-color' ).show();
            }
        }
        
    } );
    
} )( jQuery );