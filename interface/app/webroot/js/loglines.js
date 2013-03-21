function logsSearchFromSeverity(link) {
	var severity = link.text();

	$('option[value="' + severity + '"]').attr('selected', true);
	$('#logsSearchForm').submit();
}

function logsSearchFromDate(link) {
        var from_datetime = link.text().split(/\s+.*\s+/);

        var from_date = from_datetime[0].split('/');
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

        $('#datefrom').val(from_date[2] + '-' + from_date[1] + '-' + from_date[0] + ' ' + from_datetime[1]);
        $('#filtersForm').submit();
}

$('#severity').children(':nth-child(4)').attr('label-color', '#df4b00');
$('#severity').children(':nth-child(1)').attr('label-color', '#558d9d');

for(var i = 5; i < 9; i++)
	$('#severity').children(':nth-child(' + i  + ')').attr('label-color', '#cc0000');
