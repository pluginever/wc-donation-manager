/**
 * External dependencies
 */
import { useEntity, useEntityOptions } from '@byteever/data';
import { Link, useNavigate, useParams } from '@byteever/router';
import {
	Button,
	Group,
	Page,
	useBeforeUnload,
	useDataForm,
	type DataFormSchema,
} from '@byteever/ui';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { app } from '../../app';
import { DataForm } from '../../components';

/**
 * Adds or edits one campaign as its own page.
 *
 * @since 2.0.0
 * @return {Element} The campaign form page.
 */
export default function Edit() {
	const params = useParams( { strict: false } );
	const id = params.campaignId ? Number( params.campaignId ) : null;
	const navigate = useNavigate();

	const { record, options, recordIsLoading, optionsIsLoading, save } =
		useEntity( 'wcdm', 'campaigns', {
			id: id ?? undefined,
			withOptions: true,
		} );

	const form = useDataForm( {
		fields: ( options.form_fields ?? {} ) as DataFormSchema,
		values: id && record ? record : {},
		onSubmit: async ( values ) => {
			await app.toast.promise(
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

			navigate( { to: '/campaigns', replace: true } );
		},
	} );

	useBeforeUnload( form.formState.isDirty );

	return (
		<Page
			title={
				id
					? __( 'Edit campaign', 'wc-donation-manager' )
					: __( 'Add campaign', 'wc-donation-manager' )
			}
			subtitle={
				<Link to="/campaigns">
					{ __( '← Back to campaigns', 'wc-donation-manager' ) }
				</Link>
			}
			maxWidth="narrow"
			actions={
				<Group>
					<Button onClick={ () => navigate( { to: '/campaigns' } ) }>
						{ __( 'Cancel', 'wc-donation-manager' ) }
					</Button>
					<Button
						variant="solid"
						tone="primary"
						onClick={ form.submit }
						loading={ form.formState.isSubmitting }
						disabled={
							optionsIsLoading || recordIsLoading || ! form.formState.isDirty
						}
					>
						{ __( 'Save', 'wc-donation-manager' ) }
					</Button>
				</Group>
			}
		>
			<DataForm form={ form } loading={ optionsIsLoading } />
		</Page>
	);
}
