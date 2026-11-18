/**
 * External dependencies
 */
import { useSettings } from '@byteever/data';
import {
	Button,
	Page,
	Paywall,
	SettingsForm,
	Stack,
	useBeforeUnload,
	useSettingsForm,
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
import { vars } from '../../vars';

/**
 * The group paywalled whole; free users see it dimmed behind the paywall.
 */
const GATED_GROUP = 'emails';

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

	// Fields the Pro add-on unlocks render disabled for free users; a
	// disabled field keeps its value and is excluded from the submit.
	const gatedFields = useMemo( () => {
		if ( vars.isPro || ! fields ) {
			return fields;
		}

		return Object.fromEntries(
			Object.entries( fields ).map( ( [ name, field ] ) => [
				name,
				field.gated ? { ...field, disabled: true } : field,
			] )
		);
	}, [ fields ] );

	const form = useSettingsForm( {
		fields: gatedFields,
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

	const paywalled = GATED_GROUP === group && ! vars.isPro;
	const hasGated =
		! vars.isPro &&
		Object.values( fields ?? {} ).some( ( field ) => field.gated );

	const formNode = <SettingsForm form={ form } loading={ isLoading } />;

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
					disabled={ isLoading || paywalled || ! form.formState.isDirty }
				>
					{ __( 'Save changes', 'wc-donation-manager' ) }
				</Button>
			}
		>
			<Stack vertical gap="xl" align="stretch">
				{ paywalled ? (
					<Paywall
						blocked
						title={ __( 'Available in Pro', 'wc-donation-manager' ) }
						description={ __(
							'Unlock these settings, and every other Pro feature, by upgrading to Donation Manager Pro.',
							'wc-donation-manager'
						) }
						action={
							<Button
								variant="solid"
								tone="promo"
								href={ vars.upgradeUrl }
								target="_blank"
							>
								{ __( 'Upgrade to Pro', 'wc-donation-manager' ) }
							</Button>
						}
					>
						{ formNode }
					</Paywall>
				) : (
					formNode
				) }
				{ hasGated && ! paywalled && (
					<Paywall
						blocked
						title={ __(
							'Customize labels & buttons',
							'wc-donation-manager'
						) }
						description={ __(
							'Brand the donation experience with your own labels. Available in Donation Manager Pro.',
							'wc-donation-manager'
						) }
						action={
							<Button
								variant="solid"
								tone="promo"
								href={ vars.upgradeUrl }
								target="_blank"
							>
								{ __( 'Upgrade to Pro', 'wc-donation-manager' ) }
							</Button>
						}
					/>
				) }
			</Stack>
		</Page>
	);
}
