/**
 * Admin JavaScript for Garo Text Preview Plugin
 */

jQuery(document).ready(function($) {
    'use strict';
    
    var fontIndex = $('.garo-font-row').length;
    var globalFieldIndex = $('.garo-global-field-row').length;
    var customFieldIndex = $('.garo-custom-field-row').length;
    
    // Add font
    $('#garo-add-font').on('click', function(e) {
        e.preventDefault();
        var template = $('#garo-font-row-template').html();
        template = template.replace(/\{\{INDEX\}\}/g, fontIndex);
        $('#garo-fonts-container').append(template);
        fontIndex++;
    });
    
    // Remove font
    $(document).on('click', '.garo-remove-font', function(e) {
        e.preventDefault();
        $(this).closest('.garo-font-row').remove();
    });
    
    // Add global field
    $('#garo-add-global-field').on('click', function(e) {
        e.preventDefault();
        var template = $('#garo-global-field-row-template').html();
        template = template.replace(/\{\{INDEX\}\}/g, globalFieldIndex);
        $('#garo-global-fields-container').append(template);
        globalFieldIndex++;
    });
    
    // Remove global field
    $(document).on('click', '.garo-remove-global-field', function(e) {
        e.preventDefault();
        $(this).closest('.garo-global-field-row').remove();
    });
    
    // Toggle categories field visibility
    $(document).on('change', '.garo-apply-to', function() {
        var $row = $(this).closest('.garo-global-field-row');
        var $categoriesField = $row.find('.garo-categories-field');
        
        if ($(this).val() === 'categories') {
            $categoriesField.show();
        } else {
            $categoriesField.hide();
        }
    });
    
    // Add custom field (product level)
    $('#garo-add-custom-field').on('click', function(e) {
        e.preventDefault();
        var template = $('#garo-custom-field-template').html();
        template = template.replace(/\{\{INDEX\}\}/g, customFieldIndex);
        $('#garo-custom-fields-container').append(template);
        customFieldIndex++;
    });
    
    // Remove custom field
    $(document).on('click', '.garo-remove-custom-field', function(e) {
        e.preventDefault();
        $(this).closest('.garo-custom-field-row').remove();
    });
    
    // Toggle customization fields visibility
    $('input[name="garo_text_preview_enabled"]').on('change', function() {
        if ($(this).is(':checked')) {
            $('#garo-product-customization').slideDown();
        } else {
            $('#garo-product-customization').slideUp();
        }
    });
    
    // Update default font dropdown when fonts are added/removed
    $(document).on('input', 'input[name*="[name]"]', function() {
        updateDefaultFontDropdown();
    });
    
    function updateDefaultFontDropdown() {
        var $defaultFontSelect = $('#garo-default-font');
        var currentValue = $defaultFontSelect.val();
        var fonts = [];
        
        // Collect all font names
        $('input[name*="garo_fonts"][name*="[name]"]').each(function() {
            var fontName = $(this).val().trim();
            if (fontName) {
                fonts.push(fontName);
            }
        });
        
        // Rebuild dropdown
        $defaultFontSelect.empty();
        $defaultFontSelect.append('<option value="">Select Default Font</option>');
        
        $.each(fonts, function(index, fontName) {
            var selected = (fontName === currentValue) ? 'selected' : '';
            $defaultFontSelect.append('<option value="' + fontName + '" ' + selected + '>' + fontName + '</option>');
        });
    }
    
    // Initialize
    updateDefaultFontDropdown();
});
