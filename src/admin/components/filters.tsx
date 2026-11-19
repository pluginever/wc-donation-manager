/**
 * External dependencies
 */
import { Filters as BaseFilters, Card, type FiltersProps } from '@byteever/ui';

/**
 * WordPress dependencies
 */
import { useMemo } from '@wordpress/element';

/**
 * Internal dependencies
 */
import { app } from '../app';
import type { useFilters } from '../hooks';

/**
 * The Filters with the app's registered controls, plus any the screen adds.
 *
 * @since 2.0.0
 * @param {FiltersProps} props          Filters props.
 * @param {Object}       props.controls Controls only this screen draws.
 * @return {Element} The filters.
 */
export default function Filters( { controls, ...props }: FiltersProps ) {
	const registry = useMemo(
		() => ( {
			...( app.get( 'controls' ) as FiltersProps[ 'controls' ] ),
			...controls,
		} ),
		[ controls ]
	);

	return <BaseFilters { ...props } controls={ registry } />;
}

type FilterPanelProps = {
	filters: ReturnType< typeof useFilters >;
};

/**
 * The filters panel of a list: the fields, opened on the applied value.
 *
 * @since 2.0.0
 * @param {FilterPanelProps} props         Component props.
 * @param {Object}           props.filters The list's filters, as `useFilters` returns them.
 * @return {Element} The panel.
 */
export function FilterPanel( { filters }: FilterPanelProps ) {
	return (
		<Card.Section>
			<Filters
				key={ JSON.stringify( filters.value ?? null ) }
				fields={ filters.fields }
				defaultValue={ filters.value }
				onApply={ filters.apply }
			/>
		</Card.Section>
	);
}
