<?php

global $BookingPress, $wpdb;

$bookingpress_old_paypalpro_version = get_option('bookingpress_paypalpro_payment_gateway', true);

if (version_compare($bookingpress_old_paypalpro_version, '1.3', '<') ) {

    $tbl_bookingpress_customize_settings = $wpdb->prefix . 'bookingpress_customize_settings';
    $booking_form = array(
        'paypalpro_text' => __('Credit Card', 'bookingpress-paypalpro'),
    );
    foreach($booking_form as $key => $value) {
        $bookingpress_customize_settings_db_fields = array(
            'bookingpress_setting_name'  => $key,
            'bookingpress_setting_value' => $value,
            'bookingpress_setting_type'  => 'package_booking_form',
        );
        $wpdb->insert( $tbl_bookingpress_customize_settings, $bookingpress_customize_settings_db_fields );
    }

}

if (version_compare($bookingpress_old_paypalpro_version, '1.4', '<') ) {

    $tbl_bookingpress_customize_settings = $wpdb->prefix . 'bookingpress_customize_settings';
    $booking_form = array(
        'paypalpro_text' => __('Credit Card', 'bookingpress-paypalpro'),
    );
    foreach($booking_form as $key => $value) {
        $bookingpress_customize_settings_db_fields = array(
            'bookingpress_setting_name'  => $key,
            'bookingpress_setting_value' => $value,
            'bookingpress_setting_type'  => 'gift_card_form',
        );
        $wpdb->insert( $tbl_bookingpress_customize_settings, $bookingpress_customize_settings_db_fields );
    }

}

$bookingpress_paypalpro_new_version = '1.4';
update_option('bookingpress_paypalpro_payment_gateway', $bookingpress_paypalpro_new_version);
update_option('bookingpress_paypalpro_updated_date_' . $bookingpress_paypalpro_new_version, current_time('mysql'));

?>