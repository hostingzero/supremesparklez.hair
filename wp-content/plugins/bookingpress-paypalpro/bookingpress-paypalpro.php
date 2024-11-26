<?php
/*
Plugin Name: BookingPress - Paypalpro Payment Gateway Addon
Description: Extension for BookingPress plugin to accept payments using Paypalpro Payment Gateway.
Version: 1.4
Requires at least: 5.0
Requires PHP:      5.6
Plugin URI: https://www.bookingpressplugin.com/
Author: Repute InfoSystems
Author URI: https://www.bookingpressplugin.com/
Text Domain: bookingpress-paypalpro
Domain Path: /languages
*/

define('BOOKINGPRESS_PAYPALPRO_DIR_NAME', 'bookingpress-paypalpro');
define('BOOKINGPRESS_PAYPALPRO_DIR', WP_PLUGIN_DIR . '/' . BOOKINGPRESS_PAYPALPRO_DIR_NAME);

if (file_exists( BOOKINGPRESS_PAYPALPRO_DIR . '/autoload.php')) {
    require_once BOOKINGPRESS_PAYPALPRO_DIR . '/autoload.php';
}