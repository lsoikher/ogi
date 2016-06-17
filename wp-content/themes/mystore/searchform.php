<?php
/**
 * The template for displaying search forms in myStore
 *
 */
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label>
		<input type="search" class="search-field" placeholder="<?php echo esc_attr_x( 'Search &amp; hit enter&hellip;', 'placeholder', 'mystore' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" name="s" title="<?php _ex( 'Search for:', 'label', 'mystore' ); ?>" />
	</label>
	<input type="submit" class="search-submit" value="<?php echo esc_attr_x( '&nbsp;', 'submit button', 'mystore' ); ?>" />
</form>