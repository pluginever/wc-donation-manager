(function ($) {
	var suggestedAmounts = function () {
		$( ".suggested-amount" ).each(function() {
			$(this).on("click", function(){
				$( ".suggested-amount" ).each(function() {
					$(this).removeClass('selected');
				});
				$(this).addClass('selected');
				$('#donation_amount').val(Number($(this).attr('value')).toFixed(2));
			});
		});
	}
	// Dom Ready
	$(function () {
		suggestedAmounts();
	});
})(jQuery);
