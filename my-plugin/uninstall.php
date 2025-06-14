<?php
if ( ! defined('WP_UNINSTALL_PLUGIN') ) {
    exit;
}

// Remove plugin options
delete_option('my_plugin_activated');
