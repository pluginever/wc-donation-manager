/**
 * WC Donation Manager
 * https://www.pluginever.com
 *
 * Copyright (c) 2018 pluginever
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
			// $(".suggested-amount").each(function() {
			// 	$(this).removeClass('selected');
			// });
			console.log(value);
			$("input[name='suggested-amount[]']").map(function () {
				// return $(this).val();
				let thiss = $(this).val();
				$(".suggested-amount").each(function() {
					$(this).removeClass('selected');
					if(value == thiss ){
						$(this).addClass('selected');
					}
				});
			});

			// console.log(arr);

			// if($.inArray($(this).val(), arr) !== -1){
				// e.addClass('selected');
				// console.log('Hi modhu!');
			// }
		});
	});
}(jQuery));
