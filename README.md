# wp-plugin-boilerplate — quy chuẩn plugin WordPress của HP

Khung plugin chuẩn để nhân bản khi làm plugin mới, **kèm quy chuẩn bắt buộc cho mọi plugin của mình** (`wp-plugin-*`). Mục tiêu: cài bao nhiêu plugin cùng lúc cũng **không trùng tên, không xung đột**, và nhìn tên là biết plugin làm gì.

---

## 1. Quy tắc bắt buộc (tóm tắt)

1. **Mỗi plugin có một "mã" (slug) duy nhất**, mọi thứ trong plugin đều bắt đầu bằng mã đó.
2. **Không có hàm toàn cục trần.** Dùng `namespace` + class; nếu buộc phải có hàm toàn cục thì đặt tiền tố đầy đủ.
3. **Không dùng tiền tố chung chung** như `hp_`, `my_`, `custom_`, `wp_` một mình — luôn `hp_<mã>_`.
4. **Mọi tên "toàn cục" của WordPress cũng phải có tiền tố**: option, transient, meta key, shortcode, AJAX action, REST route, handle script/style, cron, bảng DB, post type, taxonomy, capability, text domain.
5. **Không tự nhúng lại thư viện có sẵn của WordPress** (jQuery, wp-color-picker…) — dùng handle gốc làm dependency.
6. **Thư viện bên thứ ba nhúng vào plugin phải đổi handle** theo tiền tố plugin để không đè lên bản của plugin khác.
7. **Không để bản sao plugin lồng bên trong plugin**, không để file zip, ảnh chụp màn hình trong thư mục cài đặt.
8. **Kiểm tra quyền + nonce + escape** ở mọi chỗ nhận dữ liệu và in ra.
9. **Version tăng mỗi lần phát hành**, ghi `CHANGELOG.md`, release kèm file zip đúng tên thư mục.
10. **Gỡ plugin phải dọn sạch** dữ liệu của chính nó (`uninstall.php`), không đụng dữ liệu plugin khác.

---

## 2. Bảng tên — từ một mã suy ra tất cả

Ví dụ plugin flash sale cho WooCommerce, **mã = `woo-flash-sale`**:

| Thành phần | Quy tắc | Ví dụ |
|---|---|---|
| Repo GitHub | `wp-plugin-<mã>` | `wp-plugin-woo-flash-sale` |
| Thư mục plugin (= tên zip) | `hp-<mã>` | `hp-woo-flash-sale/` |
| File chính | `hp-<mã>.php` | `hp-woo-flash-sale.php` |
| Plugin Name (header) | `HP <Tên đọc được>` | `HP Woo Flash Sale` |
| Text Domain | `hp-<mã>` | `hp-woo-flash-sale` |
| Namespace PHP | `HP\<TenPascal>` | `HP\WooFlashSale` |
| Hàm toàn cục (nếu có) | `hp_<mã_gạch_dưới>_*` | `hp_woo_flash_sale_get_price()` |
| Hằng số | `HP_<MÃ_IN_HOA>_*` | `HP_WOO_FLASH_SALE_VERSION` |
| Option / transient | `hp_<mã>_*` | `hp_woo_flash_sale_settings` |
| Post / user / term meta | `_hp_<mã>_*` (gạch dưới đầu = ẩn khỏi custom fields) | `_hp_woo_flash_sale_end` |
| Shortcode | `hp_<mã>` hoặc `hp_<mã>_*` | `[hp_woo_flash_sale]` |
| AJAX action | `hp_<mã>_*` | `wp_ajax_hp_woo_flash_sale_buy` |
| REST namespace | `hp-<mã>/v1` | `/wp-json/hp-woo-flash-sale/v1/...` |
| Handle script/style | `hp-<mã>-*` | `hp-woo-flash-sale-admin` |
| Cron hook | `hp_<mã>_*` | `hp_woo_flash_sale_expire` |
| Bảng DB | `{$wpdb->prefix}hp_<mã>_*` | `wp_hp_woo_flash_sale_logs` |
| Post type / taxonomy (tối đa 20 ký tự) | `hp_<viết tắt>` | `hp_fs_campaign` |
| Capability | `hp_<mã>_manage` | `hp_woo_flash_sale_manage` |
| CSS class / ID | `.hp-<mã>__*` (BEM) | `.hp-woo-flash-sale__timer` |
| JS global / localize | `HP<TenPascal>` | `window.HPWooFlashSale` |
| Hook do plugin phát ra | `hp/<mã>/<sự-kiện>` | `do_action('hp/woo-flash-sale/started')` |

**Quy ước mã:** chữ thường, gạch ngang, mô tả đúng tính năng; plugin cần WooCommerce bắt đầu bằng `woo-`. Ví dụ: `woo-quick-buy`, `woo-product-review`, `woo-viettelpost-shipping`, `post-rating`, `branch-finder`.

---

## 3. Cấu trúc thư mục

```
hp-<mã>/
├── hp-<mã>.php           # header plugin + bootstrap (không chứa logic)
├── uninstall.php         # dọn option/meta/bảng của plugin khi xóa
├── README.md
├── CHANGELOG.md
├── src/                  # code PHP theo namespace HP\<TenPascal>
│   ├── Plugin.php        # khởi tạo, đăng ký hook
│   ├── Admin/            # trang cài đặt, metabox
│   ├── Frontend/         # shortcode, template hook
│   ├── Rest/             # REST route / AJAX
│   └── Support/          # helper dùng chung trong plugin
├── templates/            # file hiển thị, theme có thể ghi đè
├── assets/
│   ├── css/  js/  img/
│   └── vendor/           # thư viện bên thứ ba (đã đổi handle)
└── languages/            # hp-<mã>.pot
```

