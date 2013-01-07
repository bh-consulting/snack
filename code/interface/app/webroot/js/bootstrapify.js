/*
* Twitter Bootstrappifier for CakePHP
*
* Author: Julien Guepin
*
* CakePHP Twitter Bootstrappifier
*
* Selects all CakePHP incompatible forms and buttons,
* and converts them into pretty Twitter Bootstrap style.
*
*/

var Boostrapify = {
    load    : function(){

        // FORMS
        $('form').addClass('form-horizontal');
        $('div.input').wrap('<div class="control-group"></div>');
        $('input').wrap('<div class="controls"></div>');
        $('textarea').wrap('<div class="controls"></div>');
        $('select').wrap('<div class="controls"></div>');
        $('label').addClass('control-label');
        //All submit forms converted to primary button
        $('input[type="submit"]').addClass('btn btn-primary');

        // FLASH MESSAGES
        $('#flashMessage').prepend('<button type="button" class="close" data-dismiss="alert">×</button>');
        $('.message').addClass('alert alert-info');
        $('.error-message').addClass('alert alert-error');

        // ELEMENTS
        $('table').addClass('table table-hover table-bordered');

	// PAGINATION
	$('div.pagination').wrapInner('<ul/>');
	$('div.pagination span').replaceWith(function() { 
		var content = $(this).html();
		var attr		= $(this).attr("class");

		if( attr != null )
			$(this).replaceWith("<li class='" + attr + "'>" + content + "</li>");
		else
			$(this).replaceWith("<li>" + content + "</li>");
	});
	$('div.pagination li.disabled').wrapInner('<a href="#"></a>');

	// DATETIMEPICKER
	$('.datetimepicker').wrap('<div class="input-append date datetimepickerwrap" />');
	$('.datetimepicker').attr('data-format', 'dd/MM/yyyy hh:mm:ss');
	$('<span class="add-on datetimepickeradd" />').insertAfter('.datetimepicker');
	$('.datetimepickeradd').append('<i data-time-icon="icon-time" data-date-icon="icon-calendar" />');
	$('.datetimepickerwrap').datetimepicker({
		language: 'fr'
	});
    }
}

// styling starts when document loads
$(document).ready(function(){
    Boostrapify.load();
});
