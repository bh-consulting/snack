function logsToggleSearch() {
	$('#logsSearchForm').slideToggle();
	$('#openpan').find('i').toggleClass('icon-chevron-up');
	$('#openpan').find('i').toggleClass('icon-chevron-down');
}

function logsSearchFromSeverity(link) {
	var severity = link.text();

	$('option[value="' + severity + '"]').attr('selected', true);
	$('#logsSearchForm').submit();
}

function logsSearchFromDate(link) {
	var from_datetime = link.text().split(' ');
	var from_date = from_datetime[0].split('-');
	var from_time = from_datetime[1].split(':');

	var to_datetime = $('#dateto').attr('value').split(' ');

	if(to_datetime[1] != undefined) {
		var to_date = to_datetime[0].split('/');
		var to_time = to_datetime[1].split(':');

		var unix_from = new Date(from_date[0], from_date[1], from_date[2], from_time[0], from_time[1], from_time[2]).getTime();
		var unix_to = new Date(to_date[2], to_date[1], to_date[0], to_time[0], to_time[1], to_time[2]).getTime();

		if(unix_to < unix_from)
			$('#dateto').attr('value', '');
	}

	$('#datefrom').val(from_date[2] + '/' + from_date[1] + '/' + from_date[0] + ' ' + from_datetime[1]);
	$('#logsSearchForm').submit();
}

$(function() {
	var shortseverities = [
		'debug',
		'info',
		'notice',
		'warn',
		'err',
		'crit',
		'alert' ,
		'emerg' 
	];

	$("#severityslider").slider({
		range: "min",
		value: 0,
		min: 0,
		max: shortseverities.length - 1,
		step: 1,
		slide: function(event, ui) {
			$("#severity").val(shortseverities[ui.value]);
			$("#severitysliderlabel").text($('option[value="' + shortseverities[ui.value] + '"]').text());
		}
	});

// TODO
// * Valeur par défaut sur le curseur
// * Valeur texte par défaut en full
// * En faire un input()

	var current = $("#severity").val();
	$("#severitysliderlabel").text(current.charAt(0).toUpperCase() + current.slice(1));
});
