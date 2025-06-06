/**
 * Plugin Name: Z Text Upfunker
 * Admin scripts
 * 
 * Author: Zodan
 * Author URI: https://zodan.nl
 */
(function ($) {
    'use strict';


    // Dynamically add custom menu items
    var ia_next_row;
    ia_next_row = parseInt($('.z-text-upfunker-btn-add-ia').attr('data-last')) + 1;
    console.log($('.z-text-upfunker-btn-add-ia').attr('data-last'));
    console.log('ia_next_row: ' + ia_next_row);

    $('.z-text-upfunker-btn-add-ia').click(function (e) {
        e.preventDefault();

        $('#no-funk').remove();

        var html  = '<div class="z-text-upfunker-ia-item" data-id="' + ia_next_row + '">';
            html += '<p><label>Selector(s)</label><input class="ztu-input" type="text" id="item[' + ia_next_row + '][elem]" name="z_text_upfunker_plugin_options[items][' + ia_next_row + '][elem]"></p>';
            html += '<p><label>Animation style</label><select class="ztu-input" id="item[' + ia_next_row + '][type]" name="z_text_upfunker_plugin_options[items][' + ia_next_row + '][type]">';
            
            var availableAnimTypes = zTextUpfunkerAdminParams.availableAnimTypes;

            for (var key in availableAnimTypes) {
                if (availableAnimTypes.hasOwnProperty(key)) {
                    var val = availableAnimTypes[key];
                    html += '<option value="'+ key +'">'+ val +'</option>';
                }
            }

            html += '</select></p>';
            html += '<p><label>Max. loops</label><input class="ztu-input" type="number" id="item[' + ia_next_row + '][cycles]" name="z_text_upfunker_plugin_options[items][' + ia_next_row + '][cycles]"></p>';
            html += '<div class="z-mini-menu-btn-remove-ia">-</div></div>';
		
        $(this).before(html);

        ia_next_row++;
    });

    // Remove custom menu items
    $(document).on('click', '.z-text-upfunker-btn-remove-ia', function (e) {
        e.preventDefault();
        $(this).parent().remove();
    });

})(jQuery);