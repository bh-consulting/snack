var secPerMinute = 60;
var secPerHour = secPerMinute * 60;
var secPerDay = secPerHour * 24;
var secPerMonth = secPerDay * 30;
var secPerYear = secPerMonth * 12;

function addAsecondToDurations() {
    $('*[data-duration]').each(function() {
	var time = $(this).attr('data-duration');
	var format = $(this).attr('data-duration-format').split(',');
	var duration = ' ';

	var interval = parseInt(time) + 1;

	var years = Math.floor(interval / secPerYear);
	interval = interval - (years * secPerYear);

	var months = Math.floor(interval / secPerMonth);
	interval = interval - (months * secPerMonth);

	var days = Math.floor(interval / secPerDay);
	interval = interval - (days * secPerDay);

	var hours = Math.floor(interval / secPerHour);
	interval = interval - (hours * secPerHour);

	var minutes = Math.floor(interval / secPerMinute);
	interval = interval - (minutes * secPerMinute);

	var seconds = interval;

	if(years > 0)
	    duration += years + format[0] + ' ';
	if(months > 0)
	    duration += months + format[1] + ' ';
	if(days > 0)
	    duration += days + format[2] + ' ';
	if(hours > 0)
	    duration += hours + format[3] + ' ';
	if(minutes > 0)
	    duration += minutes + format[4] + ' ';
	if(seconds > 0)
	    duration += seconds + format[5] + ' ';
	
	duration = duration.substring(1);

	$(this).attr('data-duration', parseInt(time) + 1);
	$(this).text(duration);
    });

    setTimeout("addAsecondToDurations()", 1000);
}

$(document).ready(function() {
    addAsecondToDurations();
});
