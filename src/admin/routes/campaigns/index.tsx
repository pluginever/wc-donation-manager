/**
 * External dependencies
 */
import { Button, Group, Page } from '@byteever/ui';
import { plus } from '@byteever/ui/icons';
import { useNavigate } from '@byteever/router';

/**
 * WordPress dependencies
 */
import { useCallback } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import List from './list';
import type { OnAction } from './types';

/**
 * The campaigns screen: the list; add and edit are their own routes.
 *
 * @since 2.0.0
 * @return {Element} The campaigns page.
 */
export default function Campaigns() {
	const navigate = useNavigate();

	const handleAction = useCallback< OnAction >(
		async ( action, ids = [] ) => {
			switch ( action ) {
				case 'add':
					navigate( { to: '/campaigns/new' } );
					break;

				case 'edit':
					navigate( { to: `/campaigns/${ ids[ 0 ] }/edit` } );
					break;
			}
		},
		[ navigate ]
	);

	return (
		<Page
			title={ __( 'Campaigns', 'wc-donation-manager' ) }
			maxWidth="wide"
			actions={
				<Group>
					<Button
						variant="solid"
						tone="primary"
						icon={ plus }
						onClick={ () => handleAction( 'add' ) }
					>
						{ __( 'Add campaign', 'wc-donation-manager' ) }
					</Button>
				</Group>
			}
		>
			<List onAction={ handleAction } />
		</Page>
	);
}

