/**
 * External dependencies
 */
import apiFetch from '@wordpress/api-fetch';
import { useEffect, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import {
	Card,
	DataTable,
	Empty,
	ErrorBoundary,
	type DataTableColumn,
} from '@byteever/ui';

/**
 * Internal dependencies
 */
import { vars } from '../../vars';

type Donor = {
	id: number;
	name: string;
	email: string;
	amount: number;
	order_id: number;
	type: string;
};

const COLUMNS: DataTableColumn< Donor >[] = [
	{
		key: 'name',
		title: __( 'Name', 'wc-donation-manager' ),
		rowHeader: true,
		minWidth: 180,
	},
	{
		key: 'email',
		title: __( 'Email', 'wc-donation-manager' ),
		minWidth: 200,
	},
	{
		key: 'amount',
		title: __( 'Donation amount', 'wc-donation-manager' ),
		align: 'end',
		minWidth: 120,
	},
	{
		key: 'order_id',
		title: __( 'Order', 'wc-donation-manager' ),
		minWidth: 90,
		render: ( id: number ) =>
			id ? (
				<a
					href={ `post.php?post=${ id }&action=edit` }
					target="_blank"
					rel="noreferrer"
				>
					{ `#${ id }` }
				</a>
			) : null,
	},
	{
		key: 'type',
		title: __( 'Type', 'wc-donation-manager' ),
		minWidth: 90,
	},
];

/**
 * The donors list, served by the Pro add-on.
 *
 * @since 2.0.0
 * @return {Element} The donors page.
 */
export default function Donors() {
	const [ donors, setDonors ] = useState< Donor[] >( [] );
	const [ isLoading, setIsLoading ] = useState( true );
	const [ error, setError ] = useState( '' );

	useEffect( () => {
		let mounted = true;

		setIsLoading( true );

		apiFetch< { donors?: Donor[] } >( {
			path: `/${ vars.rest.namespace }/donors`,
		} )
			.then( ( data ) => {
				if ( mounted ) {
					setDonors( data.donors ?? [] );
					setIsLoading( false );
				}
			} )
			.catch( ( err ) => {
				if ( mounted ) {
					setError(
						( err as { message?: string } ).message ?? ''
					);
					setIsLoading( false );
				}
			} );

		return () => {
			mounted = false;
		};
	}, [] );

	if ( error ) {
		return (
			<Empty
				title={ __( 'Donors are unavailable', 'wc-donation-manager' ) }
				description={ error }
			/>
		);
	}

	return (
		<Card>
			<Card.Section flush>
				<ErrorBoundary>
					<DataTable
						columns={ COLUMNS }
						items={ donors }
						total={ donors.length }
						loading={ isLoading }
						empty={
							<Empty
								title={ __(
									'No donors yet',
									'wc-donation-manager'
								) }
								description={ __(
									'Donations completed through WooCommerce show up here.',
									'wc-donation-manager'
								) }
							/>
						}
					/>
				</ErrorBoundary>
			</Card.Section>
		</Card>
	);
}
