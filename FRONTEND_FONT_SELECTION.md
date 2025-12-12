# Frontend Font Selection - Feature Update

## Overview

The plugin now allows customers to select fonts on the frontend (product page) rather than having a fixed font set by the admin.

## How It Works

### Backend (Admin Setup)

**Step 1: Add Fonts**
- Go to Text Preview → Settings
- Add fonts (Google Fonts or custom URLs)
- Set a global default font (optional)

**Step 2: Configure Product Fields**
- Edit a product
- Enable text preview customization
- Add text fields with:
  - Field Label (e.g., "Your Name")
  - Placeholder (e.g., "Enter your name")
  - Min/Max characters
  - Additional price
  - **Default Font** (dropdown) - This is the initial font shown to customers
  - Position coordinates
  - Required checkbox

**Example Backend Configuration:**
```
Field 1:
- Label: "Engraved Name"
- Placeholder: "Your name"
- Default Font: "Playfair Display"
- Min: 2, Max: 20
- Price: $15
```

---

### Frontend (Customer Experience)

**What Customers See:**

1. **Text Input Field**
   - Label: "Engraved Name"
   - Input box with placeholder text
   - Character limits shown below

2. **Font Selector Dropdown**
   - Label: "Select Font:"
   - Dropdown showing all available fonts
   - Pre-selected to the default font set by admin

3. **Real-Time Preview**
   - Text updates as customer types
   - Font changes when customer selects different font
   - Preview shows exact appearance

**Customer Workflow:**
```
1. Customer sees field labeled "Engraved Name"
2. Default font is "Playfair Display" (set by admin)
3. Customer types: "Sarah"
4. Customer can change font to "Roboto" using dropdown
5. Text input and preview update with new font
6. Customer adds to cart
```

---

### Cart & Checkout Display

**In Cart:**
```
Product: Gold Necklace - $100
Customizations:
- Engraved Name: Sarah (Font: Roboto) (+$15)

Total: $115
```

**In Checkout:**
- Same display as cart
- Font information carried through

**In Order (Admin View):**
```
Customizations:
- Engraved Name: Sarah (Font: Roboto) (+$15.00)

Additional Meta (hidden):
- Text: Sarah
- Font: Roboto
- Position: X=100, Y=200
- Price: $15
```

---

## User Interface Elements

### Product Page Structure

```html
<div class="garo-customization-container">
    <h3>Customize Your Product</h3>
    
    <!-- Field 1 -->
    <div class="garo-field-group">
        <label>Engraved Name *</label>
        <input type="text" class="garo-custom-field" placeholder="Your name">
        
        <div class="garo-font-selector">
            <label>Select Font:</label>
            <select class="garo-font-dropdown">
                <option value="Playfair Display" selected>Playfair Display</option>
                <option value="Roboto">Roboto</option>
                <option value="Montserrat">Montserrat</option>
            </select>
        </div>
        
        <small>Between 2 and 20 characters</small>
    </div>
    
    <!-- Field 2 -->
    <div class="garo-field-group">
        <!-- Similar structure -->
    </div>
    
    <!-- Preview -->
    <div class="garo-preview-container">
        <h4>Preview</h4>
        <div id="garo-text-preview">
            <!-- Live preview with selected fonts -->
        </div>
    </div>
</div>
```

---

## Technical Implementation

### Data Flow

**1. Admin Configuration:**
```php
// Stored in product meta
_garo_custom_fields = [
    [
        'label' => 'Engraved Name',
        'default_font' => 'Playfair Display',
        'min_chars' => 2,
        'max_chars' => 20,
        'additional_price' => 15,
        'position_x' => 100,
        'position_y' => 200,
        'required' => true
    ]
]
```

**2. Frontend Display:**
```html
<!-- Text input -->
<input name="garo_custom_text[0]" value="">

<!-- Font selector (NEW) -->
<select name="garo_custom_font[0]">
    <option value="Playfair Display" selected>Playfair Display</option>
    <option value="Roboto">Roboto</option>
</select>

<!-- Hidden data -->
<input type="hidden" name="garo_field_data[0][label]" value="Engraved Name">
<input type="hidden" name="garo_field_data[0][price]" value="15">
```

**3. Customer Submission:**
```php
$_POST = [
    'garo_custom_text' => [
        0 => 'Sarah'
    ],
    'garo_custom_font' => [  // NEW
        0 => 'Roboto'
    ],
    'garo_field_data' => [
        0 => [
            'label' => 'Engraved Name',
            'price' => 15,
            'position_x' => 100,
            'position_y' => 200
        ]
    ]
]
```

