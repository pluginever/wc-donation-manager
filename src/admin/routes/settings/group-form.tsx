/**
 * External dependencies
 */
import { useSettings } from '@byteever/data';
import {
	Button,
	Page,
	SettingsForm,
	Stack,
	useBeforeUnload,
	useSettingsForm,
} from '@byteever/ui';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { app } from '../../app';
import { vars } from '../../vars';

type GroupFormProps = {
	group: string;
};

/**
 * Draws one settings group from its declared fields.
 *
 * @since 2.0.0
 * @param {GroupFormProps} props       Component props.
 * @param {string}         props.group Settings group id.
 * @return {Element} The settings page.
 */
export default function GroupForm( { group }: GroupFormProps ) {
	const { groups, fields, values, isLoading, save } = useSettings(
		'wcdm-settings',
		group
	);
	const current = groups.find( ( item ) => item.id === group );

	const form = useSettingsForm( {
		fields,
		values,
		onSubmit: async ( data ) => {
			await app.toast.promise( save( data ), {
				loading: __( 'Saving…', 'wc-donation-manager' ),
				success: __( 'Settings saved.', 'wc-donation-manager' ),
				error: ( error ) =>
					( error as { message?: string } )?.message ??
					__(
						'Failed to save the settings.',
						'wc-donation-manager'
					),
			} );
		},
	} );

	useBeforeUnload( form.formState.isDirty );

	return (
		<Page
			title={ current ? String( current.title ) : '' }
			subtitle={
				current?.description ? String( current.description ) : null
			}
			maxWidth="narrow"
			actions={
				<Button
					variant="solid"
					tone="primary"
					onClick={ form.submit }
					loading={ form.formState.isSubmitting }
					disabled={ isLoading || ! form.formState.isDirty }
				>
					{ __( 'Save changes', 'wc-donation-manager' ) }
				</Button>
			}
		>
			<Stack vertical gap="xl" align="stretch">
				<SettingsForm form={ form } loading={ isLoading } />
			</Stack>
		</Page>
	);
}
