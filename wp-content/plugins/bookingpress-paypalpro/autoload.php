<?php

if (is_ssl()) {
    define('BOOKINGPRESS_PAYPALPRO_URL', str_replace('http://', 'https://', WP_PLUGIN_URL . '/' . BOOKINGPRESS_PAYPALPRO_DIR_NAME));
} else {
    define('BOOKINGPRESS_PAYPALPRO_URL', WP_PLUGIN_URL . '/' . BOOKINGPRESS_PAYPALPRO_DIR_NAME);
}

if(file_exists(BOOKINGPRESS_PAYPALPRO_DIR . "/core/classes/class.bookingpress-paypalpro.php") ){
	require_once BOOKINGPRESS_PAYPALPRO_DIR . "/core/classes/class.bookingpress-paypalpro.php";
}

if(file_exists(BOOKINGPRESS_PAYPALPRO_DIR . "/core/classes/class.bookingpress-paypalpro-payment.php") ){
	require_once BOOKINGPRESS_PAYPALPRO_DIR . "/core/classes/class.bookingpress-paypalpro-payment.php";
}

global $bookingpress_paypalpro_version;
$bookingpress_paypalpro_version = '1.4';
define('BOOKINGPRESS_PAYPALPRO_VERSION', $bookingpress_paypalpro_version);

load_plugin_textdomain( 'bookingpress-paypalpro', false, 'bookingpress-paypalpro/languages/' );


define( 'BOOKINGPRESS_PAYPALPRO_STORE_URL', 'https://www.bookingpressplugin.com/' );

if ( ! class_exists( 'bookingpress_pro_updater' ) ) {
	require_once BOOKINGPRESS_PAYPALPRO_DIR . '/core/classes/class.bookingpress_pro_plugin_updater.php';
}


function bookingpress_paypalpro_plugin_updater() {
	
	$plugin_slug_for_update = 'bookingpress-paypalpro/bookingpress-paypalpro.php';

	// To support auto-updates, this needs to run during the wp_version_check cron job for privileged users.
	$doing_cron = defined( 'DOING_CRON' ) && DOING_CRON;
	if ( ! current_user_can( 'manage_options' ) && ! $doing_cron ) {
		return;
	}

	// retrieve our license key from the DB
	$license_key = trim( get_option( 'bkp_paypalpro_license_key' ) );
	$package = trim( get_option( 'bkp_paypalpro_license_package' ) );

	// setup the updater
	$edd_updater = new bookingpress_pro_updater(
		BOOKINGPRESS_PAYPALPRO_STORE_URL,
		$plugin_slug_for_update,
		array(
			'version' => BOOKINGPRESS_PAYPALPRO_VERSION,  // current version number
			'license' => $license_key,             // license key (used get_option above to retrieve from DB)
			'item_id' => $package,       // ID of the product
			'author'  => 'Repute Infosystems', // author of this plugin
			'beta'    => false,
		)
	);

}
add_action( 'init', 'bookingpress_paypalpro_plugin_updater' );
?>