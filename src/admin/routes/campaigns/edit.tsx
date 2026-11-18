/**
 * External dependencies
 */
import { useEntity } from '@byteever/data';
import {
	Drawer,
	useBeforeUnload,
	useDataForm,
	type DataFormSchema,
} from '@byteever/ui';

/**
 * WordPress dependencies
 */
import { useMemo } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { app } from '../../app';
import { DataForm } from '../../components';

type EditProps = {
	id: number | null;
	open: boolean;
	onClose: () => void;
	onSave?: ( record: Record< string, unknown > ) => void;
};

/**
 * Adds or edits one campaign in a right-side drawer.
 *
 * @since 2.0.0
 * @param {EditProps}    props         Component props.
 * @param {number|null}  props.id      Campaign id, or null to add one.
 * @param {boolean}      props.open    Whether the drawer is open.
 * @param {() => void}   props.onClose Called once the drawer closes.
 * @param {OnSave}       props.onSave  Called with the saved campaign.
 * @return {Element} The drawer.
 */
export default function Edit( { id, open, onClose, onSave }: EditProps ) {
	const { record, options, recordIsLoading, optionsIsLoading, save } =
		useEntity( 'wcdm', 'campaigns', {
			id: id ?? undefined,
			withOptions: true,
		} );

	// A new record starts from the declaration's defaults; the form only
	// binds what it is given, so the schema defaults are seeded here.
	const defaults = useMemo( () => {
		const declared = ( options.form_fields ?? {} ) as DataFormSchema;

		return Object.fromEntries(
			Object.entries( declared )
				.filter(
					( [ , field ] ) =>
						'section' !== field.type &&
						( field as { default?: unknown } ).default !==
							undefined
				)
				.map( ( [ name, field ] ) => [
					name,
					( field as { default?: unknown } ).default,
				] )
		);
	}, [ options.form_fields ] );

	const form = useDataForm( {
		fields: ( options.form_fields ?? {} ) as DataFormSchema,
		values: id && record ? record : defaults,
		onSubmit: async ( values ) => {
			const saved = await app.toast.promise(
				save( {
					...values,
					...( id ? { id } : {} ),
				} ),
				{
					loading: __( 'Saving…', 'wc-donation-manager' ),
					success: id
						? __( 'Campaign updated.', 'wc-donation-manager' )
						: __( 'Campaign added.', 'wc-donation-manager' ),
					error: __(
						'Failed to save the campaign.',
						'wc-donation-manager'
					),
				}
			);

			onSave?.( saved );
			onClose();
		},
	} );

	useBeforeUnload( open && form.formState.isDirty );

	return (
		<Drawer
			open={ open }
			onOpenChange={ ( next ) => ! next && onClose() }
			onOpenChangeComplete={ ( next ) => ! next && form.reset() }
			title={
				id
					? __( 'Edit campaign', 'wc-donation-manager' )
					: __( 'Add campaign', 'wc-donation-manager' )
			}
			size={ 560 }
			loading={ optionsIsLoading || recordIsLoading }
			okText={ __( 'Save', 'wc-donation-manager' ) }
			okButtonProps={ {
				onClick: form.submit,
				loading: form.formState.isSubmitting,
				disabled: ! form.formState.isDirty,
			} }
		>
			<DataForm form={ form } />
		</Drawer>
	);
}
