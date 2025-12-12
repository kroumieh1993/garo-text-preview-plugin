# Plugin Architecture

## Overview

The Garo Text Preview Plugin follows WordPress plugin best practices with a modular, object-oriented architecture.

## Component Diagram

```
┌─────────────────────────────────────────────────────────────┐
│                  garo-text-preview.php                      │
│                   (Main Plugin File)                        │
│  - Plugin initialization                                    │
│  - WordPress hooks registration                             │
│  - Script/style enqueuing                                   │
└────────────────────┬────────────────────────────────────────┘
                     │
                     │ loads & initializes
                     │
        ┌────────────┴────────────┐
        │                         │
        ▼                         ▼
┌──────────────────┐    ┌──────────────────┐
│  Admin Classes   │    │ Frontend Classes │
└──────────────────┘    └──────────────────┘
        │                         │
        │                         │
   ┌────┴─────┐             ┌────┴─────┐
   │          │             │          │
   ▼          ▼             ▼          ▼
┌─────────┐ ┌────────┐ ┌─────────┐ ┌─────┐
│ Admin   │ │Product │ │Frontend │ │Cart │
│Settings │ │Fields  │ │Display  │ │     │
└─────────┘ └────────┘ └─────────┘ └─────┘
```

## File Responsibilities

### 1. garo-text-preview.php
**Purpose:** Main plugin bootstrap file

**Functions:**
- Defines plugin constants
- Checks for WooCommerce dependency
- Loads all class files
- Initializes plugin components
- Registers activation/deactivation hooks
- Enqueues admin and frontend assets

**Key Actions:**
```php
plugins_loaded          → init()
admin_enqueue_scripts   → admin_enqueue_scripts()
wp_enqueue_scripts      → frontend_enqueue_scripts()
```

---

### 2. class-garo-admin.php
**Purpose:** Admin settings management

**Functions:**
- Creates admin menu page
- Manages font library
- Handles global fields configuration
- Saves plugin settings

**Key Actions:**
```php
admin_menu     → add_admin_menu()
admin_init     → register_settings()
```

**Options Stored:**
- `garo_text_preview_fonts` - Array of font definitions
- `garo_text_preview_default_font` - Default font name
- `garo_text_preview_global_fields` - Global field definitions

---

### 3. class-garo-product-fields.php
**Purpose:** Product-level customization

**Functions:**
- Adds meta box to product edit screen
- Renders text field configuration UI
- Saves product customization settings
- Manages per-product fonts and fields

**Key Actions:**
```php
add_meta_boxes      → add_product_meta_box()
save_post_product   → save_product_meta()
```

**Product Meta Keys:**
- `_garo_text_preview_enabled` - Enable/disable flag
- `_garo_selected_font` - Selected font name
- `_garo_custom_fields` - Array of field configurations
- `_garo_image_upload_enabled` - Image upload flag

---

### 4. class-garo-frontend.php
**Purpose:** Frontend display and interaction

**Functions:**
- Displays customization fields on product page
- Loads custom fonts in page head
- Validates user input
- Handles form submissions
- Processes image uploads
- Applies global fields to products

**Key Actions/Filters:**
```php
woocommerce_before_add_to_cart_button  → display_customization_fields()
woocommerce_add_to_cart_validation     → validate_customization_fields()
woocommerce_add_cart_item_data         → add_cart_item_data()
wp_head                                → load_custom_fonts()
```

---

### 5. class-garo-cart.php
**Purpose:** Cart and order integration

**Functions:**
- Displays customizations in cart
- Adds custom prices to cart items
- Saves customizations to orders
- Shows customizations in admin orders

**Key Actions/Filters:**
```php
woocommerce_get_item_data                  → display_cart_item_data()
woocommerce_before_calculate_totals        → add_custom_price()
woocommerce_checkout_create_order_line_item → add_order_item_meta()
woocommerce_admin_order_item_headers       → admin_order_item_headers()
woocommerce_admin_order_item_values        → admin_order_item_values()
```

---

## Data Flow

### Adding Customization to Cart

```
1. Customer fills text fields on product page
   ↓
2. Frontend validation (JavaScript)
   ↓
3. Form submission to WooCommerce
   ↓
4. Server-side validation (class-garo-frontend.php)
   ↓
5. Add customization data to cart item (class-garo-frontend.php)
   ↓
6. Calculate custom price (class-garo-cart.php)
   ↓
7. Display in cart with price (class-garo-cart.php)
   ↓
8. Checkout and save to order (class-garo-cart.php)
```

### Configuration Flow

