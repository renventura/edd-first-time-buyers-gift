<?php

/**
 *	Delete options when the plugin is deleted
 *
 *	@package EDD First Time Buyer's Gift
 *	@since 1.0
 *	@author Ren Ventura
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) )
    exit();

$settings = edd_get_settings();

if ( $settings ) {

	foreach ( $settings as $key => $val ) {

		if ( substr( $key, 0, 9 ) === 'edd_ftbg_' )
			edd_delete_option( $key );

	}

}
