<?php
/**
 * Plugin Name: Gator Forms
 * Plugin URI: https://gatorforms.com
 * Description: Gator Forms: the WordPress Contact Form for sharp people.
 * Version: 2.5.0
 * Text Domain: pwebcontact
 * Author: Gator Forms
 * Author URI: https://gatorforms.com
 * License: GNU/GPL http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access
function_exists('add_action') or die;

// Do not use any PHP 5.3+ syntax in this file

function pwebcontact_upgrader_pre_download($reply = false, $package = null, $WP_Upgrader = null) {

    if (strpos($package, 'pwebcontact') !== false) {
        $data = get_plugin_data(dirname(__FILE__).'/pwebcontact.php', false, false);
        if (preg_match('/\s+PRO\s*$/i', $data['Name']) AND ! preg_match('/_pro\.zip$/i', $package)) {
            return new WP_Error('process_failed', sprintf(__('You are trying to downgrade %s to a FREE version. Your website can not connect to our Update Server and is fetching update information from wordpress.org about a FREE version. Update your PRO version manually or try later.', 'pwebcontact'), $data['Name']));
        }
    }
    return $reply;
}
add_action( 'upgrader_pre_download', 'pwebcontact_upgrader_pre_download' );

if (version_compare($GLOBALS['wp_version'], '3.5', '>=') AND version_compare(PHP_VERSION, '5.3', '>=')) {

    require_once dirname( __FILE__ ) . '/site.php';

    if ( is_admin() ) {

        // Load CSS for menu icon always
        function pwebcontact_admin_menu_style() {
            wp_register_style('pwebcontact_admin_menu_style', plugins_url('media/css/menu-icon.css', __FILE__, false));
            wp_enqueue_style('pwebcontact_admin_menu_style');
        }
        add_action( 'admin_enqueue_scripts', 'pwebcontact_admin_menu_style' );

        if (defined( 'DOING_AJAX' )) {

            add_action('wp_ajax_pwebcontact_sendEmail', array('PWebContact', 'sendEmailAjax'));
            add_action('wp_ajax_nopriv_pwebcontact_sendEmail', array('PWebContact', 'sendEmailAjax'));

            add_action('wp_ajax_pwebcontact_checkCaptcha', array('PWebContact', 'checkCaptchaAjax'));
            add_action('wp_ajax_nopriv_pwebcontact_checkCaptcha', array('PWebContact', 'checkCaptchaAjax'));

            add_action('wp_ajax_pwebcontact_uploader', array('PWebContact', 'uploaderAjax'));
            add_action('wp_ajax_nopriv_pwebcontact_uploader', array('PWebContact', 'uploaderAjax'));

            add_action('wp_ajax_pwebcontact_getToken', array('PWebContact', 'getTokenAjax'));
            add_action('wp_ajax_nopriv_pwebcontact_getToken', array('PWebContact', 'getTokenAjax'));
        }
        else {

            require_once dirname( __FILE__ ) . '/install.php';
            require_once dirname( __FILE__ ) . '/admin.php';

            register_activation_hook( __FILE__, 'pwebcontact_install' );
            register_uninstall_hook( __FILE__, 'pwebcontact_uninstall' );
        }
    }
    else {

        add_action('init', array('PWebContact', 'init'));

        add_action('wp_footer', array('PWebContact', 'displayFormsInFooter'), 100);

        add_shortcode('pwebcontact', array('PWebContact', 'displayFormByShortcode'));
    }

    require_once dirname( __FILE__ ) . '/widget.php';
}
else {

    function pwebcontact_requirements_notice() {
        ?>
        <div class="error">
            <p><?php printf(__( 'Gator Forms plugin requires WordPress %s and PHP %s', 'pwebcontact' ), '3.5+', '5.3+'); ?></p>
        </div>
        <?php
    }
    add_action( 'admin_notices', 'pwebcontact_requirements_notice' );
}
