<?php
/* @var $list_table WC_Autoship_Admin_LogListTable */
?>

<h2>Autoship Log</h2>

<?php wc_autoship_print_messages(); ?>

<form method="get" class="wc-autoship-search-form">
	<input type="hidden" name="page" class="wc-autoship-admin-page" value="<?php echo esc_attr( $_REQUEST['page'] ); ?>" />
	<?php echo $list_table->search_box( 'Search log', 'wc-autoship-admin-search' ); ?>
</form>

<p>Manage autoship log.</p>

<?php 
$export_url = add_query_arg( array(
	'action' => 'export_autoship_log',
	'per_page' => '999999',
	's' => isset( $_REQUEST['s'] ) ? $_REQUEST['s'] : '',
	'orderby' => isset( $_REQUEST['orderby'] ) ? $_REQUEST['orderby'] : '',
	'order' => isset( $_REQUEST['order'] ) ? $_REQUEST['order'] : ''
), admin_url( 'admin-ajax.php' ) );
?>
<p><a href="<?php echo esc_attr( $export_url ); ?>" 
	target="_blank" class="button">Export Log Results</a></p>

<form id="log-filter" method="post" class="wc-autoship-admin-form">
	<input type="hidden" name="page" class="wc-autoship-admin-page" value="<?php echo esc_attr( $_REQUEST['page'] ); ?>" />
	<?php $list_table->display(); ?>
</form>