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
		<li><?php _e( '{formatted_discount} - Outputs the formatted discount value (i.e. 15% or $10)', 'edd' ); ?></li>
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

		//* Enable the functionality
		'edd_ftbg_enable_gifts' => array(
			'id' => 'edd_ftbg_enable_gifts',
			'name' => __( 'Enable', 'edd' ),
			'desc' => __( 'This will enable discount gifts to your first-time customers.', 'edd' ),
			'type' => 'checkbox'
		),

		//* Minimum total to receive a discount
		'edd_ftbg_first_time_buyer_min_total' => array(
			'id' => 'edd_ftbg_first_time_buyer_min_total',
			'name' => __( 'Minimum Total', 'edd' ),
			'desc' => __( 'The mimumin total a first-time customer must spend to receive the discount; <strong>default 1.00</strong>.', 'edd' ),
			'type' => 'number',
			'size' => 'small'
		),

		//* Discount amount input
		'edd_ftbg_first_time_buyer_discount_amount' => array(
			'id' => 'edd_ftbg_first_time_buyer_discount_amount',
			'name' => __( 'First-time Buyer Discount', 'edd' ),
			'desc' => __( 'A discount to gift first-time buyers after their first purchase; <strong>default 1.00</strong>.', 'edd' ),
			'type' => 'number',
			'size' => 'small'
		),

		//* Discount type select
		'edd_ftbg_first_time_buyer_discount_type' => array(
			'id' => 'edd_ftbg_first_time_buyer_discount_type',
			'name' => __( 'Discount Type', 'edd' ),
			'desc' => __( 'Discount type for first-time buyer discount - flat or percentage; <strong>default "flat"</strong>.', 'edd' ),
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
