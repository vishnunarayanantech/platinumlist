<?php
/**
 * Fired when the plugin is uninstalled.
 */

// If uninstall not called from WordPress, exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

global $wpdb;

// Table names to drop
$tables = [
    $wpdb->prefix . 'emp_event_organizer_map',
    $wpdb->prefix . 'emp_event_tag_map',
    $wpdb->prefix . 'emp_event_faqs',
    $wpdb->prefix . 'emp_event_images',
    $wpdb->prefix . 'emp_events',
    $wpdb->prefix . 'emp_venues',
    $wpdb->prefix . 'emp_categories',
    $wpdb->prefix . 'emp_event_tags',
    $wpdb->prefix . 'emp_organizers'
];

foreach ( $tables as $table ) {
    $wpdb->query( "DROP TABLE IF EXISTS $table" );
}

// Delete options
delete_option( 'emp_db_version' );
delete_option( 'emp_settings' );
