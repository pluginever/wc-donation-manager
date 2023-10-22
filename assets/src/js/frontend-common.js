(function ($) {
	var wcDonationManager = function() {

		$('#suggested-amounts-01').on('click', function() {
			$(this).addClass('selected');
			$('#donation_amount').val($(this).val());

			$('#suggested-amounts-02').removeClass('selected');
			$('#suggested-amounts-03').removeClass('selected');
			$('#suggested-amounts-04').removeClass('selected');
		});

		$('#suggested-amounts-02').on('click', function () {
			$(this).addClass('selected');
			$('#donation_amount').val($(this).val());

			$('#suggested-amounts-01').removeClass('selected');
			$('#suggested-amounts-03').removeClass('selected');
			$('#suggested-amounts-04').removeClass('selected');
		});

		$('#suggested-amounts-03').on('click', function () {
			$(this).addClass('selected');
			$('#donation_amount').val($(this).val());

			$('#suggested-amounts-01').removeClass('selected');
			$('#suggested-amounts-02').removeClass('selected');
			$('#suggested-amounts-04').removeClass('selected');
		});

		$('#suggested-amounts-04').on('click', function () {
			$(this).addClass('selected');
			$('#donation_amount').val($(this).val());

			$('#suggested-amounts-01').removeClass('selected');
			$('#suggested-amounts-02').removeClass('selected');
			$('#suggested-amounts-03').removeClass('selected');
		});
	}
	// Dom Ready
	$(function () {
		wcDonationManager();
	});
})(jQuery);
