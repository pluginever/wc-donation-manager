/**
 * External dependencies
 */
import { Button, Page } from '@byteever/ui';
import { plus } from '@byteever/ui/icons';

/**
 * WordPress dependencies
 */
import { useCallback, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import List from './list';
import Edit from './edit';
import type { OnAction } from './types';

/**
 * The campaigns screen: the list with the add and edit drawer.
 *
 * @since 2.0.0
 * @return {Element} The campaigns page.
 */
export default function Campaigns() {
	const [ dialog, setDialog ] = useState< 'edit' | null >( null );
	const [ editingId, setEditingId ] = useState< number | null >( null );

	const handleAction = useCallback< OnAction >(
		async ( action, ids = [] ) => {
			switch ( action ) {
				case 'add':
					setEditingId( null );
					setDialog( 'edit' );
					break;

				case 'edit':
					setEditingId( Number( ids[ 0 ] ) );
					setDialog( 'edit' );
					break;
			}
		},
		[]
	);

	return (
		<Page
			title={ __( 'Campaigns', 'wc-donation-manager' ) }
			maxWidth="wide"
			actions={
				<Button
					variant="solid"
					tone="primary"
					icon={ plus }
					onClick={ () => handleAction( 'add' ) }
				>
					{ __( 'Add campaign', 'wc-donation-manager' ) }
				</Button>
			}
		>
			<List onAction={ handleAction } />

			<Edit
				id={ editingId }
				open={ 'edit' === dialog }
				onClose={ () => setDialog( null ) }
			/>
		</Page>
	);
}
