<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Initialize plugin features
 */
function hp_standard_init_features() {
    add_action('init', 'hp_standard_setup_features');
    add_action('wp_enqueue_scripts', 'hp_standard_enqueue_frontend_assets');
    add_action('admin_enqueue_scripts', 'hp_standard_enqueue_admin_assets');
}

/**
 * Setup plugin features
 */
function hp_standard_setup_features() {
    // Setup code
}

/**
 * Enqueue frontend assets
 */
function hp_standard_enqueue_frontend_assets() {
    // Frontend CSS/JS
}

/**
 * Enqueue admin assets
 */
function hp_standard_enqueue_admin_assets($hook) {
    // Only load on plugin page
    if (strpos($hook, HP_STANDARD_SLUG) !== false) {
        wp_enqueue_script('hp-standard-features-js', HP_STANDARD_ASSETS_URL . 'js/f0-features-handler.js', array(), HP_STANDARD_VERSION, true);
    }
}

// Initialize features when plugin loads
hp_standard_init_features();

// Render main page features
if (!function_exists('hp_standard_features_render_main_page')) {
    function hp_standard_features_render_main_page() {
        echo '<div class="wrap"><h2>HP Standard Dashboard</h2><p>Welcome to HP Standard features!</p></div>';
    }
}