Khung trong repo này (`core/` + `features/`) vẫn dùng được cho plugin nhỏ; plugin mới nên theo cấu trúc `src/` ở trên.

---

## 4. File chính mẫu

```php
<?php
/**
 * Plugin Name:       HP Woo Flash Sale
 * Description:       Flash sale cho WooCommerce: khung giờ, đếm ngược, giới hạn số lượng.
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

// Chặn nạp hai lần (vd. còn sót bản cũ cùng plugin)
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

## 5. Tránh xung đột — chi tiết

- **Hàm/class:** dùng namespace thì không cần `function_exists()`. Chỉ bọc `function_exists()` cho hàm *pluggable* có chủ đích cho phép theme ghi đè.
- **Code dùng chung giữa nhiều plugin của mình** (menu "HP Plugin" chung, helper): đặt trong namespace `HP\Core` và kiểm tra phiên bản để luôn dùng bản mới nhất — không dựa vào "plugin nào nạp trước thắng":

  ```php
  add_action('plugins_loaded', function () {
      if (!defined('HP_CORE_VERSION') || version_compare(HP_CORE_VERSION, '1.1.0', '<')) {
          // nạp core đi kèm plugin này
      }
  }, 1);
  ```

- **Script/style:** dependency dùng handle gốc của WordPress (`jquery`, `wp-color-picker`, `wp-util`…). Thư viện tự kèm theo đổi handle: `hp-<mã>-color-picker-alpha`. Chỉ enqueue ở trang cần dùng (kiểm tra `$hook` trong admin, `is_product()`… ở frontend).
- **JS:** bọc trong IIFE hoặc module; không tạo biến toàn cục ngoài `window.HP<TenPascal>`.
- **CSS:** mọi selector bắt đầu bằng `.hp-<mã>`; không style thẻ trần (`button`, `input`) toàn cục.
- **Hook của WooCommerce/theme:** dùng priority rõ ràng, có comment lý do; gỡ hook bằng `remove_action` đúng priority.
- **WooCommerce:** khai báo tương thích HPOS:

  ```php
  add_action('before_woocommerce_init', function () {
      if (class_exists(\Automattic\WooCommerce\Utilities\FeaturesUtil::class)) {
          \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', FILE, true);
      }
  });
  ```

---

## 6. Bảo mật

- Form và AJAX: `wp_nonce_field()` / `check_ajax_referer()` + `current_user_can()`.
- Dữ liệu vào: `sanitize_text_field()`, `absint()`, `sanitize_key()`, `wp_kses_post()`.
- Dữ liệu ra: `esc_html()`, `esc_attr()`, `esc_url()`, `wp_kses_post()`.
- Truy vấn DB: luôn `$wpdb->prepare()`.
- Không lưu khóa API thật trong code; lưu trong option, hiển thị dạng ẩn ở trang cài đặt.

## 7. Đóng gói & phát hành

1. Tăng `Version` ở header + hằng `VERSION`, ghi `CHANGELOG.md`.
2. Zip **thư mục `hp-<mã>/`** (không zip cả repo, không kèm `.git`, `node_modules`, ảnh chụp, zip cũ).
3. `git tag vX.Y.Z` → GitHub Release đính `hp-<mã>-X.Y.Z.zip`.
4. Cài thử lên site sạch **cùng các plugin HP khác** + bật `WP_DEBUG` → không có notice/warning.

## 8. Checklist trước khi phát hành

- [ ] Mã plugin duy nhất; repo / thư mục / text domain / namespace đúng bảng mục 2
- [ ] Không có hàm toàn cục trần, không tiền tố chung chung
- [ ] Option, meta, shortcode, AJAX, REST, handle, cron, bảng DB đều có tiền tố
- [ ] Không nhúng lại thư viện core của WordPress; thư viện kèm theo đã đổi handle
- [ ] Nonce + quyền + sanitize + escape
- [ ] Không có thư mục lồng / file thừa trong zip
- [ ] Bật cùng toàn bộ plugin HP + WooCommerce: không lỗi, không trùng tính năng
- [ ] `uninstall.php` chỉ xóa dữ liệu của chính plugin

---

## 9. Đối chiếu các plugin hiện có

| Repo | Mã | Trạng thái so với quy chuẩn |
|---|---|---|
| wp-plugin-woo-flash-sale | `woo-flash-sale` | Tiền tố `hp_` ổn; đổi thư mục/text domain sang `hp-woo-flash-sale` ở lần cập nhật tới |
| wp-plugin-woo-quick-buy | `woo-quick-buy` | Text domain đang là `quick-buy` → đổi `hp-woo-quick-buy` |
| wp-plugin-woo-product-review | `woo-product-review` | Đổi handle `wp-color-picker-alpha` → `hp-woo-product-review-color-picker-alpha` |
| wp-plugin-woo-viettelpost-shipping | `woo-viettelpost-shipping` | Bản 0.1.0, áp dụng quy chuẩn ngay khi phát triển tiếp |
| wp-plugin-post-rating | `post-rating` | Ổn |
| wp-plugin-branch-finder | `branch-finder` | Đã bỏ bản sao lồng bên trong (1.0.6); đổi handle color picker như trên |

Kiểm tra ngày 16/10/2026: các plugin trên **không trùng tên hàm, class, hằng số, shortcode, AJAX, option, bảng DB** với nhau; chỉ trùng handle `wp-color-picker-alpha` / `wp-color-picker-init` nhưng là cùng một file nên chưa gây lỗi.

---
Phạm Quốc Hoàng · https://hoangpham.is-a.dev
