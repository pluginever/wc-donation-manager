/**
 * External dependencies
 */
import { DataForm as BaseDataForm, type DataFormProps } from '@byteever/ui';

/**
 * WordPress dependencies
 */
import { useMemo } from '@wordpress/element';

/**
 * Internal dependencies
 */
import { app } from '../app';

/**
 * The DataForm with the app's registered controls, plus any the screen adds.
 *
 * @since 2.0.0
 * @param {DataFormProps} props          DataForm props.
 * @param {Object}        props.controls Controls only this screen draws.
 * @return {Element} The form.
 */
export default function DataForm( { controls, ...props }: DataFormProps ) {
	const registry = useMemo(
		() => ( {
			...( app.get( 'controls' ) as DataFormProps[ 'controls' ] ),
			...controls,
		} ),
		[ controls ]
	);

	return <BaseDataForm { ...props } controls={ registry } />;
}
