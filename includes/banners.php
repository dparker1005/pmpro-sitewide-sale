<?php
/**
 * Generates banners for Sitewide Sale
 *
 * @package pmpro-sitewide-sale/includes
 */

add_action( 'wp', 'pmpro_sws_init_banners' );
/**
 * Logic for when to show banners/which banner to show
 */
function pmpro_sws_init_banners() {
	global $pmpro_pages;
	$options = pmprosws_get_options();

	if ( false !== $options['discount_code_id'] &&
				false !== $options['landing_page_post_id'] &&
				'no' !== $options['use_banner'] &&
				pmpro_sws_code_active() &&
				! is_page('login') &&
				! in_array( $GLOBALS['pagenow'], array( 'wp-login.php', 'wp-register.php' ) ) &&
				! is_page( intval( $options['landing_page_post_id'] ) ) &&
				! ( $options['hide_on_checkout'] && is_page( $pmpro_pages['checkout'] ) ) &&
				! in_array( pmpro_getMembershipLevelForUser()->ID, $options['hide_for_levels'], true )
			) {

		// Display the appropriate banner
		// $options['use_banner'] will be something like top, bottom, etc.
		if ( file_exists( PMPROSWS_DIR . '/includes/banners/' . $options['use_banner'] . '.php' ) ) {
			require_once PMPROSWS_DIR . '/includes/banners/' . $options['use_banner'] . '.php';
			// Maybe call a function here...
		}
	}
}

function pmpro_sws_code_active() {
	global $wpdb;
	$options = pmprosws_get_options();
	$code = $wpdb->get_results( $wpdb->prepare( "SELECT code FROM $wpdb->pmpro_discount_codes WHERE id=%s", $options['discount_code_id'] ) );
	return ( is_array( $code ) && ! empty( $code[0] ) && ! empty( $code[0]->code ) && pmpro_checkDiscountCode( $code[0]->code ) );
}
