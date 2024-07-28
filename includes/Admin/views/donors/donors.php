<?php
/**
 * List of Donors
 *
 * @package WooCommerceDonationsManager
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

?>
	<h1 class="wp-heading-inline">
		<?php esc_html_e( 'Donors', 'wc-donation-manager' ); ?>
		<?php if ( $this->list_table->get_request_search() ) : ?>
			<?php // translators: %s: search query. ?>
			<span class="subtitle"><?php echo esc_html( sprintf( __( 'Search results for "%s"', 'wc-donation-manager' ), esc_html( $this->list_table->get_request_search() ) ) ); ?></span>
		<?php endif; ?>
	</h1>
	<hr class="wp-header-end">

	<form id="wcdm-campaigns-table" method="get" action="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>">
		<?php $this->list_table->views(); ?>
		<?php $this->list_table->search_box( __( 'Search', 'wc-donation-manager' ), 'search' ); ?>
		<?php $this->list_table->display(); ?>
		<input type="hidden" name="page" value="wcdm-donors"/>
	</form>
<?php
