# Installation Guide

## Step 1: Install the Plugin

1. Download the plugin files or clone from GitHub
2. Upload to `/wp-content/plugins/garo-text-preview-plugin/`
3. Activate via WordPress admin → Plugins

## Step 2: Configure Global Settings

1. Navigate to **Text Preview** in the WordPress admin menu
2. Add your fonts:
   - Click "Add Font"
   - Example: Name: "Playfair Display", URL: "https://fonts.googleapis.com/css2?family=Playfair+Display&display=swap"
   - Add more fonts as needed
3. Select a default font from the dropdown
4. (Optional) Add global fields that appear on all products or specific categories
5. Click "Save Settings"

## Step 3: Configure a Product

1. Go to **Products** → Edit any product
2. Scroll to **Text Preview Customization** meta box
3. Check "Enable text preview customization for this product"
4. Select a font for this product
5. Add text fields:
   - Click "Add Text Field"
   - Fill in:
     - **Field Label**: e.g., "Engraved Name"
     - **Placeholder**: e.g., "Enter your name"
     - **Min Characters**: e.g., 2
     - **Max Characters**: e.g., 20
     - **Additional Price**: e.g., 10.00 (optional)
     - **Position X**: e.g., 100 (pixels from left)
     - **Position Y**: e.g., 50 (pixels from top)
     - **Required**: Check if mandatory
6. (Optional) Enable image upload
7. Update the product

## Step 4: Test on Frontend

1. Visit the product page
2. You'll see the customization fields above the "Add to Cart" button
3. Fill in text fields - they'll preview with the selected font
4. Add to cart
5. View cart - customizations are displayed with additional prices
6. Complete checkout
7. Check order in admin - all customizations are saved

## Example Configuration

### Jewelry Engraving Example

**Product**: Gold Necklace

**Fonts Setup**:
- Font 1: "Elegant Script" → Google Fonts URL
- Font 2: "Modern Sans" → Google Fonts URL
- Default: "Elegant Script"

**Product Fields**:
1. Field 1:
   - Label: "Front Engraving"
   - Placeholder: "Your name or message"
   - Min: 1, Max: 15
   - Price: $15.00
   - Position: X=100, Y=200
   - Required: Yes

2. Field 2:
   - Label: "Back Engraving"
   - Placeholder: "Optional message"
   - Min: 0, Max: 10
   - Price: $10.00
   - Position: X=100, Y=300
   - Required: No

**Image Upload**: Enabled (for custom designs)

## Troubleshooting

### Fonts Not Loading
- Check that font URLs are correct and accessible
- For Google Fonts, use the full embed URL
- For custom fonts, ensure files are uploaded and URLs are correct

### Customizations Not Saving
- Ensure you have enough PHP memory (at least 128MB)
- Check that WooCommerce is active and up to date
- Clear WordPress cache

### Price Not Adding
- Verify the additional price is set correctly in the field settings
- Check that the price is a valid number (use decimal point, not comma)
- Test with a simple product first

## Advanced Usage

### Adding Global Fields for Categories

1. In Text Preview settings, click "Add Global Field"
2. Set field label and type
3. Choose "Specific Categories"
4. Select the categories (e.g., "Rings", "Necklaces")
5. These fields will appear on all products in those categories

### Using Custom Fonts

1. Upload your font files (WOFF, WOFF2, TTF) to your server
2. Get the full URL (e.g., `https://yoursite.com/wp-content/uploads/fonts/myfont.woff2`)
3. Add to fonts in settings
4. The font will be available for selection on products

## Support

For issues or questions:
- Check the README.md for detailed documentation
- Visit the GitHub repository
- Contact support
