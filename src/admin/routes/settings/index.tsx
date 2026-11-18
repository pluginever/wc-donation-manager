/**
 * External dependencies
 */
import { useSettings } from '@byteever/data';
import { Outlet, useLocation, useNavigate } from '@byteever/router';
import { Layout, Sidebar, Skeleton } from '@byteever/ui';
import { heart, mail, pencil, settings } from '@byteever/ui/icons';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */

const ICONS: Record< string, readonly unknown[] > = {
	general: heart,
	emails: mail,
	advanced: settings,
};

/**
 * The settings screen: a sidebar of the declared groups and the group form.
 *
 * @since 2.0.0
 * @return {Element} The settings page.
 */
export default function Settings() {
	const { groups, isLoading } = useSettings( 'wcdm-settings' );
	const { pathname } = useLocation();
	const navigate = useNavigate();

	const items = groups
		.filter( ( group ) => ! group.hidden )
		.sort(
			( a, b ) =>
				Number( a.priority ?? 10 ) - Number( b.priority ?? 10 )
		)
		.map( ( group ) => ( {
			id: group.id,
			to: `/settings/${ group.id }`,
			active: pathname.startsWith( `/settings/${ group.id }` ),
			icon: ICONS[ group.id ] ?? pencil,
			label: String( group.title ),
		} ) );

	return (
		<Layout>
			<Layout.Aside bordered>
				<Sidebar title={ __( 'Settings', 'wc-donation-manager' ) }>
					{ isLoading && ! groups.length ? (
						<Skeleton rows={ 4 } title={ false } />
					) : (
						<Sidebar.Menu
							items={ items }
							onSelect={ ( to ) => navigate( { to } ) }
						/>
					) }
				</Sidebar>
			</Layout.Aside>
			<Layout.Content>
				<Outlet />
			</Layout.Content>
		</Layout>
	);
}
