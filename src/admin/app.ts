/**
 * External dependencies
 */
import apiFetch from '@wordpress/api-fetch';
import { createEntities, createSettings } from '@byteever/data';
import { createApp } from '@byteever/ui';

/**
 * Internal dependencies
 */
import { menuItems, routes } from './routes';
import { vars } from './vars';

const { namespace } = vars.rest;

/**
 * The store holding the plugin's entities; every hook and dispatch
 * addresses it by name.
 */
export const store = createEntities( {
	name: 'wcdm',
	entities: [
		{
			name: 'campaigns',
			endpoint: `/${ namespace }/campaigns`,
		},
		{
			name: 'products',
			endpoint: `/${ namespace }/products`,
		},
	],
} );

/**
 * The store holding the plugin's settings groups.
 */
export const settings = createSettings( {
	name: 'wcdm-settings',
	endpoint: `/${ namespace }/settings`,
} );

export const app = createApp( 'wcdm', {
	data: {
		vars,
		store,
		settings,
	},
	routes,
	menu: menuItems,
	preflight: [
		// The mount node lives on plugin screens only; give every fetch the
		// REST root and nonce the PHP side localized.
		() => {
			apiFetch.use( apiFetch.createRootURLMiddleware( vars.rest.url ) );
			apiFetch.use( apiFetch.createNonceMiddleware( vars.rest.nonce ) );
		},
	],
} );
