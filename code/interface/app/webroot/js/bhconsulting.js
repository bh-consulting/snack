function toggleFiltersPan() {
	$('#filtersForm').slideToggle();
	$('#filtersPan').find('i').toggleClass('icon-chevron-up');
	$('#filtersPan').find('i').toggleClass('icon-chevron-down');
}
