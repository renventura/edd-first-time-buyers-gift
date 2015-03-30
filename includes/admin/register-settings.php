<?php

/**
 *	Register plugin settings under the Misc. tab in the EDD settings (includes/admin/settings/register-settings.php)
 *
 *	@package EDD First Time Buyer's Gift
 *	@since 1.0
 *	@author Ren Ventura
 *
 *	@param array $settings Current settings
 *
 *	@return array $new_settings Plugin settings + current settings
 */
add_filter( 'edd_settings_misc', 'edd_ftbg_add_misc_settings' );
function edd_ftbg_add_misc_settings( $settings ) {

	//* Prepare the instructions for creating a custom gift message
	ob_start(); ?>

	<p><?php printf( '%1$s <strong>%2$s</strong> %3$s.', __( 'Write a custom message your customers will see when they have a discount available. Display the notice anywhere via the', 'edd' ), __( '[edd_ftbg_gift_notice]', 'edd' ), __( 'shortcode' ) ); ?></p>

	<br/>

	<p><?php _e( 'The following tag(s) can be used in the message:', 'edd' ); ?></p>

	<ul>
		<li><?php _e( '{code} - Outputs the user\'s discount code', 'edd' ); ?></li>
	</ul>

	<?php $custom_description_instructions = ob_get_clean();

	$new_settings = array(

		//* Settings Header
		'edd_ftbg_settings' => array(
			'id' => 'edd_ftbg_settings',
			'name' => '<strong>' . __( 'First Time Buyer\'s Gift', 'edd' ) . '</strong>',
			'desc' => __( 'Configure the discount settings for the gist a customer receives after their first purchase.', 'edd' ),
			'type' => 'header'
		),

		//* Discount amount input
		'edd_ftbg_first_time_buyer_discount_amount' => array(
			'id' => 'edd_ftbg_first_time_buyer_discount_amount',
			'name' => __( 'First-time Buyer Discount', 'edd' ),
			'desc' => __( 'A discount to gift first-time buyers after their first purchase.', 'edd' ),
			'type' => 'number',
			'size' => 'small'
		),

		//* Discount type select
		'edd_ftbg_first_time_buyer_discount_type' => array(
			'id' => 'edd_ftbg_first_time_buyer_discount_type',
			'name' => __( 'Discount Type', 'edd' ),
			'desc' => __( 'Discount type for first-time buyer discount - flat discount or percentage.', 'edd' ),
			'type' => 'select',
			'options' => array(
				'flat' => __( 'Flat', 'edd' ),
				'percent' => __( 'Percent', 'edd' )
			)
		),

		//* Display message
		'edd_ftbg_first_time_buyer_display_message' => array(
			'id' => 'edd_ftbg_first_time_buyer_display_message',
			'name' => __( 'Display Message', 'edd' ),
			'desc' => $custom_description_instructions,
			'type' => 'rich_editor'
		)

	);

	return array_merge( $settings, $new_settings );

}

//* Replace the default notice with the custom notice when one is entered
add_filter( 'edd_ftbg_gift_notice_text', 'edd_ftbg_custom_display_message', 15, 2 );
function edd_ftbg_custom_display_message( $message, $code ) {

	if ( edd_get_option( 'edd_ftbg_first_time_buyer_display_message' ) )
		$message = str_replace( '{code}', $code, edd_get_option( 'edd_ftbg_first_time_buyer_display_message' ) );

	return $message;

}