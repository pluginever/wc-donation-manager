/**
 * Donation Manager for WooCommerce - Admin Common JS
 * @link https://pluginever.com
 *
 * Copyright (c) 2025 PluginEver
 * Licensed under the GPLv2+ license.
 */
/*jslint browser: true */
/*global jQuery:false */
jQuery(document).ready(function ($) {
	'use strict';

	$.wc_donation_manager = {
		init: function () {
			$('#donation_products').select2({
				ajax: {
					cache: true,
					delay: 500,
					url: wcdm_admin_vars.ajaxurl,
					method: 'POST',
					dataType: 'json',
					data(params) {
						return {
							action: 'wcdm_search_products',
							nonce: wcdm_admin_vars.security,
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
				placeholder: wcdm_admin_vars.i18n.search_products,
				minimumInputLength: 1,
				allowClear: true,
			});
		},
	};

	$.wc_donation_manager.init();

	// Helper function to show admin error
	function wcdm_show_admin_error(message) {
		$('.wcdm-admin-error').remove();
		var $notice = $('<div class="notice notice-error is-dismissible wcdm-admin-error"><p>' + message + '</p></div>');
		$('.wrap').first().prepend($notice);
		$('html, body').animate({scrollTop: $('.wrap').first().offset().top - 50}, 200);
	}

	// Validate on product save
	$('#post').on('submit', function (e) {
		var prodType = $('#product-type').val() || $('select[name="product-type"]').val() || $('input[name="product-type"]').val();
		if (prodType !== 'donation') return true;

		var min = parseFloat($('#wcdm_min_amount').val());
		var max = parseFloat($('#wcdm_max_amount').val());
		min = isNaN(min) ? 0 : min;
		max = isNaN(max) ? 0 : max;
		var campaign = $('#wcdm_campaign_id').val() || $('select[name="wcdm_campaign_id"]').val();

		if (!campaign || campaign === '0' || parseInt(campaign, 10) <= 0) {
			e.preventDefault();
			wcdm_show_admin_error((typeof wcdm_admin_vars !== 'undefined' && wcdm_admin_vars.i18n && wcdm_admin_vars.i18n.select_campaign_error) ? wcdm_admin_vars.i18n.select_campaign_error : 'Please select a campaign for donation products.');
			return false;
		}

		if (min > 0 && max > 0 && min > max) {
			e.preventDefault();
			wcdm_show_admin_error((typeof wcdm_admin_vars !== 'undefined' && wcdm_admin_vars.i18n && wcdm_admin_vars.i18n.min_gt_max_error) ? wcdm_admin_vars.i18n.min_gt_max_error : 'Minimum amount cannot be greater than maximum amount.');
			return false;
		}

		return true;
	});
});
