<?php
/**
 * HP Core - Shared functionality for all HP plugins
 * 
 * This file contains all core functionality, configuration, and admin menu
 * that will be shared across all HP plugins. DO NOT MODIFY THIS FILE.
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// ============================================================================
// SHARED MENU CONFIGURATION - FIXED VALUES (DO NOT MODIFY)
// ============================================================================

// Shared Menu Configuration - Fixed values
if (!defined('HP_SHARED_MENU_SLUG')) define('HP_SHARED_MENU_SLUG', 'hp-shared-menu');
if (!defined('HP_SHARED_MENU_NAME')) define('HP_SHARED_MENU_NAME', 'HP Plugin');
if (!defined('HP_SHARED_MENU_POSITION')) define('HP_SHARED_MENU_POSITION', 15);
if (!defined('HP_MENU_ICON_H')) define('HP_MENU_ICON_H', 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyMCAyMCI+PHRleHQgeD0iNTAlIiB5PSI1NCUiIGZvbnQtc2l6ZT0iMTUiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGFsaWdubWVudC1iYXNlbGluZT0ibWlkZGxlIiBmb250LWZhbWlseT0iLWFwcGxlLXN5c3RlbSxCbGlua01hY3N5c3RlbUZvbnQsU2Vnb2UgVUksUm9ib3RvLE94eWdlbi1TYW5zLFVidW50dSxDYW50YXJlbGwsSGVsdmV0aWNhIE5ldWUsZ2VvcmdpYSxzZXJpZiIgZm9udC13ZWlnaHQ9ImJvbGQiIGZpbGw9IiNhMGIyYzEiPkg8L3RleHQ+PC9zdmc+');

// ============================================================================
// COMPANY INFORMATION - SHARED ACROSS ALL PLUGINS (DO NOT MODIFY)
// ============================================================================

// Company Information - Fixed values for all HP plugins
if (!defined('HP_COMPANY_NAME')) define('HP_COMPANY_NAME', 'HOANGPHAM');
if (!defined('HP_COMPANY_CONTACT_NAME')) define('HP_COMPANY_CONTACT_NAME', 'Phạm Quốc Hoàng');
if (!defined('HP_COMPANY_PHONE')) define('HP_COMPANY_PHONE', '0971 955 144');
if (!defined('HP_COMPANY_EMAIL')) define('HP_COMPANY_EMAIL', 'quochoang.idx@gmail.com');
if (!defined('HP_COMPANY_WEBSITE')) define('HP_COMPANY_WEBSITE', 'https://hoangpham.vn/');
if (!defined('HP_COMPANY_WEBSITE_TEXT')) define('HP_COMPANY_WEBSITE_TEXT', 'hoangpham.vn');

// ============================================================================
// CORE ADMIN MENU FUNCTIONS
// ============================================================================

/**
 * Core function to create shared admin menu
 */
if (!function_exists('hp_core_create_shared_menu')) {
function hp_core_create_shared_menu($plugin_slug, $plugin_name, $plugin_description, $plugin_version, $assets_url, $main_page_callback) {
    
    // Enqueue admin styles
    add_action('admin_enqueue_scripts', function($hook) use ($assets_url, $plugin_version, $plugin_slug) {
        if (strpos($hook, HP_SHARED_MENU_SLUG) !== false) {
            wp_enqueue_style(
                'hp-core-admin-styles-' . sanitize_title($plugin_slug),
                $assets_url . 'core.css',
                array(),
                $plugin_version
            );
        }
    });
    
    // Register admin menu
    add_action('admin_menu', function() use ($plugin_slug, $plugin_name, $main_page_callback) {
        // Check if shared menu already exists
        global $menu;
        $menu_exists = false;
        
        foreach ($menu as $item) {
            if (isset($item[2]) && $item[2] === HP_SHARED_MENU_SLUG) {
                $menu_exists = true;
                break;
            }
        }
        
        // Create shared menu if it doesn't exist
        if (!$menu_exists) {
            add_menu_page(
                HP_SHARED_MENU_NAME,
                HP_SHARED_MENU_NAME,
                'manage_options',
                HP_SHARED_MENU_SLUG,
                'hp_core_shared_main_page',
                HP_MENU_ICON_H,
                HP_SHARED_MENU_POSITION
            );
            
            // Add Dashboard submenu to main menu
            add_submenu_page(
                HP_SHARED_MENU_SLUG,
                'Dashboard',
                'Dashboard',
                'manage_options',
                HP_SHARED_MENU_SLUG,
                'hp_core_shared_main_page'
            );
        }
        
        // Add submenu for this plugin
        add_submenu_page(
            HP_SHARED_MENU_SLUG,
            $plugin_name,
            $plugin_name,
            'manage_options',
            $plugin_slug,
            $main_page_callback
        );
    }, 15);
}
}

