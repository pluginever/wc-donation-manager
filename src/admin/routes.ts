/**
 * External dependencies
 */
import { redirect } from '@byteever/router';
import { coins, heart, settings, users } from '@byteever/ui/icons';

/**
 * WordPress dependencies
 */
import { resolveSelect } from '@wordpress/data';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { vars } from './vars';

export const routes = [
	{
		id: 'home',
		path: '/',
		position: 9,
		beforeLoad: () => {
			throw redirect( { to: '/campaigns', replace: true } );
		},
	},
	{
		id: 'campaigns',
		title: __( 'Campaigns', 'wc-donation-manager' ),
		path: '/campaigns',
		position: 10,
		lazy: () => import( './routes/campaigns' ),
	},
	{
		id: 'donors',
		title: __( 'Donors', 'wc-donation-manager' ),
		path: '/donors',
		position: 20,
		lazy: () => import( './routes/donors' ),
	},
	{
		id: 'settings',
		title: __( 'Settings', 'wc-donation-manager' ),
		path: '/settings',
		position: 30,
		lazy: () => import( './routes/settings' ),
	},
	{
		id: 'settings.index',
		parent: 'settings',
		path: '/',
		position: 5,
		beforeLoad: async () => {
			const groups = await resolveSelect( 'wcdm-settings' ).getSettingGroups();

			if ( groups.length ) {
				throw redirect( {
					to: `/settings/${ groups[ 0 ].id }`,
					replace: true,
				} );
			}
		},
	},
	{
		id: 'settings.group',
		parent: 'settings',
		title: __( 'Settings', 'wc-donation-manager' ),
		path: '$group',
		position: 10,
		lazy: () => import( './routes/settings/group' ),
	},
];

export const menuItems = [
	{
		id: 'campaigns',
		label: __( 'Campaigns', 'wc-donation-manager' ),
		to: '/campaigns',
		icon: heart,
		position: 10,
	},
	{
		id: 'donors',
		label: __( 'Donors', 'wc-donation-manager' ),
		to: '/donors',
		icon: users,
		position: 20,
	},
	{
		id: 'settings',
		label: __( 'Settings', 'wc-donation-manager' ),
		to: '/settings',
		icon: settings,
		position: 30,
	},
	{
		id: 'docs',
		label: __( 'Docs', 'wc-donation-manager' ),
		href: vars.docsUrl,
		target: '_blank',
		icon: coins,
		position: 9000,
	},
];
