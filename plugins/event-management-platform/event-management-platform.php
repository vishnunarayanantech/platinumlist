<?php
/**
 * Plugin Name:       Event Management Platform
 * Plugin URI:        https://github.com/emp/event-management-platform
 * Description:       An enterprise-level, SaaS-grade Event Management Platform for WordPress, designed to scale to thousands of events.
 * Version:           1.1.0
 * Author:            Antigravity
 * Author URI:        https://github.com/emp
 * License:           GPL-2.0-or-later
 * Text Domain:       event-management-platform
 * Domain Path:       /languages
 * Requires PHP:      8.1
 * Requires WP:       6.0
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define Constants.
define( 'EMP_VERSION', '1.1.0' );
define( 'EMP_PATH', plugin_dir_path( __FILE__ ) );
define( 'EMP_URL', plugin_dir_url( __FILE__ ) );

// Load Composer Autoloader.
if ( file_exists( EMP_PATH . 'vendor/autoload.php' ) ) {
    require_once EMP_PATH . 'vendor/autoload.php';
}

// Hook Activation and Deactivation.
register_activation_hook( __FILE__, [ 'EventManagementPlatform\Plugin', 'activate' ] );
register_deactivation_hook( __FILE__, [ 'EventManagementPlatform\Plugin', 'deactivate' ] );

// Initialize the plugin.
add_action( 'plugins_loaded', function() {
    \EventManagementPlatform\Plugin::getInstance()->init();
} );
