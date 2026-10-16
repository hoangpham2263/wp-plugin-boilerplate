<?php
if (!defined('ABSPATH')) {
    exit;
}

// Plugin Information
define('HP_STANDARD_VERSION', '1.0.0');
define('HP_STANDARD_NAME', 'HP Standard');
define('HP_STANDARD_SLUG', 'hp-standard');
define('HP_STANDARD_DESCRIPTION', 'Plugin HP Standard cho hệ thống HP');
define('HP_STANDARD_ASSETS_URL', plugin_dir_url(dirname(__FILE__)) . 'features/assets/');

/**
 * Plugin main page callback function
 */
function hp_standard_main_page() {
    if (function_exists('hp_standard_features_render_main_page')) {
        hp_standard_features_render_main_page();
    } else {
        echo '<div class="wrap"><h2>HP Standard Content</h2><p>Features not loaded.</p></div>';
    }
}

/**
 * Plugin initialization function
 */
function hp_standard_init_config() {
    // Add your plugin initialization code here
}

/**
 * Plugin activation function
 */
function hp_standard_activate_config() {
    // Add your plugin activation code here
}
