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

//* Include required files
require_once 'includes/functions.php';
require_once 'includes/admin/register-settings.php';
include_once 'includes/admin/shortcodes.php';

/**
 *	Main plugin class
 *	@since 1.0
 */
class EDD_First_Time_Buyers_Gift {

	private $user_id, $user_email, $user_purchases, $discount_id, $discount_code;

	public function __construct() {

		//* Plugin constants
		define( 'EDD_FTBG_VERSION', '1.0' );
		define( 'EDD_FTBG_PLUGIN_FILE', __FILE__ );
		define( 'EDD_FTBG_PLUGIN_BASENAME', untrailingslashit( plugin_basename( __FILE__ ) ) );

		//* Make sure EDD is running, bail if not
		register_activation_hook( __FILE__, array( $this, 'edd_ftbg_plugin_activate' ) );

		//* Run process after a purchase is made
		add_action( 'edd_complete_purchase', array( $this, 'edd_ftbg_register_discount' ) );

		//* Allow the user's custom notice to be used in emails
		add_action( 'edd_add_email_tags', array( $this, 'edd_ftbg_add_email_tag' ), 999 );

	}

	public function edd_ftbg_plugin_activate() {

		if ( ! is_plugin_active( 'easy-digital-downloads/easy-digital-downloads.php' ) ) {

			deactivate_plugins( EDD_FTBG_PLUGIN_BASENAME );

			wp_die( sprintf( __( 'The EDD First Time Buyer\'s Gift requires Easy Digital Downloads to be active.', 'edd' ) ) );

		}

	}

	/**
	 *	Register the discount (/includes/payments/actions.php)
	 *	@param $payment_id
	 */
	public function edd_ftbg_register_discount( $payment_id ) {

		//* Get some info about the buyer
		$this->user_id = intval( get_post_meta( $payment_id, '_edd_payment_user_id', true ) );
		$this->user_email = get_post_meta( $payment_id, '_edd_payment_user_email', true );

		/**
		 *	Get number of purchases of the buyer (/includes/user-functions.php)
		 *
		 *	Returns total number of purchases a customer has made
		 *
		 *	@param       $user mixed - ID or email
		 *	@return      int - the total number of purchases
		 */
		$this->user_purchases = edd_count_purchases_of_customer( $this->user_id );

		/**
		 *	Add the discount if buyer has not purchased before and the purchase is at least as much as the mimium total required
		 *	Existing purchases must equal 1 because this process runs after a customer's first purchase, which makes the total number of purchases equal to 1
		 */
		if ( $this->user_purchases == 1 && edd_get_payment_amount( $payment_id ) >= edd_get_option( 'edd_ftbg_first_time_buyer_min_total' ) ) {

			//* Generate 18 character code
			$this->discount_code = substr( md5( $this->user_email ), 0, 18 );

			//* Bail if the discount exists
			if ( edd_get_discount_by_code( $this->discount_code ) )
				return;

			//* Default settings
			$amount = !edd_get_option( 'edd_ftbg_first_time_buyer_discount_amount' ) ? 1 : edd_get_option( 'edd_ftbg_first_time_buyer_discount_amount' );
			$type = !edd_get_option( 'edd_ftbg_first_time_buyer_discount_type' ) ? 'flat' : edd_get_option( 'edd_ftbg_first_time_buyer_discount_type' );

			//* Assemble the discount
			$default_discount_args = array(
				'name' => $this->user_email,
				'code' => $this->discount_code,
				'type' => $type,
				'amount' => $amount,
				'start'  => '-1 day',
				'max' => 1,
				'use_once' => true,
				//'min_price' => '', # min price for the discount to be applied
				//'product_reqs'      => array(), # IDs of products that are required to use the discount
				//'product_condition' => '', # any or all pertaining to the required products
				//'is_not_global' => '', # 0 for specific products or 1 for entire purchase
				//'status' => '', # active or inactive
				//'excluded_products' => array() # IDs of products the discount cannot be used on
			);

			//* Allow the default discount args to be filtered
			$args = wp_parse_args( apply_filters( 'edd_ftbg_discount_args', $args ), $default_discount_args );

			//* Create/save the discount
			$this->discount_id = edd_store_discount( $args );

			//* After the discount has been created
			$user_id = $this->user_id;
			$discount_code = $this->discount_code;
			do_action( 'edd_ftbg_after_discount_registered', $user_id, $discount_code );

		}

	}

	public function edd_ftbg_add_email_tag() {

		edd_add_email_tag( 'edd_ftbg_gift_notice', 'Insert the custom message you entered to display an eligible first-time buyer\'s discount code and message.', array( $this, 'edd_ftbg_add_email_tag_callback' ) );

	}

	public function edd_ftbg_add_email_tag_callback() {

		return do_shortcode( '[edd_ftbg_gift_notice]' );

	}

}

/**
 *	Run the functionality if the setting is enabled
 *	@since 1.0
 */
add_action( 'plugins_loaded', 'edd_ftbg_run_if_enabled' );
function edd_ftbg_run_if_enabled() {

	//* Instantiate the class
	if ( function_exists( 'is_edd_ftbg_enabled' ) && is_edd_ftbg_enabled() )
		$EDD_FTBG = new EDD_First_Time_Buyers_Gift;

}
