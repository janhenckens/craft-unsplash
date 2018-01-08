jQuery(document).ready(function($) {
    var container = $('#splashing-container');
    $.LoadingOverlaySetup({
        color           : "rgba(241,241,241,0.5)",
        maxSize         : "80px",
        minSize         : "20px",
        resizeInterval  : 0,
        size            : "30%"
    });

    $('div.splashing img').click(function (e) {
        var element = $(this);
        // If not saving, then proceed
        if(!element.hasClass('saving')){
            element.addClass('saving');

            payload = {
                id: element.parent().data('id'),
                attr: element.parent().data('attr'),
            }
            payload[window.csrfTokenName] = window.csrfTokenValue;
            $.ajax({
                type: 'POST',
                url: Craft.getActionUrl('unsplash/download/save'),
                dataType: 'JSON',
                data: payload,
                beforeSend: function() {
                    element.LoadingOverlay("show");
                },
                success: function(response) {
                    element.LoadingOverlay("hide");
                    Craft.cp.displayNotice(Craft.t('Image saved!'));
                },
                error: function(xhr, status, error) {
                    element.LoadingOverlay("hide");
                    Craft.cp.displayError(Craft.t('Oops, something went wrong!'));
                }
            });
        };
    });
});
