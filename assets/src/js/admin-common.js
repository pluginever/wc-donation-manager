
/**
 * WC Min Max Quantities Pro Admin
 * https://www.pluginever.com
 *
 * Copyright (c) 2018 pluginever
 * Licensed under the GPLv2+ license.
 */

/*jslint browser: true */
/*global jQuery:false */
jQuery(document).ready(function ($) {
	'use strict';
	$.wc_min_max_quantities_pro = {
		init: function () {
			$( '#woocommerce-product-data' ).on( 'woocommerce_variations_loaded', function() {
				$.wc_min_max_quantities_pro.handleMinMaxOption;
			});

			//after category form submition clear the form
			$( '#addtag #submit' ).on( 'click', function () {
				$.ajax({
					complete: function(){
						//reset form after ajax call for category insertion
						var category_form = document.querySelector('#addtag');
						category_form.reset();
					}
				});
			});

			$('#donation_products').select2({
				ajax: {
					cache: true,
					delay: 500,
					url: wcmmq_pro_admin_vars.ajaxurl,
					method: 'POST',
					dataType: 'json',
					data(params) {
						return {
							action: 'wcmmq_pro_search_products',
							nonce: wcmmq_pro_admin_vars.security,
							term: params.term,
							page: params.page,
						};
					},
					processResults(data, params) {
						params.page = params.page || 1;
						return {
							results: data.results,
							pagination: {
								more: data.pagination.more,
							},
						};
					},
				},
				placeholder: wcmmq_pro_admin_vars.i18n.search_products,
				minimumInputLength: 1,
				allowClear: true,
			});

			$('#wcmmq_category_ids').select2({
				ajax: {
					cache: true,
					delay: 500,
					url: wcmmq_pro_admin_vars.ajaxurl,
					method: 'POST',
					dataType: 'json',
					data(params) {
						return {
							action: 'wcmmq_pro_search_categories',
							nonce: wcmmq_pro_admin_vars.security,
							term: params.term,
							page: params.page,
						};
					},
					processResults(data, params) {
						params.page = params.page || 1;
						return {
							results: data.results,
							pagination: {
								more: data.pagination.more,
							},
						};
					},
				},
				placeholder: wcmmq_pro_admin_vars.i18n.search_categories,
				minimumInputLength: 1,
				allowClear: true,
			});

			$('#wcmmq_limit_type').on('change', function () {
				var restriction_type = $(this).val();
				var $wrapper = $(this).closest('form');
				if (restriction_type === 'products') {
					$wrapper.find('#wcmmq_product_ids').closest('tr').show();
					$wrapper.find('#wcmmq_category_ids').closest('tr').hide();
				}else if (restriction_type === 'categories') {
					$wrapper.find('#wcmmq_product_ids').closest('tr').hide();
					$wrapper.find('#wcmmq_category_ids').closest('tr').show();
				}else {
					$wrapper.find('#wcmmq_product_ids').closest('tr').hide();
					$wrapper.find('#wcmmq_category_ids').closest('tr').hide();
				}
			}).change();

			$('#wcmmq_product_limits').on('change', function () {
				var is_enabled = $(this).is(':checked');
				var $wrapper = $(this).closest('form');
				// select all form fields which have input or select class start with wcmmq_ except wcmmq_enable
				var dependents = $wrapper.find('.wcmmq-product-limits');

				// if enabled then show all dependent fields
				if (is_enabled) {
					dependents.closest('.form-field').show();
				}else {
					dependents.closest('.form-field').hide();
				}
			}).change()

			$('#wcmmq_category_limits').on('change', function () {
				var is_enabled = $(this).is(':checked');
				var $wrapper = $(this).closest('form');
				var dependents = $wrapper.find('.wcmmq-category-limits');

				// if enabled then show all dependent fields
				if (is_enabled) {
					dependents.closest('.form-field').show();
				}else {
					dependents.closest('.form-field').hide();
				}
			}).change();
		},
		handleMinMaxOption:function(){
			var minMaxContent = $('.manage_minmax_quantities_options');
			var checkbox = $('.checkbox.manage_minmax_quantities');
			if (checkbox.is(':checked')) {
				minMaxContent.show();
			} else {
				minMaxContent.hide();
			}
		},

	};


	$.wc_min_max_quantities_pro.init();
});
