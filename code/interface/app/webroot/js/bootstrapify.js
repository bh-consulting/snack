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
        $('form').not('#MultiSelectionIndexForm').addClass('form-horizontal');
        $('div.input').not('div.error').wrap('<div class="control-group"></div>');
        $('div.input.error').wrap('<div class="control-group error"></div>');
        $('input').wrap('<div class="controls"></div>');
        $('textarea').wrap('<div class="controls"></div>');
        $('select').wrap('<div class="controls"></div>');
        $('label').addClass('control-label');
        //All submit forms converted to primary button
        $('input[type="submit"]').addClass('btn btn-primary');

        // FORM ERROR MESSAGES
		$('div.error-message').replaceWith(function() { 
			var content = $(this).html();
			$(this).replaceWith("<span class='help-inline'>" + content + "</span>");
		});
		helps = $('.help-inline');
		console.log(helps);
		for(var i = 0; i < helps.length; i++){
			prev = $(helps[i]).prev();
			prev.append(helps[i]);
		}

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

		// SLIDER MAX
		$('<div class="slider"><div></div><span></span></div>').insertAfter('.slidermax');
		$('.slidermax + div :first-child').each(function(index) {
			var select = $(this).parent().prev();
			var iSelected = select.find('option').index(select.children('option[selected]'));

			var value = function(val) { return select.children(':nth-child(' + (val + 1) + ')').attr('value'); };
			var label = function(val) { return select.children(':nth-child(' + (val + 1) + ')').text(); };

			select.hide();

			var labelColor = select.children('option[selected]').attr('label-color');
			$(this).next().css('color', labelColor == undefined ? '#000' : labelColor);
			$(this).next().text(label(iSelected));

			$(this).slider({
				range: 'max',
				value: iSelected,
				min: 0,
				max: select.find('option').size() - 1,
				step: 1,
				slide: function(event, ui) {
					select.val(value(ui.value));
					$(this).next().text(label(ui.value));

					var labelColor = select.children('option[selected]').attr('label-color');
					$(this).next().css('color', labelColor == undefined ? '#000' : labelColor);
				}
			});
		});
	}
}

// styling starts when document loads
$(document).ready(function(){
	Boostrapify.load();
});
