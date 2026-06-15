jQuery(function($) {
    // Character count for meta title
    $('[name="_aseo_meta_title"]').on('keyup', function() {
        var length = $(this).val().length;
        $('#title-length').text(length);
        if (length > 60) {
            $('#title-length').css('color', '#d32f2f');
        } else {
            $('#title-length').css('color', '#0073aa');
        }
    });
    
    // Character count for meta description
    $('[name="_aseo_meta_description"]').on('keyup', function() {
        var length = $(this).val().length;
        $('#desc-length').text(length);
        if (length > 160) {
            $('#desc-length').css('color', '#d32f2f');
        } else if (length < 120) {
            $('#desc-length').css('color', '#ff9800');
        } else {
            $('#desc-length').css('color', '#0073aa');
        }
    });
    
    // Initialize counts
    $('[name="_aseo_meta_title"]').trigger('keyup');
    $('[name="_aseo_meta_description"]').trigger('keyup');
});
