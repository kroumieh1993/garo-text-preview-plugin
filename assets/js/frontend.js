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
