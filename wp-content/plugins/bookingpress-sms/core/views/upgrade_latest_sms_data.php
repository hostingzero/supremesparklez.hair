<?php

global $BookingPress, $wpdb, $bookingpress_sms_version;
$bookingpress_sms_old_version = get_option( 'bookingpress_sms_gateway' );

if (version_compare($bookingpress_sms_old_version, '1.4', '<') ) {
    
    $tbl_bookingpress_notifications = $wpdb->prefix . 'bookingpress_notifications';
    $wpdb->query("ALTER TABLE {$tbl_bookingpress_notifications} ADD bookingpress_sms_admin_number VARCHAR(60) NULL DEFAULT NULL AFTER bookingpress_send_sms_notification");  // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_notifications is table name defined globally. False Positive alarm

}

if (version_compare($bookingpress_sms_old_version, '1.5', '<') ) {

    global $BookingPress;
    $selected_sms_api = $BookingPress->bookingpress_get_settings('bookingpress_selected_sms_gateway','notification_setting');

    if( !empty( $selected_sms_api ) && 'SMS API' == $selected_sms_api ){
        update_option('bookingpress_display_sms_api_update_notice',true);
    }    

}

if(version_compare($bookingpress_sms_old_version, '1.8', '<')){

    global $BookingPress;
    $selected_sms_api = $BookingPress->bookingpress_get_settings('bookingpress_selected_sms_gateway','notification_setting');

    if( (!empty( $selected_sms_api ) && 'RingCaptcha' == $selected_sms_api) || (!empty( $selected_sms_api ) && 'Routee' == $selected_sms_api) ){
        update_option('bookingpress_display_sms_api_update_notice_warning',true);
    }
}


$bookingpress_sms_new_version = '2.0';
update_option('bookingpress_sms_gateway', $bookingpress_sms_new_version);
update_option('bookingpress_sms_gateway_updated_date_' . $bookingpress_sms_new_version, current_time('mysql'));