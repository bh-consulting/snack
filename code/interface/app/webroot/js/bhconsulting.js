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
