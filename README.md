# wp-plugin-boilerplate: HP WordPress plugin standard

A standard plugin skeleton to clone when starting a new plugin, **along with the mandatory standard for all my plugins** (`wp-plugin-*`). The goal: any number of plugins can be installed together **without name clashes or conflicts**, and each name says what the plugin does.

---

## 1. Mandatory rules (summary)

1. **Each plugin has one unique code (slug)**, and everything in the plugin starts with that code.
2. **No bare global functions.** Use `namespace` + class; if a global function is unavoidable, give it the full prefix.
3. **No generic prefixes** such as `hp_`, `my_`, `custom_` or `wp_` alone. Always use `hp_<code>_`.
4. **Every WordPress "global" name also needs the prefix**: options, transients, meta keys, shortcodes, AJAX actions, REST routes, script/style handles, cron hooks, DB tables, post types, taxonomies, capabilities and text domains.
5. **Do not re-bundle libraries WordPress already ships** (jQuery, wp-color-picker and so on). Use the core handle as a dependency.
6. **Third-party libraries bundled in a plugin must have their handles renamed** with the plugin prefix, so they do not override another plugin's copy.
7. **No plugin copies nested inside a plugin**, and no zip files or screenshots in the install folder.
8. **Check permissions + nonce + escaping** everywhere data is received or output.
9. **Bump the version on every release**, update `CHANGELOG.md`, and attach a zip named after the folder to the release.
10. **Uninstalling must clean up** the plugin's own data (`uninstall.php`) without touching other plugins' data.

---

## 2. Naming table: everything derives from one code

Example: a flash sale plugin for WooCommerce, **code = `woo-flash-sale`**:

| Item | Rule | Example |
|---|---|---|
| GitHub repo | `wp-plugin-<code>` | `wp-plugin-woo-flash-sale` |
| Plugin folder (= zip name) | `hp-<code>` | `hp-woo-flash-sale/` |
| Main file | `hp-<code>.php` | `hp-woo-flash-sale.php` |
| Plugin Name (header) | `HP <Readable Name>` | `HP Woo Flash Sale` |
| Text Domain | `hp-<code>` | `hp-woo-flash-sale` |
| PHP namespace | `HP\<PascalName>` | `HP\WooFlashSale` |
| Global function (if any) | `hp_<code_with_underscores>_*` | `hp_woo_flash_sale_get_price()` |
| Constant | `HP_<UPPERCASE_CODE>_*` | `HP_WOO_FLASH_SALE_VERSION` |
| Option / transient | `hp_<code>_*` | `hp_woo_flash_sale_settings` |
| Post / user / term meta | `_hp_<code>_*` (leading underscore hides it from custom fields) | `_hp_woo_flash_sale_end` |
| Shortcode | `hp_<code>` or `hp_<code>_*` | `[hp_woo_flash_sale]` |
| AJAX action | `hp_<code>_*` | `wp_ajax_hp_woo_flash_sale_buy` |
| REST namespace | `hp-<code>/v1` | `/wp-json/hp-woo-flash-sale/v1/...` |
| Script/style handle | `hp-<code>-*` | `hp-woo-flash-sale-admin` |
| Cron hook | `hp_<code>_*` | `hp_woo_flash_sale_expire` |
| DB table | `{$wpdb->prefix}hp_<code>_*` | `wp_hp_woo_flash_sale_logs` |
| Post type / taxonomy (20 characters max) | `hp_<abbreviation>` | `hp_fs_campaign` |
| Capability | `hp_<code>_manage` | `hp_woo_flash_sale_manage` |
| CSS class / ID | `.hp-<code>__*` (BEM) | `.hp-woo-flash-sale__timer` |
| JS global / localize | `HP<PascalName>` | `window.HPWooFlashSale` |
| Hook fired by the plugin | `hp/<code>/<event>` | `do_action('hp/woo-flash-sale/started')` |

**Code convention:** lowercase with hyphens, describing the actual feature; plugins that need WooCommerce start with `woo-`. Examples: `woo-quick-buy`, `woo-product-review`, `woo-viettelpost-shipping`, `post-rating`, `branch-finder`.

---

## 3. Folder structure

```
hp-<code>/
├── hp-<code>.php         # plugin header + bootstrap (no logic)
├── uninstall.php         # removes the plugin's options, meta and tables on delete
├── README.md
├── CHANGELOG.md
├── src/                  # PHP code in the HP\<PascalName> namespace
│   ├── Plugin.php        # initialization, hook registration
│   ├── Admin/            # settings pages, metaboxes
│   ├── Frontend/         # shortcodes, template hooks
│   ├── Rest/             # REST routes / AJAX
│   └── Support/          # helpers shared within the plugin
├── templates/            # display files that themes can override
├── assets/
│   ├── css/  js/  img/
│   └── vendor/           # third-party libraries (handles renamed)
└── languages/            # hp-<code>.pot
```

The skeleton in this repo (`core/` + `features/`) still works for small plugins; new plugins should follow the `src/` structure above.

---

## 4. Sample main file

