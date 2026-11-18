/**
 * External dependencies
 */
import { FormatDate, type DataTableColumn } from '@byteever/ui';

/**
 * Internal dependencies
 */
import { vars } from '../../vars';
import type { Campaign } from './types';

const STATUS_LABELS: Record< string, string > = {
	pending: 'Pending',
	publish: 'Active',
	draft: 'Draft',
	closed: 'Closed',
};

/**
 * An amount in the site's currency, prefixed by its symbol.
 *
 * @since 2.0.0
 * @param {number} amount Amount to format.
 * @return {string} The formatted amount.
 */
function money( amount: number ): string {
	const symbol = vars.currencySymbol || '$';

	return `${ symbol }${ Number( amount || 0 ).toFixed( 2 ) }`;
}

/**
 * The campaigns list renders.
 *
 * @since 2.0.0
 */
export const COLUMNS: Record<
	string,
	Partial< DataTableColumn< Campaign > >
> = {
	name: {
		render: ( name: string ) => name,
	},
	cause: {
		render: ( cause: string ) => {
			if ( ! cause ) {
				return null;
			}

			// The cause is rich text (the campaign editor); the cell shows a
			// plain-text excerpt.
			const text =
				new DOMParser().parseFromString( cause, 'text/html' )
					.body.textContent ?? '';

			return text.length > 60 ? `${ text.slice( 0, 60 ) }…` : text;
		},
	},
	goal_amount: {
		render: ( amount: number, campaign: Campaign ) => (
			<span>
				{ money( campaign.raised_amount ) }
				{ campaign.goal_amount > 0
					? ` / ${ money( campaign.goal_amount ) }`
					: '' }
			</span>
		),
	},
	end_date: {
		render: ( date: string ) => ( date ? <FormatDate date={ date } /> : '—' ),
	},
	status: {
		render: ( status: string ) => STATUS_LABELS[ status ] ?? status,
	},
};
