jQuery(function($) {

    // Initialize variables
    var custom_uploader;
    var input = '#woocommerce_jp_gcash_manual_gcash_qr';
    var buttonText = 'Select QR';

    // Ensure the input element exists before continuing
    if ($(input).length) {
        
        var selected = $(input).val();
        
        $(input).wrap('<div class="jp-gcash-upload-fields">');
        $("<span id='prev_url_gcash_jp'></span>").insertBefore(input);
        
        if (selected) {
            buttonText = "Update QR";
            $('#prev_url_gcash_jp').html('Loading preview..')
            wp.media.attachment(selected).fetch().then(function (data) {
                $('#prev_url_gcash_jp').html('<img src="' + wp.media.attachment(selected).get('url') + '" />');
            });
        }

        $("<button type='button' class='button media-button select-mode-toggle-button' id='woocommerce_jp_gcash_manual_gcash_qr_select'>" + buttonText + "</button>")
            .insertBefore(input);
    }

    // Event listener for the select button
    $('body').on('click', '#woocommerce_jp_gcash_manual_gcash_qr_select', function(e) {
        e.preventDefault();

        // Open the media uploader or create it if it doesn't exist
        if (custom_uploader) {
            custom_uploader.open();
            return;
        }

        custom_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Image',
            button: { text: 'Choose Image' },
            multiple: false // Assuming only one image can be selected
        });

        // Handle the selection of an image
        custom_uploader.on('select', function() {
            var attachment = custom_uploader.state().get('selection').first().toJSON();
            $(input).val(attachment.id).trigger('change');
            $('#woocommerce_jp_gcash_manual_gcash_qr_preview').hide();
            $('#prev_url_gcash_jp').html('<img src="' + attachment.url + '" />');
        });

        custom_uploader.open();
    });

});
