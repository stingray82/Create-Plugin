<?php
/**
 * Plugin Name: My Plugin
 * Plugin URI:  https://example.com/
 * Description: A lightweight WordPress plugin starter template.
 * Version:     1.0
 * License:     GPL2
 */

if ( ! defined('ABSPATH') ) {
    exit; // Prevent direct access
}

// Define plugin constants
define('MY_PLUGIN_VERSION', '1.0');
define('MY_PLUGIN_SLUG', 'my_plugin'); // Replace with your unique slug if needed
define('MY_PLUGIN_MAIN_FILE', __FILE__);
define('MY_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('MY_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include functions (assuming functions.php is in the includes folder)
require_once MY_PLUGIN_DIR . 'includes/functions.php';

// Activation hook
function my_plugin_activate() {
    update_option('my_plugin_activated', time());
}
register_activation_hook(__FILE__, 'my_plugin_activate');

// Deactivation hook
function my_plugin_deactivate() {
    delete_option('my_plugin_activated');
}
register_deactivation_hook(__FILE__, 'my_plugin_deactivate');
