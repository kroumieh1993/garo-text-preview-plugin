/**
 * Frontend JavaScript for Garo Text Preview Plugin
 */

jQuery(document).ready(function($) {
    'use strict';
    
    var $customizationContainer = $('.garo-customization-container');
    
    if (!$customizationContainer.length) {
        return;
    }
    
    // Create preview overlay on product image
    function createPreviewOverlay() {
        // Check if preview fields data exists
        if (typeof garoPreviewFields === 'undefined' || !garoPreviewFields.length) {
            return;
        }
        
        // Wait for gallery to be available
        var $gallery = $('.woocommerce-product-gallery');
        if (!$gallery.length) {
            // Try alternative selectors
            $gallery = $('.product .images, .single-product .images, div.images');
        }
        
        if (!$gallery.length) {
            console.log('Garo: Product gallery not found');
            return;
        }
        
        // Check if overlay already exists
        if ($('#garo-image-preview-overlay').length) {
            return;
        }
        
        // Make gallery relative positioned
        $gallery.css('position', 'relative');
        
        // Create overlay container
        var $overlay = $('<div id="garo-image-preview-overlay"></div>');
        $overlay.css({
            'position': 'absolute',
            'top': '0',
            'left': '0',
            'width': '100%',
            'height': '100%',
            'pointer-events': 'none',
            'z-index': '100'
        });
        
        // Create preview text elements
        $.each(garoPreviewFields, function(i, field) {
            var $previewText = $('<div></div>');
            $previewText.attr({
                'class': 'garo-preview-text',
                'id': 'garo_preview_' + field.index,
                'data-field-index': field.index
            });
            $previewText.css({
                'position': 'absolute',
                'left': field.position_x + 'px',
                'top': field.position_y + 'px',
                'font-family': field.default_font,
                'font-size': '24px',
                'color': '#333',
                'font-weight': 'bold',
                'text-shadow': '0 0 2px rgba(255,255,255,0.8)',
                'pointer-events': 'none',
                'white-space': 'nowrap',
                'display': 'none'
            });
            $overlay.append($previewText);
        });
        
        // Append overlay to gallery
        $gallery.append($overlay);
        
        console.log('Garo: Preview overlay created successfully');
    }
    
    // Initialize preview overlay
    setTimeout(function() {
        createPreviewOverlay();
    }, 100);
    
    // Try again after a delay in case gallery loads late
    setTimeout(function() {
        if (!$('#garo-image-preview-overlay').length) {
            createPreviewOverlay();
        }
    }, 1000);
    
    // Handle font selection changes
    $('.garo-font-dropdown').on('change', function() {
        var fieldIndex = $(this).data('field-index');
        var selectedFont = $(this).val();
        var $textField = $('#garo_field_' + fieldIndex);
        var $previewText = $('#garo_preview_' + fieldIndex);
        
        // Apply font to the text field
        if (selectedFont) {
            $textField.css('font-family', selectedFont);
            $previewText.css('font-family', selectedFont);
        }
        
        updatePreview();
    });
    
    // Initialize fonts on page load
    $('.garo-font-dropdown').each(function() {
        var fieldIndex = $(this).data('field-index');
        var selectedFont = $(this).val();
        var $textField = $('#garo_field_' + fieldIndex);
        var $previewText = $('#garo_preview_' + fieldIndex);
        
        if (selectedFont) {
            $textField.css('font-family', selectedFont);
            $previewText.css('font-family', selectedFont);
        }
    });
    
    // Real-time character count validation and preview update
    $('.garo-custom-field').on('input', function() {
        validateField($(this));
        updatePreview();
    });
    
    // Validate field
    function validateField($field) {
        var value = $field.val();
        var min = parseInt($field.data('min')) || 0;
        var max = parseInt($field.data('max')) || 0;
        var required = $field.data('required') === 1;
        var isValid = true;
        var errorMsg = '';
        
        // Remove previous error
        $field.removeClass('error');
        $field.next('.garo-error-message').remove();
        
        // Check required
        if (required && value.length === 0) {
            isValid = false;
            errorMsg = 'This field is required.';
        }
        
        // Check min length
        if (value.length > 0 && min > 0 && value.length < min) {
            isValid = false;
            errorMsg = 'Minimum ' + min + ' characters required.';
        }
        
        // Note: max length is enforced by maxlength attribute
        
        if (!isValid) {
            $field.addClass('error');
            $field.after('<span class="garo-error-message show">' + errorMsg + '</span>');
        }
        
        return isValid;
    }
    
    // Update preview on product image
    function updatePreview() {
        $('.garo-custom-field').each(function() {
            var $field = $(this);
            var value = $field.val();
            var fieldIndex = $field.data('field-index');
            var $previewText = $('#garo_preview_' + fieldIndex);
            var $fontDropdown = $('#garo_font_' + fieldIndex);
            var font = $fontDropdown.val();
            
            if (value) {
                $previewText.text(value);
                if (font) {
                    $previewText.css('font-family', font);
                }
                $previewText.show();
            } else {
                $previewText.hide();
            }
        });
    }
    
    // Validate before add to cart
    $('form.cart').on('submit', function(e) {
        var isValid = true;
        
        $('.garo-custom-field').each(function() {
            if (!validateField($(this))) {
                isValid = false;
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            $('html, body').animate({
                scrollTop: $('.garo-customization-container').offset().top - 100
            }, 500);
            return false;
        }
    });
    
    // Handle image upload preview
    $('#garo_custom_image').on('change', function(e) {
        var file = e.target.files[0];
        
        if (file) {
            // Validate file type
            if (!file.type.match('image.*')) {
                alert('Please select a valid image file.');
                $(this).val('');
                return;
            }
            
            // Validate file size (5MB max)
            var maxSize = 5 * 1024 * 1024; // 5MB
            if (file.size > maxSize) {
                alert('Image file is too large. Maximum size is 5MB.');
                $(this).val('');
                return;
            }
            
            // Show preview
            var reader = new FileReader();
            reader.onload = function(e) {
                var $preview = $('.garo-image-preview');
                
                if (!$preview.length) {
                    $preview = $('<div class="garo-image-preview" style="margin-top: 10px;"></div>');
                    $('#garo_custom_image').after($preview);
                }
                
                $preview.html('<img src="' + e.target.result + '" style="max-width: 150px; max-height: 150px; border: 1px solid #ddd; border-radius: 3px;">');
            };
            reader.readAsDataURL(file);
        }
    });
    
    // Initialize
    updatePreview();
});
