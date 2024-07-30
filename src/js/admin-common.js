
/**
 * WC Donation Manager
 * https://www.pluginever.com
 *
 * Copyright (c) 2018 pluginever
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
});
