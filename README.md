# Garo Text Preview Plugin

A WordPress/WooCommerce plugin for jewelry stores that allows customers to customize their jewelry with text engraving and preview their customizations in real-time.

## Features

- **Font Management**: Add and manage custom fonts via URLs (Google Fonts or custom font files)
- **Product-Level Customization**: Define multiple text fields per product with individual settings
- **Font Selection**: Choose one font per product that applies to all text fields
- **Character Limits**: Set minimum and maximum character limits per text field
- **Additional Pricing**: Add extra costs per customization field
- **Image Positioning**: Define X/Y coordinates for text placement on product images
- **Image Upload**: Allow customers to upload custom images
- **Global Fields**: Create text fields that apply to all products or specific product categories
- **Cart Integration**: Customizations are saved with cart items and displayed throughout checkout
- **Order Management**: View all customizations in the admin order details

## Installation

1. Upload the `garo-text-preview-plugin` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Ensure WooCommerce is installed and activated
4. Navigate to "Text Preview" in the WordPress admin menu to configure settings

## Configuration

### Global Settings

1. Go to **Text Preview** → **Settings** in WordPress admin
2. **Add Fonts**:
   - Click "Add Font"
   - Enter font name (e.g., "Roboto")
   - Enter font URL (e.g., Google Fonts link or custom font file URL)
   - Repeat for multiple fonts
3. **Set Default Font**: Select which font should be used by default
4. **Global Fields** (Optional):
   - Create fields that appear on all products or specific categories
   - Choose field type (text or textarea)
   - Select "All Products" or "Specific Categories"
   - If specific categories, select which ones

### Product-Level Settings

1. Edit any WooCommerce product
2. Find the **Text Preview Customization** meta box
3. Check "Enable text preview customization for this product"
4. **Select Font**: Choose the font for this product's text fields
5. **Add Custom Text Fields**:
   - Click "Add Text Field"
   - Set field label (e.g., "Engraved Name")
   - Add placeholder text
   - Set minimum/maximum character limits
   - Add additional price if needed
   - Set X/Y coordinates for text placement on image
   - Mark as required if needed
6. **Image Upload**: Enable if customers should be able to upload images
7. Save the product

## Usage

### For Customers

1. Navigate to a product with text customization enabled
2. Fill in the customization fields
3. Text preview updates in real-time with the selected font
4. Upload an image if enabled
5. Add to cart - customizations are saved
6. View customizations in cart, checkout, and order confirmation

### For Store Admins

1. View orders in WooCommerce
2. See all customizations in the order details
3. Each customization shows:
   - Field label
   - Customer's text
   - Additional price charged
   - Font used
   - Position coordinates

## Requirements

- WordPress 5.8 or higher
- PHP 7.4 or higher
- WooCommerce 5.0 or higher

## Font Setup Examples

### Google Fonts
- Name: `Roboto`
- URL: `https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap`

### Custom Font Files
- Name: `MyCustomFont`
- URL: `https://yoursite.com/wp-content/uploads/fonts/mycustomfont.woff2`

## File Structure

```
garo-text-preview-plugin/
├── assets/
│   ├── css/
│   │   ├── admin.css
│   │   └── frontend.css
│   └── js/
│       ├── admin.js
│       └── frontend.js
├── includes/
│   ├── class-garo-admin.php
│   ├── class-garo-product-fields.php
│   ├── class-garo-frontend.php
│   └── class-garo-cart.php
├── garo-text-preview.php
└── README.md
```

## Hooks and Filters

The plugin provides several hooks for developers:

### Actions
- `garo_before_customization_fields` - Before customization fields are displayed
- `garo_after_customization_fields` - After customization fields are displayed

### Filters
- `garo_custom_field_validation` - Filter field validation
- `garo_additional_price_calculation` - Modify price calculations
- `garo_font_list` - Modify available fonts list

## Support

For issues, feature requests, or contributions, please visit the [GitHub repository](https://github.com/kroumieh1993/garo-text-preview-plugin).

## License

GPL v2 or later

## Changelog

### 1.0.0
- Initial release
- Font management system
- Product-level text field customization
- Character limit validation
- Additional pricing per field
- Image upload functionality
- Global fields for all products or categories
- Cart and checkout integration
- Order management features
