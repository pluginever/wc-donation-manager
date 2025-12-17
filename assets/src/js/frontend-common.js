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
	});
}(jQuery));
