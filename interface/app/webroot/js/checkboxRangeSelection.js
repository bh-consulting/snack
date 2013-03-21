(function($) {
	$.fn.enableCheckboxRangeSelection = function() {
		var lastCheckbox = null;
		var $spec = this;
		$spec.unbind("click.checkboxrange");
		$spec.bind("click.checkboxrange", function(e) {
			if (lastCheckbox != null && (e.shiftKey || e.metaKey)) {
				$spec.slice(
					Math.min($spec.index(lastCheckbox), $spec.index(e.target)),
					Math.max($spec.index(lastCheckbox), $spec.index(e.target)) + 1
				).attr('checked', e.target.checked);
			}
			lastCheckbox = e.target;
		});
		};
})(jQuery);

$("div.range input").enableCheckboxRangeSelection();
$("div.rangeAll input").click( function(e){
	$("div.range input").attr('checked', e.target.checked);
});
$("div.range input").click( function(e){
	$("div.rangeAll input").attr('checked', false);
});
