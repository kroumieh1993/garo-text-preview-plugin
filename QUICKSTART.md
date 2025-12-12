# Quick Start Guide

Get your text preview plugin up and running in 5 minutes!

## Step 1: Install (2 minutes)

### Option A: Manual Installation
1. Download all files from this repository
2. Upload to `/wp-content/plugins/garo-text-preview-plugin/`
3. Go to WordPress Admin → Plugins
4. Find "Garo Text Preview Plugin"
5. Click "Activate"

### Option B: Direct Upload
1. Zip the `garo-text-preview-plugin` folder
2. Go to WordPress Admin → Plugins → Add New
3. Click "Upload Plugin"
4. Choose the zip file
5. Click "Install Now" then "Activate"

✅ **Done!** Plugin is now active.

---

## Step 2: Add Fonts (1 minute)

1. Go to **WordPress Admin → Text Preview**
2. Click **"Add Font"**
3. Enter a font name and URL:

### Example 1: Google Font - Elegant Script
```
Name: Playfair Display
URL: https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap
```

### Example 2: Google Font - Modern Sans
```
Name: Roboto
URL: https://fonts.googleapis.com/css2?family=Roboto&display=swap
```

### Example 3: Custom Font
```
Name: My Custom Font
URL: https://yoursite.com/wp-content/uploads/fonts/myfont.woff2
```

4. Select one as **Default Font**
5. Click **"Save Settings"**

✅ **Done!** Fonts are ready.

---

## Step 3: Configure a Product (2 minutes)

### Basic Example: Simple Name Engraving

1. Go to **Products** → Edit any product
2. Find **"Text Preview Customization"** box (below product description)
3. Check ☑ **"Enable text preview customization"**
4. Select a **Font** (e.g., "Playfair Display")
5. Click **"Add Text Field"**
6. Fill in:
   - **Field Label:** `Your Name`
   - **Placeholder:** `Enter your name`
   - **Min Characters:** `2`
   - **Max Characters:** `20`
   - **Additional Price:** `15.00`
   - **Position X:** `100`
   - **Position Y:** `200`
   - Check ☑ **Required**
7. Click **"Update"** to save

✅ **Done!** Product is ready.

---

## Step 4: Test It! (1 minute)

1. Visit the product page on your site
2. You'll see the customization fields above "Add to Cart"
3. Type something in the field
4. See it preview with your selected font
5. Add to cart
6. View cart - see your customization and price
7. Complete checkout (or just test)

✅ **Success!** Everything works!

---

## Real-World Examples

### Example 1: Gold Necklace with Front Engraving

**Product:** Gold Heart Necklace ($100)

**Configuration:**
- Font: "Playfair Display"
- Field 1: "Front Engraving"
  - Min: 1, Max: 10
  - Price: +$25
  - Position: X=150, Y=250
  - Required: Yes

**Customer Experience:**
- Customer sees: "Your Name" field
- Types: "Sarah"
- Sees preview in elegant script
- Adds to cart
- Total: $125 (base $100 + engraving $25)

---

### Example 2: Ring with Inside Inscription

**Product:** Silver Ring ($75)

**Configuration:**
- Font: "Roboto"
- Field 1: "Inside Inscription"
  - Min: 0, Max: 15
  - Price: +$20
  - Position: X=200, Y=100
  - Required: No

**Customer Experience:**
- Customer sees optional field
- Types: "Forever & Always"
- Adds to cart
- Total: $95

---

### Example 3: Bracelet with Multiple Engravings

**Product:** Custom Bracelet ($120)

**Configuration:**
- Font: "Playfair Display"
- Field 1: "First Name"
  - Min: 2, Max: 10
  - Price: +$15
  - Required: Yes
- Field 2: "Date (Optional)"
  - Min: 0, Max: 10
  - Price: +$10
  - Required: No
- Image Upload: Enabled

**Customer Experience:**
- Types name: "Emma"
- Types date: "12/25/2024"
- Uploads custom design image
- Total: $145 (base + $15 + $10)

---

## Advanced: Global Fields

Want a field on ALL jewelry products?

1. Go to **Text Preview → Settings**
2. Scroll to **"Global Fields"**
3. Click **"Add Global Field"**
4. Configure:
   - **Label:** `Special Message`
   - **Type:** Text Field
   - **Apply To:** All Products
5. Save

Now every product has this field automatically!

### Category-Specific Example

Want a field only on rings?

1. Add Global Field
2. Set **"Apply To"** → **"Specific Categories"**
3. Select **"Rings"** category
4. Save

Now all ring products have this field!

---

## Troubleshooting

### Problem: Fonts not showing
**Solution:** 
- Check font URL is correct
- For Google Fonts, use the full `<link>` URL
- Clear browser cache

### Problem: Customization not saving
**Solution:**
- Ensure WooCommerce is active
- Check product is updated/saved
- Clear WordPress cache

### Problem: Price not adding
**Solution:**
- Check "Additional Price" has value (e.g., 15.00)
- Use decimal point, not comma
- Customer must fill the field

### Problem: Plugin not appearing
**Solution:**
- Ensure WooCommerce is installed first
- Activate plugin after WooCommerce
- Check PHP version is 7.4+

---

## Pro Tips

### Tip 1: Use Clear Labels
❌ Bad: "Text 1"
✅ Good: "Your Name for Engraving"

### Tip 2: Set Reasonable Limits
- Names: 10-20 characters
- Short messages: 20-30 characters
- Long inscriptions: 50-100 characters

### Tip 3: Test Before Launch
- Test with different text lengths
- Test required vs optional fields
- Test price calculations
- Test on mobile devices

### Tip 4: Positioning
- X = horizontal (left to right)
- Y = vertical (top to bottom)
- Measure in pixels from top-left corner
- Use browser inspector to find exact positions

### Tip 5: Pricing Strategy
- Simple engravings: $10-25
- Complex engravings: $25-50
- Image uploads: $15-30
- Multiple fields: Discount on bundle

---

## Common Use Cases

### 1. Name Necklaces
- 1 field, 2-15 characters
- Required, $20-30
- Elegant script font

### 2. Wedding Bands
- 2 fields (inside + outside)
- Optional, $15-25 each
- Classic serif font

### 3. Charm Bracelets
- 1 field + image upload
- 5-20 characters, $15
- Modern sans font

### 4. Dog Tags
- 2 fields (name + phone)
- Both required, $10 each
- Block letters font

### 5. Photo Lockets
- Image upload only
- No text fields
- $20 for custom photo

---

## Next Steps

### Learn More
- Read `README.md` for complete documentation
- Check `INSTALLATION.md` for detailed setup
- See `REQUIREMENTS_VERIFICATION.md` for all features
- Review `ARCHITECTURE.md` for technical details

### Get Help
- Check existing GitHub issues
- Create new issue with details
- Include error messages
- Describe expected vs actual behavior

### Customize
- Modify CSS in `assets/css/`
- Extend JavaScript in `assets/js/`
- Add hooks in class files
- Follow WordPress coding standards

---

## Success Checklist

Before going live:

- [ ] Fonts load correctly
- [ ] All products configured
- [ ] Pricing is accurate
- [ ] Validation works
- [ ] Cart displays customizations
- [ ] Orders save customizations
- [ ] Mobile responsive
- [ ] Error handling works
- [ ] Customer can upload images
- [ ] Admin can see all data

---

## Support

Need help? Have questions?

1. Check the documentation files
2. Review this Quick Start again
3. Search existing issues on GitHub
4. Create new issue if needed

---

**Congratulations!** 🎉

Your jewelry customization plugin is ready. Happy selling!
