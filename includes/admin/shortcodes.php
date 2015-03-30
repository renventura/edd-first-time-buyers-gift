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

	// Get the discount's name
	$discount_name = get_post_meta( intval( edd_get_discount_id_by_code( $code ) ), '_edd_discount_name', true );

	// If code exists and the discount's name is the user's email, the code belongs to the user
	if ( edd_get_discount_by_code( $code ) && $discount_name == $user_email ) {

		ob_start(); ?>

		<div class="ftbg-notice">
			<p><?php printf( '%1$s <strong>%2$s</strong>', __( 'Congratulations on your first purchase! As a way to say thanks, we would like to gift you a discount to use toward your next purchase. Your discount code is' ), $code ); ?></p>
		</div>

		<?php $message = ob_get_clean();

		$message = apply_filters( 'edd_ftbg_gift_notice_text', $message, $code );

	} else $message = null;

	return $message;

}