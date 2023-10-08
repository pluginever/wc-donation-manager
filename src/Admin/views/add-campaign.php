<?php

defined( 'ABSPATH' ) || exit;

?>
<h1><?php esc_html_e( 'Add New Campaign', 'wc-donation-manager' ); ?></h1>
<p><?php esc_html_e( 'You can create a new campaign here. This form will create a campaign for the user, and optionally an associated order. Created orders will be marked as pending payment.', 'wc-donation-manager' ); ?></p>
<form method="POST" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
	<table class="form-table">
		<tbody>

		<tr valign="top">
			<th scope="row">
				<label for="name"><?php esc_html_e( 'Campaign Name', 'wc-donation-manager' ); ?></label>
			</th>
			<td>
				<input type="text" name="name" id="name" class="regular-text" value="" required/>
				<p class="description">
					<?php esc_html_e( 'Enter the name of the campaign.', 'wc-donation-manager' ); ?>
				</p>
			</td>
		</tr>

<!--		< !--product-- -->
<!--		<tr valign="top">-->
<!--			<th scope="row">-->
<!--				<label for="campaign_product">--><?php //esc_html_e( 'Campaign Product', 'wc-donation-manager' ); ?><!--</label>-->
<!--			</th>-->
<!--			<td>-->
<!--				<select id="campaign_product" name="campaign_product" class="wc-product-search" style="width: 300px" required>-->
<!--					<option value="">--><?php //esc_html_e( 'Select a campaign product...', 'wc-donation-manager' ); ?><!--</option>-->
<!--					--><?php //foreach ( wc_get_products( array() ) as $product ) : ?>
<!--						<option value="--><?php //echo esc_attr( $product->get_id() ); ?><!--">--><?php //echo esc_html( sprintf( '%s (#%s)', $product->get_name(), $product->get_id() ) ); ?><!--</option>-->
<!--					--><?php //endforeach; ?>
<!--				</select>-->
<!--				<p class="description">-->
<!--					--><?php //esc_html_e( 'Select the product associated with the campaign.', 'wc-donation-manager' ); ?>
<!--				</p>-->
<!--			</td>-->
<!--		</tr>-->
<!---->
<!--		< !--Create Order---->
<!--		<tr valign="top">-->
<!--			<th scope="row">-->
<!--				<label for="status">--><?php //esc_html_e( 'Campaign Status', 'wc-donation-manager' ); ?><!--</label>-->
<!--			</th>-->
<!--			<td>-->
<!--				<p>-->
<!--					<label>-->
<!--						<input type="radio" name="campaign_status" value="new" class="checkbox" checked/>-->
<!--						--><?php //esc_html_e( 'Create as a new campaign.', 'wc-donation-manager' ); ?>
<!--					</label>-->
<!--				</p>-->
<!--				<p>-->
<!--					<label>-->
<!--						<input type="radio" name="campaign_status" value="create_order" class="checkbox"/>-->
<!--						--><?php //esc_html_e( 'Create a new corresponding order for this new campaign.', 'wc-donation-manager' ); ?>
<!--					</label>-->
<!--				</p>-->
<!--				<p>-->
<!--					<label>-->
<!--						<input type="radio" name="campaign_status" value="existing_order" class="checkbox"/>-->
<!--						--><?php //esc_html_e( 'Assign this campaign to an existing order.', 'wc-donation-manager' ); ?>
<!--					</label>-->
<!--				</p>-->
<!--			</td>-->
<!--		</tr>-->
<!--		<tr valign="top">-->
<!--			<th scope="row">-->
<!--				<label for="customer_id">-->
<!--					--><?php //esc_html_e( 'Customer', 'wc-donation-manager' ); ?>
<!--				</label>-->
<!--			</th>-->
<!--			<td>-->
<!--				<select name="customer_id" id="customer_id" class="wc-customer-search" data-placeholder="--><?php //esc_attr_e( 'Guest', 'wc-donation-manager' ); ?><!--" data-allow_clear="true">-->
<!--				</select>-->
<!--				<p class="description">-->
<!--					--><?php //esc_html_e( 'Select the customer associated with the campaign.', 'wc-donation-manager' ); ?>
<!--				</p>-->
<!--			</td>-->
<!--		</tr>-->
<!---->
<!--		<tr valign="top">-->
<!--			<th scope="row">-->
<!--				<label for="order_id">--><?php //esc_html_e( 'Order ID', 'wc-donation-manager' ); ?><!--</label>-->
<!--			</th>-->
<!--			<td>-->
<!--				<input type="number" name="order_id" id="order_id" class="regular-text" value=""/>-->
<!--				<p class="description">-->
<!--					--><?php //esc_html_e( 'Enter the order id of the campaign.', 'wc-donation-manager' ); ?>
<!--				</p>-->
<!--			</td>-->
<!--		</tr>-->

		<tr valign="top">
			<th scope="row">&nbsp;</th>
			<td>
				<input type="hidden" name="action" value="wcdm_add_campaign"/>
				<?php wp_nonce_field( 'wcdm_add_campaign' ); ?>
				<?php submit_button( __( 'Add Campaign', 'wc-donation-manager' ), 'primary', 'add_campaign' ); ?>
			</td>
		</tr>
		</tbody>
	</table>


</form>

<script type="text/javascript">
	// when campaign_status is new customer_id and order_id are not required and hidden.
	// when campaign_status is create_order customer_id and order_id are required and visible.
	// when campaign_status is existing_order order_id is required and customer_id is not required and hidden.

	// Domdocument on load.
	document.addEventListener("DOMContentLoaded", function () {
		// Function to show or hide the customer_id and order_id fields based on the selected campaign_status
		function toggleFields() {
			const campaignStatus = document.querySelector('input[name="campaign_status"]:checked').value;
			const customerField = document.getElementById('customer_id');
			const customerFieldRow = customerField.closest('tr');
			const orderField = document.getElementById('order_id');
			const orderFieldRow = orderField.closest('tr');

			if (campaignStatus === 'new') {
				customerField.removeAttribute('required');
				orderField.removeAttribute('required');
				customerFieldRow.style.display = 'none';
				orderFieldRow.style.display = 'none';
			} else if (campaignStatus === 'create_order') {
				customerField.setAttribute('required', 'required');
				orderField.removeAttribute('required');
				orderFieldRow.style.display = 'none';
				customerFieldRow.style.display = 'table-row';
			} else if (campaignStatus === 'existing_order') {
				customerField.removeAttribute('required');
				orderField.setAttribute('required', 'required');
				customerFieldRow.style.display = 'none';
				orderFieldRow.style.display = 'table-row';
			}
		}

		// Call the toggleFields function on page load
		toggleFields();

		// Attach an event listener to the campaign_status radio buttons
		const campaignStatusRadios = document.querySelectorAll('input[name="campaign_status"]');
		campaignStatusRadios.forEach(radio => {
			radio.addEventListener('change', toggleFields);
		});
	});

</script>
