<?php
/**
 * Plugin Name: HP Standard
 * Plugin URI: https://hoangpham.vn/
 * Description: Plugin HP Standard cho hệ thống HP
 * Version: 1.0.0
 * Author: HOANGPHAM
 * Author URI: https://hoangpham.vn/
 * Text Domain: hp-standard
 * Requires at least: 5.0
 * Tested up to: 6.4
 * Requires PHP: 7.4
 */

if (!defined('ABSPATH')) {
    exit;
}

// Load core
require_once plugin_dir_path(__FILE__) . 'core/core.php';

// Load config
require_once plugin_dir_path(__FILE__) . 'core/config.php';

// Load plugin features
require_once plugin_dir_path(__FILE__) . 'features/f0-hp-features-handler.php';

// Plugin initialization
function hp_standard_init() {
    if (function_exists('hp_standard_init_config')) {
        hp_standard_init_config();
    }
}

// Plugin activation
function hp_standard_activate() {
    if (function_exists('hp_standard_activate_config')) {
        hp_standard_activate_config();
    }
}

// Register hooks
add_action('init', 'hp_standard_init');
register_activation_hook(__FILE__, 'hp_standard_activate');

// Initialize the shared admin menu using core functionality
hp_core_create_shared_menu(
    HP_STANDARD_SLUG,
    HP_STANDARD_NAME,
    HP_STANDARD_DESCRIPTION,
    HP_STANDARD_VERSION,
    plugin_dir_url(__FILE__) . 'core/',
    'hp_standard_main_page'
);