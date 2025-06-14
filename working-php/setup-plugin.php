<?php
if ($argc < 7) {
    echo "Usage: php setup-plugin.php <plugin_dir> <plugin_name> <description> <function_prefix> <plugin_slug_underscores> <lowercase_prefix>\n";
    exit(1);
}

$plugin_dir              = rtrim($argv[1], DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
$plugin_name             = $argv[2];
$description             = $argv[3];
$function_prefix         = $argv[4]; // e.g. "rup_"
$plugin_slug_underscores = $argv[5]; // e.g. "my_plugin"
$lowercase_prefix        = $argv[6]; // e.g. "rup_my_plugin"
$plugin_slug_hyphen      = str_replace('_', '-', $plugin_slug_underscores);

// UPPERCASE versions for constants
$upper_prefix  = strtoupper($function_prefix);
$upper_slug    = strtoupper($plugin_slug_underscores);
$upper_prefix_slug = "{$upper_prefix}{$upper_slug}";

// Construct new constant names
$new_VERSION   = "{$upper_prefix_slug}_VERSION";
$new_SLUG      = "{$upper_prefix_slug}_SLUG";
$new_MAIN_FILE = "{$upper_prefix_slug}_MAIN_FILE";
$new_DIR       = "{$upper_prefix_slug}_DIR";
$new_URL       = "{$upper_prefix_slug}_URL";

// Build search/replace array
$search_replace = [
    // Replace constant definitions
    "define('MY_PLUGIN_VERSION'"   => "define('{$new_VERSION}'",
    "define('MY_PLUGIN_SLUG'"      => "define('{$new_SLUG}'",
    "define('MY_PLUGIN_MAIN_FILE'" => "define('{$new_MAIN_FILE}'",
    "define('MY_PLUGIN_DIR'"       => "define('{$new_DIR}'",
    "define('MY_PLUGIN_URL'"       => "define('{$new_URL}'",

    // Replace constant usage
    "MY_PLUGIN_VERSION"   => $new_VERSION,
    "MY_PLUGIN_SLUG"      => $new_SLUG,
    "MY_PLUGIN_MAIN_FILE" => $new_MAIN_FILE,
    "MY_PLUGIN_DIR"       => $new_DIR,
    "MY_PLUGIN_URL"       => $new_URL,

    // Replace constant *values*
    "'my_plugin'" => "'{$plugin_slug_hyphen}'",

    // Function names
    "function my_plugin_" => "function {$lowercase_prefix}_",
    "register_activation_hook(__FILE__, 'my_plugin_activate')"   => "register_activation_hook(__FILE__, '{$lowercase_prefix}_activate')",
    "register_deactivation_hook(__FILE__, 'my_plugin_deactivate')" => "register_deactivation_hook(__FILE__, '{$lowercase_prefix}_deactivate')",
    "update_option('my_plugin_activated'"   => "update_option('{$lowercase_prefix}_activated'",
    "delete_option('my_plugin_activated'"   => "delete_option('{$lowercase_prefix}_activated'",

    // Header fields
    "My Plugin" => $plugin_name,
    "A lightweight WordPress plugin starter template." => $description,

    // Updater usage
    "'slug'        => 'my_plugin'," => "'slug'        => '{$plugin_slug_hyphen}',",

    // Other references
    "Your Plugin"           => $plugin_name,
    "your-textdomain"       => strtolower($plugin_slug_hyphen) . '-textdomain',
    "your-plugin-menu-slug" => strtolower($plugin_slug_hyphen) . '-menu-slug',
    "my-plugin-page"        => strtolower($plugin_slug_hyphen) . '-page',
    "my-plugin-deactivation-page" => strtolower($plugin_slug_hyphen) . '-deactivation-page',

    // Literal textdomain/text fallback
    "my_plugin" => strtolower($plugin_slug_underscores)
];

function replace_in_file($file, $search_replace) {
    $content = file_get_contents($file);
    foreach ($search_replace as $search => $replace) {
        if (strpos($content, $search) !== false) {
            $content = str_replace($search, $replace, $content);
        }
    }
    file_put_contents($file, $content);
}

// Iterate recursively
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($plugin_dir));
foreach ($iterator as $file) {
    if ($file->isFile() && pathinfo($file, PATHINFO_EXTENSION) === "php") {
        replace_in_file($file->getPathname(), $search_replace);
    }
}

echo "✅ Plugin files have been successfully updated.\n";
