/**
 * myStore Theme Custom Functionality
 *
 */
( function( $ ) {
    
    jQuery( document ).ready( function() {
        
        // Add button to sub-menu item to show nested pages / Only used on mobile
        $( '.main-navigation li.page_item_has_children, .main-navigation li.menu-item-has-children' ).prepend( '<span class="menu-dropdown-btn"><i class="fa fa-angle-down"></i></span>' );
        // Mobile nav button functionality
        $( '.menu-dropdown-btn' ).bind( 'click', function() {
            $(this).parent().toggleClass( 'open-page-item' );
        });
        
        // Show / Hide Search
        $('.menu-search .fa-search.search-btn').toggle(function(){
            $('body').addClass('show-site-search');
            $('.site-header .search-block').animate( { opacity: '1' }, 200 );
            $(".site-header .search-field").focus();
        },function(){
            $('.site-header .search-block').animate( { opacity: '0' }, 200 );
            $('body').removeClass('show-site-search');
        });
        
        // The menu button
        $('.header-menu-button').click(function(e){
            $('body').toggleClass('show-main-menu');
        });
        
        $('.main-menu-close').click(function(e){
            $('.header-menu-button').click();
        });
		
    });
    
    $(window).resize(function () {
        
        
        
    }).resize();
    
    $(window).load(function() {
        
        mystore_home_slider();
        
    });
    
    // Hide Search is user clicks anywhere else
    $(document).mouseup(function (e) {
        var container = $('.site-header .search-block');
        if (!container.is(e.target) && container.has(e.target).length === 0) {
            $('.site-header .search-block').animate( { opacity: '0' }, 200 );
            $('body').removeClass('show-site-search');
        }
    });
    
    function mystore_home_slider() {
        
        $(".home-slider").carouFredSel({
            responsive: true,
            circular: true,
            infinite: false,
            width: 1200,
            height: 'variable',
            items: {
                visible: 1,
                width: 1200,
                height: 'variable'
            },
            onCreate: function(items) {
                $(".home-slider-wrap").removeClass("home-slider-remove");
            },
            scroll: {
                fx: 'uncover-fade',
                duration: 450
            },
            auto: 5800,
            prev: ".home-slider-prev",
            next: ".home-slider-next"
        });
        
    }
    
} )( jQuery );