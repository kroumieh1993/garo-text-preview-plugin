/**
 * Frontend JavaScript for Garo Text Preview Plugin
 */

jQuery(document).ready(function($) {
    'use strict';
    
    var $customizationContainer = $('.garo-customization-container');
    
    if (!$customizationContainer.length) {
        return;
    }
    
    // Handle font selection changes
    $('.garo-font-dropdown').on('change', function() {
        var fieldIndex = $(this).data('field-index');
        var selectedFont = $(this).val();
        var $textField = $('#garo_field_' + fieldIndex);
        
        // Apply font to the text field
        if (selectedFont) {
            $textField.css('font-family', selectedFont);
        }
        
        updatePreview();
    });
    
    // Initialize fonts on page load
    $('.garo-font-dropdown').each(function() {
        var fieldIndex = $(this).data('field-index');
        var selectedFont = $(this).val();
        var $textField = $('#garo_field_' + fieldIndex);
        
        if (selectedFont) {
            $textField.css('font-family', selectedFont);
        }
    });
    
    // Real-time character count validation
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
        
        // Check max length
        if (max > 0 && value.length > max) {
            isValid = false;
            errorMsg = 'Maximum ' + max + ' characters allowed.';
        }
        
        if (!isValid) {
            $field.addClass('error');
            $field.after('<span class="garo-error-message show">' + errorMsg + '</span>');
        }
        
        return isValid;
    }
    
    // Update preview
    function updatePreview() {
        var $preview = $('#garo-text-preview');
        var $previewContainer = $('.garo-preview-container');
        var hasContent = false;
        
        $preview.empty();
        
        $('.garo-custom-field').each(function() {
            var $field = $(this);
            var value = $field.val();
            var fieldIndex = $field.data('field-index');
            var $fontDropdown = $('#garo_font_' + fieldIndex);
            var font = $fontDropdown.val();
            
            if (value) {
                hasContent = true;
                var $previewText = $('<div class="garo-preview-text"></div>');
                $previewText.text(value);
                
                if (font) {
                    $previewText.css('font-family', font);
                }
                
                $previewText.css({
                    'margin-bottom': '10px',
                    'padding': '10px',
                    'background': '#fff',
                    'border': '1px solid #ddd',
                    'border-radius': '4px',
                    'font-size': '18px'
                });
                
                $preview.append($previewText);
            }
        });
        
        if (hasContent) {
            $previewContainer.show();
        } else {
            $previewContainer.hide();
        }
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
                
                $preview.html('<img src="' + e.target.result + '" style="max-width: 200px; max-height: 200px; border: 1px solid #ddd; border-radius: 4px;">');
            };
            reader.readAsDataURL(file);
        }
    });
    
    // Calculate and display total price with customizations
    function updateTotalPrice() {
        var additionalPrice = 0;
        
        $('.garo-custom-field').each(function() {
            var value = $(this).val();
            var price = parseFloat($(this).data('price')) || 0;
            
            if (value && value.length > 0 && price > 0) {
                additionalPrice += price;
            }
        });
        
        if (additionalPrice > 0) {
            var $priceDisplay = $('.garo-total-additional-price');
            
            if (!$priceDisplay.length) {
                $priceDisplay = $('<div class="garo-total-additional-price" style="margin-top: 15px; padding: 10px; background: #fff; border: 1px solid #ddd; border-radius: 4px;"></div>');
                $customizationContainer.append($priceDisplay);
            }
            
            $priceDisplay.html('<strong>Customization Cost:</strong> ' + formatPrice(additionalPrice));
            $priceDisplay.show();
        } else {
            $('.garo-total-additional-price').hide();
        }
    }
    
    function formatPrice(price) {
        // This is a simple formatter - WordPress will handle actual formatting
        return '$' + price.toFixed(2);
    }
    
    // Update price when fields change
    $('.garo-custom-field').on('input', function() {
        updateTotalPrice();
    });
    
    // Initialize
    updatePreview();
    updateTotalPrice();
});
