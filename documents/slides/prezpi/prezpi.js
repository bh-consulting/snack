$(document).keypress(function(event) {
    if ( event.which == 81 || event.which == 113) {
        if($('.maskQR').is(":visible"))
            $('.maskQR').hide();
        else
            $('.maskQR').show();

        if($('.divQR').is(":visible"))
            $('.divQR').hide();
        else
            $('.divQR').show();
    }
});
