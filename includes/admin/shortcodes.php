<?php

/**
 *	Register applicable shortcodes
 *
 *	@package EDD First Time Buyer's Gift
 *	@since 1.0
 *	@author Ren Ventura
 */

//* Display a message to customers with a discount
add_shortcode( 'edd_ftbg_gift_notice', 'edd_ftbg_gift_notice_shortcode' );
function edd_ftbg_gift_notice_shortcode() {

	// Get the current user's email
	global $current_user;
	$user_email = $current_user->user_email;

	// Hash the email to get the code
	$code = substr( md5( $user_email ), 0, 18 );

	// Get the discount's ID and name
	$discount_id = edd_get_discount_id_by_code( $code );
	$discount_name = get_post_meta( $discount_id, '_edd_discount_name', true );

	// If discount exists, is active and the name equals the user's email, output the notice
	if ( $discount_id && edd_is_discount_active( $discount_id ) && $discount_name == $user_email ) {

		// Get some info about the discount
		$amount = edd_get_discount_amount( $discount_id );
		$type = edd_get_discount_type( $discount_id );

		if ( $type == 'percent' )
			$formatted_discount = $amount . '%';

		else
			$formatted_discount = edd_currency_filter( edd_format_amount( $amount ) );

		ob_start(); ?>

		<div class="ftbg-notice">
			<p><?php printf( '%1$s %2$s %3$s <strong>%4$s</strong>.', __( 'Congratulations on your first purchase! As a way to say thanks, we would like to gift you a ', 'edd' ), $formatted_discount, __( 'discount to use toward your next purchase. Your discount code is' ), $code ); ?></p>
		</div>

		<?php $message = ob_get_clean();

		$message = apply_filters( 'edd_ftbg_gift_notice_text', $message, $code, $formatted_discount );

	} else $message = null;

	return $message;

}

//* Replace the default customer notice with the custom notice when one is entered
add_filter( 'edd_ftbg_gift_notice_text', 'edd_ftbg_custom_display_message', 15, 3 );
function edd_ftbg_custom_display_message( $message, $code, $formatted_discount ) {

	if ( edd_get_option( 'edd_ftbg_first_time_buyer_display_message' ) ) {

		$tags = array( '{code}', '{formatted_discount}' );

		$replacements = array( $code, $formatted_discount );

		$message = str_replace( $tags, $replacements, edd_get_option( 'edd_ftbg_first_time_buyer_display_message' ) );

	}

	return $message;

}
