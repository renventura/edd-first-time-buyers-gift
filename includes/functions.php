<?php

/**
 *	Whether the First Time Buyer's Gift option is enabled
 *
 *	@return boolean - true if enabled, false if not
 */
function is_edd_ftbg_enabled() {

	if ( edd_get_option( 'edd_ftbg_enable_gifts' ) )
		return true;

	else
		return false;

}