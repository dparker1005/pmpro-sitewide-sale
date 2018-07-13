<?php
/**
 * Accesses stored information about sale.
 *
 * @package pmpro-sitewide-sale/includes
 */

/**
 * Get the Sitewide Sale Options
 **/
function pmprosws_get_options() {

	$options = get_option( 'pmpro_sitewide_sale' );

	// Set the defaults.
	if ( empty( $options ) ) {
		$options = array(
			'active_sitewide_sale_id' => false,
		);
	}

	if ( ! array_key_exists( 'active_sitewide_sale_id', $options ) ) {
		// If statement necessary in case user was using non-cpt version of sws.
		$options['active_sitewide_sale_id'] = false;
	}

	return $options;
}

/**
 * [pmprosws_save_options description]
 *
 * @param array $options contains information about sale to be saved.
 */
function pmprosws_save_options( $options ) {
	return update_option( 'pmpro_sitewide_sale', $options, 'no' );
}