```
1. Admin adds fonts in settings
   ↓
2. Fonts saved to wp_options
   ↓
3. Admin enables customization on product
   ↓
4. Admin adds text fields with settings
   ↓
5. Fields saved as product meta
   ↓
6. Frontend loads fonts and displays fields
   ↓
7. Customer interacts with fields
```

---

## JavaScript Components

### admin.js
**Purpose:** Admin interface interactions

**Features:**
- Add/remove font rows
- Add/remove global field rows
- Add/remove product field rows
- Toggle category selector visibility
- Dynamic font dropdown updates

### frontend.js
**Purpose:** Customer-facing interactions

**Features:**
- Real-time field validation
- Character count checking
- Text preview generation
- Image upload validation
- Price calculation display
- Error message display

---

## CSS Components

### admin.css
**Purpose:** Admin interface styling

**Styles:**
- Meta box layouts
- Field row containers
- Button styles
- Form element spacing
- Error/success messages

### frontend.css
**Purpose:** Customer-facing styling

**Styles:**
- Customization container
- Field groups and labels
- Input styling and focus states
- Preview area
- Responsive design
- Error indicators

---

## Security Measures

1. **Input Sanitization**
   - All user inputs sanitized before saving
   - `sanitize_text_field()` for text
   - `intval()` for numbers
   - `esc_url()` for URLs

2. **Output Escaping**
   - `esc_html()` for text output
   - `esc_attr()` for attributes
   - `esc_url()` for URLs
   - `wp_kses_post()` for HTML

3. **Nonce Verification**
   - Admin forms use WordPress nonces
   - Settings page has nonce check
   - Product meta save has nonce check

4. **Permission Checks**
   - `current_user_can('manage_options')` for settings
   - `current_user_can('edit_post')` for products
   - WooCommerce capability checks

5. **SQL Injection Prevention**
   - Using WordPress meta functions (update_post_meta, get_post_meta)
   - No direct SQL queries
   - WordPress handles all database interactions

6. **File Upload Security**
   - File type validation (images only)
   - File size limits
   - Using WordPress `wp_handle_upload()`
   - Proper error handling

---

## WordPress Standards Compliance

✅ **Coding Standards**
- WordPress PHP Coding Standards
- Proper indentation and spacing
- Meaningful variable names
- Comprehensive inline comments

✅ **Plugin Structure**
- Single main file with header
- Organized class files
- Separate assets directory
- Proper file naming conventions

✅ **Hooks and Filters**
- Using WordPress and WooCommerce hooks
- No modification of core files
- Extensible architecture

✅ **Internationalization**
- All strings in `__()` or `esc_html__()`
- Text domain: 'garo-text-preview'
- Translation-ready

✅ **Database**
- Using WordPress Options API
- Using Post Meta API
- No custom database tables
- Proper data cleanup

---

## Extensibility

The plugin provides hooks for developers:

### Available Hooks (Future Enhancement)

```php
// Before displaying fields
do_action('garo_before_customization_fields', $product_id);

// After displaying fields
do_action('garo_after_customization_fields', $product_id);

// Filter font list
$fonts = apply_filters('garo_font_list', $fonts);

// Filter field validation
$is_valid = apply_filters('garo_custom_field_validation', $is_valid, $field, $value);

// Filter additional price
$price = apply_filters('garo_additional_price_calculation', $price, $field, $value);
```

---

## Performance Considerations

1. **Asset Loading**
   - Scripts only loaded on relevant pages
   - Conditional enqueuing
   - Minification ready

2. **Database Queries**
   - Efficient use of WordPress meta functions
   - Caching via WordPress object cache
   - No unnecessary queries

3. **Font Loading**
   - Fonts loaded only when needed
   - Supports font-display property
   - Google Fonts optimization

---

## Testing Strategy

### Manual Testing Checklist

1. ✅ Install plugin
2. ✅ Add multiple fonts
3. ✅ Create global fields
4. ✅ Enable on products
5. ✅ Test frontend display
6. ✅ Test validation
7. ✅ Test cart integration
8. ✅ Test checkout
9. ✅ Test order admin
10. ✅ Test responsive design

### Edge Cases Handled

- Empty fields
- Missing fonts
- Invalid coordinates
- Large file uploads
- Long text strings
- Special characters
- Multiple products in cart
- Mixed customized/non-customized products

---

## Future Enhancement Ideas

1. Visual preview on product image
2. Font size customization
3. Text color picker
4. Text alignment options
5. Font weight selection
6. Export/import configurations
7. Multi-language support
8. AJAX add to cart
9. Live image preview with text overlay
10. Template system for common setups
