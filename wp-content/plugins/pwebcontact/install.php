<?php
/**
 * @version 2.3.0
 * @package Gator Forms
 * @copyright (C) 2018 Gator Forms, All rights reserved. https://gatorforms.com
 * @license GNU/GPL http://www.gnu.org/licenses/gpl-3.0.html
 * @author Piotr Moćko
 */

// No direct access
function_exists('add_action') or die;


function pwebcontact_install() {

    pwebcontact_install_db();
    pwebcontact_install_upload_dir();
}

function pwebcontact_uninstall() {

    if (file_exists(dirname(__FILE__) . '/uninstall.txt')) {
        delete_option('pwebcontact_debug');
        delete_option('pwebcontact_settings');
        delete_option('pwebcontact_tickets');
        delete_option('pwebcontact_googledocs_token');
        pwebcontact_uninstall_db();
        pwebcontact_uninstall_upload_dir();
    }
}

// create database table for contact forms settings
function pwebcontact_install_db() {

    global $wpdb;
    global $charset_collate;

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    $sql =
    "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}pwebcontact_forms` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `title` varchar(100) NOT NULL,
      `publish` tinyint(1) NOT NULL DEFAULT '1',
      `position` varchar(20) NOT NULL DEFAULT 'footer',
      `layout` varchar(20) NOT NULL DEFAULT 'slidebox',
      `modify_date` datetime NOT NULL,
      `params` text,
      `fields` text,
      PRIMARY KEY (`id`)
    ) $charset_collate AUTO_INCREMENT=1;";

    dbDelta( $sql );

    $sql = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}pwebcontact_messages` (
      `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
      `form_id` int(11) NOT NULL,
      `sent` tinyint(1) NOT NULL DEFAULT '0',
      `created_at` datetime NOT NULL,
      `payload` text NOT NULL,
      `ip_address` varchar(45) DEFAULT '',
      `browser` varchar(255) DEFAULT '',
      `os` varchar(255) DEFAULT '',
      `user_id` bigint(20) unsigned NOT NULL DEFAULT '0',
      `ticket` varchar(50) DEFAULT NULL,
      PRIMARY KEY (`id`)
    ) {$charset_collate} AUTO_INCREMENT=1;";
    dbDelta($sql);
}


function pwebcontact_uninstall_db() {

    global $wpdb;

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    $sql = "DROP TABLE IF EXISTS `{$wpdb->prefix}pwebcontact_forms`;";

    dbDelta( $sql );

    $sql = "DROP TABLE IF EXISTS `{$wpdb->prefix}pwebcontact_messages`";
    dbDelta($sql);
}

function pwebcontact_check_db() {

    global $wpdb;

    $show = $wpdb->hide_errors();

    try {
        if (false === $wpdb->query( 'SELECT `id` FROM `'.$wpdb->prefix.'pwebcontact_forms` LIMIT 1' )
            || false === $wpdb->query( 'SELECT `id` FROM `'.$wpdb->prefix.'pwebcontact_messages` LIMIT 1' )
        ) {
            pwebcontact_install_db();
        }
    } catch (Exception $e) {

    }

    $wpdb->show_errors($show);
}

function pwebcontact_install_upload_dir() {

    $upload_dir = wp_upload_dir();
    $path = $upload_dir['basedir'].'/pwebcontact/';

    require_once ABSPATH . 'wp-admin/includes/file.php';

    if (function_exists('WP_Filesystem') AND WP_Filesystem()) {
        global $wp_filesystem;

        // create wirtable upload path
        if (!$wp_filesystem->is_dir($path)) {
            $wp_filesystem->mkdir($path, 0755);
        }
    }
    else {
        // create wirtable upload path
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
    }
}

function pwebcontact_uninstall_upload_dir() {

    $upload_dir = wp_upload_dir();
    $path = $upload_dir['basedir'].'/pwebcontact/';

    require_once ABSPATH . 'wp-admin/includes/file.php';

    if (function_exists('WP_Filesystem') AND WP_Filesystem()) {
        global $wp_filesystem;

        if ($wp_filesystem->is_dir($path)) {
            $wp_filesystem->rmdir($path, true);
        }
    }
}
