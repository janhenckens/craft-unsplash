jQuery(document).ready(function($) {
    var container = $('#splashing_images');
    $.LoadingOverlaySetup({
        color           : "rgba(241,241,241,0.5)",
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
                url: Craft.getActionUrl('unsplash/download/save'),
                dataType: 'JSON',
                data: payload,
                beforeSend: function() {
                    image.LoadingOverlay("show");
                },
                success: function(response) {
                    image.LoadingOverlay("hide");
                    Craft.cp.displayNotice(Craft.t('Image saved!'));

                },
                error: function(xhr, status, error) {
                    image.LoadingOverlay("hide");
                    Craft.cp.displayError(Craft.t('Oops, something went wrong!'));
                }
            });
        };
    });
});