/**
 * Core shared main page function
 */
if (!function_exists('hp_core_shared_main_page')) {
function hp_core_shared_main_page() {
    // Get active HP plugins
    $hp_plugins = hp_core_get_active_plugins();
?>
    <div class="wrap hp-shared-main">
        <h1 class="wp-heading-inline">
            <span style="display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; background: #0073aa; color: #fff; border-radius: 4px; margin-right: 10px; font-weight: bold; font-size: 20px;">H</span>
            HP Plugin Dashboard
        </h1>
        <hr class="wp-header-end">

        <div class="hp-dashboard-content">
            <!-- Welcome Section -->
            <div class="hp-welcome-section">
                <div class="hp-card">
                    <h2>Welcome to HP Plugin!</h2>
                    <p>This is the central management dashboard for all HP plugins. From here you can manage all active HP plugins and monitor system information.</p>
                </div>
            </div>

            <!-- Active Plugins Section -->
            <div class="hp-plugins-section">
                <h2><span class="dashicons dashicons-admin-plugins"></span>Active HP Plugins</h2>
                <?php if (!empty($hp_plugins)): ?>
                    <div class="hp-plugins-table-wrapper">
                        <table class="wp-list-table widefat fixed striped">
                            <thead>
                                <tr>
                                    <th scope="col" class="manage-column column-plugin-stt">Stt</th>
                                    <th scope="col" class="manage-column column-plugin-name">Plugin Name</th>
                                    <th scope="col" class="manage-column column-plugin-description">Description</th>
                                    <th scope="col" class="manage-column column-plugin-version">Version</th>
                                    <th scope="col" class="manage-column column-plugin-status">Status</th>
                                    <th scope="col" class="manage-column column-plugin-actions">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($hp_plugins as $index => $plugin): ?>
                                    <tr>
                                        <td class="column-plugin-stt">
                                            <span class="hp-stt-number"><?php echo $index + 1; ?></span>
                                        </td>
                                        <td class="column-plugin-name">
                                            <strong><?php echo esc_html($plugin['name']); ?></strong>
                                        </td>
                                        <td class="column-plugin-description">
                                            <span><?php echo esc_html($plugin['description']); ?></span>
                                        </td>
                                        <td class="column-plugin-version">
                                            <span class="hp-version-badge"><?php echo esc_html($plugin['version']); ?></span>
                                        </td>
                                        <td class="column-plugin-status">
                                            <span class="hp-status-active">
                                                <span class="dashicons dashicons-yes-alt"></span>Active
                                            </span>
                                        </td>
                                        <td class="column-plugin-actions">
                                            <a href="<?php echo esc_attr($plugin['admin_url']); ?>" class="button button-primary">
                                                <span class="dashicons dashicons-admin-settings"></span>Manage
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="hp-no-plugins">
                        <div class="hp-card">
                            <p><strong>No HP plugins are currently active.</strong></p>
                            <p>Please activate HP plugins to start using this dashboard.</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- System Information Section -->
            <div class="hp-system-section">
                <h2><span class="dashicons dashicons-chart-bar"></span>System Information</h2>
                <div class="hp-system-grid">
                    <div class="hp-card">
                        <h3><span class="dashicons dashicons-admin-plugins"></span>HP Plugins</h3>
                        <div class="hp-stat-number"><?php echo count($hp_plugins); ?></div>
                        <div class="hp-stat-label">Active Plugins</div>
                    </div>
                    <div class="hp-card">
                        <h3><span class="dashicons dashicons-wordpress"></span>WordPress</h3>
                        <div class="hp-stat-number"><?php echo esc_html(get_bloginfo('version')); ?></div>
                        <div class="hp-stat-label">Current Version</div>
                    </div>
                    <div class="hp-card">
                        <h3><span class="dashicons dashicons-admin-tools"></span>PHP</h3>
                        <div class="hp-stat-number"><?php echo esc_html(PHP_VERSION); ?></div>
                        <div class="hp-stat-label">Server Version</div>
                    </div>
                    <div class="hp-card">
                        <h3><span class="dashicons dashicons-admin-appearance"></span>Theme</h3>
                        <div class="hp-stat-number"><?php echo esc_html(strtoupper(wp_get_theme()->get('Name'))); ?></div>
                        <div class="hp-stat-label">Active Theme</div>
                    </div>
                </div>
            </div>

            <!-- Company Information Section -->
            <div class="hp-company-section">
                <h2><span class="dashicons dashicons-businessman"></span>Company Information</h2>
                <div class="hp-company-info">
                    <div class="hp-company-card">
                        <div class="hp-company-logo">
                        </div>
                        <div class="hp-company-details">
                            <h3><?php echo esc_html(HP_COMPANY_NAME); ?></h3>
                            <div class="hp-company-contact">
                                <div class="hp-contact-item">
                                    <span class="dashicons dashicons-admin-users"></span>
                                    <strong>Name:</strong> <?php echo esc_html(HP_COMPANY_CONTACT_NAME); ?>
                                </div>
                                <div class="hp-contact-item">
                                    <span class="dashicons dashicons-phone"></span>
                                    <strong>Hotline:</strong> <?php echo esc_html(HP_COMPANY_PHONE); ?>
                                </div>
                                <div class="hp-contact-item">
                                    <span class="dashicons dashicons-email"></span>
                                    <strong>Email:</strong> <?php echo esc_html(HP_COMPANY_EMAIL); ?>
                                </div>
                                <div class="hp-contact-item">
                                    <span class="dashicons dashicons-admin-links"></span>
                                    <strong>Website:</strong> 
                                    <a href="<?php echo esc_url(HP_COMPANY_WEBSITE); ?>" target="_blank" rel="noopener noreferrer">
                                        <?php echo esc_html(HP_COMPANY_WEBSITE_TEXT); ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
}
}

