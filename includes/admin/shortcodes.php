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

	//* Don't run if user isn't logged in
	if ( ! is_user_logged_in() )
		return;

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

		ob_start(); ?>

		<div class="ftbg-notice">
			<p><?php printf( '%1$s <strong>%2$s</strong>', __( 'Congratulations on your first purchase! As a way to say thanks, we would like to gift you a discount to use toward your next purchase. Your discount code is' ), $code ); ?></p>
		</div>

		<?php $message = ob_get_clean();

		$message = apply_filters( 'edd_ftbg_gift_notice_text', $message, $code );

	} else $message = null;

	return $message;

}