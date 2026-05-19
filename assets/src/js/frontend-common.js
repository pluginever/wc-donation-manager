/**
 * Donation Manager for WooCommerce - Frontend Common JS
 * @link https://pluginever.com
 *
 * Copyright (c) 2025 PluginEver
 * Licensed under the GPLv2+ license.
 */
(function ($) {
	'use strict';
	$(document).ready(function () {
		$(".suggested-amount").each(function() {
			$(this).on("click", function(){
				$(".suggested-amount").each(function() {
					$(this).removeClass('selected');
				});
				$(this).addClass('selected');
				$('#donation_amount').val(Number($('input[name="suggested-amount[]"]:checked').val()).toFixed(2));
			});
		});

		$('#donation_amount').on('change', function (){
			let value = $(this).val();
			$("input[name='suggested-amount[]']").map(function () {
				if ( value === $(this).val() ) {
					$(this).parent().addClass('selected');
				} else {
					$(this).parent().removeClass('selected');
				}
			});
		});

		$('.wcdm-progress-bar').each(function () {

			const $progressCard = $(this);

			const raised = parseFloat($progressCard.attr('data-raised')) || 0;
			const goal = parseFloat($progressCard.attr('data-goal')) || 0;
			const currency = $progressCard.attr('data-currency') || '$';

			const percentage = goal > 0
				? Math.min((raised / goal) * 100, 100)
				: 0;

			console.log(percentage);


			$progressCard
				.find('.progress-bar')
				.css('width', percentage + '%');

			$progressCard
				.find('.donation-percent')
				.text(Math.round(percentage) + '%');

			$progressCard
				.find('.raised-amount')
				.text(currency + raised.toLocaleString());

			$progressCard
				.find('.goal-amount')
				.text(currency + goal.toLocaleString());
		});
	});
}(jQuery));