/**
 * Core function to automatically detect and get list of active HP plugins
 */
if (!function_exists('hp_core_get_active_plugins')) {
function hp_core_get_active_plugins() {
    $hp_plugins = array();
    
    if (!function_exists('get_plugins')) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
    
    $all_plugins = get_plugins();
    $active_plugins = get_option('active_plugins');
    
    foreach ($active_plugins as $plugin_path) {
        $plugin_dir = dirname($plugin_path);
        // Detect plugins starting with hp- or qhw- (for transition)
        if (strpos($plugin_dir, 'hp-') === 0 || strpos($plugin_dir, 'qhw-') === 0) {
            $plugin_data = $all_plugins[$plugin_path];
            
            $plugin_info = array(
                'name' => $plugin_data['Name'],
                'description' => $plugin_data['Description'],
                'version' => $plugin_data['Version'],
                'plugin_path' => $plugin_path,
                'plugin_slug' => $plugin_dir,
                'admin_url' => hp_core_get_plugin_admin_url($plugin_dir)
            );
            
            $hp_plugins[] = $plugin_info;
        }
    }
    
    return $hp_plugins;
}
}

/**
 * Core function to get plugin admin URL
 */
if (!function_exists('hp_core_get_plugin_admin_url')) {
function hp_core_get_plugin_admin_url($plugin_slug) {
    return admin_url('admin.php?page=' . $plugin_slug);
}
}
