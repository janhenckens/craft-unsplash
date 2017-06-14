jQuery(document).ready(function($) {
    var container = $('#splashing_images');
    $.LoadingOverlaySetup({
        color           : "rgba(241,241,241,0.7)",
        maxSize         : "80px",
        minSize         : "20px",
        resizeInterval  : 0,
        size            : "30%"
    });

    $('a.upload').click(function(e){
        var element = $(this);
        var image = element.find('img');
        // If not saving, then proceed
        if(!element.hasClass('saving')){
            element.addClass('saving');
            e.preventDefault();
            var payload = { source : $(this).data('source'), author: $(this).data('author'), credit: $(this).data('credit')};
            payload[window.csrfTokenName] = window.csrfTokenValue;
            $.ajax({
                type: 'POST',
                url: Craft.getActionUrl('unsplash/save'),
                dataType: 'JSON',
                data: payload,
                beforeSend: function() {
                    console.log('Submitting image for download');
                    image.LoadingOverlay("show");
                },
                success: function(response) {
                    console.log(response);
                    image.LoadingOverlay("hide");
                    var checkmark = '<svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52"><circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none"/><path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/></svg>';
                    element.append(checkmark);
                    setTimeout(function() {
                        element.children('svg.checkmark').remove();
                    }, 1400);
                },
                error: function(xhr, status, error) {
                    console.log(error);
                }
            });
        };
    });
});