function updateSelect() {
	var list = $(this);
	var select = $( '#select-' + list.attr('id') );
	var options = ( select.prop ) ? select.prop('options') : select.attr('options');

	if( options )
		$('option', select).remove();

	list.find('li').each( function( n ) {
		if( options )
			( options[options.length] = new Option( $(this).html(), $(this).attr("id")) ).setAttribute("selected", true);

		$(this).removeClass();
		$(this).addClass( list.attr("subClass") );
	});	
}

$(function() {
	$( ".sortList" ).sortable({
		connectWith: ".connectedList",
		create: updateSelect,
		update: updateSelect,
		placeholder: "label"
	}).disableSelection();
});
