<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://www.codetides.com/
 * @since             1.2.0
 * @package           Advanced_Floating_Content
 *
 * @wordpress-plugin
 * Plugin Name:       Advanced Floating Content
 * Plugin URI:        http://www.codetides.com/advanced-floating-content/
 * Description:       Advanced Floating Content Plugin is an all in one plugin with easy to use controls, helps you demonstrate sticky footer or sticky header warning, imparting social networking connections. High level responsiveness and so on.
 * Version:           1.2.0
 * Author:            Code Tides
 * Author URI:        http://www.codetides.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       advanced-floating-content
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-advanced-floating-content-activator.php
 */
function activate_advanced_floating_content() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-advanced-floating-content-activator.php';
	Advanced_Floating_Content_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-advanced-floating-content-deactivator.php
 */
function deactivate_advanced_floating_content() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-advanced-floating-content-deactivator.php';
	Advanced_Floating_Content_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_advanced_floating_content' );
register_deactivation_hook( __FILE__, 'deactivate_advanced_floating_content' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-advanced-floating-content.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_advanced_floating_content() {

	$plugin = new Advanced_Floating_Content();
	$plugin->run();

}
run_advanced_floating_content();
