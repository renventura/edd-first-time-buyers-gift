<?php

/**
 * Plugin Name: EDD First Time Buyer's Gift
 * Plugin URI: http://www.engagewp.com/
 * Description: Increase customer satisfaction and repeat business by generating and assigning discounts for buyers after their first purchase.
 * Version: 1.0
 * Author: Ren Ventura
 * Author URI: http://www.engagewp.com/
 *
 * License: GPL 2.0+
 * License URI: http://www.opensource.org/licenses/gpl-license.php
 *
 */

 /*

	Copyright 2015  Ren Ventura, EngageWP.com

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

	Permission is hereby granted, free of charge, to any person obtaining a copy of this
	software and associated documentation files (the "Software"), to deal in the Software
	without restriction, including without limitation the rights to use, copy, modify, merge,
	publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons
	to whom the Software is furnished to do so, subject to the following conditions:

	The above copyright notice and this permission notice shall be included in all copies or
	substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
	THE SOFTWARE.

*/

//* Plugin constants
define( 'EDD_FTBG_VERSION', '1.0' );
define( 'EDD_FTBG_PLUGIN_FILE', __FILE__ );
define( 'EDD_FTBG_PLUGIN_BASENAME', untrailingslashit( plugin_basename( __FILE__ ) ) );

//* Include required files
require_once 'includes/admin/register-settings.php';
include_once 'includes/admin/shortcodes.php';

//* Check to see if EDD is active and bail if not
register_activation_hook( __FILE__, 'edd_ftbg_plugin_activate' );
function edd_ftbg_plugin_activate() {

	if ( ! is_plugin_active( 'easy-digital-downloads/easy-digital-downloads.php' ) ) {

		deactivate_plugins( EDD_FTBG_PLUGIN_BASENAME );

		wp_die( sprintf( __( 'The EDD First Time Buyer\'s Gift requires Easy Digital Downloads to be active.', 'edd' ) ) );

	}

}

/**
 *	Create a discount for buyers after their first purchase
 *	@since 1.0
 */
class EDD_First_Time_Buyers_Gift {

	private $user_id, $user_email, $user_purchases, $discount_code;

	public function __construct() {

		add_action( 'edd_complete_purchase', array( $this, 'edd_ftbg_register_discount' ) );

	}

	/**
	 *	If the user has no more than one purchase (/includes/payments/actions.php)
	 *	@param $payment_id
	 */
	public function edd_ftbg_register_discount( $payment_id ) {

		//* Get some info about the buyer
		$this->user_id = intval( get_post_meta( $payment_id, '_edd_payment_user_id', true ) );
		$this->user_email = get_post_meta( $payment_id, '_edd_payment_user_email', true );

		/**
		 *	Get number of purchases of the customer (/includes/user-functions.php)
		 *
		 *	Returns total number of purchases a customer has made
		 *
		 *	@param       $user mixed - ID or email
		 *	@return      int - the total number of purchases
		 */
		$this->user_purchases = edd_count_purchases_of_customer( $this->user_id );

		//* Add the discount if buyer has not purchased before
		if ( $this->user_purchases < 1 ) {

			//* Generate 18 character code
			$this->discount_code = substr( md5( $this->user_email ), 0, 18 );

			//* Bail if the discount exists
			if ( edd_get_discount_by_code( $this->discount_code ) )
				return;

			//* Assemble the discount
			$default_discount_args = array(
				'name'       => $this->user_email,
				'code'       => $this->discount_code,
				'max'        => 1,
				'amount'     => edd_get_option( 'edd_ftbg_first_time_buyer_discount_amount' ),
				'start'      => '-1 day',
				'type'       => edd_get_option( 'edd_ftbg_first_time_buyer_discount_type' ),
				'use_once'   => true
			);

			//* Allow the default discount args to be filtered
			$args = wp_parse_args( apply_filters( 'edd_ftbg_discount_args', $args ), $default_discount_args );

			//* Create/save the discount
			$discount_id = edd_store_discount( $args );

			//* After the discount has been created
			$user_id = $this->user_id;
			$discount_code = $this->discount_code;
			do_action( 'edd_ftbg_after_discount_registered', $user_id, $discount_code );

		}

	}

	public function edd_ftbg_get_discount() {

		return $this->discount_code;

	}

}

//* Instantiate the class
$EDD_FTBG = new EDD_First_Time_Buyers_Gift;
