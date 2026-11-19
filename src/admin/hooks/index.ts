/**
 * External dependencies
 */
import { useNavigate, useSearch } from '@byteever/router';
import {
	toObject,
	useListQuery,
	type FilterDefinition,
	type FiltersValue,
} from '@byteever/ui';

/**
 * WordPress dependencies
 */
import { useDispatch } from '@wordpress/data';
import { useCallback, useMemo, useState } from '@wordpress/element';

/**
 * Internal dependencies
 */
import { app, store } from '../app';

/**
 * The list query held in the current route's search params.
 *
 * @since 2.0.0
 * @param {Object} defaults Empty values for the screen's own query keys, which the list query drops otherwise.
 * @return {Object} The query and its setters, as `useListQuery` returns them.
 */
export function useListParams( defaults?: Record< string, unknown > ) {
	const search = useSearch( { strict: false } );
	const navigate = useNavigate();

	return useListQuery( {
		value: search,
		defaults,
		onChange: ( next ) =>
			void navigate( { to: '.', search: () => next, replace: true } ),
	} );
}

/**
 * A list's filters: the REST options' fields, whether the panel shows, and
 * the applied value.
 *
 * @since 2.0.0
 * @param {unknown}    filters Filters from the REST options, keyed by field.
 * @param {Object}     list    The screen's list query, as `useListParams` returns it.
 * @return {Object} The fields, whether the panel is open, its toggle, the applied value and the function applying one.
 */
export function useFilters( filters: unknown, list: ReturnType< typeof useListParams > ) {
	const { query, setQuery, filterCount } = list;
	const [ show, setShow ] = useState( false );
	const fields = useMemo(
		() =>
			( toObject( filters ) ?? {} ) as Record< string, FilterDefinition >,
		[ filters ]
	);

	return {
		fields,
		isOpen: show || filterCount > 0,
		toggle: () => setShow( ( previous ) => ! previous ),
		value: query.filters as FiltersValue | undefined,
		apply: ( value: FiltersValue ) => setQuery( { filters: value } ),
	};
}

type Messages = {
	deleted: ( count: number ) => string;
	failed: ( count: number ) => string;
};

/**
 * Deletes records of an entity, reporting the ones deleted and why any were kept.
 *
 * @since 2.0.0
 * @param {string}   entity   Entity name.
 * @param {Messages} messages The deleted and failed toasts, each given a count.
 * @return {(ids: Array< string | number >) => Promise< void >} Deletes the given ids.
 */
export function useRemove( entity: string, messages: Messages ) {
	const { batchRecords } = useDispatch( store );

	return useCallback(
		async ( ids: Array< string | number > ) => {
			try {
				const results = ( await batchRecords( entity, {
					delete: ids,
				} ) ) as { delete: Array< { error?: { message: string } } > };
				const kept = results.delete.filter(
					( result ) => result.error
				);
				const deleted = ids.length - kept.length;

				if ( deleted > 0 ) {
					app.toast.success( messages.deleted( deleted ) );
				}

				if ( kept.length > 0 ) {
					app.toast.error( kept[ 0 ].error?.message );
				}
			} catch ( error ) {
				app.toast.error( error, messages.failed( ids.length ) );
				throw error;
			}
		},
		[ batchRecords, entity, messages ]
	);
}
