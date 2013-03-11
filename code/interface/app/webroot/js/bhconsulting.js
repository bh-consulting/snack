function toggleBlock(button) {
	$(button).next().slideToggle();
	$(button).find('i').toggleClass('icon-chevron-up');
	$(button).find('i').toggleClass('icon-chevron-down');
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
        $('#rootwizard').find('.bar').css({width:$percent+'%'});

        // If it's the last tab then hide the last button and show the finish instead
        if($current >= $total) {
            $('#rootwizard').find('.pager .next').hide();
            $('#rootwizard').find('.pager .finish').show();
            $('#rootwizard').find('.pager .finish').removeClass('disabled');
        } else {
            $('#rootwizard').find('.pager .next').show();
            $('#rootwizard').find('.pager .finish').hide();
        }
    }});
});