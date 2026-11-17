/**
 * External dependencies
 */
import {
	Outlet,
	useLocation,
	useNavigate,
	useRouteTitle,
} from '@byteever/router';
import { useMenu } from '@byteever/ui';
import { Button, Layout, Navigation, Tooltip } from '@byteever/ui';
import { circleQuestionMark, settings } from '@byteever/ui/icons';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { vars } from './vars';

export default function Root() {
	useRouteTitle();

	const navigate = useNavigate();
	const menu = useMenu();
	const { pathname } = useLocation();
	const section = menu.find( ( item ) => item.to && pathname.startsWith( item.to ) );

	return (
		<Layout>
			<Layout.Header
				sticky
				align="center"
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
					items={ menu.filter(
						( item ) => item.id !== 'docs' && item.id !== 'settings'
					) }
					onChange={ ( to ) => navigate( { to: String( to ) } ) }
				/>
			</Layout.Header>
			<Layout.Content>
				<Outlet />
			</Layout.Content>
		</Layout>
	);
}
