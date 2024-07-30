<?php
/**
 * View: Admin Page
 *
 * @since 1.0.0
 * @subpackage Admin/Views
 * @package WooCommerceKeyManager
 * @var string $page_id Page ID.
 */

defined( 'ABSPATH' ) || exit;

$current_tab  = filter_input( INPUT_GET, 'tab', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
$current_page = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

$tabs        = isset( $tabs ) ? $tabs : array();
$tabs        = apply_filters( 'wc_donation_manager_' . $page_id . '_tabs', $tabs );
$current_tab = ! empty( $current_tab ) && array_key_exists( $current_tab, $tabs ) ? $current_tab : key( $tabs );

?>
<div class="wrap bk-wrap woocommerce">
	<?php if ( ! empty( $tabs ) && count( $tabs ) > 1 ) : ?>
		<nav class="nav-tab-wrapper bk-navbar">
			<?php
			foreach ( $tabs as $name => $label ) {
				printf(
					'<a href="%s" class="nav-tab %s">%s</a>',
					esc_url( admin_url( 'admin.php?page=' . $current_page . '&tab=' . $name ) ),
					esc_attr( $current_tab === $name ? 'nav-tab-active' : '' ),
					esc_html( $label )
				);
			}
			?>
			<?php
			/**
			 * Fires after the tabs on the settings page.
			 *
			 * @param string $current_tab Current tab..
			 * @param array  $tabs Tabs.
			 *
			 * @since 1.0.0
			 */
			do_action( 'wc_donation_manager_' . $page_id . '_nav_items', $current_tab, $tabs );
			?>
		</nav>
	<?php endif; ?>
	<?php
	if ( ! empty( $current_tab ) && $page_id !== $current_tab ) {
		/**
		 * Action: Admin Page Tab
		 *
		 * @param string $current_tab Current tab.
		 *
		 * @since 1.0.0
		 */
		do_action( "wc_donation_manager_{$page_id}_{$current_tab}_content", $current_tab );
	} else {
		/**
		 * Action: Admin Page Content
		 *
		 * @param string $current_tab Current tab.
		 *
		 * @since 1.0.0
		 */
		do_action( "wc_donation_manager_{$page_id}_content", $current_tab );
	}
	?>
</div>
<?php
