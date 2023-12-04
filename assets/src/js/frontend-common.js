(function ($) {
	var suggestedAmounts = function () {
		$( ".suggested-amount" ).each(function() {
			$(this).on("click", function(){
				$('#donation_amount').val(Number($(this).attr('value')).toFixed(2));
			});
		});
	}
	// Dom Ready
	$(function () {
		suggestedAmounts();
	});
})(jQuery);
