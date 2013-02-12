$('input:radio').click(function () { 
    var a = $('input:radio[name="a"]:checked');
    var b = $('input:radio[name="b"]:checked');

    if(a.val() <= b.val()) {
	if($(this).attr('name') == 'a')
	    $('input:radio[name="b"][value='+ (parseInt(a.val()) - 1)  +']').attr('checked', 'checked');

	else if($(this).attr('name') == 'b')
	    $('input:radio[name="a"][value='+ (parseInt(b.val()) + 1)  +']').attr('checked', 'checked');
     }
});
