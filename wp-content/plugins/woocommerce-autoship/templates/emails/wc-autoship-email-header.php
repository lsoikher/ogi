<?php
/**
 * Email Header
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/email-header.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	https://docs.woothemes.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates/Emails
 * @version 2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
// Get style variables
$bg              = get_option( 'woocommerce_email_background_color' );
$body            = get_option( 'woocommerce_email_body_background_color' );
$base            = get_option( 'woocommerce_email_base_color' );
$base_text       = wc_light_or_dark( $base, '#202020', '#ffffff' );
$text            = get_option( 'woocommerce_email_text_color' );

$bg_darker_10    = wc_hex_darker( $bg, 10 );
$body_darker_10  = wc_hex_darker( $body, 10 );
$base_lighter_20 = wc_hex_lighter( $base, 20 );
$base_lighter_40 = wc_hex_lighter( $base, 40 );
$text_lighter_20 = wc_hex_lighter( $text, 20 );

?>
<!DOCTYPE html>
<html dir="<?php echo is_rtl() ? 'rtl' : 'ltr'?>">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo( 'charset' ); ?>" />
        <title><?php echo get_bloginfo( 'name', 'display' ); ?></title>
	</head>
    <body <?php echo is_rtl() ? 'rightmargin' : 'leftmargin'; ?>="0" marginwidth="0" topmargin="0" marginheight="0" offset="0">
    	<div id="wrapper" style="font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;background-color: <?php echo esc_attr( $bg ); ?>; margin: 0; padding: 70px 0 70px 0; -webkit-text-size-adjust: none !important; width: 100%; color: <?php echo esc_attr( $text ); ?>;" dir="<?php echo is_rtl() ? 'rtl' : 'ltr'?>">
        	<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
            	<tr>
                	<td align="center" valign="top" style="color: <?php echo esc_attr( $text_lighter_20 ); ?>; <?php echo esc_attr( $body_darker_10 ); ?>;">
						<div id="template_header_image">
	                		<?php
	                			if ( $img = get_option( 'woocommerce_email_header_image' ) ) {
	                				echo '<p style="margin-top:0;"><img style="max-width: 600px;" src="' . esc_url( $img ) . '" alt="' . get_bloginfo( 'name', 'display' ) . '" /></p>';
	                			}
	                		?>
						</div>
                    	<table border="0" cellpadding="0" cellspacing="0" width="600" id="template_container" style="box-shadow: 0 1px 4px rgba(0,0,0,0.1) !important; background-color: <?php echo esc_attr( $body ); ?>; border: 1px solid <?php echo esc_attr( $bg_darker_10 ); ?>; border-radius: 3px !important;">
                        	<tr>
                            	<td align="center" valign="top" style="color: <?php echo esc_attr( $text_lighter_20 ); ?>; border: 1px solid <?php echo esc_attr( $body_darker_10 ); ?>;">
                                    <!-- Header -->
                                	<table border="0" cellpadding="0" cellspacing="0" width="600" id="template_header" style="background-color: <?php echo esc_attr( $base ); ?>; border-radius: 3px 3px 0 0 !important; color: <?php echo esc_attr( $base_text ); ?>; border-bottom: 0; font-weight: bold; vertical-align: middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;">
                                        <tr>
                                            <td id="header_wrapper" style="padding: 36px 48px; display: block;">
                                            	<h1 style="color: <?php echo esc_attr( $base_text ); ?>;"><?php echo $email_heading; ?></h1>
                                            </td>
                                        </tr>
                                    </table>
                                    <!-- End Header -->
                                </td>
                            </tr>
                        	<tr>
                            	<td align="center" valign="top" style="color: <?php echo esc_attr( $text_lighter_20 ); ?>; border-bottom: 1px solid <?php echo esc_attr( $body_darker_10 ); ?>;">
                                    <!-- Body -->
                                	<table border="0" cellpadding="0" cellspacing="0" width="600" id="template_body">
                                    	<tr>
                                            <td valign="top" id="body_content"  style="background-color: <?php echo esc_attr( $body ); ?>; color: <?php echo esc_attr( $text_lighter_20 ); ?>;">
                                                <!-- Content -->
                                                <table border="0" cellpadding="20" cellspacing="0" width="100%">
                                                    <tr>
                                                        <td valign="top" style="padding: 48px;" style="color: <?php echo esc_attr( $text_lighter_20 ); ?>; border: 1px solid <?php echo esc_attr( $body_darker_10 ); ?>;">
                                                            <div id="body_content_inner" style="color: <?php echo esc_attr( $text_lighter_20 ); ?>; font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif; font-size: 14px; line-height: 150%; text-align: <?php echo is_rtl() ? 'right' : 'left'; ?>;">
															<!--END OF HEADER TEMPLATE -->
