<?php

/**
 * Delete options when the plugin is deleted
 *
 * @package EDD First Time Buyer's Gift
 * @author Ren Ventura
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) )
    exit();

function edd_ftbg_run_uninstall() {

	$settings = edd_get_settings();

	foreach ( $settings as $key => $val ) {

		if ( substr( $key, 0, 9 ) === 'edd_ftbg_' )
			edd_delete_option( $key );

	}

}

edd_ftbg_run_uninstall();