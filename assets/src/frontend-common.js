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

		// Donation products redirect to cart or checkout on add-to-cart.
		// Classic form posts redirect in PHP (`woocommerce_add_to_cart_redirect`);
		// AJAX adds (theme add-to-cart, store notices) only fire this event.
		$(document.body).on('added_to_cart', function (event, fragments, cartHash, $button) {
			if (!window.wcdm_front_vars) {
				return;
			}

			const $form = $button ? $button.closest('form.cart') : $();
			if (!$form.length || !$form.find('#donation_amount').length) {
				return; // Only donation products carry the amount field.
			}

			if ('yes' === String(window.wcdm_front_vars.fast_checkout)) {
				window.location = window.wcdm_front_vars.checkout_url;
			} else if ('yes' === String(window.wcdm_front_vars.skip_cart)) {
				window.location = window.wcdm_front_vars.cart_url;
			}
		});

		$('.wcdm-progress-bar').each(function () {

			const $progressCard = $(this);

			const raised = parseFloat($progressCard.attr('data-raised')) || 0;
			const goal = parseFloat($progressCard.attr('data-goal')) || 0;
			const currency = $progressCard.attr('data-currency') || '$';

			const percentage = goal > 0
				? Math.min((raised / goal) * 100, 100)
				: 0;

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
