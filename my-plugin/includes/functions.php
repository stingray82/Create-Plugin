<?php
if ( ! defined('ABSPATH') ) {
    exit;
}

// Example function (update as needed)
function my_plugin_example_function() {
    return true;
}

/* SureCart / Licensing / Updater */
add_action('init', function(){
    // Load the SureCart Licensing Client if not already loaded.
    if ( ! class_exists('SureCart\Licensing\Client') ) {
        // Adjust the path: since this file is in the "includes" folder, Client.php is in "includes/src"
        require_once __DIR__ . '/src/Client.php';
    }
    
    // Initialize the licensing client with your plugin name and public token.
    $client = new \SureCart\Licensing\Client( 'Your Plugin', 'pt_jzieNYQdE5LMAxksscgU6H4', __FILE__ );
    
    // Set your textdomain.
    $client->set_textdomain('your-textdomain');
    
    // Add the pre-built license settings page.
    $client->settings()->add_page( [
        'type'                 => 'submenu', // Options: menu, options, submenu.
        'parent_slug'          => 'your-plugin-menu-slug', // Replace with your plugin menu slug.
        'page_title'           => 'Manage License',
        'menu_title'           => 'Manage License',
        'capability'           => 'manage_options',
        'menu_slug'            => $client->slug . '-manage-license',
        'icon_url'             => '',
        'position'             => null,
        'parent_slug'          => '', // Update as needed.
        'activated_redirect'   => admin_url('admin.php?page=my-plugin-page'),
        'deactivated_redirect' => admin_url('admin.php?page=my-plugin-deactivation-page'),
    ]);
});


// ──────────────────────────────────────────────────────────────────────────
//  Updater bootstrap (plugins_loaded priority 1):
// ──────────────────────────────────────────────────────────────────────────
add_action( 'plugins_loaded', function() {
    // 1) Load our universal drop-in. Because that file begins with "namespace UUPD\V1;",
    //    both the class and the helper live under UUPD\V1.
    require_once __DIR__ . '/updater.php';

    // 2) Build a single $updater_config array:
    $updater_config = [
        'plugin_file' => plugin_basename( __FILE__ ),             // e.g. "simply-static-export-notify/simply-static-export-notify.php"
        'slug'        => 'MY_PLUGIN',           // must match your updater‐server slug
        'name'        => 'MY_PLUGIN',         // human‐readable plugin name
        'version'     => MY_PLUGIN_VERSION, // same as the VERSION constant above
        'key'         => 'testkey123',                 // your secret key for private updater
        //'server'      => 'https://github.com/stingray82/example-plugin',
        'server'      => 'https://updater.reallyusefulplugins.com/u/',
        // 'textdomain' is omitted, so the helper will automatically use 'slug'
        //'textdomain'  => 'simply-static-export-notify',           // used to translate “Check for updates”
    ];

    // 3) Call the helper in the UUPD\V1 namespace:
    \UUPD\V1\UUPD_Updater_V1::register( $updater_config );
}, 1 );