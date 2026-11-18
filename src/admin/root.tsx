/**
 * External dependencies
 */
import {
	Outlet,
	useActiveMenu,
	useAdminMenu,
	useNavigate,
	useRouteTitle,
} from '@byteever/router';
import {
	Button,
	Layout,
	Navigation,
	Stack,
	Text,
	Tooltip,
} from '@byteever/ui';
import { circleQuestionMark, settings } from '@byteever/ui/icons';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { Logo } from './components';
import { vars } from './vars';

const UTILITY_MENU = [ 'settings', 'docs' ];

export default function Root() {
	useAdminMenu( vars.slug );
	useRouteTitle();

	const navigate = useNavigate();
	const menu = useActiveMenu();
	const section = menu.find( ( item ) => item.active );
	const primary = menu.filter( ( item ) => ! UTILITY_MENU.includes( item.id ) );

	return (
		<Layout>
			<Layout.Header
				sticky
				align="center"
				visual={
					<Button
						unstyled
						href="#/campaigns"
						label={ __( 'Donation Manager', 'wc-donation-manager' ) }
					>
						<Stack gap="sm" align="center">
							<Logo width={ 32 } height={ 32 } />
							<Text weight="medium">
								{ __( 'Donation Manager', 'wc-donation-manager' ) }
							</Text>
						</Stack>
					</Button>
				}
				actions={
					<>
						<Tooltip content={ __( 'Help', 'wc-donation-manager' ) }>
							<Button
								variant="plain"
								icon={ circleQuestionMark }
								label={ __( 'Help', 'wc-donation-manager' ) }
								href={ vars.docsUrl }
								target="_blank"
							/>
						</Tooltip>
						<Tooltip content={ __( 'Settings', 'wc-donation-manager' ) }>
							<Button
								variant="plain"
								icon={ settings }
								label={ __( 'Settings', 'wc-donation-manager' ) }
								href="#/settings"
								pressed={ 'settings' === section?.id }
							/>
						</Tooltip>
						{ ! vars.isPro && (
							<Button
								variant="solid"
								tone="promo"
								href={ vars.upgradeUrl }
								target="_blank"
							>
								{ __( 'Upgrade to Pro', 'wc-donation-manager' ) }
							</Button>
						) }
					</>
				}
			>
				<Navigation
					aria-label={ __( 'Primary', 'wc-donation-manager' ) }
					items={ primary }
					onChange={ ( to ) => navigate( { to: String( to ) } ) }
				/>
			</Layout.Header>
			<Layout.Content>
				<Outlet />
			</Layout.Content>
		</Layout>
	);
}
