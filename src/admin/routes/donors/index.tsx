/**
 * External dependencies
 */
import apiFetch from '@wordpress/api-fetch';
import { addQueryArgs } from '@wordpress/url';
import { useEffect, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import {
	Button,
	Card,
	DataTable,
	Empty,
	ErrorBoundary,
	Flex,
	Page,
	Paywall,
	Search,
	Select,
	type DataTableColumn,
} from '@byteever/ui';
import { funnel } from '@byteever/ui/icons';

/**
 * WordPress dependencies
 */
import { useCallback } from '@wordpress/element';

/**
 * Internal dependencies
 */
import { FilterPanel } from '../../components';
import { isLicensed, vars } from '../../vars';
import { useFilters, useListParams } from '../../hooks';

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

/** The demo list free users see behind the paywall; no real links. */
const DUMMY_COLUMNS: DataTableColumn< Donor >[] = COLUMNS.map( ( column ) =>
	column.key === 'order_id' ? { ...column, render: undefined } : column
);

const DUMMY_DONORS: Donor[] = [
	{
		id: 1043,
		name: 'Amelia Hart',
		email: 'amelia.hart@example.com',
		amount: 250,
		order_id: 1043,
		type: 'Onetime',
	},
	{
		id: 1042,
		name: 'Lucas Meyer',
		email: 'lucas.meyer@example.com',
		amount: 100,
		order_id: 1042,
		type: 'Onetime',
	},
	{
		id: 1041,
		name: 'Olivia Bennett',
		email: 'olivia.bennett@example.com',
		amount: 500,
		order_id: 1041,
		type: 'Onetime',
	},
	{
		id: 1040,
		name: 'Ethan Brooks',
		email: 'ethan.brooks@example.com',
		amount: 75,
		order_id: 1040,
		type: 'Onetime',
	},
	{
		id: 1039,
		name: 'Sophia Reyes',
		email: 'sophia.reyes@example.com',
		amount: 1000,
		order_id: 1039,
		type: 'Onetime',
	},
	{
		id: 1038,
		name: 'Noah Fischer',
		email: 'noah.fischer@example.com',
		amount: 50,
		order_id: 1038,
		type: 'Onetime',
	},
];

/**
 * The donors list, served by the Pro add-on; free users see a demo list
 * dimmed behind the paywall. The list searches, filters by order status
 * and pages through the collection.
 *
 * @since 2.0.0
 * @return {Element} The donors page.
 */
export default function Donors() {
	const list = useListParams( { status: '' } );
	const { query, setQuery, resetQuery, hasFilter } = list;
	const [ options, setOptions ] = useState< { query_filters?: unknown } >(
		{}
	);
	const [ donors, setDonors ] = useState< Donor[] >( [] );
	const [ meta, setMeta ] = useState< { total: number } >( { total: 0 } );
	const [ isLoading, setIsLoading ] = useState( true );
	const [ error, setError ] = useState( '' );

	const filters = useFilters( options.query_filters, list );

	const showReal = isLicensed;

	// The filter fields and the list ride the same query: filters are one
	// JSON value; everything else is its own key.
	const path = addQueryArgs( `/${ vars.rest.namespace }/donors`, {
		page: query.page ?? 1,
		per_page: query.per_page ?? 20,
		search: query.search || undefined,
		status: query.status || undefined,
		filters: query.filters ?? undefined,
	} );

	const optionsPath = `/${ vars.rest.namespace }/donors/options`;

	// The filter fields come from the options endpoint once.
	const loadOptions = useCallback( () => {
		if ( ! showReal ) {
			return;
		}

		apiFetch< { query_filters?: unknown } >( { path: optionsPath } )
			.then( ( data ) => setOptions( data ?? {} ) )
			.catch( () => undefined );
	}, [ optionsPath, showReal ] );

	useEffect( () => {
		loadOptions();
	}, [ loadOptions ] );

	useEffect( () => {
		if ( ! showReal ) {
			return;
		}

		let mounted = true;

		setIsLoading( true );

		apiFetch< { donors?: Donor[]; total?: number } >( { path } )
			.then( ( data ) => {
				if ( mounted ) {
					setDonors(
						Array.isArray( data?.donors ) ? data.donors : []
					);
					setMeta( { total: Number( data?.total ?? 0 ) } );
					setIsLoading( false );
				}
			} )
			.catch( ( err ) => {
				if ( mounted ) {
					setError( ( err as { message?: string } ).message ?? '' );
					setIsLoading( false );
				}
			} );

		return () => {
			mounted = false;
		};
	}, [ path, showReal ] );

	const table = (
		columns: DataTableColumn< Donor >[],
		items: Donor[],
		total: number,
		toolbar = false
	) => (
		<Card>
			{ toolbar && (
				<Card.Header>
					<Flex gap="sm" align="center">
						<Flex.Item grow={ 1 }>
							<Search
								placeholder={ __(
									'Search donors by name, email or order…',
									'wc-donation-manager'
								) }
								value={ String( query.search ?? '' ) }
								onChange={ ( value ) =>
									setQuery( { search: value, page: 1 } )
								}
							/>
						</Flex.Item>
						<Flex.Item basis={ 180 }>
							<Select
								value={ String( query.status || 'all' ) }
								options={ [
									{
										value: 'all',
										label: __(
											'All orders',
											'wc-donation-manager'
										),
									},
									{
										value: 'completed',
										label: __(
											'Completed',
											'wc-donation-manager'
										),
									},
									{
										value: 'processing',
										label: __(
											'Processing',
											'wc-donation-manager'
										),
									},
								] }
								onChange={ ( value ) =>
									setQuery( {
										status:
											'all' === value
												? ''
												: String( value ),
										page: 1,
									} )
								}
							/>
						</Flex.Item>
						<Button
							icon={ funnel }
							pressed={ filters.isOpen }
							onClick={ filters.toggle }
						>
							{ __( 'Filters', 'wc-donation-manager' ) }
						</Button>
					</Flex>
				</Card.Header>
			) }
			{ filters.isOpen && toolbar && <FilterPanel filters={ filters } /> }
			<Card.Section flush>
				<ErrorBoundary>
					<DataTable
						columns={ columns }
						items={ items }
						total={ total }
						loading={ showReal && isLoading }
						{ ...( toolbar
							? {
									query,
									onQueryChange: setQuery,
									pagination: {
										showSizeChanger: true,
									},
							  }
							: {} ) }
						empty={
							hasFilter ? (
								<Empty
									title={ __(
										'No donors match',
										'wc-donation-manager'
									) }
									description={ __(
										'Try a different search or clear the filters.',
										'wc-donation-manager'
									) }
								>
									<Button onClick={ resetQuery }>
										{ __(
											'Clear filters',
											'wc-donation-manager'
										) }
									</Button>
								</Empty>
							) : (
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
							)
						}
					/>
				</ErrorBoundary>
			</Card.Section>
		</Card>
	);

	if ( ! showReal ) {
		return (
			<Page
				title={ __( 'Donors', 'wc-donation-manager' ) }
				maxWidth="wide"
			>
				<Paywall
					blocked
					title={ __( 'Available in Pro', 'wc-donation-manager' ) }
					description={
						vars.isPro
							? __(
									'Activate your license to see everyone who donated, their contact details and the orders behind the donations.',
									'wc-donation-manager'
							  )
							: __(
									'See everyone who donated, their contact details and the orders behind the donations. Available in Donation Manager Pro.',
									'wc-donation-manager'
							  )
					}
					action={
						<Button
							variant="solid"
							tone="promo"
							href={
								vars.isPro
									? vars.adminUrl + 'plugins.php'
									: vars.upgradeUrl
							}
							target="_blank"
						>
							{ vars.isPro
								? __( 'Activate license', 'wc-donation-manager' )
								: __( 'Upgrade to Pro', 'wc-donation-manager' ) }
						</Button>
					}
				>
					{
						table(
							DUMMY_COLUMNS,
							DUMMY_DONORS,
							DUMMY_DONORS.length
						)
					}
				</Paywall>
			</Page>
		);
	}

	if ( error ) {
		return (
			<Page
				title={ __( 'Donors', 'wc-donation-manager' ) }
				maxWidth="wide"
			>
				<Empty
					title={ __( 'Donors are unavailable', 'wc-donation-manager' ) }
					description={ error }
				/>
			</Page>
		);
	}

	return (
		<Page
			title={ __( 'Donors', 'wc-donation-manager' ) }
			maxWidth="wide"
		>
			{ table( COLUMNS, donors, meta.total, true ) }
		</Page>
	);
}
