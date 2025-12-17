<?php
/**
 * List of Campaigns
 *
 * @package WooCommerceDonationManager
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

?>
	<h1 class="wp-heading-inline">
		<?php esc_html_e( 'Campaigns', 'wc-donation-manager' ); ?>
		<a href="<?php echo esc_attr( admin_url( 'admin.php?page=wc-donation-manager&add=yes' ) ); ?>" class="page-title-action">
			<?php esc_html_e( 'Add New', 'wc-donation-manager' ); ?>
		</a>
	</h1>
	<hr class="wp-header-end">

	<form id="wcdm-campaigns-table" method="get" action="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>">
		<?php $this->list_table->views(); ?>
		<?php $this->list_table->search_box( __( 'Search', 'wc-donation-manager' ), 'search' ); ?>
		<?php $this->list_table->display(); ?>
		<input type="hidden" name="page" value="wc-donation-manager"/>
	</form>
<?php
