<?php

/**
 *	Register plugin settings under the Misc. tab in the EDD settings (includes/admin/settings/register-settings.php)
 *
 *	@param array $settings Current settings
 *
 *	@return array $new_settings Plugin settings + current settings
 */
add_filter( 'edd_settings_misc', 'edd_ftbg_add_misc_settings' );
function edd_ftbg_add_misc_settings( $settings ) {

	$new_settings = array(

		'edd_ftbg_first_time_buyer_discount_amount' => array(
			'id' => 'edd_ftbg_first_time_buyer_discount_amount',
			'name' => __( 'First-time Buyer Discount', 'edd' ),
			'desc' => __( 'A discount to gift first-time buyers after their first purchase.', 'edd' ),
			'type' => 'number',
		),
		'edd_ftbg_first_time_buyer_discount_type' => array(
			'id' => 'edd_ftbg_first_time_buyer_discount_type',
			'name' => __( 'Discount Type', 'edd' ),
			'desc' => __( 'Discount type for first-time buyer discount - flat discount or percentage.', 'edd' ),
			'type' => 'select',
			'options' => array(
				'flat' => __( 'Flat', 'edd' ),
				'percent' => __( 'Percent', 'edd' )
			)
		)

	);

	return array_merge( $settings, $new_settings );

}