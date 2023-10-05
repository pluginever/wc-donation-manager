<?php

defined( 'ABSPATH' ) || exit;

?>
<h1><?php esc_html_e( 'Add New Thing', 'wc-donation-manager' ); ?></h1>
<p><?php esc_html_e( 'You can create a new thing here. This form will create a thing for the user, and optionally an associated order. Created orders will be marked as pending payment.', 'wc-donation-manager' ); ?></p>
<form method="POST" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
	<table class="form-table">
		<tbody>

		<tr valign="top">
			<th scope="row">
				<label for="name"><?php esc_html_e( 'Thing Name', 'wc-donation-manager' ); ?></label>
			</th>
			<td>
				<input type="text" name="name" id="name" class="regular-text" value="" required/>
				<p class="description">
					<?php esc_html_e( 'Enter the name of the thing.', 'wc-donation-manager' ); ?>
				</p>
			</td>
		</tr>

		<!--product-->
		<tr valign="top">
			<th scope="row">
				<label for="thing_product"><?php esc_html_e( 'Thing Product', 'wc-donation-manager' ); ?></label>
			</th>
			<td>
				<select id="thing_product" name="thing_product" class="wc-product-search" style="width: 300px" required>
					<option value=""><?php esc_html_e( 'Select a thing product...', 'wc-donation-manager' ); ?></option>
					<?php foreach ( wc_get_products( array() ) as $product ) : ?>
						<option value="<?php echo esc_attr( $product->get_id() ); ?>"><?php echo esc_html( sprintf( '%s (#%s)', $product->get_name(), $product->get_id() ) ); ?></option>
					<?php endforeach; ?>
				</select>
				<p class="description">
					<?php esc_html_e( 'Select the product associated with the thing.', 'wc-donation-manager' ); ?>
				</p>
			</td>
		</tr>

		<!--Create Order-->
		<tr valign="top">
			<th scope="row">
				<label for="status"><?php esc_html_e( 'Thing Status', 'wc-donation-manager' ); ?></label>
			</th>
			<td>
				<p>
					<label>
						<input type="radio" name="thing_status" value="new" class="checkbox" checked/>
						<?php esc_html_e( 'Create as a new thing.', 'wc-donation-manager' ); ?>
					</label>
				</p>
				<p>
					<label>
						<input type="radio" name="thing_status" value="create_order" class="checkbox"/>
						<?php esc_html_e( 'Create a new corresponding order for this new thing.', 'wc-donation-manager' ); ?>
					</label>
				</p>
				<p>
					<label>
						<input type="radio" name="thing_status" value="existing_order" class="checkbox"/>
						<?php esc_html_e( 'Assign this thing to an existing order.', 'wc-donation-manager' ); ?>
					</label>
				</p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="customer_id">
					<?php esc_html_e( 'Customer', 'wc-donation-manager' ); ?>
				</label>
			</th>
			<td>
				<select name="customer_id" id="customer_id" class="wc-customer-search" data-placeholder="<?php esc_attr_e( 'Guest', 'wc-donation-manager' ); ?>" data-allow_clear="true">
				</select>
				<p class="description">
					<?php esc_html_e( 'Select the customer associated with the thing.', 'wc-donation-manager' ); ?>
				</p>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row">
				<label for="order_id"><?php esc_html_e( 'Order ID', 'wc-donation-manager' ); ?></label>
			</th>
			<td>
				<input type="number" name="order_id" id="order_id" class="regular-text" value=""/>
				<p class="description">
					<?php esc_html_e( 'Enter the order id of the thing.', 'wc-donation-manager' ); ?>
				</p>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row">&nbsp;</th>
			<td>
				<input type="hidden" name="action" value="wcsp_add_thing"/>
				<?php wp_nonce_field( 'wcsp_add_thing' ); ?>
				<?php submit_button( __( 'Add Thing', 'wc-donation-manager' ), 'primary', 'add_thing' ); ?>
			</td>
		</tr>
		</tbody>
	</table>


</form>

<script type="text/javascript">
	// when thing_status is new customer_id and order_id are not required and hidden.
	// when thing_status is create_order customer_id and order_id are required and visible.
	// when thing_status is existing_order order_id is required and customer_id is not required and hidden.

	// Domdocument on load.
	document.addEventListener("DOMContentLoaded", function () {
		// Function to show or hide the customer_id and order_id fields based on the selected thing_status
		function toggleFields() {
			const thingStatus = document.querySelector('input[name="thing_status"]:checked').value;
			const customerField = document.getElementById('customer_id');
			const customerFieldRow = customerField.closest('tr');
			const orderField = document.getElementById('order_id');
			const orderFieldRow = orderField.closest('tr');

			if (thingStatus === 'new') {
				customerField.removeAttribute('required');
				orderField.removeAttribute('required');
				customerFieldRow.style.display = 'none';
				orderFieldRow.style.display = 'none';
			} else if (thingStatus === 'create_order') {
				customerField.setAttribute('required', 'required');
				orderField.removeAttribute('required');
				orderFieldRow.style.display = 'none';
				customerFieldRow.style.display = 'table-row';
			} else if (thingStatus === 'existing_order') {
				customerField.removeAttribute('required');
				orderField.setAttribute('required', 'required');
				customerFieldRow.style.display = 'none';
				orderFieldRow.style.display = 'table-row';
			}
		}

		// Call the toggleFields function on page load
		toggleFields();

		// Attach an event listener to the thing_status radio buttons
		const thingStatusRadios = document.querySelectorAll('input[name="thing_status"]');
		thingStatusRadios.forEach(radio => {
			radio.addEventListener('change', toggleFields);
		});
	});

</script>
