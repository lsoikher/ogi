<footer id="colophon" class="site-footer site-footer-standard" role="contentinfo">
	
	<div class="site-footer-widgets">
        <div class="site-container">
        	<?php if ( is_active_sidebar( 'mystore-site-footer-standard' ) ) : ?>
	            <ul>
	                <?php dynamic_sidebar( 'mystore-site-footer-standard' ); ?>
	            </ul>
            <?php else : ?>
	        	<div class="site-footer-no-widgets">
	        		<?php _e( 'Add your own widgets here', 'mystore' ); ?>
	        	</div>
	    	<?php endif; ?>
            
            <?php printf( __( '<div class="clearboth"></div></div></div><div class="site-footer-bottom-bar"><div class="site-container"><div class="site-footer-bottom-bar-left">Theme: %1$s by %2$s', 'mystore' ), 'myStore', '<a href="http://www.kairaweb.com/">Kaira</a></div><div class="site-footer-bottom-bar-right">' ); ?>
            
	        </div>
	        
	    </div>
		
        <div class="clearboth"></div>
	</div>
	
</footer>