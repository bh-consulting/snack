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
	load: function(){

		// FORMS
		$('form')
			.not('#MultiSelectionIndexForm')
			.addClass('form-horizontal');
		$('div.input')
			.not('div.error')
			.wrap('<div class="control-group"/>');
		$('div.input.error').wrap('<div class="control-group error"/>');
		$('input')
			.not('input[type="checkbox"]')
			.not('input[type="radio"]')
			.not('input[type="hidden"]')
			.wrap('<div class="controls"/>');
		$('textarea').wrap('<div class="controls"/>');
		$('select').wrap('<div class="controls"/>');
		$('label').addClass('control-label');
		$('div.checkbox').wrapInner('<label class="checkbox"/>');
		$('div.checkbox').removeClass('checkbox');
		$('label.checkbox').before(function() {
			return $(this).children('label');
		});
		$('label.checkbox').wrap('<div class="controls"/>');
		$('label:empty').remove();

		$('input[type="radio"]').wrap('<label class="radio">');
		$('div.radio').removeClass('radio');
		
		// All submit forms converted to primary button
		$('input[type="submit"]').addClass('btn btn-primary');
		
		// Special inputs
		$('input.email').wrap('<div class="input-append"/>');
		$('input.email').after('<span class="add-on">@</span>');
		$('input.path').wrap('<div class="input-append"/>');
		$('input.path').after('<span class="add-on">../</span>');

		// FORM ERROR MESSAGES
		$('div.error-message').replaceWith(function() {
			var content = $(this).html();
			$(this).replaceWith("<span class='help-inline'>" +
				content +
				"</span>"
			);
		});
		helps = $('.help-inline');
		for(var i = 0; i < helps.length; i++){
			prev = $(helps[i]).prev();
			prev.append(helps[i]);
		}

		// FLASH MESSAGES
		$('#flashMessage').prepend('<button type="button" class="close" ' +
			'data-dismiss="alert">×</button>');
		$('.message').addClass('alert alert-info');
		$('.error-message').addClass('alert alert-error');


		// ELEMENTS
		$('table').addClass('table table-hover table-bordered');

		// PAGINATION
		$('div.pagination').wrapInner('<ul/>');
		$('div.pagination span').replaceWith(function() {
			var content = $(this).html();
			var attr = $(this).attr("class");

			if(attr !== null) {
				$(this).replaceWith("<li class='" +
					attr + "'>" +
					content + "</li>"
				);
			} else {
				$(this).replaceWith("<li>" + content + "</li>");
			}
		});
		$('div.pagination li.disabled').wrapInner('<a href="#"/>');

		// DATETIMEPICKER
		$('.datetimepicker').wrap('<div class="input-append date ' +
			'datetimepickerwrap" />');
		$('.datetimepicker').attr('data-format', 'dd/MM/yyyy hh:mm:ss');
		$('<span class="add-on datetimepickeradd" />')
		.insertAfter('.datetimepicker');
		$('.datetimepickeradd').append('<i data-time-icon="icon-time" ' +
			'data-date-icon="icon-calendar" />');
		$('.datetimepickerwrap').datetimepicker({
			language: 'fr'
		});

		// SLIDER
		function applySlider(index, elt, range) {
			var select = $(elt).parent().prev();
			var iSelected = select.find('option')
			.index(select.children('option[selected]'));

			var value = function(v) {
				return select.children(':nth-child(' + (v+1) + ')')
				.attr('value');
			};

			var label = function(v) {
				return select.children(':nth-child(' + (v+1) + ')')
				.text();
			};

			select.hide();

			var labelColor = select.children('option[selected]')
			.attr('label-color');
			$(elt).next()
			.css('color', labelColor === undefined ? '#000' : labelColor);
			$(elt).next()
			.text( label(iSelected) );

			$(elt).slider({
				range: range,
				value: iSelected,
				min: 0,
				max: select.find('option').size() - 1,
				step: 1,
				slide: function(event, ui) {
					select.val( value(ui.value) );
					$(elt).next().text( label(ui.value) );

					var labelColor = select.children('option[selected]')
					.attr('label-color');
					$(elt).next()
					.css('color', labelColor === undefined ? '#000' : labelColor);
				}
			});
		}

		// slider max
		$('<div class="slider"><div></div><span></span></div>')
		.insertAfter('.slidermax');
		$('.slidermax + div :first-child').each(function(index, elt){
			applySlider(index, elt, 'max');
		});

		// slider min
		$('<div class="slider"><div></div><span></span></div>')
		.insertAfter('.slidermin');
		$('.slidermin + div :first-child').each(function(index, elt){
			applySlider(index, elt, 'min');
		});
	}
};

// styling starts when document loads
$(document).ready(function(){
	Boostrapify.load();
	$(window).resize();
});
