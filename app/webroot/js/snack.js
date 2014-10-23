function toggleBlock(button) {
	$(button).next().slideToggle();
	$(button).find('i').toggleClass('icon-chevron-up');
	$(button).find('i').toggleClass('icon-chevron-down');
}

function countItems() {
    var n = $('input:checked[type=checkbox][id^=MultiSelection][value!=all]').length;
    var button = $('#modaldel .btn-danger');

    button.text(button.text().replace(/[0-9]+/, n));
}

function loading() {
    $("div.loading").html("<h2>Veuillez patienter ...</h2>");
}

function loading_from_sidebar() {
    $("div.loading_from_sidebar").html('<div class="col-md-4"></div><div class="col-md-6"><h2>Veuillez patienter ...</h2></div>');
}

$(window).resize(function() {
    if($(window).width() > 979) {
	if($('.mainmenu').hasClass('menuphone'))
	    $('.mainmenu').toggleClass('menuphone');

	if($('.maintopmenu').hasClass('menutopphone'))
	    $('.maintopmenu').toggleClass('menutopphone');

    } else {

	if(!$('.mainmenu').hasClass('menuphone'))
	    $('.mainmenu').toggleClass('menuphone');

	if(!$('.maintopmenu').hasClass('menutopphone'))
	    $('.maintopmenu').toggleClass('menutopphone');
    }
});

$(document).ready(function() {
    $('#rootwizard').bootstrapWizard({onTabShow: function(tab, navigation, index) {
        var $total = navigation.find('li').length;
        var $current = index+1;
        var $percent = ($current/$total) * 100;
        if($percent != 0){
            $('#rootwizard').find('.bar').css({width:$percent+'%'});
        }

        // If it's the last tab then hide the last button and show the finish instead
        if($current >= $total) {
            $('#rootwizard').find('.pager .next').hide();
            $('#rootwizard').find('.pager .finish').show();
            $('#rootwizard').find('.pager .finish').removeClass('disabled');
        } else {
            $('#rootwizard').find('.pager .next').show();
            $('#rootwizard').find('.pager .finish').hide();
        }

        $('html, body').animate({scrollTop: 0}, 500);
    }});

    $("input[type='password']").val('');

    document.onkeydown = Tastendruck;
});

function refreshCode(){   
    if(document.getElementById("livelogs") !== null) {
        var newURL=window.location.protocol + "//" + window.location.host;
        var pathArray = document.URL.split( '/' );
        newURL+="/loglines/logelementradius";
        var type;
        var file="";
        if (pathArray.length <= 4) {
            newURL += "/index";
        } else {
            for (i = 4; i < pathArray.length; i++) {
                newURL += "/";
                newURL += pathArray[i];
            }
        }
        $.ajax({
                url: newURL,
                cache: false,
                success: function(html){
                  $("#livelogs").html(html);
                }
              })
    }
    if(document.getElementById("voicelivelogs") !== null) {
        var newURL=window.location.protocol + "//" + window.location.host;
        var pathArray = document.URL.split( '/' );
        newURL+="/loglines/logelementvoice";
        var type;
        var file="";
        if (pathArray.length <= 2) {
            type="index";
            file="snacklog";
        } else {
            for (i = 4; i < pathArray.length; i++) {
                newURL += "/";
                newURL += pathArray[i];
            }
        }
        $.ajax({
                url: newURL,
                cache: false,
                success: function(html){
                  $("#voicelivelogs").html(html);
                }
              })
    }
    if(document.getElementById("livesessions") !== null) {
        var newURL=window.location.protocol + "//" + window.location.host;
        var pathArray = document.URL.split( '/' );
        newURL+="/radaccts/get_sessions_ajax";
        if (pathArray.length > 2) {
            for (i = 2; i < pathArray.length; i++) {
                newURL += "/";
                newURL += pathArray[i];
            }
        }
        $.ajax({
                url: newURL,
                cache: false,
                success: function(html){
                  $("#livesessions").html(html);
                }
              })
    }
}

setInterval(function(){ refreshCode(); }, 3000)