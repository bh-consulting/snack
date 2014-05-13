/*
 * Twitter Bootstrappifier for CakePHP
 *
 * Author: Julien Guepin
 * Modified by Guillaume Roche
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
            .not('#RaduserLoginForm')
			.addClass('form-horizontal');
		$('div.input')
			.not('div.error')
			.wrap('<div class="form-group"/>');
		$('div.input.error').wrap('<div class="form-group error"/>');
		$('input')
			.not('input[type="checkbox"]')
			.not('input[type="radio"]')
			.not('input[type="hidden"]')
            .not('input[id="username_login"]')
            .not('input[id="password_login"]')
            .not('input[id="importCsvFile"]')
            .addClass('form-control')
			.wrap('<div class="col-sm-4"/>');
		$('textarea').wrap('<div class="col-sm-4"/>');
        $('select').addClass('form-control');
		$('select').wrap('<div class="col-sm-4"/>');
		$('label').addClass('col-sm-4 control-label');
		$('div.checkbox').wrapInner('<label class="checkbox"/>');
		$('div.checkbox').removeClass('checkbox');
		$('label.checkbox').before(function() {
			return $(this).children('label');
		});
		$('label.checkbox').wrap('<div class="col-sm-1 control-label"/>');
		$('label:empty').remove();
        $('.checkgroup').children('label').each(function() {
			var content = $(this).html();
            $(this).removeClass('control-label').addClass('checkbox inline');
            $(this).prev('input').prependTo($(this));
        });
        $('div.select').each(function() {
            $(this).children('.checkgroup').wrapAll('<div class="control-group col-sm-6"/>');
        });
        $('div.check-horizontal').contents().unwrap().wrap("<span class='checkgroup check-horizontal'/>");

		$('input[type="radio"]').wrap('<label class="radio">');
		$('div.radio').removeClass('radio');
		
		// All submit forms converted to primary button
		$('input[type="submit"]').addClass('btn btn-primary');
		
		// Special inputs
		$('input.email').wrap('<div class="input-group"/>');
		$('input.email').after('<span class="input-group-addon">@</span>');
		$('input.path').wrap('<div class="input-group"/>');
		$('input.path').after('<span class="input-group-addon">../</span>');

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
		/*$('div.pagination').wrapInner('<ul/>');
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
		$('div.pagination li.disabled').wrapInner('<a href="#"/>');*/

		// DATETIMEPICKER
        var_date=new Date();
        str_date=var_date.getYear()+1900+"-"+var_date.getMonth()+"-"+var_date.getDate();
        $('.datetimepicker').addClass('form-control');
        $('.datetimepicker').wrap('<div class="input-group date form_datetime col-sm-12" data-date="'+str_date+'T05:25:07Z" data-date-format="yyyy MM dd - HH:ii p"></div>');
        $('<span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>').insertAfter(".datetimepicker");
        $('<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>').insertAfter(".datetimepicker");
        $('.form_datetime').datetimepicker({
            //language:  'fr',
            format: "yyyy-mm-dd hh:ii:00",
            weekStart: 1,
            todayBtn: 1,
            autoclose: 1,
            todayHighlight: 1,
            startView: 2,
            forceParse: 0,
            showMeridian: 0
        });

		/*$('.datetimepickerwrap').datetimepicker({
			language: 'fr',
			weekStart: 1
		});*/

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

		// SLIDER MAX
		$('<div class="slider"><div></div><span></span></div>')
		.insertAfter('.slidermax');
		$('.slidermax + div :first-child').each(function(index, elt){
			applySlider(index, elt, 'max');
		});

		// SLIDER MIN
		$('<div class="slider"><div></div><span></span></div>')
		.insertAfter('.slidermin');
		$('.slidermin + div :first-child').each(function(index, elt){
			applySlider(index, elt, 'min');
		});

		// TOOLTIPS
		$('*[title]').tooltip();

		// SWITCHS
		$('.switchbtn').each(function() {
		    $(this).parent().css('padding-left', '0');
		    $(this).wrap('<div class="switchwrap">');
		    $(this).parent().toggleButtons({
			height: 23,
			width: 80,
			transitionspeed: "500%",
		    });
		});
        
        // SELECT ROLE
        $(".role").change(function() {
            var value = $(this).val();
            if (value == "master") {
                $("#ParameterMasterIp" ).prop( "readonly", true );
                $("#ParameterMasterIp" ).val("");
                $("#ParameterSlaveIpToMonitor" ).prop( "readonly", false );
            }
            if (value == "slave") {
                $("#ParameterMasterIp" ).prop( "readonly", false );
                $("#ParameterSlaveIpToMonitor" ).prop( "readonly", true );
                $("#ParameterSlaveIpToMonitor" ).val("");
            }
        });
        //$.get("SystemDetails/halog/ha-2014-04-18_02-23.log", function(data) {
        //$( "div.halogs" ).replaceWith( "<h2>New heading</h2>" );
        $(".halog").click(function(event) {
            //alert("Handler for .click() called.");
            $.get("halog/"+this.id, function(data) {            
                $("div.halogs").html(data);
                //alert(data);
            }).fail(function() {
                alert( "error" );
            })
            .always(function() {
                //alert( "finished" );
            });
        });
        $(".testslog").click(function(event) {
            //alert("Handler for .click() called.");
            $.get("testslog/"+this.id, function(data) {            
                $("div.testslogs").html(data);
                //alert(data);
            }).fail(function() {
                alert( "error" );
            })
            .always(function() {
                //alert( "finished" );
            });
        });
	} 
};

// styling starts when document loads
$(document).ready(function(){
	Boostrapify.load();
	$(window).resize();
});