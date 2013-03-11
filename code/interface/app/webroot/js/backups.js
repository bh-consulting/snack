$('input:radio').click(function () { 
    var a = $('input:radio[name="a"]:checked');
    var b = $('input:radio[name="b"]:checked');

    if(a.attr('set') >= b.attr('set')) {
	if($(this).attr('name') == 'a')
	    $('input:radio[name="b"][set='+ (parseInt(a.attr('set')) + 1)  +']').attr('checked', 'checked');

	else if($(this).attr('name') == 'b')
	    $('input:radio[name="a"][set='+ (parseInt(b.attr('set')) - 1)  +']').attr('checked', 'checked');
     }
});