**4. Cart Data:**
```php
$cart_item_data = [
    'garo_customizations' => [
        [
            'label' => 'Engraved Name',
            'text' => 'Sarah',
            'font' => 'Roboto',  // Customer's selection
            'price' => 15,
            'position_x' => 100,
            'position_y' => 200
        ]
    ],
    'garo_additional_price' => 15
]
```

**5. Order Meta:**
```php
// Visible meta
'Engraved Name' => 'Sarah (Font: Roboto) (+$15.00)'

// Hidden meta (for production use)
'_garo_customization_engraved_name' => [
    'text' => 'Sarah',
    'font' => 'Roboto',
    'price' => 15,
    'position_x' => 100,
    'position_y' => 200
]
```

---

## JavaScript Functionality

**Font Selection Handler:**
```javascript
$('.garo-font-dropdown').on('change', function() {
    var fieldIndex = $(this).data('field-index');
    var selectedFont = $(this).val();
    var $textField = $('#garo_field_' + fieldIndex);
    
    // Apply font to text field
    $textField.css('font-family', selectedFont);
    
    // Update preview
    updatePreview();
});
```

**Preview Update:**
```javascript
function updatePreview() {
    $('.garo-custom-field').each(function() {
        var fieldIndex = $(this).data('field-index');
        var text = $(this).val();
        var font = $('#garo_font_' + fieldIndex).val();
        
        // Create preview with selected font
        var $preview = $('<div>').text(text);
        $preview.css('font-family', font);
        $('#garo-text-preview').append($preview);
    });
}
```

---

## Key Differences from Previous Implementation

| Aspect | Before | After |
|--------|--------|-------|
| **Font Selection** | Backend (admin) | Frontend (customer) |
| **Font Scope** | One per product | One per field |
| **Customer Control** | None | Full control |
| **Admin Role** | Select final font | Set default font only |
| **Cart Display** | Text only | Text + Font name |
| **Order Info** | Text + hidden font | Text + Font displayed |

---

## Use Cases

### Example 1: Wedding Ring
**Admin Setup:**
- Field: "Inside Inscription"
- Default Font: "Elegant Script"
- Fonts Available: Elegant Script, Modern Sans, Classic Serif

**Customer:**
- Types: "Forever & Always"
- Prefers: "Classic Serif" (changes from default)
- Sees preview in Classic Serif
- Adds to cart

**Result:**
- Cart shows: "Forever & Always (Font: Classic Serif)"
- Order saved with font choice

### Example 2: Dog Tag
**Admin Setup:**
- Field 1: "Pet Name" (Default: Roboto)
- Field 2: "Phone Number" (Default: Montserrat)

**Customer:**
- Field 1: "Max" using "Roboto" (keeps default)
- Field 2: "555-1234" using "Roboto" (changes from Montserrat)

**Result:**
- Cart shows both fields with their selected fonts
- Customer can choose same or different fonts per field

---

## Benefits

### For Store Owners:
- ✅ Flexible default fonts per field
- ✅ Customer empowerment increases satisfaction
- ✅ No need to guess customer font preferences
- ✅ All font choices recorded for production

### For Customers:
- ✅ Visual control over appearance
- ✅ See exactly what they'll get
- ✅ Change fonts until satisfied
- ✅ Real-time preview with selected fonts

### For Production:
- ✅ Clear font specifications per item
- ✅ Text and font saved in order
- ✅ Position coordinates available
- ✅ All data for accurate production

---

## Configuration Examples

### Jewelry Store - Name Necklace
```
Product: Gold Name Necklace

Field Configuration:
- Label: "Name for Necklace"
- Default Font: "Elegant Script"
- Available Fonts: Elegant Script, Cursive Style, Block Letters
- Min: 2, Max: 15 characters
- Price: +$25

Customer selects font + types name on product page
```

### Gift Shop - Custom Mug
```
Product: Ceramic Mug

Field 1:
- Label: "Main Text"
- Default Font: "Bold Sans"
- Price: +$10

Field 2:
- Label: "Subtitle"  
- Default Font: "Light Script"
- Price: +$5

Customer can choose different fonts for each line
```

---

## Summary

The plugin now provides a complete frontend font selection experience:

1. **Admin** sets up fields with default fonts
2. **Customer** sees and selects fonts on product page
3. **System** saves font choices to cart/orders
4. **Everyone** sees font information throughout checkout

This gives customers control while maintaining admin structure and ensuring all data is captured for production.
