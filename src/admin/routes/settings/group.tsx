/**
 * External dependencies
 */
import { useParams } from '@byteever/router';

/**
 * Internal dependencies
 */
import GroupForm from './group-form';

/**
 * Draws the group the route names.
 *
 * @since 2.0.0
 * @return {Element} The group form.
 */
export default function Group() {
	const { group } = useParams( { strict: false } );

	return <GroupForm key={ group } group={ String( group ) } />;
}