```php
<?php
/**
 * Plugin Name:       HP Woo Flash Sale
 * Description:       Flash sales for WooCommerce: time slots, countdown and quantity limits.
 * Version:           1.2.0
 * Requires at least: 6.0
 * Requires PHP:      8.0
 * Requires Plugins:  woocommerce
 * Author:            Phạm Quốc Hoàng
 * Author URI:        https://hoangpham.is-a.dev
 * Text Domain:       hp-woo-flash-sale
 * Domain Path:       /languages
 */

namespace HP\WooFlashSale;

defined('ABSPATH') || exit;

const VERSION = '1.2.0';
const FILE    = __FILE__;

// Prevent loading twice (for example, a leftover old copy of the same plugin)
if (defined('HP_WOO_FLASH_SALE_VERSION')) {
    return;
}
define('HP_WOO_FLASH_SALE_VERSION', VERSION);

spl_autoload_register(function ($class) {
    $prefix = __NAMESPACE__ . '\\';
    if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
        return;
    }
    $path = __DIR__ . '/src/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
    if (is_file($path)) {
        require $path;
    }
});

add_action('plugins_loaded', [Plugin::class, 'boot']);
register_activation_hook(__FILE__, [Plugin::class, 'activate']);
register_deactivation_hook(__FILE__, [Plugin::class, 'deactivate']);
```

---

## 5. Avoiding conflicts: details

- **Functions/classes:** with a namespace, `function_exists()` is unnecessary. Wrap only *pluggable* functions that are meant to be overridden by themes.
- **Code shared between my plugins** (shared "HP Plugin" menu, helpers): put it in the `HP\Core` namespace and check the version so the newest copy is always used, rather than relying on whichever plugin loads first:

  ```php
  add_action('plugins_loaded', function () {
      if (!defined('HP_CORE_VERSION') || version_compare(HP_CORE_VERSION, '1.1.0', '<')) {
          // load the core bundled with this plugin
      }
  }, 1);
  ```

- **Scripts/styles:** dependencies use WordPress core handles (`jquery`, `wp-color-picker`, `wp-util` and so on). Rename handles of bundled libraries: `hp-<code>-color-picker-alpha`. Enqueue only on pages that need them (check `$hook` in admin, `is_product()` and similar on the frontend).
- **JS:** wrap in an IIFE or module; create no globals other than `window.HP<PascalName>`.
- **CSS:** every selector starts with `.hp-<code>`; do not style bare elements (`button`, `input`) globally.
- **WooCommerce/theme hooks:** use explicit priorities with a comment explaining why; remove hooks with `remove_action` at the correct priority.
- **WooCommerce:** declare HPOS compatibility:

  ```php
  add_action('before_woocommerce_init', function () {
      if (class_exists(\Automattic\WooCommerce\Utilities\FeaturesUtil::class)) {
          \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', FILE, true);
      }
  });
  ```

---

## 6. Security

- Forms and AJAX: `wp_nonce_field()` / `check_ajax_referer()` + `current_user_can()`.
- Input: `sanitize_text_field()`, `absint()`, `sanitize_key()`, `wp_kses_post()`.
- Output: `esc_html()`, `esc_attr()`, `esc_url()`, `wp_kses_post()`.
- DB queries: always `$wpdb->prepare()`.
- Do not keep real API keys in code; store them in an option and mask them on the settings page.

## 7. Packaging and release

1. Bump `Version` in the header and the `VERSION` constant, and update `CHANGELOG.md`.
2. Zip **the `hp-<code>/` folder** (not the whole repo, and without `.git`, `node_modules`, screenshots or old zips).
3. `git tag vX.Y.Z` → GitHub Release with `hp-<code>-X.Y.Z.zip` attached.
4. Test on a clean site **alongside the other HP plugins** with `WP_DEBUG` on: no notices or warnings.

## 8. Pre-release checklist

- [ ] Unique plugin code; repo, folder, text domain and namespace match the table in section 2
- [ ] No bare global functions, no generic prefixes
- [ ] Options, meta, shortcodes, AJAX, REST, handles, cron and DB tables are all prefixed
- [ ] No re-bundled WordPress core libraries; bundled libraries have renamed handles
- [ ] Nonce + permissions + sanitizing + escaping
- [ ] No nested folders or extra files in the zip
- [ ] Runs with all HP plugins + WooCommerce active: no errors, no duplicate features
- [ ] `uninstall.php` removes only the plugin's own data

---

## 9. Current plugin status

| Repo | Code | Status against the standard |
|---|---|---|
| wp-plugin-woo-flash-sale | `woo-flash-sale` | `hp_` prefix is fine; rename the folder and text domain to `hp-woo-flash-sale` in the next update |
| wp-plugin-woo-quick-buy | `woo-quick-buy` | Text domain is `quick-buy` → rename to `hp-woo-quick-buy` |
| wp-plugin-woo-product-review | `woo-product-review` | Rename handle `wp-color-picker-alpha` → `hp-woo-product-review-color-picker-alpha` |
| wp-plugin-woo-viettelpost-shipping | `woo-viettelpost-shipping` | Version 0.1.0; apply the standard as development continues |
| wp-plugin-post-rating | `post-rating` | OK |
| wp-plugin-branch-finder | `branch-finder` | Nested copy removed (1.0.6); rename the color picker handle as above |

Checked on 2026-10-16: these plugins **share no function, class, constant, shortcode, AJAX, option or DB table names**. They only share the `wp-color-picker-alpha` / `wp-color-picker-init` handles, which point to the same file, so no error occurs yet.

---
Phạm Quốc Hoàng · https://hoangpham.is-a.dev
