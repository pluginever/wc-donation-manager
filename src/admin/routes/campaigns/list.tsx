/**
 * External dependencies
 */
import { useEntity, useEntityOptions } from '@byteever/data';
import {
	Button,
	Card,
	DataTable,
	Empty,
	ErrorBoundary,
	Flex,
	Group,
	Search,
	type DataTableBulkAction,
	type DataTableColumn,
} from '@byteever/ui';
import { pencil, plus, trash } from '@byteever/ui/icons';

/**
 * WordPress dependencies
 */
import { useCallback, useMemo, useState } from '@wordpress/element';
import { __, _n, sprintf } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { useListParams, useRemove } from '../../hooks';
import { COLUMNS } from './columns';
import type { Campaign } from './types';

type ListProps = {
	onAction: ( action: string, ids?: Array< string | number > ) => void;
};

/**
 * The campaigns list: search, table, row and bulk actions.
 *
 * @since 2.0.0
 * @param {ListProps} props          Component props.
 * @param {ListProps} props.onAction Runs an action on the given campaign ids.
 * @return {Element} The list.
 */
export default function List( { onAction }: ListProps ) {
	const list = useListParams();
	const { query, setQuery, resetQuery, hasFilter } = list;
	const { options, isLoading: optionsIsLoading } = useEntityOptions(
		'wcdm',
		'campaigns'
	);
	const { records, meta, isLoading } = useEntity< Campaign >(
		'wcdm',
		'campaigns',
		{
			query,
		}
	);

	const remove = useRemove( 'campaigns', {
		deleted: ( count ) =>
			sprintf(
				/* translators: %d: number of campaigns deleted. */
				_n(
					'%d campaign deleted.',
					'%d campaigns deleted.',
					count,
					'wc-donation-manager'
				),
				count
			),
		failed: ( count ) =>
			_n(
				'Failed to delete the campaign.',
				'Failed to delete the campaigns.',
				count,
				'wc-donation-manager'
			),
	} );

	const columns = useMemo< DataTableColumn< Campaign >[] >( () => {
		const declared = ( options.table_columns ?? {} ) as Record<
			string,
			Partial< DataTableColumn< Campaign > > & { priority?: number }
		>;

		const list_columns = Object.entries( declared )
			.map( ( [ key, column ] ) => ( {
				...COLUMNS[ key ],
				...column,
				key,
				hidden: false,
			} ) )
			.sort( ( a, b ) => ( a.priority ?? 10 ) - ( b.priority ?? 10 ) );

		return [
			...list_columns,
			{
				key: 'actions',
				title: '',
				width: 90,
				fixed: 'end',
				render: ( _value: unknown, campaign: Campaign ) => (
					<Group size="small">
						<Button
							size="small"
							variant="plain"
							icon={ pencil }
							label={ __( 'Edit', 'wc-donation-manager' ) }
							onClick={ () =>
								onAction( 'edit', [ campaign.id ] )
							}
						/>
						<Button
							size="small"
							variant="plain"
							tone="danger"
							icon={ trash }
							label={ __( 'Delete', 'wc-donation-manager' ) }
							onClick={ () =>
								window
									.confirm(
										__(
											'Delete this campaign?',
											'wc-donation-manager'
										)
									)
									? void remove( [ campaign.id ] )
									: null
							}
						/>
					</Group>
				),
			},
		];
	}, [ options.table_columns, onAction, remove ] );

	const bulkActions = useMemo< DataTableBulkAction[] >(
		() => [
			{
				key: 'delete',
				label: __( 'Delete', 'wc-donation-manager' ),
				icon: trash,
				destructive: true,
				callback: ( keys, _rows, clear ) => {
					void remove( keys ).finally( () => clear() );
				},
			},
		],
		[ remove ]
	);

	return (
		<Card>
			<Card.Header>
				<Flex gap="sm" align="center">
					<Flex.Item grow={ 1 }>
						<Search
							placeholder={ __(
								'Search campaigns…',
								'wc-donation-manager'
							) }
							value={ query.search }
							onChange={ ( value ) =>
								setQuery( { search: value } )
							}
						/>
					</Flex.Item>
				</Flex>
			</Card.Header>

			<Card.Section flush>
				<ErrorBoundary>
					<DataTable
						columns={ columns }
						items={ records }
						total={ meta.total ?? 0 }
						loading={ isLoading || optionsIsLoading }
						query={ query }
						onQueryChange={ setQuery }
						selectable={ { actions: bulkActions } }
						pagination={ { showSizeChanger: true } }
						empty={
							hasFilter ? (
								<Empty
									title={ __(
										'No campaigns match',
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
										'No campaigns yet',
										'wc-donation-manager'
									) }
									description={ __(
										'Create a campaign to track donations for a cause.',
										'wc-donation-manager'
									) }
								>
									<Button
										variant="solid"
										tone="primary"
										icon={ plus }
										onClick={ () => onAction( 'add' ) }
									>
										{ __(
											'Add campaign',
											'wc-donation-manager'
										) }
									</Button>
								</Empty>
							)
						}
					/>
				</ErrorBoundary>
			</Card.Section>
		</Card>
	);
}
