<?php
/*
Plugin Name: BookingPress - SMS Notification Addon
Description: Extension for BookingPress plugin to send SMS notification upon appointment booking, appointment cancel, appointment reschedule etc.
Version: 2.0
Requires at least: 5.0
Requires PHP:      5.6
Plugin URI: https://www.bookingpressplugin.com/
Author: Repute InfoSystems
Author URI: https://www.bookingpressplugin.com/
Text Domain: bookingpress-sms
Domain Path: /languages
*/

define('BOOKINGPRESS_SMS_DIR_NAME', 'bookingpress-sms');
define('BOOKINGPRESS_SMS_DIR', WP_PLUGIN_DIR . '/' . BOOKINGPRESS_SMS_DIR_NAME);

if (file_exists( BOOKINGPRESS_SMS_DIR . '/autoload.php')) {
    require_once BOOKINGPRESS_SMS_DIR . '/autoload.php';
}