<?php

if (!class_exists('bookingpress_sms') && class_exists( 'BookingPress_Core') ) {
	class bookingpress_sms Extends BookingPress_Core {
        function __construct() {
            register_activation_hook(BOOKINGPRESS_SMS_DIR.'/bookingpress-sms.php', array('bookingpress_sms', 'install'));
            register_uninstall_hook(BOOKINGPRESS_SMS_DIR.'/bookingpress-sms.php', array('bookingpress_sms', 'uninstall'));
            
            //Admiin notices
            add_action('admin_notices', array($this, 'bookingpress_admin_notices'));
            if( !function_exists('is_plugin_active') ){
                include_once ABSPATH . 'wp-admin/includes/plugin.php';
            }

            if(is_plugin_active('bookingpress-appointment-booking-pro/bookingpress-appointment-booking-pro.php')) {                

                add_action('admin_enqueue_scripts',array($this,'set_sms_css'),11);
                add_action('bookingpress_add_notification_settings_section', array($this, 'bookingpress_add_notification_settings_section_func'));
                add_action( 'bookingpress_add_setting_dynamic_vue_methods', array( $this, 'bookingpress_add_setting_dynamic_vue_methods_func' ) );                

                //Hook for add view for message of sms notification
                add_action('bookingpress_add_email_notification_section', array($this, 'bookingpress_add_email_notification_section_func'),11);
                add_filter('bookingpress_add_dynamic_notification_data_fields', array($this, 'bookingpress_add_dynamic_notification_data_fields_func'));

                //Hook for pass sms notification data when click on save button
                add_action('bookingpress_add_email_notification_data', array($this, 'bookingpress_add_email_notification_data_func'));

                //Modify database save values
                add_filter('bookingpress_save_email_notification_data_filter', array($this, 'bookingpress_save_email_notification_data_filter_func'), 10, 2);

                //Set value of sms notification message when email notification data retrieved
                add_action('bookingpress_email_notification_get_data', array($this, 'bookingpress_email_notification_get_data_func'));

                add_action('wp_ajax_bookingpress_send_test_sms', array($this, 'bookingpress_send_test_sms_func'));

                //add value to debug log array
                add_filter('bookingpress_add_integration_debug_logs', array($this, 'bookingpress_add_integration_debug_logs_func'), 10, 1);

                //Filter for add data variables for debug logs
                add_filter('bookingpress_add_setting_dynamic_data_fields', array( $this, 'bookingpress_add_setting_dynamic_data_fields_func' ), 10 );

                add_filter('bookingpress_get_notifiacation_data_filter',array($this,'bookingpress_get_notifiacation_data_filter_func'));

                //Send SMS notification after appointment booked
                add_action('bookingpress_after_book_appointment', array($this, 'bookingpress_send_sms_notification_after_appointment_booked'), 11, 3);
                add_filter('bookingpress_addon_list_data_filter',array($this,'bookingpress_addon_list_data_filter_func'));

                //Send SMS notification after appointment booked from backend
                add_action('bookingpress_after_add_appointment_from_backend', array($this, 'bookingpress_after_add_appointment_from_backend_func'), 11, 3);

                //After reschedule appointment
                add_action('bookingpress_after_rescheduled_appointment', array($this, 'bookingpress_after_reschedule_appointment_func'),11);
                add_action('bookingpress_after_update_appointment', array($this,'bookingpress_after_update_appointment_func'));

                //After cancel appointment
                add_action('bookingpress_after_cancel_appointment', array($this, 'bookingpress_after_cancel_appointment_func'),11);

                //-- Add New Function For Waiting List TO Send Custom SMS Notification For Waiting List
                add_action('bookingpress_send_custom_status_sms_notification', array($this, 'bookingpress_send_custom_status_sms_notification_func'),11,2);


                //After refund appointment
                add_action('bookingpress_after_refund_appointment', array($this, 'bookingpress_after_refund_appointment_func'),11);                

                //After change status from backend
                add_action('bookingpress_after_change_appointment_status', array($this, 'bookingpress_after_change_appointment_status_func'), 11, 2);

                //Customer Cron SMS notification
                add_action('bookingpress_cron_external_notification', array($this, 'bookingpress_cron_external_notification_func'), 11, 4);

                //Staff Cron SMS notification
                add_action('bookingpress_staff_cron_external_notification', array($this, 'bookingpress_staff_cron_external_notification_func'), 11, 4);

                add_filter('bookingpress_modify_capability_data', array($this, 'bookingpress_modify_capability_data_func'), 11, 1);

                //Share URLs Hooks
                add_action('bookingpress_add_more_sharing_url_content_for_appointment', array($this, 'bookingpress_add_more_sharing_url_content_for_appointment_func'), 10);
                add_action('bookingpress_add_more_sharing_url_options_for_appointment', array($this, 'bookingpress_add_more_sharing_url_options_for_appointment_func'), 10);
                add_filter('bookingpress_modify_appointment_data_fields', array($this, 'bookingpress_modify_appointment_data_fields_func'), 11);
                add_action('bpa_externally_share_appointment_url', array($this, 'bpa_externally_share_appointment_url_func'));

                //Complete Payment Hooks
                add_action('bookingpress_add_more_complete_payment_link_option', array($this, 'bookingpress_add_more_complete_payment_link_option_func'));
                add_action('bookingpress_send_complete_payment_link_externally', array($this, 'bookingpress_send_complete_payment_link_externally_func'), 10, 2);
		 add_filter('bookingpress_modify_email_notification_data_for_extrnal_notification',array($this,'bookingpress_modify_email_notification_data_for_extrnal_notification_func'),10,4);
                add_action( 'bookingpress_page_admin_notices', array( $this, 'bookingpress_display_sms_api_method_validity_notices') );
                add_action( 'boookingpress_after_save_settings_data', array( $this, 'bookingpress_update_api_notice_flag'));
                add_filter('bookingpress_modified_notification_manage_language_translate_fields',array($this,'bookingpress_modified_notification_manage_language_translate_fields_func'),10,1);
			}

            add_action('activated_plugin',array($this,'bookingpress_is_sms_addon_activated'),11,2);

            add_action( 'admin_init', array( $this, 'bookingpress_update_sms_data') );
		}
		
        function bookingpress_modified_notification_manage_language_translate_fields_func($bookingpress_all_language_translation_fields){			
            if(isset($bookingpress_all_language_translation_fields['manage_notification_customer'])){
                $bookingpress_all_language_translation_fields['manage_notification_customer']['bookingpress_sms_notification_message'] = array('field_type'=>'textarea','field_label'=>__('SMS Notification Message', 'bookingpress-sms'),'save_field_type'=>'manage_notification_customer'); 
            }
			if(isset($bookingpress_all_language_translation_fields['manage_notification_employee'])){
                $bookingpress_all_language_translation_fields['manage_notification_employee']['bookingpress_sms_notification_message'] = array('field_type'=>'textarea','field_label'=>__('SMS Notification Message', 'bookingpress-sms'),'save_field_type'=>'manage_notification_customer'); 
            } 
            return $bookingpress_all_language_translation_fields;
        }

        function bookingpress_update_api_notice_flag( $posted_data ){

            if( !empty( $posted_data ) && 'SMS API' == $posted_data['bookingpress_selected_sms_gateway']){
                if( !empty( $posted_data['bookingpress_selected_sms_api_endpoint'] ) ){
                    update_option( 'bookingpress_display_sms_api_update_notice', false );    
                }
            } else {
                update_option( 'bookingpress_display_sms_api_update_notice', false );
            }

            if( !empty( $posted_data ) && 'RingCaptcha' == $posted_data['bookingpress_selected_sms_gateway'] ){
                if( !empty( $posted_data['bookingpress_selected_ringcaptcha_locale'] ) ){
                    update_option( 'bookingpress_display_sms_api_update_notice_warning', false );    
                }
            } else {
                update_option( 'bookingpress_display_sms_api_update_notice_warning', false );
            }

            if( !empty( $posted_data ) && 'Routee' == $posted_data['bookingpress_selected_sms_gateway'] ){
                if( !empty( $posted_data['routee_application_id'] ) && !empty( $posted_data['routee_application_secret']) ){
                    update_option( 'bookingpress_display_sms_api_update_notice_warning', false );    
                }
            } else {
                update_option( 'bookingpress_display_sms_api_update_notice_warning', false );
            }
        }

        function bookingpress_display_sms_api_method_validity_notices(){
            if( !is_admin() ){
                return;
            }

            global $wpdb,$BookingPress;

            $bookingpress_sms_new_installment = get_option('bookingpress_sms_new_installment');
            $bookingpress_sms_new_installment_chk = get_option('bookingpress_sms_new_installment_check');
            $selected_sms_api = $BookingPress->bookingpress_get_settings('bookingpress_selected_sms_gateway','notification_setting');
            $bookingpress_sms_update = get_option('bookingpress_display_sms_api_update_notice');
            $bookingpress_sms_update_notice = get_option('bookingpress_display_sms_api_update_notice_warning');

            if( empty($bookingpress_sms_new_installment) && !empty($selected_sms_api) && $selected_sms_api == 'SMS API' && !empty( $bookingpress_sms_update) && $bookingpress_sms_update == 1  ){
                $sms_redirect_link = admin_url( 'admin.php?page=bookingpress_settings&setting_page=notification_settings&setting_tab=sms');
                ?>
                    <div class="bpa-pg-warning-belt-box">
                        <p class="bpa-wbb__desc">
                            <span class="material-icons-round">warning</span>
						    <?php /* translators: 1. General settings url */ ?>
                            <?php echo sprintf( esc_html__("The API for 'SMS API' SMS Gateway has been changed. Please update your configurations with the latest changes or else the SMS might not sent. %s.", 'bookingpress-sms'), '<a href="'.esc_url($sms_redirect_link).'">here</a>' ); ?>
                        </p>
                    </div>
                <?php
            }

            if( empty($bookingpress_sms_new_installment_chk) && !empty($selected_sms_api) && $selected_sms_api == 'RingCaptcha' && !empty( $bookingpress_sms_update_notice) && $bookingpress_sms_update_notice == 1  ){
                $sms_redirect_link = admin_url( 'admin.php?page=bookingpress_settings&setting_page=notification_settings&setting_tab=sms');
                ?>
                    <div class="bpa-pg-warning-belt-box">
                        <p class="bpa-wbb__desc">
                            <span class="material-icons-round bpa-wbb__desc-icon">warning</span>
                            <span class="bpa-wbb__desc-content">
                                <?php /* translators: 1. General settings url */ ?>
                                <?php echo sprintf( esc_html__("The API for 'RingCaptcha' SMS Gateway has been changed. Please update your configurations with the latest changes or else the SMS might not sent. %s.", 'bookingpress-sms'), '<a href="'.esc_url($sms_redirect_link).'">here</a>' ); ?>
                            </span>
                        </p>
                    </div>
                <?php
            }

            if( empty($bookingpress_sms_new_installment_chk) && !empty($selected_sms_api) && $selected_sms_api == 'Routee' && !empty( $bookingpress_sms_update_notice) && $bookingpress_sms_update_notice == 1  ){
                $sms_redirect_link = admin_url( 'admin.php?page=bookingpress_settings&setting_page=notification_settings&setting_tab=sms');
                ?>
                    <div class="bpa-pg-warning-belt-box">
                        <p class="bpa-wbb__desc">
                            <span class="material-icons-round bpa-wbb__desc-icon">warning</span>
                            <span class="bpa-wbb__desc-content">
                                <?php /* translators: 1. General settings url */ ?>
                                <?php echo sprintf( esc_html__("The API for 'Routee' SMS Gateway has been changed. Please update your configurations with the latest changes or else the SMS might not sent. %s.", 'bookingpress-sms'), '<a href="'.esc_url($sms_redirect_link).'">here</a>' ); ?>
                            </span>
                        </p>
                    </div>
                <?php
            }
        }

	 function bookingpress_modify_email_notification_data_for_extrnal_notification_func($bookingpress_email_data,$notification_from,$template_type,$notification_event_action) {
            global $tbl_bookingpress_notifications,$wpdb;
            if(!empty($notification_from) && $notification_from == 'sms') {                
                $bookingpress_email_data = $wpdb->get_results( $wpdb->prepare( "SELECT bookingpress_notification_name,bookingpress_notification_service FROM {$tbl_bookingpress_notifications} WHERE bookingpress_notification_receiver_type = %s AND bookingpress_notification_type = %s AND bookingpress_notification_event_action = %s AND bookingpress_send_sms_notification = %d AND bookingpress_custom_notification_type = %s ORDER BY bookingpress_notification_id DESC", $template_type,'custom', $notification_event_action,1,'action-trigger' ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_notifications is a table name. false alarm                
            }
            return $bookingpress_email_data;
        }
		

        function bookingpress_is_sms_addon_activated($plugin,$network_activation)
        {              
            $myaddon_name = "bookingpress-sms/bookingpress-sms.php";

            if($plugin == $myaddon_name)
            {

                if(!(is_plugin_active('bookingpress-appointment-booking-pro/bookingpress-appointment-booking-pro.php')))
                {
                    deactivate_plugins($myaddon_name, FALSE);
                    $redirect_url = network_admin_url('plugins.php?deactivate=true&bkp_license_deactivate=true&bkp_deactivate_plugin='.$myaddon_name);
                    $bpa_dact_message = __('Please activate license of BookingPress premium plugin to use BookingPress SMS Add-on', 'bookingpress-sms');
					$bpa_link = sprintf( __('Please %s Click Here %s to Continue', 'bookingpress-sms'), '<a href="javascript:void(0)" onclick="window.location.href=\'' . $redirect_url . '\'">', '</a>');
					wp_die('<p>'.$bpa_dact_message.'<br/>'.$bpa_link.'</p>');
                    die;
                }

                $license = trim( get_option( 'bkp_license_key' ) );
                $package = trim( get_option( 'bkp_license_package' ) );

                if( '' === $license || false === $license ) 
                {
                    deactivate_plugins($myaddon_name, FALSE);
                    $redirect_url = network_admin_url('plugins.php?deactivate=true&bkp_license_deactivate=true&bkp_deactivate_plugin='.$myaddon_name);
                    $bpa_dact_message = __('Please activate license of BookingPress premium plugin to use BookingPress SMS Add-on', 'bookingpress-sms');
					$bpa_link = sprintf( __('Please %s Click Here %s to Continue', 'bookingpress-sms'), '<a href="javascript:void(0)" onclick="window.location.href=\'' . $redirect_url . '\'">', '</a>');
					wp_die('<p>'.$bpa_dact_message.'<br/>'.$bpa_link.'</p>');
                    die;
                }
                else
                {
                    $store_url = BOOKINGPRESS_SMS_STORE_URL;
                    $api_params = array(
                        'edd_action' => 'check_license',
                        'license' => $license,
                        'item_id'  => $package,
                        //'item_name' => urlencode( $item_name ),
                        'url' => home_url()
                    );
                    $response = wp_remote_post( $store_url, array( 'body' => $api_params, 'timeout' => 15, 'sslverify' => false ) );
                    if ( is_wp_error( $response ) ) {
                        return false;
                    }
        
                    $license_data = json_decode( wp_remote_retrieve_body( $response ) );
                    $license_data_string =  wp_remote_retrieve_body( $response );
        
                    $message = '';

                    if ( true === $license_data->success ) 
                    {
                        if($license_data->license != "valid")
                        {
                            deactivate_plugins($myaddon_name, FALSE);
                            $redirect_url = network_admin_url('plugins.php?deactivate=true&bkp_license_deactivate=true&bkp_deactivate_plugin='.$myaddon_name);
                            $bpa_dact_message = __('Please activate license of BookingPress premium plugin to use BookingPress SMS Add-on', 'bookingpress-sms');
                            $bpa_link = sprintf( __('Please %s Click Here %s to Continue', 'bookingpress-sms'), '<a href="javascript:void(0)" onclick="window.location.href=\'' . $redirect_url . '\'">', '</a>');
                            wp_die('<p>'.$bpa_dact_message.'<br/>'.$bpa_link.'</p>');
                            die;
                        }

                    }
                    else
                    {
                        deactivate_plugins($myaddon_name, FALSE);
                        $redirect_url = network_admin_url('plugins.php?deactivate=true&bkp_license_deactivate=true&bkp_deactivate_plugin='.$myaddon_name);
                        $bpa_dact_message = __('Please activate license of BookingPress premium plugin to use BookingPress SMS Add-on', 'bookingpress-sms');
                        $bpa_link = sprintf( __('Please %s Click Here %s to Continue', 'bookingpress-sms'), '<a href="javascript:void(0)" onclick="window.location.href=\'' . $redirect_url . '\'">', '</a>');
                        wp_die('<p>'.$bpa_dact_message.'<br/>'.$bpa_link.'</p>');
                        die;
                    }

                }
            }
        }
	
	function bookingpress_send_complete_payment_link_externally_func($bookingpress_appointment_data, $bookingpress_selected_options){
            global $wpdb, $BookingPress, $tbl_bookingpress_appointment_bookings;

            if( !empty($bookingpress_appointment_data) && in_array("sms", $bookingpress_selected_options) ){
                $inserted_booking_id = $bookingpress_appointment_data['bookingpress_appointment_booking_id'];
                $bookingpress_phone_no = $this->bookingpress_get_appointment_phone_number($inserted_booking_id);
                
                $bookingpress_configured_options = array();
                $this->bookingpress_send_all_sms_function($bookingpress_phone_no, '', 0, $inserted_booking_id, $bookingpress_configured_options, 'Complete Payment URL', 'customer');

                $bookingpress_sms_number = '';
                $this->bookingpress_send_all_sms_function($bookingpress_sms_number, '', 0, $inserted_booking_id, $bookingpress_configured_options, 'Complete Payment URL', 'employee');
                                
                //Send staff email notification
                $bookingpress_staffmember_details = !empty($bookingpress_appointment_data['bookingpress_staff_member_details']) ? json_decode($bookingpress_appointment_data['bookingpress_staff_member_details'], TRUE) : array();

                if(!empty($bookingpress_staffmember_details)){
                    $bookingpress_staff_phone_no = !empty($bookingpress_staffmember_details['bookingpress_staffmember_phone']) ? $bookingpress_staffmember_details['bookingpress_staffmember_phone'] : '';

                    $bookingpress_staff_country_dial_code = !empty($bookingpress_staffmember_details['bookingpress_staffmember_country_dial_code']) ? $bookingpress_staffmember_details['bookingpress_staffmember_country_dial_code'] : '';

                    if(!empty($bookingpress_staff_phone_no)){
                        $bookingpress_staff_phone_no = preg_replace('/^0/', '', $bookingpress_staff_phone_no);
                    }

                    if(!empty($bookingpress_staff_country_dial_code)){
                        $bookingpress_staff_phone_no = "+".$bookingpress_staff_country_dial_code."".$bookingpress_staff_phone_no;
                    }
                    if(!empty($bookingpress_staff_phone_no)){
                        $this->bookingpress_send_all_sms_function($bookingpress_staff_phone_no, '', 0, $inserted_booking_id, $bookingpress_configured_options, 'Complete Payment URL', 'employee');
                    }
                }
            }
        }

        function bookingpress_add_more_complete_payment_link_option_func(){
            ?>
            <el-checkbox class="bpa-front-label bpa-custom-checkbox--is-label" label="sms"><?php esc_html_e( 'Through SMS', 'bookingpress-sms' ); ?></el-checkbox>
            <?php
        }
		
	    function set_sms_css() {
            wp_register_style('bookingpress_sms_admin_css',BOOKINGPRESS_SMS_URL. '/css/bookingpress_sms_admin.css',array(),BOOKINGPRESS_SMS_VERSION);
            if ( isset( $_REQUEST['page'] ) && (sanitize_text_field( $_REQUEST['page'] ) == 'bookingpress_settings')) {
                wp_enqueue_style('bookingpress_sms_admin_css');
            }           
        }

        function bpa_externally_share_appointment_url_func($bpa_share_url_form_data){
            global $BookingPress;
            if( !empty($bpa_share_url_form_data['sms_sharing']) && ($bpa_share_url_form_data['sms_sharing'] == "true") && !empty($bpa_share_url_form_data['phone_number']) ){
                $bookingpress_phone_no = $bpa_share_url_form_data['phone_number'];
                $bookingpress_configured_options = array();
                $this->bookingpress_send_sms_function($bookingpress_phone_no, '', 0, 0, $bookingpress_configured_options, 'Share Appointment URL', 'customer');
            }
        }

        function bookingpress_modify_appointment_data_fields_func($bookingpress_appointment_vue_data_fields){
            $bookingpress_appointment_vue_data_fields['share_url_form']['phone_number'] = '';
            $bookingpress_appointment_vue_data_fields['share_url_form']['sms_sharing'] = false;
            $bookingpress_appointment_vue_data_fields['share_url_rules']['phone_number'] = array(
				array(
					'required' => true,
					'message'  => __('Please enter phone number', 'bookingpress-sms'),
					'trigger'  => 'blur',
				),
			);
            return $bookingpress_appointment_vue_data_fields;
        }

        function bookingpress_add_more_sharing_url_options_for_appointment_func(){
            ?>
                <label class="bpa-form-label bpa-custom-checkbox--is-label"> <el-checkbox v-model="share_url_form.sms_sharing" @change="bpa_enable_service_share"></el-checkbox> <?php esc_html_e( 'SMS', 'bookingpress-sms' ); ?></label>
            <?php
        }

        function bookingpress_add_more_sharing_url_content_for_appointment_func(){
            ?>
                <div class="bpa-form-body-row" v-if="share_url_form.sms_sharing == true">
                    <el-row>
                        <el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
                            <el-form-item prop="phone_number">
                                <template #label>
                                    <span class="bpa-form-label"><?php echo esc_html__('Phone Number', 'bookingpress-sms'); ?></span>
                                </template>
                                <el-input class="bpa-form-control" v-model="share_url_form.phone_number" placeholder="<?php esc_html_e('Enter phone number', 'bookingpress-sms'); ?>" @blur="bpa_enable_service_share"></el-input>
                            </el-form-item>
                        </el-col>
                    </el-row>
                </div>
            <?php
        }

        function bookingpress_update_sms_data(){
            global $BookingPress,$bookingpress_sms_version;
            $bookingpress_sms_db_version = get_option( 'bookingpress_sms_gateway' );

            if( version_compare( $bookingpress_sms_db_version, '2.0', '<' ) ){
                $bookingpress_load_sms_update_file = BOOKINGPRESS_SMS_DIR . '/core/views/upgrade_latest_sms_data.php';
                include $bookingpress_load_sms_update_file;
                $BookingPress->bookingpress_send_anonymous_data_cron();
            }
        }

        function bookingpress_modify_capability_data_func($bpa_caps){
            $bpa_caps['bookingpress_settings'][] = 'bpa_send_test_sms';
            return $bpa_caps;
        }

        function bookingpress_check_cron_sms_notification_sent_or_not( $bookingpress_email_notification_id, $bookingpress_customer_id, $bookingpress_email_address, $bookingpress_appointment_id, $bookingpress_appointment_date, $bookingpress_appointment_time, $bookingpress_appointment_status, $bookingpress_hook_name, $bookingpress_staffmember_id = 0, $bookingpress_staffmember_email = '' ) {
			global $wpdb, $tbl_bookingpress_cron_email_notifications_logs, $BookingPress;

			if(empty($bookingpress_staffmember_id)) {                
				$bookingpress_is_record_exists = $wpdb->get_var( $wpdb->prepare( "SELECT * FROM {$tbl_bookingpress_cron_email_notifications_logs} WHERE bookingpress_email_notification_id = %d AND bookingpress_customer_id = %d AND bookingpress_email_address = %s AND bookingpress_appointment_id = %d AND bookingpress_appointment_date = %s AND bookingpress_appointment_time = %s AND bookingpress_appointment_status = %s AND bookingpress_email_cron_hook_name = %s AND bookingpress_staffmember_email = %s AND bookingpress_notification_type = %s", $bookingpress_email_notification_id, $bookingpress_customer_id, $bookingpress_email_address, $bookingpress_appointment_id, $bookingpress_appointment_date, $bookingpress_appointment_time, $bookingpress_appointment_status, $bookingpress_hook_name, $bookingpress_staffmember_email,'sms' ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_cron_email_notifications_logs is a table name. false alarm                
			}else if(!empty($bookingpress_staffmember_id)) {
				$bookingpress_is_record_exists = $wpdb->get_var( $wpdb->prepare( "SELECT * FROM {$tbl_bookingpress_cron_email_notifications_logs} WHERE bookingpress_email_notification_id = %d AND bookingpress_customer_id = %d AND bookingpress_email_address = %s AND bookingpress_appointment_id = %d AND bookingpress_appointment_date = %s AND bookingpress_appointment_time = %s AND bookingpress_appointment_status = %s AND bookingpress_email_cron_hook_name = %s AND bookingpress_staffmember_id = %d AND bookingpress_staffmember_email = %s AND bookingpress_notification_type = %s", $bookingpress_email_notification_id, $bookingpress_customer_id, $bookingpress_email_address, $bookingpress_appointment_id, $bookingpress_appointment_date, $bookingpress_appointment_time, $bookingpress_appointment_status, $bookingpress_hook_name, $bookingpress_staffmember_id, $bookingpress_staffmember_email,'sms' ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_cron_email_notifications_logs is a table name. false alarm
			}
			return $bookingpress_is_record_exists;
		}

        function bookingpress_staff_cron_external_notification_func($appointment_id, $bookingpress_email_notification_name, $bookingpress_notification_id, $bookingpress_db_fields){
            global $wpdb, $BookingPress, $tbl_bookingpress_appointment_bookings, $tbl_bookingpress_cron_email_notifications_logs;
            $bookingpress_appointment_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$tbl_bookingpress_appointment_bookings} WHERE bookingpress_appointment_booking_id = %d", $appointment_id), ARRAY_A); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_appointment_bookings is table name defined globally. False Positive alarm

            $bookingpress_customer_id = !empty($bookingpress_db_fields['bookingpress_customer_id']) ? $bookingpress_db_fields['bookingpress_customer_id'] : 0;
            $bookingpress_customer_email = !empty($bookingpress_db_fields['bookingpress_email_address']) ? $bookingpress_db_fields['bookingpress_email_address'] : '';
            $bookingpress_appointment_date = !empty($bookingpress_db_fields['bookingpress_appointment_date']) ? $bookingpress_db_fields['bookingpress_appointment_date'] : '';
            $bookingpress_appointment_time = !empty($bookingpress_db_fields['bookingpress_appointment_time']) ? $bookingpress_db_fields['bookingpress_appointment_time'] : '';               
            $bookingpress_appointment_status = !empty($bookingpress_db_fields['bookingpress_appointment_status']) ? $bookingpress_db_fields['bookingpress_appointment_status'] : '';                      
            $bookingpress_email_cron_hook_name = !empty($bookingpress_db_fields['bookingpress_email_cron_hook_name']) ? $bookingpress_db_fields['bookingpress_email_cron_hook_name'] : '';
            $bookingpress_staffmember_id = !empty($bookingpress_db_fields['bookingpress_staffmember_id']) ? intval($bookingpress_db_fields['bookingpress_staffmember_id']) : '';
            $bookingpress_staffmember_email = !empty($bookingpress_db_fields['bookingpress_staffmember_email']) ? ($bookingpress_db_fields['bookingpress_staffmember_email']) : '';

            $is_sent_notification = $this->bookingpress_check_cron_sms_notification_sent_or_not( $bookingpress_notification_id, $bookingpress_customer_id, $bookingpress_customer_email, $appointment_id,$bookingpress_appointment_date, $bookingpress_appointment_time,$bookingpress_appointment_status, $bookingpress_email_cron_hook_name,$bookingpress_staffmember_id, $bookingpress_staffmember_email);

            $bookingpress_configured_options = array();

            $bookingpress_sms_admin_number = '';
            $bookingpress_send_res = $this->bookingpress_send_all_sms_function($bookingpress_sms_admin_number, '', 0, $appointment_id, $bookingpress_configured_options, $bookingpress_email_notification_name, 'employee');

            if(!empty($bookingpress_appointment_data) && empty($is_sent_notification) ){
                $bookingpress_staffmember_details = !empty($bookingpress_appointment_data['bookingpress_staff_member_details']) ? json_decode($bookingpress_appointment_data['bookingpress_staff_member_details'], TRUE) : array();

                if(!empty($bookingpress_staffmember_details)){
                    $bookingpress_staff_phone_no = !empty($bookingpress_staffmember_details['bookingpress_staffmember_phone']) ? $bookingpress_staffmember_details['bookingpress_staffmember_phone'] : '';

                    $bookingpress_staff_country_dial_code = !empty($bookingpress_staffmember_details['bookingpress_staffmember_country_dial_code']) ? $bookingpress_staffmember_details['bookingpress_staffmember_country_dial_code'] : '';

                    if(!empty($bookingpress_staff_phone_no)){
                        $bookingpress_staff_phone_no = preg_replace('/^0/', '', $bookingpress_staff_phone_no);
                    }

                    if(!empty($bookingpress_staff_country_dial_code)){
                        $bookingpress_staff_phone_no = "+".$bookingpress_staff_country_dial_code."".$bookingpress_staff_phone_no;
                    }

                    $bookingpress_send_res = $this->bookingpress_send_sms_function($bookingpress_staff_phone_no, '', 0, $appointment_id, $bookingpress_configured_options, $bookingpress_email_notification_name, 'employee');

                    $to_message = !empty($bookingpress_send_res['to_message']) ? $bookingpress_send_res['to_message'] : '';
                    $is_notification_sent = !empty($bookingpress_send_res['return_type']) && $bookingpress_send_res['return_type'] == 'success' ?  1 : 0;
                    $bookingpress_sms_posted_data = array(
                        'template_type'     => 'employee',
                        'notification_name' => $bookingpress_email_notification_name,
                        'appointment_id'    => $appointment_id,
                        'customer_email'    => $bookingpress_customer_email,
                        'template_details'  => $to_message,
                    );
                    $bookingpress_db_fields['bookingpress_notification_type'] = 'sms';
                    $bookingpress_db_fields['bookingpress_email_is_sent'] =  $is_notification_sent;
                    $bookingpress_db_fields['bookingpress_email_posted_data'] = wp_json_encode( $bookingpress_sms_posted_data );
                    $bookingpress_db_fields['bookingpress_email_response'] = $bookingpress_send_res;
                    $bookingpress_db_fields['bookingpress_email_sending_configuration'] = wp_json_encode( $bookingpress_configured_options );
                    $wpdb->insert( $tbl_bookingpress_cron_email_notifications_logs, $bookingpress_db_fields );
                }
            }
        }

        function bookingpress_cron_external_notification_func($appointment_id, $bookingpress_email_notification_name, $bookingpress_notification_id, $bookingpress_db_fields){
            global $wpdb, $BookingPress, $tbl_bookingpress_appointment_bookings, $tbl_bookingpress_cron_email_notifications_logs;
            $bookingpress_appointment_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$tbl_bookingpress_appointment_bookings} WHERE bookingpress_appointment_booking_id = %d", $appointment_id), ARRAY_A); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_appointment_bookings is table name defined globally. False Positive alarm

            $bookingpress_customer_id = !empty($bookingpress_db_fields['bookingpress_customer_id']) ? $bookingpress_db_fields['bookingpress_customer_id'] : 0;
            $bookingpress_customer_email = !empty($bookingpress_db_fields['bookingpress_email_address']) ? $bookingpress_db_fields['bookingpress_email_address'] : '';
            $bookingpress_appointment_date = !empty($bookingpress_db_fields['bookingpress_appointment_date']) ? $bookingpress_db_fields['bookingpress_appointment_date'] : '';
            $bookingpress_appointment_time = !empty($bookingpress_db_fields['bookingpress_appointment_time']) ? $bookingpress_db_fields['bookingpress_appointment_time'] : '';               
            $bookingpress_appointment_status = !empty($bookingpress_db_fields['bookingpress_appointment_status']) ? $bookingpress_db_fields['bookingpress_appointment_status'] : '';                      
            $bookingpress_email_cron_hook_name = !empty($bookingpress_db_fields['bookingpress_email_cron_hook_name']) ? $bookingpress_db_fields['bookingpress_email_cron_hook_name'] : '';
            $bookingpress_staffmember_id = !empty($bookingpress_db_fields['bookingpress_staffmember_id']) ? intval($bookingpress_db_fields['bookingpress_staffmember_id']) : '';
            $bookingpress_staffmember_email = !empty($bookingpress_db_fields['bookingpress_staffmember_email']) ? ($bookingpress_db_fields['bookingpress_staffmember_email']) : '';

            $is_sent_notification = $this->bookingpress_check_cron_sms_notification_sent_or_not( $bookingpress_notification_id, $bookingpress_customer_id, $bookingpress_customer_email, $appointment_id,$bookingpress_appointment_date, $bookingpress_appointment_time,$bookingpress_appointment_status, $bookingpress_email_cron_hook_name,$bookingpress_staffmember_id, $bookingpress_staffmember_email);

            if(!empty($bookingpress_appointment_data) && empty($is_sent_notification) ){
                $bookingpress_phone_no = $this->bookingpress_get_appointment_phone_number($appointment_id);
                
                $bookingpress_appointment_status = $bookingpress_appointment_data['bookingpress_appointment_status'];
                $bookingpress_configured_options = array();

                $bookingpress_send_res = $this->bookingpress_send_sms_function($bookingpress_phone_no, '', 0, $appointment_id, $bookingpress_configured_options, $bookingpress_email_notification_name, 'customer');

                $to_message = !empty($bookingpress_send_res['to_message']) ? $bookingpress_send_res['to_message'] : '';
                $is_notification_sent = !empty($bookingpress_send_res['return_type']) && $bookingpress_send_res['return_type'] == 'success' ?  1 : 0;
                $bookingpress_sms_posted_data = array(
                    'template_type'     => 'customer',
                    'notification_name' => $bookingpress_email_notification_name,
                    'appointment_id'    => $appointment_id,
                    'customer_email'    => $bookingpress_customer_email,
                    'template_details'  => $to_message,
                );
                $bookingpress_db_fields['bookingpress_notification_type'] = 'sms';
                $bookingpress_db_fields['bookingpress_email_is_sent'] =  $is_notification_sent;
                $bookingpress_db_fields['bookingpress_email_posted_data'] = wp_json_encode( $bookingpress_sms_posted_data );
                $bookingpress_db_fields['bookingpress_email_response'] = $bookingpress_send_res;
                $bookingpress_db_fields['bookingpress_email_sending_configuration'] = wp_json_encode( $bookingpress_configured_options );
                $wpdb->insert( $tbl_bookingpress_cron_email_notifications_logs, $bookingpress_db_fields );
            }
        }

        function bookingpress_get_sms_admin_number($bookingpress_notification_name){           
            global $wpdb,$tbl_bookingpress_notifications,$BookingPress, $bookingpress_pro_staff_members;

            $bookingpress_staffmember_module = $bookingpress_pro_staff_members->bookingpress_check_staffmember_module_activation();

            $bookingpress_admin_phone = esc_html($BookingPress->bookingpress_get_settings('company_phone_number', 'company_setting'));
            $bookingpress_admin_phone_arr = array();

            if(!empty($bookingpress_notification_name) ){

                    $bookingpress_sms_notification_data = $wpdb->get_row($wpdb->prepare("SELECT bookingpress_sms_admin_number FROM {$tbl_bookingpress_notifications} WHERE bookingpress_notification_name = %s AND bookingpress_notification_receiver_type = %s", $bookingpress_notification_name, 'employee')); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_notifications is table name defined globally. False Positive alarm
                    $bookingpress_sms_admin_number = $bookingpress_sms_notification_data->bookingpress_sms_admin_number;
                    if(!empty($bookingpress_sms_admin_number)){
                        $bookingpress_sms_admin_number = preg_replace('/^0/', '', $bookingpress_sms_admin_number);
                    }                
            }
            
            if( !($bookingpress_staffmember_module ) && !empty($bookingpress_notification_name) ){
                
                if( !empty( $bookingpress_sms_admin_number ) && !empty( $bookingpress_admin_phone ) ){
                    
                    $bookingpress_admin_phone_arr = array( $bookingpress_sms_admin_number, $bookingpress_admin_phone );
                    $bookingpress_sms_admin_number = implode( ',',$bookingpress_admin_phone_arr );

                } else {

                    if(!empty($bookingpress_admin_phone)){
                        $bookingpress_sms_admin_number = preg_replace('/^0/', '', $bookingpress_admin_phone);
                    }                
                }
            }
            return $bookingpress_sms_admin_number;            
        }

        function bookingpress_after_change_appointment_status_func($appointment_id, $appointment_new_status){
            global $wpdb, $BookingPress, $tbl_bookingpress_appointment_bookings,$tbl_bookingpress_notifications;
            $bookingpress_appointment_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$tbl_bookingpress_appointment_bookings} WHERE bookingpress_appointment_booking_id = %d", $appointment_id), ARRAY_A); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_appointment_bookings is table name defined globally. False Positive alarm

            if(!empty($bookingpress_appointment_data)){
                $bookingpress_phone_no = $this->bookingpress_get_appointment_phone_number($appointment_id);                
                $bookingpress_configured_options = array();
                $bookingpress_notification_type = '';
                if ($appointment_new_status == '1' ) {
                    $bookingpress_notification_type = 'Appointment Approved';
                } else if ($appointment_new_status == '2') {
                    $bookingpress_notification_type = 'Appointment Pending';
                } else if ($appointment_new_status == '3') {
                    $bookingpress_notification_type = 'Appointment Canceled';
                } else if ($appointment_new_status == '4') {
                    $bookingpress_notification_type = 'Appointment Rejected';
                }

                $bookingpress_notification_type = apply_filters('bookingpress_modify_send_email_notification_type',$bookingpress_notification_type,$appointment_new_status);

                $this->bookingpress_send_all_sms_function($bookingpress_phone_no, '', 0, $appointment_id, $bookingpress_configured_options,$bookingpress_notification_type,'customer');
                    
                $bookingpress_sms_number = '';
                $this->bookingpress_send_all_sms_function($bookingpress_sms_number, '', 0, $appointment_id, $bookingpress_configured_options, $bookingpress_notification_type, 'employee');

                //Send staff email notification
                $bookingpress_staffmember_details = !empty($bookingpress_appointment_data['bookingpress_staff_member_details']) ? json_decode($bookingpress_appointment_data['bookingpress_staff_member_details'], TRUE) : array();

                if(!empty($bookingpress_staffmember_details)){
                    $bookingpress_staff_phone_no = !empty($bookingpress_staffmember_details['bookingpress_staffmember_phone']) ? $bookingpress_staffmember_details['bookingpress_staffmember_phone'] : '';

                    $bookingpress_staff_country_dial_code = !empty($bookingpress_staffmember_details['bookingpress_staffmember_country_dial_code']) ? $bookingpress_staffmember_details['bookingpress_staffmember_country_dial_code'] : '';

                    if(!empty($bookingpress_staff_phone_no)){
                        $bookingpress_staff_phone_no = preg_replace('/^0/', '', $bookingpress_staff_phone_no);
                    }

                    if(!empty($bookingpress_staff_country_dial_code)){
                        $bookingpress_staff_phone_no = "+".$bookingpress_staff_country_dial_code."".$bookingpress_staff_phone_no;
                    }
                    if(!empty($bookingpress_staff_phone_no)){
                        $this->bookingpress_send_all_sms_function($bookingpress_staff_phone_no, '', 0, $appointment_id, $bookingpress_configured_options,$bookingpress_notification_type,'employee');
                    }
                }
            }
            
        }

        function bookingpress_send_custom_status_sms_notification_func($appointment_id,$bookingpress_notification_type){
            global $wpdb, $BookingPress, $tbl_bookingpress_appointment_bookings;
            $bookingpress_appointment_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$tbl_bookingpress_appointment_bookings} WHERE bookingpress_appointment_booking_id = %d", $appointment_id), ARRAY_A); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_appointment_bookings is table name defined globally. False Positive alarm

            if(!empty($bookingpress_appointment_data) && !empty($bookingpress_notification_type)){
                $bookingpress_phone_no = $this->bookingpress_get_appointment_phone_number($appointment_id);
                $bookingpress_notification_type = $bookingpress_notification_type;
                $bookingpress_configured_options = array();                

                $this->bookingpress_send_all_sms_function($bookingpress_phone_no, '', 0, $appointment_id, $bookingpress_configured_options, $bookingpress_notification_type, 'customer');

                $bookingpress_sms_number = '';
                $this->bookingpress_send_all_sms_function($bookingpress_sms_number, '', 0, $appointment_id, $bookingpress_configured_options, $bookingpress_notification_type, 'employee');

                //Send staff email notification
                $bookingpress_staffmember_details = !empty($bookingpress_appointment_data['bookingpress_staff_member_details']) ? json_decode($bookingpress_appointment_data['bookingpress_staff_member_details'], TRUE) : array();

                if(!empty($bookingpress_staffmember_details)){
                    $bookingpress_staff_phone_no = !empty($bookingpress_staffmember_details['bookingpress_staffmember_phone']) ? $bookingpress_staffmember_details['bookingpress_staffmember_phone'] : '';

                    $bookingpress_staff_country_dial_code = !empty($bookingpress_staffmember_details['bookingpress_staffmember_country_dial_code']) ? $bookingpress_staffmember_details['bookingpress_staffmember_country_dial_code'] : '';

                    if(!empty($bookingpress_staff_phone_no)){
                        $bookingpress_staff_phone_no = preg_replace('/^0/', '', $bookingpress_staff_phone_no);
                    }

                    if(!empty($bookingpress_staff_country_dial_code)){
                        $bookingpress_staff_phone_no = "+".$bookingpress_staff_country_dial_code."".$bookingpress_staff_phone_no;
                    }
                    if(!empty($bookingpress_staff_phone_no)){
                        $this->bookingpress_send_all_sms_function($bookingpress_staff_phone_no, '', 0, $appointment_id, $bookingpress_configured_options, $bookingpress_notification_type, 'employee');
                    }
                    
                }
            }
        }

        function bookingpress_after_cancel_appointment_func($appointment_id){
            global $wpdb, $BookingPress, $tbl_bookingpress_appointment_bookings;
            $bookingpress_appointment_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$tbl_bookingpress_appointment_bookings} WHERE bookingpress_appointment_booking_id = %d", $appointment_id), ARRAY_A); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_appointment_bookings is table name defined globally. False Positive alarm

            if(!empty($bookingpress_appointment_data)){
                $bookingpress_phone_no = $this->bookingpress_get_appointment_phone_number($appointment_id);
                $bookingpress_notification_type = 'Appointment Canceled';
                $bookingpress_configured_options = array();                

                $this->bookingpress_send_all_sms_function($bookingpress_phone_no, '', 0, $appointment_id, $bookingpress_configured_options, $bookingpress_notification_type, 'customer');

                $bookingpress_sms_number = '';
                $this->bookingpress_send_all_sms_function($bookingpress_sms_number, '', 0, $appointment_id, $bookingpress_configured_options, $bookingpress_notification_type, 'employee');

                //Send staff email notification
                $bookingpress_staffmember_details = !empty($bookingpress_appointment_data['bookingpress_staff_member_details']) ? json_decode($bookingpress_appointment_data['bookingpress_staff_member_details'], TRUE) : array();

                if(!empty($bookingpress_staffmember_details)){
                    $bookingpress_staff_phone_no = !empty($bookingpress_staffmember_details['bookingpress_staffmember_phone']) ? $bookingpress_staffmember_details['bookingpress_staffmember_phone'] : '';

                    $bookingpress_staff_country_dial_code = !empty($bookingpress_staffmember_details['bookingpress_staffmember_country_dial_code']) ? $bookingpress_staffmember_details['bookingpress_staffmember_country_dial_code'] : '';

                    if(!empty($bookingpress_staff_phone_no)){
                        $bookingpress_staff_phone_no = preg_replace('/^0/', '', $bookingpress_staff_phone_no);
                    }

                    if(!empty($bookingpress_staff_country_dial_code)){
                        $bookingpress_staff_phone_no = "+".$bookingpress_staff_country_dial_code."".$bookingpress_staff_phone_no;
                    }
                    if(!empty($bookingpress_staff_phone_no)){
                        $this->bookingpress_send_all_sms_function($bookingpress_staff_phone_no, '', 0, $appointment_id, $bookingpress_configured_options, $bookingpress_notification_type, 'employee');
                    }
                }
            }
        }
        function bookingpress_after_refund_appointment_func($appointment_id){           

            global $wpdb, $BookingPress, $tbl_bookingpress_appointment_bookings;

            $bookingpress_appointment_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$tbl_bookingpress_appointment_bookings} WHERE bookingpress_appointment_booking_id = %d", $appointment_id), ARRAY_A); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_appointment_bookings is table name defined globally. False Positive alarm

            if(!empty($bookingpress_appointment_data)){
                $bookingpress_phone_no = $this->bookingpress_get_appointment_phone_number($appointment_id);                
                $bookingpress_configured_options = array();
                $bookingpress_notification_type = 'Refund Payment';

                $this->bookingpress_send_all_sms_function($bookingpress_phone_no, '', 0, $appointment_id, $bookingpress_configured_options, $bookingpress_notification_type, 'customer');

                $bookingpress_sms_number = '';
                $this->bookingpress_send_all_sms_function($bookingpress_sms_number, '', 0, $appointment_id, $bookingpress_configured_options, $bookingpress_notification_type, 'employee');

                //Send staff email notification
                $bookingpress_staffmember_details = !empty($bookingpress_appointment_data['bookingpress_staff_member_details']) ? json_decode($bookingpress_appointment_data['bookingpress_staff_member_details'], TRUE) : array();

                if(!empty($bookingpress_staffmember_details)){
                    $bookingpress_staff_phone_no = !empty($bookingpress_staffmember_details['bookingpress_staffmember_phone']) ? $bookingpress_staffmember_details['bookingpress_staffmember_phone'] : '';

                    $bookingpress_staff_country_dial_code = !empty($bookingpress_staffmember_details['bookingpress_staffmember_country_dial_code']) ? $bookingpress_staffmember_details['bookingpress_staffmember_country_dial_code'] : '';

                    if(!empty($bookingpress_staff_phone_no)){
                        $bookingpress_staff_phone_no = preg_replace('/^0/', '', $bookingpress_staff_phone_no);
                    }

                    if(!empty($bookingpress_staff_country_dial_code)){
                        $bookingpress_staff_phone_no = "+".$bookingpress_staff_country_dial_code."".$bookingpress_staff_phone_no;
                    }
                    if(!empty($bookingpress_staff_phone_no)){
                        $this->bookingpress_send_all_sms_function($bookingpress_staff_phone_no, '', 0, $appointment_id, $bookingpress_configured_options, $bookingpress_notification_type, 'employee');
                    }
                }
            }
        }

        function bookingpress_after_reschedule_appointment_func($appointment_id){
            global $wpdb, $BookingPress, $tbl_bookingpress_appointment_bookings;
            if( !empty(sanitize_text_field($_REQUEST['action'])) && sanitize_text_field($_REQUEST['action']) == 'bookingpress_save_appointment_booking' ){
				update_option('bookingpress_rescheduled_appointment_sms_'.$appointment_id, '0');
			}

            if( !empty(sanitize_text_field($_REQUEST['action'])) && sanitize_text_field($_REQUEST['action']) != 'bookingpress_save_appointment_booking' ){

                $bookingpress_appointment_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$tbl_bookingpress_appointment_bookings} WHERE bookingpress_appointment_booking_id = %d", $appointment_id), ARRAY_A);

            if(!empty($bookingpress_appointment_data)){
                $bookingpress_phone_no = $this->bookingpress_get_appointment_phone_number($appointment_id);                
                $bookingpress_configured_options = array();
                $bookingpress_notification_type = 'Appointment Rescheduled';

                $this->bookingpress_send_all_sms_function($bookingpress_phone_no, '', 0, $appointment_id, $bookingpress_configured_options, $bookingpress_notification_type, 'customer');

                $bookingpress_sms_number = '';
                $this->bookingpress_send_all_sms_function($bookingpress_sms_number, '', 0, $appointment_id, $bookingpress_configured_options, $bookingpress_notification_type, 'employee');


                //Send staff email notification
                $bookingpress_staffmember_details = !empty($bookingpress_appointment_data['bookingpress_staff_member_details']) ? json_decode($bookingpress_appointment_data['bookingpress_staff_member_details'], TRUE) : array();

                if(!empty($bookingpress_staffmember_details)){
                    $bookingpress_staff_phone_no = !empty($bookingpress_staffmember_details['bookingpress_staffmember_phone']) ? $bookingpress_staffmember_details['bookingpress_staffmember_phone'] : '';

                    $bookingpress_staff_country_dial_code = !empty($bookingpress_staffmember_details['bookingpress_staffmember_country_dial_code']) ? $bookingpress_staffmember_details['bookingpress_staffmember_country_dial_code'] : '';

                    if(!empty($bookingpress_staff_phone_no)){
                        $bookingpress_staff_phone_no = preg_replace('/^0/', '', $bookingpress_staff_phone_no);
                    }

                    if(!empty($bookingpress_staff_country_dial_code)){
                        $bookingpress_staff_phone_no = "+".$bookingpress_staff_country_dial_code."".$bookingpress_staff_phone_no;
                    }
                    if(!empty($bookingpress_staff_phone_no)){
                        $this->bookingpress_send_all_sms_function($bookingpress_staff_phone_no, '', 0, $appointment_id, $bookingpress_configured_options, $bookingpress_notification_type, 'employee');
                    }
                }
                }
            }
        }

        function bookingpress_after_update_appointment_func( $appointment_id ){

            global $wpdb, $BookingPress, $tbl_bookingpress_appointment_bookings;
            $bookingpress_appointment_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$tbl_bookingpress_appointment_bookings} WHERE bookingpress_appointment_booking_id = %d", $appointment_id), ARRAY_A);

            $bpa_chk_rescheduled_appointment_id = get_option('bookingpress_rescheduled_appointment_sms_'.$appointment_id ); //check rescheduled email sent or not
			
			if( isset($bpa_chk_rescheduled_appointment_id) && $bpa_chk_rescheduled_appointment_id === '0' ){

                if(!empty($bookingpress_appointment_data)){
                    $bookingpress_phone_no = $this->bookingpress_get_appointment_phone_number($appointment_id);                
                    $bookingpress_configured_options = array();
                    $bookingpress_notification_type = 'Appointment Rescheduled';

                    $this->bookingpress_send_all_sms_function($bookingpress_phone_no, '', 0, $appointment_id, $bookingpress_configured_options, $bookingpress_notification_type, 'customer');

                    $bookingpress_sms_number = '';
                    $this->bookingpress_send_all_sms_function($bookingpress_sms_number, '', 0, $appointment_id, $bookingpress_configured_options, $bookingpress_notification_type, 'employee');


                    //Send staff email notification
                    $bookingpress_staffmember_details = !empty($bookingpress_appointment_data['bookingpress_staff_member_details']) ? json_decode($bookingpress_appointment_data['bookingpress_staff_member_details'], TRUE) : array();

                    if(!empty($bookingpress_staffmember_details)){
                        $bookingpress_staff_phone_no = !empty($bookingpress_staffmember_details['bookingpress_staffmember_phone']) ? $bookingpress_staffmember_details['bookingpress_staffmember_phone'] : '';

                        $bookingpress_staff_country_dial_code = !empty($bookingpress_staffmember_details['bookingpress_staffmember_country_dial_code']) ? $bookingpress_staffmember_details['bookingpress_staffmember_country_dial_code'] : '';

                        if(!empty($bookingpress_staff_phone_no)){
                            $bookingpress_staff_phone_no = preg_replace('/^0/', '', $bookingpress_staff_phone_no);
                        }

                        if(!empty($bookingpress_staff_country_dial_code)){
                            $bookingpress_staff_phone_no = "+".$bookingpress_staff_country_dial_code."".$bookingpress_staff_phone_no;
                        }
                        if(!empty($bookingpress_staff_phone_no)){
                            $this->bookingpress_send_all_sms_function($bookingpress_staff_phone_no, '', 0, $appointment_id, $bookingpress_configured_options, $bookingpress_notification_type, 'employee');
                        }
                    }
                    update_option('bookingpress_rescheduled_appointment_sms_'.$appointment_id, 1); //rescheduled email sent update the option
                }

            }
            
        }

        function bookingpress_addon_list_data_filter_func($bookingpress_body_res){
            global $bookingpress_slugs;
            if(!empty($bookingpress_body_res)) {
                foreach($bookingpress_body_res as $bookingpress_body_res_key =>$bookingpress_body_res_val) {
                    $bookingpress_setting_page_url = add_query_arg('page', $bookingpress_slugs->bookingpress_settings, esc_url( admin_url() . 'admin.php?page=bookingpress' ));
                    $bookingpress_config_url = add_query_arg('setting_page', 'notification_settings', $bookingpress_setting_page_url);
                    if($bookingpress_body_res_val['addon_key'] == 'bookingpress_sms_gateway') {
                        $bookingpress_body_res[$bookingpress_body_res_key]['addon_configure_url'] = $bookingpress_config_url;
                    }
                }
            }
            return $bookingpress_body_res;
        }
        
        
        function bookingpress_after_add_appointment_from_backend_func($inserted_booking_id, $bookingpress_appointment_data, $entry_id){


            if( !empty(sanitize_text_field($_REQUEST['action'])) && sanitize_text_field($_REQUEST['action']) != 'bookingpress_save_appointment_booking' ){
                return;
            }

            global $wpdb, $BookingPress, $tbl_bookingpress_appointment_bookings;
            $bookingpress_appointment_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$tbl_bookingpress_appointment_bookings} WHERE bookingpress_appointment_booking_id = %d", $inserted_booking_id), ARRAY_A); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_appointment_bookings is table name defined globally. False Positive alarm

            if(!empty($bookingpress_appointment_data)){
                $bookingpress_phone_no = $this->bookingpress_get_appointment_phone_number($inserted_booking_id);
                
                $bookingpress_appointment_status = $bookingpress_appointment_data['bookingpress_appointment_status'];
                $bookingpress_configured_options = array();

                $bookingpress_email_notification_type = '';
                if ( $bookingpress_appointment_status == '2' ) {
                    $bookingpress_email_notification_type = 'Appointment Pending';
                } elseif ( $bookingpress_appointment_status == '1' ) {
                    $bookingpress_email_notification_type = 'Appointment Approved';
                } elseif ( $bookingpress_appointment_status == '3' ) {
                    $bookingpress_email_notification_type = 'Appointment Canceled';
                } elseif ( $bookingpress_appointment_status == '4' ) {
                    $bookingpress_email_notification_type = 'Appointment Rejected';
                }

		$bookingpress_email_notification_type = apply_filters('bookingpress_modify_send_email_notification_type',$bookingpress_email_notification_type,$bookingpress_appointment_status);

                if(!empty($_POST['appointment_data']['complete_payment_url_selection']) && $_POST['appointment_data']['complete_payment_url_selection'] == 'send_payment_link' && !empty($_POST['appointment_data']['complete_payment_url_selected_method'] && in_array('sms',$_POST['appointment_data']['complete_payment_url_selected_method']))){ // phpcs:ignore WordPress.Security.NonceVerification.Missing --Reason Nonce already verified from the caller function.
                    $this->bookingpress_send_sms_function($bookingpress_phone_no, '', 0, $inserted_booking_id, $bookingpress_configured_options, 'Complete Payment URL', 'customer');
                }

                if(!empty($_POST['appointment_data']['complete_payment_url_selection']) && $_POST['appointment_data']['complete_payment_url_selection'] == 'send_payment_link' && !empty($_POST['appointment_data']['complete_payment_url_selected_method'] && in_array('sms',$_POST['appointment_data']['complete_payment_url_selected_method']))) { // phpcs:ignore WordPress.Security.NonceVerification.Missing --Reason Nonce already verified from the caller function.
                    $bookingpress_sms_number = $this->bookingpress_get_sms_admin_number('Complete Payment URL');
                    $this->bookingpress_send_sms_function($bookingpress_sms_number, '', 0, $inserted_booking_id, $bookingpress_configured_options,'Complete Payment URL', 'employee');
                }
                                
                //Send staff email notification
                $bookingpress_staffmember_details = !empty($bookingpress_appointment_data['bookingpress_staff_member_details']) ? json_decode($bookingpress_appointment_data['bookingpress_staff_member_details'], TRUE) : array();

                if(!empty($bookingpress_staffmember_details)){
                    $bookingpress_staff_phone_no = !empty($bookingpress_staffmember_details['bookingpress_staffmember_phone']) ? $bookingpress_staffmember_details['bookingpress_staffmember_phone'] : '';

                    $bookingpress_staff_country_dial_code = !empty($bookingpress_staffmember_details['bookingpress_staffmember_country_dial_code']) ? $bookingpress_staffmember_details['bookingpress_staffmember_country_dial_code'] : '';

                    if(!empty($bookingpress_staff_phone_no)){
                        $bookingpress_staff_phone_no = preg_replace('/^0/', '', $bookingpress_staff_phone_no);
                    }

                    if(!empty($bookingpress_staff_country_dial_code)){
                        $bookingpress_staff_phone_no = "+".$bookingpress_staff_country_dial_code."".$bookingpress_staff_phone_no;
                    }

                    if(!empty($bookingpress_staff_phone_no)){
                        //$this->bookingpress_send_all_sms_function($bookingpress_staff_phone_no, '', 0, $inserted_booking_id, $bookingpress_configured_options, $bookingpress_email_notification_type, 'employee');              
                    }

                    if(!empty($_POST['appointment_data']['complete_payment_url_selection']) && $_POST['appointment_data']['complete_payment_url_selection'] == 'send_payment_link' && !empty($_POST['appointment_data']['complete_payment_url_selected_method'] && in_array('sms',$_POST['appointment_data']['complete_payment_url_selected_method']))) { // phpcs:ignore WordPress.Security.NonceVerification.Missing --Reason Nonce already verified from the caller function.

                        if(!empty($bookingpress_staff_phone_no) && $appointment_send_notification != "true"){
                            $this->bookingpress_send_sms_function($bookingpress_staff_phone_no, '', 0, $inserted_booking_id, $bookingpress_configured_options, 'Complete Payment URL', 'employee');
                        }
                    }
                }
            }
        }
         

        function bookingpress_send_sms_notification_after_appointment_booked($inserted_booking_id, $entry_id, $payment_gateway_data){

            global $wpdb, $BookingPress, $tbl_bookingpress_appointment_bookings,$tbl_bookingpress_notifications, $bookingpress_pro_staff_members;
            $bookingpress_appointment_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$tbl_bookingpress_appointment_bookings} WHERE bookingpress_appointment_booking_id = %d", $inserted_booking_id), ARRAY_A); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_appointment_bookings is table name defined globally. False Positive alarm

            $bookingpress_send_single_integration_notification_after_booking = apply_filters('bookingpress_send_single_sms_notification_after_booking',true,$bookingpress_appointment_data);

            if(!empty($bookingpress_appointment_data) && $bookingpress_send_single_integration_notification_after_booking){
                $bookingpress_phone_no = $this->bookingpress_get_appointment_phone_number($inserted_booking_id);
                
                $bookingpress_appointment_status = $bookingpress_appointment_data['bookingpress_appointment_status'];
                $bookingpress_configured_options = array();

                $bookingpress_email_notification_type = '';
                if ( $bookingpress_appointment_status == '2' ) {
                    $bookingpress_email_notification_type = 'Appointment Pending';
                } elseif ( $bookingpress_appointment_status == '1' ) {
                    $bookingpress_email_notification_type = 'Appointment Approved';
                } elseif ( $bookingpress_appointment_status == '3' ) {
                    $bookingpress_email_notification_type = 'Appointment Canceled';
                } elseif ( $bookingpress_appointment_status == '4' ) {
                    $bookingpress_email_notification_type = 'Appointment Rejected';
                }
                $bookingpress_email_notification_type = apply_filters('bookingpress_modify_send_email_notification_type',$bookingpress_email_notification_type,$bookingpress_appointment_status);

                    $this->bookingpress_send_all_sms_function($bookingpress_phone_no, '', 0, $inserted_booking_id, $bookingpress_configured_options, $bookingpress_email_notification_type, 'customer');

                    $bookingpress_staffmember_module = $bookingpress_pro_staff_members->bookingpress_check_staffmember_module_activation();
                    
                    $bookingpress_sms_notification_data = $wpdb->get_row($wpdb->prepare("SELECT bookingpress_sms_admin_number FROM {$tbl_bookingpress_notifications} WHERE bookingpress_notification_name = %s AND bookingpress_notification_receiver_type = %s", $bookingpress_email_notification_type, 'employee')); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_notifications is table name defined globally. False Positive alarm
                    
                    $bookingpress_sms_admin_number = $bookingpress_sms_notification_data->bookingpress_sms_admin_number;
                if( $bookingpress_staffmember_module && !empty( $bookingpress_sms_admin_number ) ){

                    if ( ! empty( $bookingpress_sms_admin_number ) ) {
                        $bookingpress_sms_admin_number = explode( ',', $bookingpress_sms_admin_number );
                    }
                    
                    foreach ( $bookingpress_sms_admin_number as $admin_sms_key => $admin_sms_val ) {            

                        if(!empty($admin_sms_val)){
                            $admin_sms_val = preg_replace('/^0/', '', $admin_sms_val);
                        }
                        $this->bookingpress_send_all_sms_function($admin_sms_val, '', 0, $inserted_booking_id, $bookingpress_configured_options, $bookingpress_email_notification_type, 'employee');
                    }
                } else{

                    $bookingpress_sms_number = '';
                    $this->bookingpress_send_all_sms_function($bookingpress_sms_number, '', 0, $inserted_booking_id, $bookingpress_configured_options, $bookingpress_email_notification_type, 'employee');
                }

                //Send staff email notification
                $bookingpress_staffmember_details = !empty($bookingpress_appointment_data['bookingpress_staff_member_details']) ? json_decode($bookingpress_appointment_data['bookingpress_staff_member_details'], TRUE) : array();

                if(!empty($bookingpress_staffmember_details)){
                    $bookingpress_staff_phone_no = !empty($bookingpress_staffmember_details['bookingpress_staffmember_phone']) ? $bookingpress_staffmember_details['bookingpress_staffmember_phone'] : '';

                    $bookingpress_staff_country_dial_code = !empty($bookingpress_staffmember_details['bookingpress_staffmember_country_dial_code']) ? $bookingpress_staffmember_details['bookingpress_staffmember_country_dial_code'] : '';

                    if(!empty($bookingpress_staff_phone_no)){
                        $bookingpress_staff_phone_no = preg_replace('/^0/', '', $bookingpress_staff_phone_no);
                    }

                    if(!empty($bookingpress_staff_country_dial_code)){
                        $bookingpress_staff_phone_no = "+".$bookingpress_staff_country_dial_code."".$bookingpress_staff_phone_no;
                    }
                    if(!empty($bookingpress_staff_phone_no)){
                        $this->bookingpress_send_all_sms_function($bookingpress_staff_phone_no, '', 0, $inserted_booking_id, $bookingpress_configured_options, $bookingpress_email_notification_type, 'employee');
                    }
                }
            }
        }

        function bookingpress_is_appointment_field_active($appointment_field) {
            global $wpdb, $tbl_bookingpress_form_fields;            
            $bookingpress_field_list_data = $wpdb->get_row( $wpdb->prepare( 'SELECT bookingpress_field_is_default,bookingpress_form_field_name FROM ' . $tbl_bookingpress_form_fields . ' WHERE bookingpress_is_customer_field = %d AND bookingpress_field_meta_key = %s order by bookingpress_form_field_id',0,$appointment_field), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared --Reason $tbl_bookingpress_form_fields is a table name. false alarm.

            return $bookingpress_field_list_data;
        }

        function bookingpress_get_appointment_phone_number($appointment_id){
            global $BookingPress,$wpdb,$tbl_bookingpress_appointment_meta, $tbl_bookingpress_appointment_bookings;
            $bookingpress_customer_phone = '';
            $bookingpress_sms_selected_phone_number_field = $BookingPress->bookingpress_get_settings('bookingpress_selected_phone_number_field', 'notification_setting');
            if(!empty($bookingpress_sms_selected_phone_number_field)) {   
                $bookingpress_field_data = $this->bookingpress_is_appointment_field_active($bookingpress_sms_selected_phone_number_field);
                if(!empty($bookingpress_field_data))  {   
                    $is_default_field = $bookingpress_field_data['bookingpress_field_is_default'];
                    if($is_default_field == '1') {
                        $bookingpress_appointment_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$tbl_bookingpress_appointment_bookings} WHERE bookingpress_appointment_booking_id = %d", $appointment_id), ARRAY_A); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_appointment_bookings is table name defined globally. False Positive alarm

                        $bookingpress_field_name = $bookingpress_field_data['bookingpress_form_field_name'];
                        if($bookingpress_field_name == 'fullname' ) {
                            $bookingpress_customer_phone = !empty($bookingpress_appointment_data['bookingpress_customer_name']) ? $bookingpress_appointment_data['bookingpress_customer_name'] : '';
                            if(!empty($bookingpress_customer_phone)) {
                                $bookingpress_customer_phone = "+".$bookingpress_customer_phone;
                            }
                        } elseif($bookingpress_field_name == 'firstname') {
                            $bookingpress_customer_phone = !empty($bookingpress_appointment_data['bookingpress_customer_firstname']) ? $bookingpress_appointment_data['bookingpress_customer_firstname'] : '';
                            if(!empty($bookingpress_customer_phone)) {
                                $bookingpress_customer_phone = "+".$bookingpress_customer_phone;
                            }
                        } elseif($bookingpress_field_name == 'lastname') {
                            $bookingpress_customer_phone = !empty($bookingpress_appointment_data['bookingpress_customer_lastname']) ? $bookingpress_appointment_data['bookingpress_customer_lastname'] : '';
                            if(!empty($bookingpress_customer_phone)) {
                                $bookingpress_customer_phone = "+".$bookingpress_customer_phone;
                            }                       
                        } elseif($bookingpress_field_name == 'phone_number') {
                            $bookingpress_customer_phone = !empty($bookingpress_appointment_data['bookingpress_customer_phone']) ? $bookingpress_appointment_data['bookingpress_customer_phone'] : '';                            
                            $bookingpress_country_dial_code = !empty($bookingpress_appointment_data['bookingpress_customer_phone_dial_code']) ? $bookingpress_appointment_data['bookingpress_customer_phone_dial_code'] : '';                
                            if(!empty($bookingpress_customer_phone)){
                                $bookingpress_customer_phone = preg_replace('/^0/', '', $bookingpress_customer_phone);
                            }            
                            if(!empty($bookingpress_country_dial_code) && !empty($bookingpress_customer_phone)){
                                $bookingpress_customer_phone = "+".$bookingpress_country_dial_code."".$bookingpress_customer_phone;
                            }            
                        }
                    } else {
                        $bookingpress_field_name = $bookingpress_sms_selected_phone_number_field;

                        $bookingpress_appointment_meta_data = $wpdb->get_results( $wpdb->prepare( "SELECT bookingpress_appointment_meta_value,bookingpress_appointment_meta_key FROM {$tbl_bookingpress_appointment_meta} WHERE bookingpress_appointment_id = %d", $appointment_id ), ARRAY_A );// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason $tbl_bookingpress_appointment_meta is a table name. false alarm.

                        $bookingpress_appointment_field_meta_data  = array();
                        foreach($bookingpress_appointment_meta_data as $key => $value) {                            
                            if($value['bookingpress_appointment_meta_key'] == 'appointment_form_fields_data') {
                                $bookingpress_appointment_field_meta_data = !empty($value['bookingpress_appointment_meta_value']) ? json_decode
                                ($value['bookingpress_appointment_meta_value'],true): array();
                            }                            
                        }     
                        $bookingpress_appointment_form_fields = !empty($bookingpress_appointment_field_meta_data['form_fields']) ? $bookingpress_appointment_field_meta_data['form_fields'] : array();                                               
                        $bookingpress_customer_phone = !empty($bookingpress_appointment_form_fields[$bookingpress_field_name]) ? $bookingpress_appointment_form_fields[$bookingpress_field_name] : '';
                        
                        if(!empty($bookingpress_customer_phone)) {
                            $bookingpress_customer_phone = "+".$bookingpress_customer_phone;
                        }
                    }                    
                }    
            }
            return $bookingpress_customer_phone;
        }

        function bookingpress_admin_notices(){
            
            if( !function_exists('is_plugin_active') ){
                include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
            }

            if(!is_plugin_active('bookingpress-appointment-booking-pro/bookingpress-appointment-booking-pro.php')){
                echo "<div class='notice notice-warning'><p>" . esc_html__('Bookingpress - Sms plugin requires Bookingpress Premium Plugin installed and active.', 'bookingpress-sms
                ') . "</p></div>";
            }
            
            if( file_exists( WP_PLUGIN_DIR . '/bookingpress-appointment-booking-pro/bookingpress-appointment-booking-pro.php' ) ){
                $bpa_pro_plugin_info = get_plugin_data( WP_PLUGIN_DIR . '/bookingpress-appointment-booking-pro/bookingpress-appointment-booking-pro.php' );
                $bpa_pro_plugin_version = $bpa_pro_plugin_info['Version'];

                if( version_compare( $bpa_pro_plugin_version, '2.8', '<' ) ){
                    echo "<div class='notice notice-error is-dismissible'><p>".esc_html__("It's Required to update the BookingPress Premium Plugin to version 2.8 or higher in order to use the BookingPress SMS plugin", "bookingpress-sms")."</p></div>";
                }
            }
        }

        function bookingpress_add_integration_debug_logs_func($bookingpress_integration_debug_logs_arr){
            $bookingpress_integration_debug_logs_arr[] = array(
                'integration_name' => __('SMS Debug Logs', 'bookingpress-sms'),
                'integration_key' => 'sms_debug_logs'
            );
            return $bookingpress_integration_debug_logs_arr;
        }

        function bookingpress_send_all_sms_function($to_number, $to_message = '', $is_test = 0, $appointment_id = 0, $bookingpress_configured_options = array(), $notification_type = 'Appointment Approved', $notification_receiver_type = 'customer') {

            global $BookingPress;
            $bookingpress_send_all_sms_arr[] = $notification_type;
            
            $bookingpress_send_all_sms_arr = apply_filters('bookingpress_send_all_custom_email_notifications',$bookingpress_send_all_sms_arr,$notification_receiver_type,$appointment_id,'sms');

            // Send customer sms
            foreach($bookingpress_send_all_sms_arr as $key => $email_notification_name) {
                if (! empty($email_notification_name) ) {                    
                    if($notification_receiver_type == 'employee' && $to_number == '') {
                        $bookingpress_sms_number = $this->bookingpress_get_sms_admin_number($email_notification_name);
                        if(!empty($bookingpress_sms_number)) {

                            if ( ! empty( $bookingpress_sms_number ) ) {
                                $bookingpress_sms_number = explode( ',', $bookingpress_sms_number );
                            }
                            foreach ( $bookingpress_sms_number as $admin_sms_key => $admin_sms_val ) {                            
                                if(!empty($admin_sms_val)){
                                    $admin_sms_val = preg_replace('/^0/', '', $admin_sms_val);
                                }
                                $this->bookingpress_send_sms_function($admin_sms_val, $to_message, $is_test, $appointment_id, $bookingpress_configured_options, $email_notification_name, $notification_receiver_type);
                            }
                        }
                    } else {
                        $this->bookingpress_send_sms_function($to_number, $to_message, $is_test, $appointment_id, $bookingpress_configured_options, $email_notification_name, $notification_receiver_type);
                    }

                }
            }
        }

        function bookingpress_send_sms_function($to_number, $to_message = '', $is_test = 0, $appointment_id = 0, $bookingpress_configured_options = array(), $notification_type = 'Appointment Approved', $notification_receiver_type = 'customer'){

            global $wpdb, $BookingPress, $tbl_bookingpress_notifications, $tbl_bookingpress_appointment_bookings, $bookingpress_debug_integration_log_id;
        
            $bookingpress_send_sms_notification = 0;
            if($is_test == 0) {
                $bookingpress_configured_options = array(
                    'bookingpress_selected_sms_gateway' => $BookingPress->bookingpress_get_settings('bookingpress_selected_sms_gateway', 'notification_setting')
                );
            }
            $bookingpress_debug_log_data = array(
                'to_number' => $to_number,
                'to_message' => $to_message,
                'is_test' => $is_test,
                'appointment_id' => $appointment_id,
                'configure_options' => $bookingpress_configured_options,
                'notification_type' => $notification_type,
                'notification_receiver_type' => $notification_receiver_type
            );

            do_action('bookingpress_integration_log_entry', 'sms_debug_logs', '', 'Send SMS Params', 'Core SMS Sending Function', $bookingpress_debug_log_data, $bookingpress_debug_integration_log_id);

            if($is_test == 0){
                $bookingpress_sms_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$tbl_bookingpress_notifications} WHERE bookingpress_notification_name = %s AND bookingpress_notification_receiver_type = %s ORDER BY bookingpress_notification_id DESC", $notification_type, $notification_receiver_type), ARRAY_A); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_notifications is table name defined globally. False Positive alarm


                //Modify SMS Content
                $bookingpress_appointment_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$tbl_bookingpress_appointment_bookings} WHERE bookingpress_appointment_booking_id = %d", $appointment_id), ARRAY_A); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_appointment_bookings is table name defined globally. False Positive alarm

                /* New Filter Added */
                $bookingpress_sms_data = apply_filters( 'bookingpress_replace_notification_content_language_wise', $bookingpress_sms_data, array('bookingpress_sms_notification_message'), $notification_receiver_type,$notification_type,$bookingpress_appointment_data );

                if(!empty($bookingpress_sms_data['bookingpress_sms_notification_message'])){
                    $to_message = stripslashes_deep($bookingpress_sms_data['bookingpress_sms_notification_message']);
                }

                if(!empty($bookingpress_sms_data['bookingpress_send_sms_notification'])){
                    $bookingpress_send_sms_notification = $bookingpress_sms_data['bookingpress_send_sms_notification'];
                }

                if(!empty($bookingpress_appointment_data)){
                    $bookingpress_appointment_data['notification_language_compare_field'] = 'bookingpress_sms_notification_message';
                }
                
                $to_message = apply_filters( 'bookingpress_modify_email_notification_content', $to_message, $bookingpress_appointment_data,$notification_type, $notification_receiver_type );
                $notification_name = '';
            }

            do_action('bookingpress_integration_log_entry', 'sms_debug_logs', '', 'Modified SMS Content', 'SMS Send Content', $to_message, $bookingpress_debug_integration_log_id);

            $bookingpress_return_res = array(
                'return_type' => 'error',
                'return_res' => __('Something went wrong while sending sms', 'bookingpress-sms'),
                'to_message' => $to_message,
            );
            
            $bookingpress_selected_sms_gateway = !empty($bookingpress_configured_options['bookingpress_selected_sms_gateway']) ? $bookingpress_configured_options['bookingpress_selected_sms_gateway'] : '';

            if(!empty($bookingpress_selected_sms_gateway) && ( $is_test == 1 || $bookingpress_send_sms_notification == 1 )){

                $bookingpress_sms_gateway_list = $this->bookingpress_sms_gateway_list();
                if($bookingpress_selected_sms_gateway == "Clickatell"){

                    $bookingpress_clickatell_api_key = !empty($bookingpress_configured_options['clickatell_api_key']) ? $bookingpress_configured_options['clickatell_api_key'] : $BookingPress->bookingpress_get_settings('clickatell_api_key', 'notification_setting');
                    $bookingpress_clickatell_api_id = !empty($bookingpress_configured_options['clickatell_api_id']) ? $bookingpress_configured_options['clickatell_api_id'] : $BookingPress->bookingpress_get_settings('clickatell_api_id', 'notification_setting');

                    $bookingpress_url = "https://platform.clickatell.com/v1/message";
                    $bookingpress_curl = curl_init($bookingpress_url);
                    curl_setopt($bookingpress_curl, CURLOPT_URL, $bookingpress_url);
                    curl_setopt($bookingpress_curl, CURLOPT_POST, true);
                    curl_setopt($bookingpress_curl, CURLOPT_RETURNTRANSFER, true);

                    $headers = array(
                    "Content-Type: application/json",
                    "Accept: application/json",
                    "Authorization: ".$bookingpress_clickatell_api_key,
                    );
                    curl_setopt($bookingpress_curl, CURLOPT_HTTPHEADER, $headers);

                    $data = '{"messages": [{ "channel": "whatsapp", "to": "'.$to_number.'", "content": "'.$to_message.'" }, { "channel": "sms", "to": "'.$to_number.'", "content": "'.$to_message.'" }]}';

                    do_action('bookingpress_integration_log_entry', 'sms_debug_logs', '', 'SMS Addon', 'Clickatell Send Data', $data, $bookingpress_debug_integration_log_id);

                    curl_setopt($bookingpress_curl, CURLOPT_POSTFIELDS, $data);

                    curl_setopt($bookingpress_curl, CURLOPT_SSL_VERIFYHOST, false);
                    curl_setopt($bookingpress_curl, CURLOPT_SSL_VERIFYPEER, false);

                    $bookingpress_curl_resp = curl_exec($bookingpress_curl);
                    curl_close($bookingpress_curl);

                    $bookingpress_send_res = json_decode($bookingpress_curl_resp, TRUE);

                    do_action('bookingpress_integration_log_entry', 'sms_debug_logs', '', 'SMS Addon', 'Clickatell Send Res', $bookingpress_send_res, $bookingpress_debug_integration_log_id);

                    if(empty($bookingpress_send_res['error'])){
                        $bookingpress_return_res['return_type'] = 'success';
                        $bookingpress_return_res['return_res'] = __('SMS Sent Successfully', 'bookingpress-sms');
                    }else{

                        $error_code = (isset($bookingpress_send_res['error']['code']))?$bookingpress_send_res['error']['code']:'';
                        $error_msg =  (isset($bookingpress_send_res['error']['description']))?$bookingpress_send_res['error']['description']:'';

                        $bookingpress_return_res['return_type'] = 'error';
                        $bookingpress_return_res['return_res'] = __('SMS not sent successfully due to ', 'bookingpress-sms')." ".$error_code." ".$error_msg;


                        //$bookingpress_return_res['return_res'] = !empty($bookingpress_send_res['error']) ? $bookingpress_send_res['error'] : __('Something went wrong while sending SMS with Clickatell', 'bookingpress-sms');
                    }

                }else if($bookingpress_selected_sms_gateway == "Redoxygen"){

                    $bookingpress_redoxygen_send_url = "http://sms1.redoxygen.net/sms.dll?Action=SendSMS";

                    $bookingpress_redoxygen_account_id = !empty($bookingpress_configured_options['redoxygen_account_id']) ? $bookingpress_configured_options['redoxygen_account_id'] : $BookingPress->bookingpress_get_settings('redoxygen_account_id', 'notification_setting');
                    $bookingpress_redoxygen_email = !empty($bookingpress_configured_options['redoxygen_email']) ? $bookingpress_configured_options['redoxygen_email'] : $BookingPress->bookingpress_get_settings('redoxygen_email', 'notification_setting');
                    $bookingpress_redoxygen_password = !empty($bookingpress_configured_options['redoxygen_password']) ? $bookingpress_configured_options['redoxygen_password'] : $BookingPress->bookingpress_get_settings('redoxygen_password', 'notification_setting');

                    $bookingpress_redoxygen_send_url .= '&AccountID='.$bookingpress_redoxygen_account_id.'&Email='.$bookingpress_redoxygen_email.'&Password='.$bookingpress_redoxygen_password.'&Recipient='.$to_number.'&Message='.$to_message;

                    $bookingpress_redoxygen_params = array(
                        'timeout' => 5000
                    );

                    $bookingpress_send_sms_res = wp_remote_get($bookingpress_redoxygen_send_url, $bookingpress_redoxygen_params);

                    do_action('bookingpress_integration_log_entry', 'sms_debug_logs', '', 'SMS Addon', 'Redoxygen send data', $bookingpress_redoxygen_send_url, $bookingpress_debug_integration_log_id);

                    do_action('bookingpress_integration_log_entry', 'sms_debug_logs', '', 'SMS Addon', 'Redoxygen received res', $bookingpress_send_sms_res, $bookingpress_debug_integration_log_id);

                    if(!is_wp_error($bookingpress_send_sms_res)){
                        $bookingpress_return_res['return_type'] = 'success';
                        $bookingpress_return_res['return_res'] = __('SMS Sent Successfully', 'bookingpress-sms');
                    }else{
                        $bookingpress_return_res['return_res'] = $bookingpress_send_sms_res->get_error_message();
                    }

                }else if($bookingpress_selected_sms_gateway == "1s2u"){

                    $bookingpress_1s2u_send_url = "https://api.1s2u.io/bulksms";
                    $bookingpress_username = !empty($bookingpress_configured_options['sms_1s2u_username']) ? $bookingpress_configured_options['sms_1s2u_username'] : $BookingPress->bookingpress_get_settings('sms_1s2u_username', 'notification_setting');
                    $bookingpress_password = !empty($bookingpress_configured_options['sms_1s2u_password']) ? $bookingpress_configured_options['sms_1s2u_password'] : $BookingPress->bookingpress_get_settings('sms_1s2u_password', 'notification_setting');

                    $to_number = str_replace('+','',$to_number);

                    $bookingpress_1s2u_send_url .= "&username=".$bookingpress_username."&password=".$bookingpress_password."&MT='1' &mno=".$to_number."&msg=".$to_message;

                    $bookingpress_1s2u_params = array(
                        'timeout' => 5000
                    );

                    $bookingpress_send_sms_res = wp_remote_get($bookingpress_1s2u_send_url, $bookingpress_1s2u_params);

                    do_action('bookingpress_integration_log_entry', 'sms_debug_logs', '', 'SMS Addon', '1s2u Send data', $bookingpress_1s2u_send_url, $bookingpress_debug_integration_log_id);

                    do_action('bookingpress_integration_log_entry', 'sms_debug_logs', '', 'SMS Addon', '1s2u received response', $bookingpress_send_sms_res, $bookingpress_debug_integration_log_id);

                    if(!is_wp_error($bookingpress_send_sms_res)){
                        $bookingpress_return_res['return_type'] = 'success';
                        $bookingpress_return_res['return_res'] = __('SMS Sent Successfully', 'bookingpress-sms');
                    }else{
                        $bookingpress_return_res['return_res'] = $bookingpress_send_sms_res->get_error_message();
                    }

                }else if($bookingpress_selected_sms_gateway == "Experttexting"){

                    $bookingpress_experttexting_send_url = "https://www.experttexting.com/ExptRestApi/sms/json/Message/Send";
                    $bookingpress_username = !empty($bookingpress_configured_options['experttexting_username']) ? $bookingpress_configured_options['experttexting_username'] : $BookingPress->bookingpress_get_settings('experttexting_username', 'notification_setting');

                    $bookingpress_password = !empty($bookingpress_configured_options['experttexting_password']) ? $bookingpress_configured_options['experttexting_password'] : $BookingPress->bookingpress_get_settings('experttexting_password', 'notification_setting');

                    $bookingpress_api_key = !empty($bookingpress_configured_options['experttexting_api_key']) ? $bookingpress_configured_options['experttexting_api_key'] : $BookingPress->bookingpress_get_settings('experttexting_api_key', 'notification_setting');

                    $bookingpress_sender_id = !empty($bookingpress_configured_options['experttexting_sender_id']) ? $bookingpress_configured_options['experttexting_sender_id'] : $BookingPress->bookingpress_get_settings('experttexting_sender_id', 'notification_setting');

                    $bookingpress_experttexting_send_url .= "&username=".$bookingpress_username."&password=".$bookingpress_password."&api_key=".$bookingpress_api_key."&from=".$bookingpress_sender_id."&to=".$to_number."&text=".$to_message."&type=unicode";

                    $bookingpress_experttexting_params = array(
                        'timeout' => 5000
                    );

                    $bookingpress_send_sms_res = wp_remote_get($bookingpress_experttexting_send_url, $bookingpress_experttexting_params);

                    do_action('bookingpress_integration_log_entry', 'sms_debug_logs', '', 'SMS Addon', 'Experttexting send data', $bookingpress_experttexting_send_url, $bookingpress_debug_integration_log_id);

                    do_action('bookingpress_integration_log_entry', 'sms_debug_logs', '', 'SMS Addon', 'Experttexting received response', $bookingpress_send_sms_res, $bookingpress_debug_integration_log_id);

                    if(!is_wp_error($bookingpress_send_sms_res)){
                        $bookingpress_return_res['return_type'] = 'success';
                        $bookingpress_return_res['return_res'] = __('SMS Sent Successfully', 'bookingpress-sms');
                    }else{
                        $bookingpress_return_res['return_res'] = $bookingpress_send_sms_res->get_error_message();
                    }

                }else if($bookingpress_selected_sms_gateway == "BearSMS"){

                    $bookingpress_bearsms_send_url = "http://app.bearsms.com/index.php?app=ws&op=pv";
                    $bookingpress_username = !empty($bookingpress_configured_options['bearsms_username']) ? $bookingpress_configured_options['bearsms_username'] : $BookingPress->bookingpress_get_settings('bearsms_username', 'notification_setting');

                    $bookingpress_password = !empty($bookingpress_configured_options['bearsms_password']) ? $bookingpress_configured_options['bearsms_password'] : $BookingPress->bookingpress_get_settings('bearsms_password', 'notification_setting');

                    $bookingpress_sender_id = !empty($bookingpress_configured_options['bearsms_sender_id']) ? $bookingpress_configured_options['bearsms_sender_id'] : $BookingPress->bookingpress_get_settings('bearsms_sender_id', 'notification_setting');

                    $bookingpress_bearsms_send_url .= "&u=".$bookingpress_username."&p=".$bookingpress_password."&from=".$bookingpress_sender_id."&to=".$to_number."&text=".$to_message."&unicode=1";

                    $bookingpress_bearsms_params = array(
                        'timeout' => 5000
                    );

                    $bookingpress_send_sms_res = wp_remote_get($bookingpress_bearsms_send_url, $bookingpress_bearsms_params);

                    do_action('bookingpress_integration_log_entry', 'sms_debug_logs', '', 'SMS Addon', 'Bearsms send data', $bookingpress_bearsms_send_url, $bookingpress_debug_integration_log_id);
                    
                    do_action('bookingpress_integration_log_entry', 'sms_debug_logs', '', 'SMS Addon', 'Bearsms received response', $bookingpress_send_sms_res, $bookingpress_debug_integration_log_id);
                    
                    
                    if(is_wp_error($bookingpress_send_sms_res)){
                        $bookingpress_return_res['return_res'] = $bookingpress_send_sms_res->get_error_message();
                    }else{
                        $bookingpress_send_sms_res = !empty( $bookingpress_send_sms_res['body']) ? json_decode($bookingpress_send_sms_res['body'],true) : array();

                        if( isset($bookingpress_send_sms_res['status']) && ($bookingpress_send_sms_res['status'] == 'ERR' )){
                            $bookingpress_sms_error_msg = $bookingpress_send_sms_res['error_string'];
                            $bookingpress_return_res['return_type'] = 'error';
                            
                            $bookingpress_return_res['return_res'] = __('SMS not sent successfully due to error ', 'bookingpress-sms') . $bookingpress_sms_error_msg;

                        } else {
                            $bookingpress_return_res['return_type'] = 'success';
                            $bookingpress_return_res['return_res'] = __('SMS Sent Successfully', 'bookingpress-sms');
                        }
                    }
                    
                }else if($bookingpress_selected_sms_gateway == "Spirius"){

                    $bookingpress_spirius_send_url = "https://get.spiricom.spirius.com:55001/cgi-bin/sendsms";

                    $bookingpress_username = !empty($bookingpress_configured_options['spirius_username']) ? $bookingpress_configured_options['spirius_username'] : $BookingPress->bookingpress_get_settings('spirius_username', 'notification_setting');

                    $bookingpress_password = !empty($bookingpress_configured_options['spirius_password']) ? $bookingpress_configured_options['spirius_password'] : $BookingPress->bookingpress_get_settings('spirius_password', 'notification_setting');

                    $bookingpress_from_number = !empty($bookingpress_configured_options['spirius_from_number']) ? $bookingpress_configured_options['spirius_from_number'] : $BookingPress->bookingpress_get_settings('spirius_from_number', 'notification_setting');

                    $bookingpress_spirius_send_url .= "&User=".$bookingpress_username."&Pass=".$bookingpress_password."&From=".$bookingpress_from_number."&TO=".$to_number."&Msg=".$to_message;

                    $bookingpress_spirius_params = array(
                        'timeout' => 5000
                    );

                    $bookingpress_send_sms_res = wp_remote_get($bookingpress_spirius_send_url, $bookingpress_spirius_params);

                    do_action('bookingpress_integration_log_entry', 'sms_debug_logs', '', 'SMS Addon', 'Spirius send data', $bookingpress_spirius_send_url, $bookingpress_debug_integration_log_id);

                    
                    if($bookingpress_send_sms_res){
                        $bookingpress_send_sms_res = (array)$bookingpress_send_sms_res;
                    }                    
                    do_action('bookingpress_integration_log_entry', 'sms_debug_logs', '', 'SMS Addon', 'Spirius received response', $bookingpress_send_sms_res, $bookingpress_debug_integration_log_id);

                    if(is_wp_error($bookingpress_send_sms_res)){
                        $bookingpress_return_res['return_res'] = $bookingpress_send_sms_res->get_error_message();
                    }else{
                        if(isset($bookingpress_send_sms_res['errors']) || isset($bookingpress_send_sms_res['response']) && $bookingpress_send_sms_res['response'] != 200 ){
                            
                            $error_code = (isset($bookingpress_send_sms_res['response']['code']))?$bookingpress_send_sms_res['response']['code']:'';
                            $error_msg =  (isset($bookingpress_send_sms_res['response']['message']))?$bookingpress_send_sms_res['response']['message']:'';
                            if(isset($bookingpress_send_sms_res['errors'])){
                                $error_msg = (isset($bookingpress_send_sms_res['errors']['http_request_failed'][0]))?$bookingpress_send_sms_res['errors']['http_request_failed'][0]:$error_msg;
                            }
                            $bookingpress_return_res['return_type'] = 'error';
                            $bookingpress_return_res['return_res'] = __('SMS not sent successfully due to ', 'bookingpress-sms')." ".$error_code." ".$error_msg;
                        } else {

                            $bookingpress_return_res['return_type'] = 'success';
                            $bookingpress_return_res['return_res'] = __('SMS Sent Successfully', 'bookingpress-sms');
                        }
                    }
                    
                }else if($bookingpress_selected_sms_gateway == "D7 SMS"){

                    $bookingpress_d7sms_send_url = "http://smsc.d7networks.com:1401/send";

                    $bookingpress_username = !empty($bookingpress_configured_options['d7sms_username']) ? $bookingpress_configured_options['d7sms_username'] : $BookingPress->bookingpress_get_settings('d7sms_username', 'notification_setting');

                    $bookingpress_password = !empty($bookingpress_configured_options['d7sms_password']) ? $bookingpress_configured_options['d7sms_password'] : $BookingPress->bookingpress_get_settings('d7sms_password', 'notification_setting');

                    $bookingpress_sender_name = !empty($bookingpress_configured_options['d7sms_sender_name']) ? $bookingpress_configured_options['d7sms_sender_name'] : $BookingPress->bookingpress_get_settings('d7sms_sender_name', 'notification_setting');

                    $bookingpress_d7sms_send_url .= "&username=".$bookingpress_username."&password=".$bookingpress_password."&from=".$bookingpress_sender_name."&to=".$to_number."&content=".$to_message."&data_coding=unicode";

                    $bookingpress_d7sms_params = array(
                        'timeout' => 5000
                    );

                    $bookingpress_send_sms_res = wp_remote_get($bookingpress_d7sms_send_url, $bookingpress_d7sms_params);

                    do_action('bookingpress_integration_log_entry', 'sms_debug_logs', '', 'SMS Addon', 'D7 SMS send data', $bookingpress_d7sms_send_url, $bookingpress_debug_integration_log_id);

                    do_action('bookingpress_integration_log_entry', 'sms_debug_logs', '', 'SMS Addon', 'D7 SMS received response', json_encode($bookingpress_send_sms_res), $bookingpress_debug_integration_log_id);

                     
                    if(is_wp_error($bookingpress_send_sms_res)){
                        $bookingpress_return_res['return_res'] = $bookingpress_send_sms_res->get_error_message();
                    }else{
                        if( isset($bookingpress_send_sms_res['response']) && $bookingpress_send_sms_res['response'] != 200 ){
                            $error_code = $bookingpress_send_sms_res['response']['code'];
                            $error_msg =  $bookingpress_send_sms_res['response']['message'];
                            $bookingpress_return_res['return_type'] = 'error';
                            $bookingpress_return_res['return_res'] = __('SMS not sent successfully due to', 'bookingpress-sms')." ".$error_code." ".$error_msg;
                        } else {

                            $bookingpress_return_res['return_type'] = 'success';
                            $bookingpress_return_res['return_res'] = __('SMS Sent Successfully', 'bookingpress-sms');
                        }
                    }
                    
                }else if($bookingpress_selected_sms_gateway == "EZTexting"){

                    $bookingpress_eztexting_send_url = "https://app.eztexting.com/sending/messages?format=json";

                    $bookingpress_username = !empty($bookingpress_configured_options['eztexting_user']) ? $bookingpress_configured_options['eztexting_user'] : $BookingPress->bookingpress_get_settings('eztexting_user', 'notification_setting');

                    $bookingpress_password = !empty($bookingpress_configured_options['eztexting_password']) ? $bookingpress_configured_options['eztexting_password'] : $BookingPress->bookingpress_get_settings('eztexting_password', 'notification_setting');

                    $bookingpress_eztexting_params = array(
                        'timeout' => 5000,
                        'headers' => array(
                            'Content-Type' => 'application/json',
                        ),
                        'body' => json_encode(array(
                            'User' => $bookingpress_username,
                            'Password' => $bookingpress_password,
                            'PhoneNumbers' => $to_number,
                            'Message' => $to_message,
                        )),
                    );

                    $bookingpress_send_sms_res = wp_remote_post($bookingpress_eztexting_send_url, $bookingpress_eztexting_params);

                    do_action('bookingpress_integration_log_entry', 'sms_debug_logs', '', 'SMS Addon', 'EZTexting send data', $bookingpress_eztexting_params, $bookingpress_debug_integration_log_id);

                    do_action('bookingpress_integration_log_entry', 'sms_debug_logs', '', 'SMS Addon', 'EZTexting received response', $bookingpress_send_sms_res, $bookingpress_debug_integration_log_id);

                    if(is_wp_error($bookingpress_send_sms_res)){
                        $bookingpress_return_res['return_res'] = $bookingpress_send_sms_res->get_error_message();
                    }else{
                        if( isset($bookingpress_send_sms_res['response']) && $bookingpress_send_sms_res['response'] != 200 ){
                            $error_code = $bookingpress_send_sms_res['response']['code'];
                            $error_msg =  $bookingpress_send_sms_res['response']['message'];
                            $bookingpress_return_res['return_type'] = 'error';
                            $bookingpress_return_res['return_res'] = __('SMS not sent successfully due to ', 'bookingpress-sms')." ".$error_code." ".$error_msg;
                        } else {

                            $bookingpress_return_res['return_type'] = 'success';
                            $bookingpress_return_res['return_res'] = __('SMS Sent Successfully', 'bookingpress-sms');
                        }
                    }
                    
                }else if($bookingpress_selected_sms_gateway == "RingCaptcha"){

                    global $BookingPress;

                    $bookingpress_sms_ringcaptcha_locale = $BookingPress->bookingpress_get_settings('bookingpress_selected_ringcaptcha_locale', 'notification_setting');

                    if( !empty( $bookingpress_sms_ringcaptcha_locale )){
                        $bookingpress_sms_locale = $bookingpress_sms_ringcaptcha_locale;
                    }else {
                        $bookingpress_sms_locale = 'en';
                    }

                    $bookingpress_app_key = !empty($bookingpress_configured_options['ringcaptcha_app_key']) ? $bookingpress_configured_options['ringcaptcha_app_key'] : $BookingPress->bookingpress_get_settings('ringcaptcha_app_key', 'notification_setting');

                    $bookingpress_api_key = !empty($bookingpress_configured_options['ringcaptcha_api_key']) ? $bookingpress_configured_options['ringcaptcha_api_key'] : $BookingPress->bookingpress_get_settings('ringcaptcha_api_key', 'notification_setting');

                    $bookingpress_ringcaptcha_send_url = "https://api.ringcaptcha.com/".$bookingpress_app_key."/sms";

                    $bookingpress_ringcaptcha_params = array(
                        'timeout' => 5000,
                        'body' => array(
                            'app_key' => $bookingpress_app_key,
                            'api_key' => $bookingpress_api_key,
                            'phone' => $to_number,
                            'message' => $to_message,
                            'locale' => $bookingpress_sms_locale,
                        ),
                    );

                    $bookingpress_send_sms_res = wp_remote_post($bookingpress_ringcaptcha_send_url, $bookingpress_ringcaptcha_params);

                    do_action('bookingpress_integration_log_entry', 'sms_debug_logs', '', 'SMS Addon', 'RingCaptcha send data', $bookingpress_ringcaptcha_params, $bookingpress_debug_integration_log_id);

                    do_action('bookingpress_integration_log_entry', 'sms_debug_logs', '', 'SMS Addon', 'RingCaptcha received response', $bookingpress_send_sms_res, $bookingpress_debug_integration_log_id);

                    if(is_wp_error($bookingpress_send_sms_res)){
                        $bookingpress_return_res['return_res'] = $bookingpress_send_sms_res->get_error_message();
                    }else{
                        $bookingpress_send_sms_res = !empty( $bookingpress_send_sms_res['body']) ? json_decode($bookingpress_send_sms_res['body'],true) : array();
                        if( isset($bookingpress_send_sms_res['status']) && $bookingpress_send_sms_res['status'] == 'ERROR' ){
                            $bookingpress_sms_error_msg = $bookingpress_send_sms_res['message'];
                            $bookingpress_return_res['return_type'] = 'error';
                            $bookingpress_return_res['return_res'] = __('SMS not sent successfully due to ', 'bookingpress-sms').$bookingpress_sms_error_msg;

                        } else {
                            $bookingpress_return_res['return_type'] = 'success';
                            $bookingpress_return_res['return_res'] = __('SMS Sent Successfully', 'bookingpress-sms');
                        }
                    }
                    
                }else if($bookingpress_selected_sms_gateway == "Bulksms"){

                    $bookingpress_bulksms_send_url = "https://api.bulksms.com/v1/messages?auto-unicode=true";

                    $bookingpress_username = !empty($bookingpress_configured_options['bulksms_username']) ? $bookingpress_configured_options['bulksms_username'] : $BookingPress->bookingpress_get_settings('bulksms_username', 'notification_setting');

                    $bookingpress_password = !empty($bookingpress_configured_options['bulksms_password']) ? $bookingpress_configured_options['bulksms_password'] : $BookingPress->bookingpress_get_settings('bulksms_password', 'notification_setting');

                    $messages = array(
                        array(
                            'to' => str_replace(' ', '', $to_number ),
                            'body' => $to_message,
                        ),
                    );

                    $bookingpress_bulksms_params = array(
                        'method'=> 'POST',
                        'headers' => array( 
                            'Authorization' =>  'Basic '. base64_encode( $bookingpress_username.':'.$bookingpress_password ),
                            'Content-Type' => 'application/json',
                        ),
                        'body' => json_encode($messages),
                    );

                    $bookingpress_send_sms_res = wp_remote_post($bookingpress_bulksms_send_url, $bookingpress_bulksms_params);

                    do_action('bookingpress_integration_log_entry', 'sms_debug_logs', '', 'SMS Addon', 'Bulksms send data', $bookingpress_bulksms_params, $bookingpress_debug_integration_log_id);

                    do_action('bookingpress_integration_log_entry', 'sms_debug_logs', '', 'SMS Addon', 'Bulksms received response', $bookingpress_send_sms_res, $bookingpress_debug_integration_log_id);

                    if(is_wp_error($bookingpress_send_sms_res)){
                        $bookingpress_return_res['return_res'] = $bookingpress_send_sms_res->get_error_message();
                    }else{ 
                        $bookingpress_send_sms_res = !empty( $bookingpress_send_sms_res['body']) ? json_decode($bookingpress_send_sms_res['body'],true) : array();
                        if( isset($bookingpress_send_sms_res['status']) && $bookingpress_send_sms_res['status'] != 201 ){
                            $bookingpress_sms_error_status = $bookingpress_send_sms_res['status'];
                            $bookingpress_sms_error_msg = $bookingpress_send_sms_res['title'];
                            $bookingpress_return_res['return_type'] = 'error';
                            $bookingpress_return_res['return_res'] = __('SMS not sent successfully due to ', 'bookingpress-sms').$bookingpress_sms_error_status." ".$bookingpress_sms_error_msg;

                        } else {
                            $bookingpress_return_res['return_type'] = 'success';
                            $bookingpress_return_res['return_res'] = __('SMS Sent Successfully', 'bookingpress-sms');
                        }
                    }
                    
                }else if($bookingpress_selected_sms_gateway == "Exotel"){
                    
                    $bookingpress_api_key = !empty($bookingpress_configured_options['exotel_api_key']) ? $bookingpress_configured_options['exotel_api_key'] : $BookingPress->bookingpress_get_settings('exotel_api_key', 'notification_setting');

                    $bookingpress_api_token = !empty($bookingpress_configured_options['exotel_api_token']) ? $bookingpress_configured_options['exotel_api_token'] : $BookingPress->bookingpress_get_settings('exotel_api_token', 'notification_setting');

                    $bookingpress_subdomain = !empty($bookingpress_configured_options['exotel_sub_domain']) ? $bookingpress_configured_options['exotel_sub_domain'] : $BookingPress->bookingpress_get_settings('exotel_sub_domain', 'notification_setting');

                    $bookingpress_account_sid = !empty($bookingpress_configured_options['exotel_account_sid']) ? $bookingpress_configured_options['exotel_account_sid'] : $BookingPress->bookingpress_get_settings('exotel_account_sid', 'notification_setting');
                    
                    $bookingpress_sender_id = !empty($bookingpress_configured_options['exotel_sender_id']) ? $bookingpress_configured_options['exotel_sender_id'] : $BookingPress->bookingpress_get_settings('exotel_sender_id', 'notification_setting');

                    $bookingpress_exotel_send_url = "https://".$bookingpress_api_key.":".$bookingpress_api_token."".$bookingpress_subdomain."/v1/Accounts/".$bookingpress_account_sid."/Sms/send";

                    $bookingpress_exotel_params = array(
                        'timeout' => 5000,
                        'headers' => array(
                            'Content-Type' => 'application/json',
                        ),
                        'body' => array(
                            'api_key' => $bookingpress_api_key,
                            'api_token' => $bookingpress_api_token,
                            'subdomain' => $bookingpress_subdomain,
                            'account_sid' => $bookingpress_account_sid,
                            'from' => $bookingpress_sender_id,
                            'To' => $to_number,
                            'Body' => $to_message,
                            'EncodingType' => 'unicode',
                        ),
                    );

                    $bookingpress_send_sms_res = wp_remote_post($bookingpress_exotel_send_url, $bookingpress_exotel_params);

                    do_action('bookingpress_integration_log_entry', 'sms_debug_logs', '', 'SMS Addon', 'Exotel send data', $bookingpress_exotel_params, $bookingpress_debug_integration_log_id);

                    do_action('bookingpress_integration_log_entry', 'sms_debug_logs', '', 'SMS Addon', 'Exotel received response', json_encode($bookingpress_send_sms_res), $bookingpress_debug_integration_log_id);

                    if(!is_wp_error($bookingpress_send_sms_res)){
                        $bookingpress_return_res['return_type'] = 'success';
                        $bookingpress_return_res['return_res'] = __('SMS Sent Successfully', 'bookingpress-sms');
                    }else{
                        $bookingpress_return_res['return_res'] = $bookingpress_send_sms_res->get_error_message();
                    }
                    
                }else if($bookingpress_selected_sms_gateway == "Text Local"){
                    
                    $bookingpress_text_local_send_url = "https://api.textlocal.in/send";

                    $bookingpress_api_key = !empty($bookingpress_configured_options['textlocal_api_key']) ? $bookingpress_configured_options['textlocal_api_key'] : $BookingPress->bookingpress_get_settings('textlocal_api_key', 'notification_setting');

                    $bookingpress_api_sender_name = !empty($bookingpress_configured_options['textlocal_sender_name']) ? $bookingpress_configured_options['textlocal_sender_name'] : $BookingPress->bookingpress_get_settings('textlocal_sender_name', 'notification_setting');

                    $bookingpress_text_local_params = array(
                        'timeout' => 5000,
                        'body' => array(
                            'apikey' => $bookingpress_api_key,
                            'sender' => $bookingpress_api_sender_name,
                            'numbers' => $to_number,
                            'message' => $to_message,
                            'unicode' => 'true',
                        ),
                    );

                    $bookingpress_send_sms_res = wp_remote_post($bookingpress_text_local_send_url, $bookingpress_text_local_params);

                    do_action('bookingpress_integration_log_entry', 'sms_debug_logs', '', 'SMS Addon', 'Text Local send data', $bookingpress_text_local_params, $bookingpress_debug_integration_log_id);

                    do_action('bookingpress_integration_log_entry', 'sms_debug_logs', '', 'SMS Addon', 'Text Local received response', $bookingpress_send_sms_res, $bookingpress_debug_integration_log_id);

                    

                    if(is_wp_error($bookingpress_send_sms_res)){
                        $bookingpress_return_res['return_res'] = $bookingpress_send_sms_res->get_error_message();
                    }else{
                        if( !empty($bookingpress_send_sms_res)){
                            $res_body = !empty($bookingpress_send_sms_res['body']) ? json_decode($bookingpress_send_sms_res['body']) : array();
                            if( $res_body->status == 'failure'){

                                $error = $res_body->errors;
                                foreach($error as $key=>$val){
                                    $error_code = $val->code;
                                    $error_message = $val->message;
                                }
                                $bookingpress_return_res['return_type'] = 'error';
                                $bookingpress_return_res['return_res'] = __('SMS not sent successfully due to error code', 'bookingpress-sms').' '. $error_code .' '. $error_message;
                            } else {
                                $bookingpress_return_res['return_type'] = 'success';
                                $bookingpress_return_res['return_res'] = __('SMS Sent Successfully', 'bookingpress-sms');
                            }
                        }
                    }
                    
                }else if($bookingpress_selected_sms_gateway == "Nexmo"){

                    $bookingpress_nexmo_send_url = "https://rest.nexmo.com/sms/json";

                    $bookingpress_api_key = !empty($bookingpress_configured_options['nexmo_api_key']) ? $bookingpress_configured_options['nexmo_api_key'] : $BookingPress->bookingpress_get_settings('nexmo_api_key', 'notification_setting');

                    $bookingpress_api_secret = !empty($bookingpress_configured_options['nexmo_api_secret']) ? $bookingpress_configured_options['nexmo_api_secret'] : $BookingPress->bookingpress_get_settings('nexmo_api_secret', 'notification_setting');

                    $bookingpress_sender_id = !empty($bookingpress_configured_options['nexmo_sender_id']) ? $bookingpress_configured_options['nexmo_sender_id'] : $BookingPress->bookingpress_get_settings('nexmo_sender_id', 'notification_setting');

                    $bookingpress_nexmo_params = array(
                        'timeout' => 5000,
                        'headers' => array(
                            'Content-Type' => 'application/json',
                        ),
                        'body' => json_encode(array(
                            'api_key' => $bookingpress_api_key,
                            'api_secret' => $bookingpress_api_secret,
                            'from' => $bookingpress_sender_id,
                            'type' => 'unicode',
                            'to' => $to_number,
                            'text' => $to_message
                        )),
                    );

                    $bookingpress_send_sms_res = wp_remote_post($bookingpress_nexmo_send_url, $bookingpress_nexmo_params);

                    do_action('bookingpress_integration_log_entry', 'sms_debug_logs', '', 'SMS Addon', 'Nexmo send data', $bookingpress_nexmo_params, $bookingpress_debug_integration_log_id);

                    do_action('bookingpress_integration_log_entry', 'sms_debug_logs', '', 'SMS Addon', 'Nexmo received response', $bookingpress_send_sms_res, $bookingpress_debug_integration_log_id);

                    if(!is_wp_error($bookingpress_send_sms_res)){
                        $bookingpress_return_res['return_type'] = 'success';
                        $bookingpress_return_res['return_res'] = __('SMS Sent Successfully', 'bookingpress-sms');
                    }else{
                        $bookingpress_return_res['return_res'] = $bookingpress_send_sms_res->get_error_message();
                    }
                    
                }else if($bookingpress_selected_sms_gateway == "MSG91"){

                    $bookingpress_msg91_send_url = "http://api.msg91.com/api/sendhttp.php";

                    $bookingpress_auth_key = !empty($bookingpress_configured_options['msg91_auth_key']) ? $bookingpress_configured_options['msg91_auth_key'] : $BookingPress->bookingpress_get_settings('msg91_auth_key', 'notification_setting');

                    $bookingpress_sender_id = !empty($bookingpress_configured_options['msg91_sender_id']) ? $bookingpress_configured_options['msg91_sender_id'] : $BookingPress->bookingpress_get_settings('msg91_sender_id', 'notification_setting');

                    $bookingpress_msg91_params = array(
                        'timeout' => 5000,
                        'body' => array(
                            'authkey' => $bookingpress_auth_key,
                            'sender' => $bookingpress_sender_id,
                            'mobiles' => $to_number,
                            'message' => $to_message,
                        ),
                    );

                    $bookingpress_send_sms_res = wp_remote_post($bookingpress_msg91_send_url, $bookingpress_msg91_params);

                    do_action('bookingpress_integration_log_entry', 'sms_debug_logs', '', 'SMS Addon', 'MSG91 send data', $bookingpress_msg91_params, $bookingpress_debug_integration_log_id);

                    do_action('bookingpress_integration_log_entry', 'sms_debug_logs', '', 'SMS Addon', 'MSG91 received response', $bookingpress_send_sms_res, $bookingpress_debug_integration_log_id);

                    if(!is_wp_error($bookingpress_send_sms_res)){
                        $bookingpress_return_res['return_type'] = 'success';
                        $bookingpress_return_res['return_res'] = __('SMS Sent Successfully', 'bookingpress-sms');
                    }else{
                        $bookingpress_return_res['return_res'] = $bookingpress_send_sms_res->get_error_message();
                    }
                    
                }else if($bookingpress_selected_sms_gateway == "Textbelt"){

                    $bookingpress_textbelt_send_url = "https://textbelt.com/text";

                    $bookingpress_key = !empty($bookingpress_configured_options['textbelt_key']) ? $bookingpress_configured_options['textbelt_key'] : $BookingPress->bookingpress_get_settings('textbelt_key', 'notification_setting');

                    $bookingpress_textbelt_params = array(
                        'timeout' => 5000,
                        'body' => array(
                            'key' => $bookingpress_key,
                            'phone' => $to_number,
                            'message' => $to_message,
                        )
                    );

                    $bookingpress_send_sms_res = wp_remote_post($bookingpress_textbelt_send_url, $bookingpress_textbelt_params);

                    do_action('bookingpress_integration_log_entry', 'sms_debug_logs', '', 'SMS Addon', 'Textbelt send data', $bookingpress_textbelt_params, $bookingpress_debug_integration_log_id);

                    do_action('bookingpress_integration_log_entry', 'sms_debug_logs', '', 'SMS Addon', 'Textbelt received response', $bookingpress_send_sms_res, $bookingpress_debug_integration_log_id);

                    if(is_wp_error($bookingpress_send_sms_res)){
                        $bookingpress_return_res['return_res'] = $bookingpress_send_sms_res->get_error_message();
                    }else{
                        if( !empty($bookingpress_send_sms_res )){
                            $textbelt_response = !empty( $bookingpress_send_sms_res['body']) ? json_decode($bookingpress_send_sms_res['body']) : array();
                            
                            if( isset( $textbelt_response->error) && !empty($textbelt_response->error) ){
                                $textbelt_error_message = $textbelt_response->error;
                                $bookingpress_return_res['return_type'] = 'error';
                                $bookingpress_return_res['return_res'] = __('SMS not sent successfully due to error', 'bookingpress-sms'). $textbelt_error_message;        
                            } else {
                                $bookingpress_return_res['return_type'] = 'success';
                                $bookingpress_return_res['return_res'] = __('SMS Sent Successfully', 'bookingpress-sms');
                            }
                        }
                    }
                    
                }else if($bookingpress_selected_sms_gateway == "Vianett"){

                    $bookingpress_vianett_send_url = "https://smsc.vianett.no/v3/send";

                    $bookingpress_username = !empty($bookingpress_configured_options['vianett_username']) ? $bookingpress_configured_options['vianett_username'] : $BookingPress->bookingpress_get_settings('vianett_username', 'notification_setting');

                    $bookingpress_password = !empty($bookingpress_configured_options['vianett_password']) ? $bookingpress_configured_options['vianett_password'] : $BookingPress->bookingpress_get_settings('vianett_password', 'notification_setting');

                    $bookingpress_vianett_params = array(
                        'timeout' => 5000,
                        'body' => array(
                            'username' => $bookingpress_username,
                            'password' => $bookingpress_password,
                            'tel' => $to_number,
                            'msg' => $to_message
                        )
                    );

                    $bookingpress_send_sms_res = wp_remote_post($bookingpress_vianett_send_url, $bookingpress_vianett_params);

                    do_action('bookingpress_integration_log_entry', 'sms_debug_logs', '', 'SMS Addon', 'Vianett send data', $bookingpress_vianett_params, $bookingpress_debug_integration_log_id);

                    do_action('bookingpress_integration_log_entry', 'sms_debug_logs', '', 'SMS Addon', 'Vianett received response', $bookingpress_send_sms_res, $bookingpress_debug_integration_log_id);

                    if(!is_wp_error($bookingpress_send_sms_res)){
                        $bookingpress_return_res['return_type'] = 'success';
                        $bookingpress_return_res['return_res'] = __('SMS Sent Successfully', 'bookingpress-sms');
                    }else{
                        $bookingpress_return_res['return_res'] = $bookingpress_send_sms_res->get_error_message();
                    }

                    
                }else if($bookingpress_selected_sms_gateway == "SMS Global"){

                    $bookingpress_smsglobal_send_url = "https://api.smsglobal.com/http-api.php";

                    $bookingpress_username = !empty($bookingpress_configured_options['smsglobal_user']) ? $bookingpress_configured_options['smsglobal_user'] : $BookingPress->bookingpress_get_settings('smsglobal_user', 'notification_setting');

                    $bookingpress_password = !empty($bookingpress_configured_options['smsglobal_password']) ? $bookingpress_configured_options['smsglobal_password'] : $BookingPress->bookingpress_get_settings('smsglobal_password', 'notification_setting');

                    $bookingpress_sender_id = !empty($bookingpress_configured_options['smsglobal_sender_id']) ? $bookingpress_configured_options['smsglobal_sender_id'] : $BookingPress->bookingpress_get_settings('smsglobal_sender_id', 'notification_setting');

                    $to_number = str_replace( '+','',$to_number );
                    $bookingpress_smsglobal_params = array(
                        'timeout' => 5000,
                        'body' => array(
                            'action' => 'sendsms',
                            'user' => $bookingpress_username,
                            'password' => $bookingpress_password,
                            'from' => $bookingpress_sender_id,
                            'to' => $to_number,
                            'text' => $to_message,
                        )
                    );

                    $bookingpress_send_sms_res = wp_remote_post($bookingpress_smsglobal_send_url, $bookingpress_smsglobal_params);

                    do_action('bookingpress_integration_log_entry', 'sms_debug_logs', '', 'SMS Addon', 'SMS Global send data', $bookingpress_smsglobal_params, $bookingpress_debug_integration_log_id);

                    do_action('bookingpress_integration_log_entry', 'sms_debug_logs', '', 'SMS Addon', 'SMS Global received response', $bookingpress_send_sms_res, $bookingpress_debug_integration_log_id);

                    if(is_wp_error($bookingpress_send_sms_res)){
                        $bookingpress_return_res['return_res'] = $bookingpress_send_sms_res->get_error_message();
                    }else{
                        if(!empty( $bookingpress_send_sms_res)){
                            $error_code = $bookingpress_send_sms_res['body'];
                            $bookingpress_send_sms_res_str = $bookingpress_send_sms_res['body'];
                            $result = substr($bookingpress_send_sms_res_str, 0, 5);
                            if( $result == 'ERROR'){
                                $bookingpress_return_res['return_type'] = 'error';
                                $bookingpress_return_res['return_res'] = __('SMS not send successfully due to error ','bookingpress-sms'). $error_code;
                            } else {
                                $bookingpress_return_res['return_type'] = 'success';
                                $bookingpress_return_res['return_res'] = __('SMS Sent Successfully', 'bookingpress-sms');
                            }
                        }
                    }

                }else if($bookingpress_selected_sms_gateway == "Text Marketer"){

                    $bookingpress_text_marketer_send_url = "https://api.textmarketer.co.uk/gateway";

                    $bookingpress_username = !empty($bookingpress_configured_options['textmarketer_username']) ? $bookingpress_configured_options['textmarketer_username'] : $BookingPress->bookingpress_get_settings('textmarketer_username', 'notification_setting');

                    $bookingpress_password = !empty($bookingpress_configured_options['textmarketer_password']) ? $bookingpress_configured_options['textmarketer_password'] : $BookingPress->bookingpress_get_settings('textmarketer_password', 'notification_setting');

                    $bookingpress_originator = !empty($bookingpress_configured_options['textmarketer_originator']) ? $bookingpress_configured_options['textmarketer_originator'] : $BookingPress->bookingpress_get_settings('textmarketer_originator', 'notification_setting');

                    $bookingpress_text_marketer_params = array(
                        'timeout' => 5000,
                        'body' => array(
                            'username' => $bookingpress_username,
                            'password' => $bookingpress_password,
                            'orig' => $bookingpress_originator,
                            'to' => $to_number,
                            'message' => $to_message
                        ),
                    );

                    $bookingpress_send_sms_res = wp_remote_post($bookingpress_text_marketer_send_url, $bookingpress_text_marketer_params);

                    do_action('bookingpress_integration_log_entry', 'sms_debug_logs', '', 'SMS Addon', 'Text Marketer send data', $bookingpress_text_marketer_params, $bookingpress_debug_integration_log_id);

                    do_action('bookingpress_integration_log_entry', 'sms_debug_logs', '', 'SMS Addon', 'Text Marketer received response', $bookingpress_send_sms_res, $bookingpress_debug_integration_log_id);

                    if(is_wp_error($bookingpress_send_sms_res)){
                        $bookingpress_return_res['return_res'] = $bookingpress_send_sms_res->get_error_message();
                    }else{
                        if( !empty($bookingpress_send_sms_res) ){
                            $bookingpress_response = $bookingpress_send_sms_res['response'];
                            if( $bookingpress_response['code'] != 200 ){
                                $text_marketer_error_msg = $bookingpress_response['message'];

                                $bookingpress_return_res['return_type'] = 'error';
                                $bookingpress_return_res['return_res'] = __('SMS not sent Successfully due to error','bookingpress-sms'). $text_marketer_error_msg; 
                            } else {
                                $bookingpress_return_res['return_type'] = 'success';
                                $bookingpress_return_res['return_res'] = __('SMS Sent Successfully', 'bookingpress-sms');    
                            }
                        }
                    }
                    
                }else if($bookingpress_selected_sms_gateway == "Swift SMS Gateway"){

                    $bookingpress_account_key = !empty($bookingpress_configured_options['swiftsms_account_key']) ? $bookingpress_configured_options['swiftsms_account_key'] : $BookingPress->bookingpress_get_settings('swiftsms_account_key', 'notification_setting');

                    $bookingpress_swift_sms_gateway_url = "http://smsgateway.ca/services/message.svc/".$bookingpress_account_key."/".$to_number;

                    $bookingpress_swift_gateway_params = array(
                        'timeout' => 5000,
                        'body' => array(
                            'account_key' => $bookingpress_account_key,
                            'CellNumber' => $to_number,
                            'MessageBody' => $to_message
                        )
                    );

                    $bookingpress_send_sms_res = wp_remote_post($bookingpress_swift_sms_gateway_url, $bookingpress_swift_gateway_params);

                    do_action('bookingpress_integration_log_entry', 'sms_debug_logs', '', 'SMS Addon', 'Swift SMS Gateway send data', $bookingpress_swift_gateway_params, $bookingpress_debug_integration_log_id);

                    do_action('bookingpress_integration_log_entry', 'sms_debug_logs', '', 'SMS Addon', 'Swift SMS Gateway received response', $bookingpress_send_sms_res, $bookingpress_debug_integration_log_id);

                    if(!is_wp_error($bookingpress_send_sms_res)){
                        $bookingpress_return_res['return_type'] = 'success';
                        $bookingpress_return_res['return_res'] = __('SMS Sent Successfully', 'bookingpress-sms');
                    }else{
                        $bookingpress_return_res['return_res'] = $bookingpress_send_sms_res->get_error_message();
                    }
                    
                }else if($bookingpress_selected_sms_gateway == "MSG Club"){

                    $bookingpress_msg_club_send_url = "http://msg.msgclub.net/rest/services/sendSMS/sendGroupSms";

                    $bookingpress_auth_key = !empty($bookingpress_configured_options['msgclub_auth_key']) ? $bookingpress_configured_options['msgclub_auth_key'] : $BookingPress->bookingpress_get_settings('msgclub_auth_key', 'notification_setting');

                    $bookingpress_sender_id = !empty($bookingpress_configured_options['msgclub_sender_id']) ? $bookingpress_configured_options['msgclub_sender_id'] : $BookingPress->bookingpress_get_settings('msgclub_sender_id', 'notification_setting');

                    $bookingpress_msg_club_params = array(
                        'timeout' => 5000,
                        'body' => array(
                            'AUTH_KEY' => $bookingpress_auth_key,
                            'senderId' => $bookingpress_sender_id,
                            'to' => $to_number,
                            'message' => $to_message,
                        )
                    );

                    $bookingpress_send_sms_res = wp_remote_post($bookingpress_msg_club_send_url, $bookingpress_msg_club_params);

                    do_action('bookingpress_integration_log_entry', 'sms_debug_logs', '', 'SMS Addon', 'MSG Club Gateway send data', $bookingpress_msg_club_params, $bookingpress_debug_integration_log_id);

                    do_action('bookingpress_integration_log_entry', 'sms_debug_logs', '', 'SMS Addon', 'MSG Club Gateway received response', $bookingpress_send_sms_res, $bookingpress_debug_integration_log_id);

                    if(is_wp_error($bookingpress_send_sms_res)){
                        $bookingpress_return_res['return_res'] = $bookingpress_send_sms_res->get_error_message();
                    }else{
                        if(!empty($bookingpress_send_sms_res) ){

                            $bookingpress_send_sms_response = json_decode($bookingpress_send_sms_res['body']);
                            $response_code = $bookingpress_send_sms_response->responseCode;
                            $response_message = $bookingpress_send_sms_response->response;
                            if( $response_code != 3001 ){
                                $bookingpress_return_res['return_type'] = 'error';
                                $bookingpress_return_res['return_res'] = __('SMS not sent successfully due to error','bookingpress-sms').' '.$response_code .' '.$response_message;
                            } else {
                                $bookingpress_return_res['return_type'] = 'success';
                                $bookingpress_return_res['return_res'] = __('SMS Sent Successfully', 'bookingpress-sms');        
                            }
                        }
                    }
                    
                }else if($bookingpress_selected_sms_gateway == "Easy Send Sms"){

                    $bookingpress_easy_send_sms_url = "https://api.easysendsms.app/bulksms";

                    $bookingpress_username = !empty($bookingpress_configured_options['easysendsms_username']) ? $bookingpress_configured_options['easysendsms_username'] : $BookingPress->bookingpress_get_settings('easysendsms_username', 'notification_setting');
                    
                    $bookingpress_password = !empty($bookingpress_configured_options['easysendsms_password']) ? $bookingpress_configured_options['easysendsms_password'] : $BookingPress->bookingpress_get_settings('easysendsms_password', 'notification_setting');

                    $bookingpress_sender_name = !empty($bookingpress_configured_options['easysendsms_sender_name']) ? $bookingpress_configured_options['easysendsms_sender_name'] : $BookingPress->bookingpress_get_settings('easysendsms_sender_name', 'notification_setting');

                    $to_number = str_replace('+', '', $to_number );
                    $to_number = str_replace(' ', '', $to_number );

                    $bookingpress_easy_send_sms_params = array(
                        'timeout' => 5000,
                        'body' => array(
                            'username' => $bookingpress_username,
                            'password' => $bookingpress_password,
                            'from' => $bookingpress_sender_name,
                            'to' => $to_number,
                            'text' => $to_message,
                            'type' => 1,
                        )
                    );

                    $bookingpress_send_sms_res = wp_remote_post($bookingpress_easy_send_sms_url, $bookingpress_easy_send_sms_params);

                    do_action('bookingpress_integration_log_entry', 'sms_debug_logs', '', 'SMS Addon', 'Easy Send Sms Gateway send data', $bookingpress_easy_send_sms_params, $bookingpress_debug_integration_log_id);

                    do_action('bookingpress_integration_log_entry', 'sms_debug_logs', '', 'SMS Addon', 'Easy Send Sms Gateway received response', $bookingpress_send_sms_res, $bookingpress_debug_integration_log_id);

                    if(is_wp_error($bookingpress_send_sms_res)){
                        $bookingpress_return_res['return_res'] = $bookingpress_send_sms_res->get_error_message();
                    }else{
                        if(!empty( $bookingpress_send_sms_res)){
                            $bookingpress_send_sms_res = $bookingpress_send_sms_res['body'];
                            $result = substr($bookingpress_send_sms_res, 0, 2);
                            if( $result == 'OK'){
                                $bookingpress_return_res['return_type'] = 'success';
                                $bookingpress_return_res['return_res'] = __('SMS Sent Successfully', 'bookingpress-sms');
                            } else {
                                $bookingpress_send_sms_error_code = $bookingpress_send_sms_res;
                                $bookingpress_return_res['return_type'] = 'error';
                                $bookingpress_return_res['return_res'] = __('SMS not sent with error code','bookingpress-sms').' '.$bookingpress_send_sms_error_code;
                            }
                        }
                    }
                    
                }else if($bookingpress_selected_sms_gateway == "Cheap Global SMS"){

                    $bookingpress_cheap_global_sms_url = "http://cheapglobalsms.com/api_v1?action=send_sms";

                    $bookingpress_account_no = !empty($bookingpress_configured_options['cheapglobalsms_account_no']) ? $bookingpress_configured_options['cheapglobalsms_account_no'] : $BookingPress->bookingpress_get_settings('cheapglobalsms_account_no', 'notification_setting');

                    $bookingpress_account_pass = !empty($bookingpress_configured_options['cheapglobalsms_account_password']) ? $bookingpress_configured_options['cheapglobalsms_account_password'] : $BookingPress->bookingpress_get_settings('cheapglobalsms_account_password', 'notification_setting');

                    $bookingpress_sender_id = !empty($bookingpress_configured_options['cheapglobalsms_sender_id']) ? $bookingpress_configured_options['cheapglobalsms_sender_id'] : $BookingPress->bookingpress_get_settings('cheapglobalsms_sender_id', 'notification_setting');

                    $bookingpress_cheap_global_sms_params = array(
                        'timeout' => 5000,
                        'body' => array(
                            'sub_account' => $bookingpress_account_no,
                            'sub_account_pass' => $bookingpress_account_pass,
                            'sender_id' => $bookingpress_sender_id,
                            'recipients' => $to_number,
                            'message' => $to_message,
                        )
                    );

                    $bookingpress_send_sms_res = wp_remote_post($bookingpress_cheap_global_sms_url, $bookingpress_cheap_global_sms_params);

                    do_action('bookingpress_integration_log_entry', 'sms_debug_logs', '', 'SMS Addon', 'Cheap Global SMS Gateway send data', $bookingpress_cheap_global_sms_params, $bookingpress_debug_integration_log_id);

                    do_action('bookingpress_integration_log_entry', 'sms_debug_logs', '', 'SMS Addon', 'Cheap Global SMS Gateway received response', $bookingpress_send_sms_res, $bookingpress_debug_integration_log_id);

                    if(is_wp_error($bookingpress_send_sms_res)){
                        $bookingpress_return_res['return_res'] = $bookingpress_send_sms_res->get_error_message();
                    }else{
                        if( !empty($bookingpress_send_sms_res )){

                            $send_sms_response = json_decode($bookingpress_send_sms_res['body']);
                            if( isset( $send_sms_response->error_code) && !empty($send_sms_response->error_code)){
                                $send_sms_response = $send_sms_response->error;
                                $bookingpress_return_res['return_type'] = 'error';
                                $bookingpress_return_res['return_res'] = __('SMS not sent successfully due to','bookingpress-sms').' '.$send_sms_response;
                            } else {
                                $bookingpress_return_res['return_type'] = 'success';
                                $bookingpress_return_res['return_res'] = __('SMS Sent Successfully', 'bookingpress-sms');
                            }
                        }
                    }
                    
                }else if($bookingpress_selected_sms_gateway == "Messente"){

                    $bookingpress_messente_url = "https://api2.messente.com/send_sms";

                    $bookingpress_username = !empty($bookingpress_configured_options['messente_username']) ? $bookingpress_configured_options['messente_username'] : $BookingPress->bookingpress_get_settings('messente_username', 'notification_setting');

                    $bookingpress_password = !empty($bookingpress_configured_options['messente_password']) ? $bookingpress_configured_options['messente_password'] : $BookingPress->bookingpress_get_settings('messente_password', 'notification_setting');

                    $bookingpress_sender_name = !empty($bookingpress_configured_options['messente_sender_name']) ? $bookingpress_configured_options['messente_sender_name'] : $BookingPress->bookingpress_get_settings('messente_sender_name', 'notification_setting');

                    $bookingpress_messente_params = array(
                        'timeout' => 5000,
                        'body' => array(
                            'username' => $bookingpress_username,
                            'password' => $bookingpress_password,
                            'from' => $bookingpress_sender_name,
                            'to' => $to_number,
                            'text' => $to_message
                        )
                    );

                    $bookingpress_send_sms_res = wp_remote_post($bookingpress_messente_url, $bookingpress_messente_params);

                    do_action('bookingpress_integration_log_entry', 'sms_debug_logs', '', 'SMS Addon', 'Messente Gateway send data', $bookingpress_messente_params, $bookingpress_debug_integration_log_id);

                    do_action('bookingpress_integration_log_entry', 'sms_debug_logs', '', 'SMS Addon', 'Messente Gateway received response', $bookingpress_send_sms_res, $bookingpress_debug_integration_log_id);

                    if(!is_wp_error($bookingpress_send_sms_res)){
                        $bookingpress_return_res['return_type'] = 'success';
                        $bookingpress_return_res['return_res'] = __('SMS Sent Successfully', 'bookingpress-sms');
                    }else{
                        $bookingpress_return_res['return_res'] = $bookingpress_send_sms_res->get_error_message();
                    }
                    
                }else if($bookingpress_selected_sms_gateway == "Message Bird"){

                    $bookingpress_message_bird_url = "https://rest.messagebird.com/messages";

                    $bookingpress_authorization_key = !empty($bookingpress_configured_options['messagebird_authorization_key']) ? $bookingpress_configured_options['messagebird_authorization_key'] : $BookingPress->bookingpress_get_settings('messagebird_authorization_key', 'notification_setting');

                    $bookingpress_originator = !empty($bookingpress_configured_options['messagebird_originator']) ? $bookingpress_configured_options['messagebird_originator'] : $BookingPress->bookingpress_get_settings('messagebird_originator', 'notification_setting');

                    $bookingpress_message_bird_params = array(
                        'timeout' => 5000,
                        'headers' => array(
                            "Authorization" => "AccessKey ".$bookingpress_authorization_key
                        ),
                        'body' => array(
                            'originator' => $bookingpress_originator,
                            'recipients' => $to_number,
                            'body' => $to_message,
                            'datacoding' => 'unicode',
                        ),
                    );

                    $bookingpress_send_sms_res = wp_remote_post($bookingpress_message_bird_url, $bookingpress_message_bird_params);

                    do_action('bookingpress_integration_log_entry', 'sms_debug_logs', '', 'SMS Addon', 'Message Bird Gateway send data', $bookingpress_message_bird_params, $bookingpress_debug_integration_log_id);

                    do_action('bookingpress_integration_log_entry', 'sms_debug_logs', '', 'SMS Addon', 'Message Bird Gateway received response', $bookingpress_send_sms_res, $bookingpress_debug_integration_log_id);

                    if(is_wp_error($bookingpress_send_sms_res)){
                        $bookingpress_return_res['return_res'] = $bookingpress_send_sms_res->get_error_message();
                    }else{
                        $bookingpress_send_sms_res = !empty( $bookingpress_send_sms_res['body']) ? json_decode($bookingpress_send_sms_res['body'],true) : array();
                        if( isset($bookingpress_send_sms_res['errors'])){
                            foreach($bookingpress_send_sms_res['errors'] as $key=>$val){
                                $bookingpress_sms_error_msg = $val['description'];
                            }
                            $bookingpress_return_res['return_type'] = 'error';
                            $bookingpress_return_res['return_res'] = __('SMS not sent successfully due to error','bookingpress-sms').' '.$bookingpress_sms_error_msg;

                        } else {
                            $bookingpress_return_res['return_type'] = 'success';
                            $bookingpress_return_res['return_res'] = __('SMS Sent Successfully', 'bookingpress-sms');
                        }
                    }
                    
                }else if($bookingpress_selected_sms_gateway == "Telnyx"){

                    $bookingpress_telnyx_send_url = "https://sms.telnyx.com/messages";

                    $bookingpress_profile_secret = !empty($bookingpress_configured_options['telnyx_profile_secret']) ? $bookingpress_configured_options['telnyx_profile_secret'] : $BookingPress->bookingpress_get_settings('telnyx_profile_secret', 'notification_setting');

                    $bookingpress_from_number = !empty($bookingpress_configured_options['telnyx_from_number']) ? $bookingpress_configured_options['telnyx_from_number'] : $BookingPress->bookingpress_get_settings('telnyx_from_number', 'notification_setting');

                    $bookingpress_telnyx_params = array(
                        'timeout' => 5000,
                        'headers' => array(
                            'x-profile-secret' => $bookingpress_profile_secret
                        ),
                        'body' => array(
                            'from' => $bookingpress_from_number,
                            'to' => $to_number,
                            'body' => $to_message
                        ),
                    );

                    $bookingpress_send_sms_res = wp_remote_post($bookingpress_telnyx_send_url, $bookingpress_telnyx_params);

                    do_action('bookingpress_integration_log_entry', 'sms_debug_logs', '', 'SMS Addon', 'Telnyx Gateway send data', $bookingpress_telnyx_params, $bookingpress_debug_integration_log_id);

                    do_action('bookingpress_integration_log_entry', 'sms_debug_logs', '', 'SMS Addon', 'Telnyx Gateway received response', $bookingpress_send_sms_res, $bookingpress_debug_integration_log_id);

                    if(is_wp_error($bookingpress_send_sms_res)){
                        $bookingpress_return_res['return_res'] = $bookingpress_send_sms_res->get_error_message();
                    }else{
                        if( !empty( $bookingpress_send_sms_res )){
                            if( !empty($bookingpress_send_sms_res['response']) && $bookingpress_send_sms_res['response']['code'] != 200){
                                $errorcode = $bookingpress_send_sms_res['response']['code'];
                                $errormsg = $bookingpress_send_sms_res['response']['message'];
                                $bookingpress_return_res['return_type'] = 'error';
                                $bookingpress_return_res['return_res'] = __('SMS not send successfully due to error','bookingpress-sms').' '.$errorcode.' '.$errormsg;
                            } else {
                                $bookingpress_return_res['return_type'] = 'success';
                                $bookingpress_return_res['return_res'] = __('SMS Sent Successfully', 'bookingpress-sms');
                            }
                        }
                    }
                    
                }else if($bookingpress_selected_sms_gateway == "Routee"){

                    $bookingpress_routee_application_id = !empty($bookingpress_configured_options['routee_application_id']) ? $bookingpress_configured_options['routee_application_id'] : $BookingPress->bookingpress_get_settings('routee_application_id', 'notification_setting');

                    $bookingpress_routee_application_secret = !empty($bookingpress_configured_options['routee_application_secret']) ? $bookingpress_configured_options['routee_application_secret'] : $BookingPress->bookingpress_get_settings('routee_application_secret', 'notification_setting');

                    $bookingpress_routee_sender_name = !empty($bookingpress_configured_options['routee_from_number']) ? $bookingpress_configured_options['routee_from_number'] : $BookingPress->bookingpress_get_settings('routee_from_number', 'notification_setting');
                    if( !empty($bookingpress_routee_application_id) && !empty($bookingpress_routee_application_secret) ){

                        $bookingpress_get_routee_access_expire_and_current_time = $BookingPress->bookingpress_get_settings('bookingpress_routee_sms_token_expire_time', 'notification_setting');

                        if( !empty( $bookingpress_get_routee_access_expire_and_current_time )){

                            $current_time = current_time('timestamp');

                            if( $current_time > $bookingpress_get_routee_access_expire_and_current_time ){

                                $bookingpress_sms_response_token = $this->sms_routee_func( $bookingpress_routee_application_id, $bookingpress_routee_application_secret );

                                do_action('bookingpress_integration_log_entry', 'sms_debug_logs', '', 'SMS Addon', 'Routee Gateway token data', json_encode($bookingpress_sms_response_token), $bookingpress_debug_integration_log_id);

                            } else {

                                $bookingpress_routee_sms_api = 'https://connect.routee.net/sms';
                                $bookingpress_routee_access_token = $BookingPress->bookingpress_get_settings('bookingpress_routee_sms_token','notification_setting');
    
                                $bookingpress_routee_sms_param = array(
                                    'timeout' => 5000,
                                    'headers' => array(
                                        'Content-Type' => 'application/json',
                                        'Authorization' => 'Bearer '. "$bookingpress_routee_access_token",
                                    ),
                                    'body' => json_encode(array(
                                        'from' => $bookingpress_routee_sender_name,
                                        'to' => $to_number,
                                        'body' => $to_message,
                                    )),
                                );
                                $bookingpress_sms_response_sms = wp_remote_post($bookingpress_routee_sms_api, $bookingpress_routee_sms_param);

                                do_action('bookingpress_integration_log_entry', 'sms_debug_logs', '', 'SMS Addon', 'Routee Gateway send data', $bookingpress_routee_sms_param, $bookingpress_debug_integration_log_id);

                                do_action('bookingpress_integration_log_entry', 'sms_debug_logs', '', 'SMS Addon', 'Routee Gateway received response', $bookingpress_sms_response_sms, $bookingpress_debug_integration_log_id);

                                if(is_wp_error($bookingpress_sms_response_sms)){
                                    $bookingpress_return_res['return_res'] = $bookingpress_sms_response_sms->get_error_message();
                                }else{
                                    if( !empty( $bookingpress_sms_response_sms )){
                                        
                                        if( !empty($bookingpress_sms_response_sms['response']['code']) && '200' != $bookingpress_sms_response_sms['response']['code'] ) {

                                            $bookingpress_sms_response_sms_body = !empty( $bookingpress_sms_response_sms['body']) ? json_decode($bookingpress_sms_response_sms['body']) : array();

                                            $errorcode = $bookingpress_sms_response_sms['response']['code'];
                                            $errormsg = $bookingpress_sms_response_sms_body->developerMessage;

                                            if( empty($errormsg)){
                                                
                                                $errormsg = $bookingpress_sms_response_sms['response']['message'];
                                            }

                                            $bookingpress_return_res['return_type'] = 'error';
                                            $bookingpress_return_res['return_res'] = __('SMS not send successfully due to error','bookingpress-sms').' '.$errorcode.' '.$errormsg;
                                        } else {
                                            $bookingpress_return_res['return_type'] = 'success';
                                            $bookingpress_return_res['return_res'] = __('SMS Sent Successfully', 'bookingpress-sms');
                                        }
                                    }
                                }
                            }
                        } else {
                            $bookingpress_sms_response_token = $this->sms_routee_func( $bookingpress_routee_application_id, $bookingpress_routee_application_secret );
                        }
                    }
                    
                }else if($bookingpress_selected_sms_gateway == "Wavecell"){

                    $bookingpress_authorization = !empty($bookingpress_configured_options['wavecell_authorization']) ? $bookingpress_configured_options['wavecell_authorization'] : $BookingPress->bookingpress_get_settings('wavecell_authorization', 'notification_setting');

                    $bookingpress_sub_account_id = !empty($bookingpress_configured_options['wavecell_sub_account_id']) ? $bookingpress_configured_options['wavecell_sub_account_id'] : $BookingPress->bookingpress_get_settings('wavecell_sub_account_id', 'notification_setting');

                    $bookingpress_source_id = !empty($bookingpress_configured_options['wavecell_source']) ? $bookingpress_configured_options['wavecell_source'] : $BookingPress->bookingpress_get_settings('wavecell_source', 'notification_setting');

                    $bookingpress_wavecell_send_url = "https://api.wavecell.com/sms/v1/".$bookingpress_sub_account_id."/single";

                    $bookingpress_wavecell_params = array(
                        'timeout' => 5000,
                        'headers' => array(
                            'authorization' => 'Bearer '.$bookingpress_authorization
                        ),
                        'body' => json_encode(array(
                            'sub_account' => $bookingpress_sub_account_id,
                            'source' => $bookingpress_source_id,
                            'destination' => $to_number,
                            'text' => $to_message
                        )),
                    );

                    $bookingpress_send_sms_res = wp_remote_post($bookingpress_wavecell_send_url, $bookingpress_wavecell_params);

                    do_action('bookingpress_integration_log_entry', 'sms_debug_logs', '', 'SMS Addon', 'Wavecell Gateway send data', $bookingpress_wavecell_params, $bookingpress_debug_integration_log_id);

                    do_action('bookingpress_integration_log_entry', 'sms_debug_logs', '', 'SMS Addon', 'Wavecell Gateway received response', $bookingpress_send_sms_res, $bookingpress_debug_integration_log_id);

                    if(!is_wp_error($bookingpress_send_sms_res)){
                        $bookingpress_return_res['return_type'] = 'success';
                        $bookingpress_return_res['return_res'] = __('SMS Sent Successfully', 'bookingpress-sms');
                    }else{
                        $bookingpress_return_res['return_res'] = $bookingpress_send_sms_res->get_error_message();
                    }
                    
                }else if($bookingpress_selected_sms_gateway == "EngageSPARK"){

                    $bookingpress_engagespark_send_url = "https://api.engagespark.com/v1/sms/phonenumber";

                    $bookingpress_authorization = !empty($bookingpress_configured_options['engagespark_authorization']) ? $bookingpress_configured_options['engagespark_authorization'] : $BookingPress->bookingpress_get_settings('engagespark_authorization', 'notification_setting');

                    $bookingpress_org_id = !empty($bookingpress_configured_options['engagespark_orgid']) ? $bookingpress_configured_options['engagespark_orgid'] : $BookingPress->bookingpress_get_settings('engagespark_orgid', 'notification_setting');

                    $bookingpress_sender_id = !empty($bookingpress_configured_options['engagespark_senderid']) ? $bookingpress_configured_options['engagespark_senderid'] : $BookingPress->bookingpress_get_settings('engagespark_senderid', 'notification_setting');

                    $to_number = str_replace('+','',$to_number);

                    $bookingpress_enagagespark_params = array(
                        'timeout' => 5000,
                        'headers' => array(
                            'Authorization' => 'Token '.$bookingpress_authorization
                        ),
                        'body' => json_encode(array(
                            'orgId' => $bookingpress_org_id,
                            'from' => $bookingpress_sender_id,
                            'to' => $to_number,
                            'message' => $to_message
                        )),
                    );

                    $bookingpress_send_sms_res = wp_remote_post($bookingpress_engagespark_send_url, $bookingpress_enagagespark_params);

                    do_action('bookingpress_integration_log_entry', 'sms_debug_logs', '', 'SMS Addon', 'EngageSPARK Gateway send data', $bookingpress_enagagespark_params, $bookingpress_debug_integration_log_id);

                    do_action('bookingpress_integration_log_entry', 'sms_debug_logs', '', 'SMS Addon', 'EngageSPARK Gateway received response', $bookingpress_send_sms_res, $bookingpress_debug_integration_log_id);

                    if(is_wp_error($bookingpress_send_sms_res)){
                        $bookingpress_return_res['return_res'] = $bookingpress_send_sms_res->get_error_message();
                    }else{
                        if( !empty( $bookingpress_send_sms_res )){
                            if( !empty( $bookingpress_send_sms_res['response']) && $bookingpress_send_sms_res['response']['code'] != 200 ){
                                $error_code = $bookingpress_send_sms_res['response']['code'];
                                $error_message = $bookingpress_send_sms_res['response']['message'];
                                $bookingpress_return_res['return_type'] = 'error';
                                $bookingpress_return_res['return_res'] = __('SMS not sent successfully due to error','bookingpress-sms').' '.$error_code. ' '.$error_message;
                            } else {

                                $bookingpress_return_res['return_type'] = 'success';
                                $bookingpress_return_res['return_res'] = __('SMS Sent Successfully', 'bookingpress-sms');
                            }
                        }
                    }
                    
                }else if($bookingpress_selected_sms_gateway == "SMS Gateway Center"){

                    $bookingpress_sms_gateway_center_send_url = "http://enterprise.smsgatewaycenter.com/SMSApi/rest/send";

                    $bookingpress_api_key = !empty($bookingpress_configured_options['smsgatewaycenter_api_key']) ? $bookingpress_configured_options['smsgatewaycenter_api_key'] : $BookingPress->bookingpress_get_settings('smsgatewaycenter_api_key', 'notification_setting');

                    $bookingpress_user_id = !empty($bookingpress_configured_options['smsgatewaycenter_user_id']) ? $bookingpress_configured_options['smsgatewaycenter_user_id'] : $BookingPress->bookingpress_get_settings('smsgatewaycenter_user_id', 'notification_setting');

                    $bookingpress_password = !empty($bookingpress_configured_options['smsgatewaycenter_password']) ? $bookingpress_configured_options['smsgatewaycenter_password'] : $BookingPress->bookingpress_get_settings('smsgatewaycenter_password', 'notification_setting');

                    $bookingpress_sender_id = !empty($bookingpress_configured_options['smsgatewaycenter_sender_id']) ? $bookingpress_configured_options['smsgatewaycenter_sender_id'] : $BookingPress->bookingpress_get_settings('smsgatewaycenter_sender_id', 'notification_setting');

                    $bookingpress_sms_gateway_center_params = array(
                        'timeout' => 5000,
                        'headers' => array(
                            'apiKey' => $bookingpress_api_key
                        ),
                        'body' => array(
                            'userId' => $bookingpress_user_id,
                            'password' => $bookingpress_password,
                            'sendMethod' => 'simpleMsg',
                            'senderId' => $bookingpress_sender_id,
                            'msgType' => 'unicode',
                            'mobile' => $to_number,
                            'msg' => $to_message,
                        ),
                    );

                    $bookingpress_send_sms_res = wp_remote_post($bookingpress_sms_gateway_center_send_url, $bookingpress_sms_gateway_center_params);

                    do_action('bookingpress_integration_log_entry', 'sms_debug_logs', '', 'SMS Addon', 'SMS Gateway Center Gateway send data', $bookingpress_sms_gateway_center_params, $bookingpress_debug_integration_log_id);

                    do_action('bookingpress_integration_log_entry', 'sms_debug_logs', '', 'SMS Addon', 'SMS Gateway Center Gateway received response', $bookingpress_send_sms_res, $bookingpress_debug_integration_log_id);

                    if(!is_wp_error($bookingpress_send_sms_res)){
                        $bookingpress_return_res['return_type'] = 'success';
                        $bookingpress_return_res['return_res'] = __('SMS Sent Successfully', 'bookingpress-sms');
                    }else{
                        $bookingpress_return_res['return_res'] = $bookingpress_send_sms_res->get_error_message();
                    }
                    
                }else if($bookingpress_selected_sms_gateway == "SMS API"){

                    global $BookingPress;

                    $bookingpress_sms_endpoint = $BookingPress->bookingpress_get_settings('bookingpress_selected_sms_api_endpoint', 'notification_setting');

                    if( !empty( $bookingpress_sms_endpoint )){
                        $bookingpress_sms_api_endpoint = $bookingpress_sms_endpoint;
                    }else {
                        $bookingpress_sms_api_endpoint = 'api.smsapi.com';
                    }

                    $bookingpress_sms_api_send_url = "https://".$bookingpress_sms_api_endpoint."/sms.do";

                    $bookingpress_access_token = !empty($bookingpress_configured_options['smsapi_access_token']) ? $bookingpress_configured_options['smsapi_access_token'] : $BookingPress->bookingpress_get_settings('smsapi_access_token', 'notification_setting');

                    $bookingpress_from_number = !empty($bookingpress_configured_options['smsapi_from_number']) ? $bookingpress_configured_options['smsapi_from_number'] : $BookingPress->bookingpress_get_settings('smsapi_from_number', 'notification_setting');
                    
                    if( $bookingpress_sms_api_endpoint == 'api.smsapi.pl') {
                        $check_tonumber_length = strlen($to_number) - substr_count($to_number, ' ');
                        if( $check_tonumber_length >= 11 ){
                            $check_tonumber = substr($to_number,0,2);
                            if( $check_tonumber == 48 ){
                                $to_number = substr($to_number,2);
                            }
                        }
                    }

                    $bookingpress_sms_api_params = array(
                        'timeout' => 5000,
                        'headers' => array(
                            'Authorization' => 'Bearer '.$bookingpress_access_token
                        ),
                        'body' => array(
                            'from' => $bookingpress_from_number,
                            'to' => $to_number,
                            'message' => $to_message,
                            'encoding' => 'utf-8',
                        ),
                    );

                    $bookingpress_send_sms_res = wp_remote_post($bookingpress_sms_api_send_url, $bookingpress_sms_api_params);

                    do_action('bookingpress_integration_log_entry', 'sms_debug_logs', '', 'SMS Addon', 'SMS API Gateway send data', $bookingpress_sms_api_params, $bookingpress_debug_integration_log_id);

                    do_action('bookingpress_integration_log_entry', 'sms_debug_logs', '', 'SMS Addon', 'SMS API Gateway received response', $bookingpress_send_sms_res, $bookingpress_debug_integration_log_id);

                    
                    if(is_wp_error($bookingpress_send_sms_res)){
                        $bookingpress_return_res['return_res'] = $bookingpress_send_sms_res->get_error_message();
                    }else{
                        if(!empty( $bookingpress_send_sms_res)){
                            $bookingpress_send_sms_res = $bookingpress_send_sms_res['body'];
                            $result = substr($bookingpress_send_sms_res, 0, 5);
                            if( $result == 'ERROR'){
                                $bookingpress_return_res['return_type'] = 'error';
                                $bookingpress_return_res['return_res'] = __('SMS not sent successfully','bookingpress-sms');
                            } else {
                                $bookingpress_return_res['return_type'] = 'success';
                                $bookingpress_return_res['return_res'] = __('SMS Sent Successfully', 'bookingpress-sms');
                            }
                        }
                    }
                    
                }else if($bookingpress_selected_sms_gateway == "Fast 2 SMS"){

                    $bookingpress_fast2sms_send_url = "https://www.fast2sms.com/dev/bulk";

                    $bookingpress_api_key = !empty($bookingpress_configured_options['fast2sms_api_key']) ? $bookingpress_configured_options['fast2sms_api_key'] : $BookingPress->bookingpress_get_settings('fast2sms_api_key', 'notification_setting');

                    $bookingpress_sender_id = !empty($bookingpress_configured_options['fast2sms_sender_id']) ? $bookingpress_configured_options['fast2sms_sender_id'] : $BookingPress->bookingpress_get_settings('fast2sms_sender_id', 'notification_setting');

                    $bookingpress_fast2sms_params = array(
                        'timeout' => 5000,
                        'headers' => array(
                            'authorization' => 'api_key '.$bookingpress_api_key
                        ),
                        'body' => array(
                            'sender_id' => $bookingpress_sender_id,
                            'route' => 'p',
                            'language' => 'english',
                            'numbers' => $to_number,
                            'message' => $to_message
                        ),
                    );

                    $bookingpress_send_sms_res = wp_remote_post($bookingpress_fast2sms_send_url, $bookingpress_fast2sms_params);

                    do_action('bookingpress_integration_log_entry', 'sms_debug_logs', '', 'SMS Addon', 'Fast 2 SMS Gateway send data', $bookingpress_fast2sms_params, $bookingpress_debug_integration_log_id);

                    do_action('bookingpress_integration_log_entry', 'sms_debug_logs', '', 'SMS Addon', 'Fast 2 SMS Gateway received response', $bookingpress_send_sms_res, $bookingpress_debug_integration_log_id);

                    if(is_wp_error($bookingpress_send_sms_res)){
                        $bookingpress_return_res['return_res'] = $bookingpress_send_sms_res->get_error_message();
                    }else{
                        if( !empty( $bookingpress_send_sms_res) ){
                            if(!empty( $bookingpress_send_sms_res['response']) && !empty($bookingpress_send_sms_res['response']['code'] != 200 ) ){
                                $error_code = $bookingpress_send_sms_res['response']['code'];
                                $error_msg = $bookingpress_send_sms_res['response']['message'];
                                $bookingpress_return_res['return_type'] = 'error';
                                $bookingpress_return_res['return_res'] = __('SMS not sent successfuly due to error code','bookingpress-sms').' '.$error_code.' '.$error_msg;

                            } else {
                                $bookingpress_return_res['return_type'] = 'success';
                                $bookingpress_return_res['return_res'] = __('SMS Sent Successfully', 'bookingpress-sms');
                            }
                        }
                    }
                    
                }else if($bookingpress_selected_sms_gateway == "Alcodes"){

                    $bookingpress_alcodes_send_url = "https://www.alcodes.com/api/sms-compose";

                    $bookingpress_api_key = !empty($bookingpress_configured_options['alcodes_api_key']) ? $bookingpress_configured_options['alcodes_api_key'] : $BookingPress->bookingpress_get_settings('alcodes_api_key', 'notification_setting');

                    $bookingpress_sender_id = !empty($bookingpress_configured_options['alcodes_sender_id']) ? $bookingpress_configured_options['alcodes_sender_id'] : $BookingPress->bookingpress_get_settings('alcodes_sender_id', 'notification_setting');

                    $bookingpress_alcodes_params = array(
                        'timeout' => 5000,
                        'headers' => array(
                            'authorization' => 'api_key '.$bookingpress_api_key,
                        ),
                        'body' => json_encode(array(
                            'is_otp' => false,
                            'sender_id' => $bookingpress_sender_id,
                            'phoneNumbers' => $to_number,
                            'message' => $to_message,
                            'smsTypeId' => 3,
                        )),
                    );

                    $bookingpress_send_sms_res = wp_remote_post($bookingpress_alcodes_send_url, $bookingpress_alcodes_params);

                    /* need to confirm with azharsir */
                    $bookingpress_appointment_data = $wpdb->get_row($wpdb->prepare("SELECT bookingpress_customer_phone_dial_code FROM {$tbl_bookingpress_appointment_bookings} WHERE bookingpress_appointment_booking_id = %d", $appointment_id), ARRAY_A);

                    $to_number = str_replace('+'.$bookingpress_appointment_data['bookingpress_customer_phone_dial_code'].'','',$to_number);

                    do_action('bookingpress_integration_log_entry', 'sms_debug_logs', '', 'SMS Addon', 'Alcodes Gateway send data', $bookingpress_alcodes_params, $bookingpress_debug_integration_log_id);

                    do_action('bookingpress_integration_log_entry', 'sms_debug_logs', '', 'SMS Addon', 'Alcodes Gateway received response', $bookingpress_send_sms_res, $bookingpress_debug_integration_log_id);

                    if(is_wp_error($bookingpress_send_sms_res)){
                        $bookingpress_return_res['return_res'] = $bookingpress_send_sms_res->get_error_message();
                    }else{
                        if( !empty( $bookingpress_send_sms_res)){
                            if( !empty( $bookingpress_send_sms_res['body'])){
                                $bookingpress_sms_response = json_decode($bookingpress_send_sms_res['body']);
                                if( isset( $bookingpress_sms_response->status ) && !empty( $bookingpress_sms_response->status ) && $bookingpress_sms_response->status == 'error'  ){
                                     $error_message = $bookingpress_sms_response->error;
                                    $bookingpress_return_res['return_type'] = 'error';  
                                    $bookingpress_return_res['return_res'] = __('SMS not send successfully due to error','bookingpress-sms').' '.$error_message;
                                } else {

                                    $bookingpress_return_res['return_type'] = 'success';
                                    $bookingpress_return_res['return_res'] = __('SMS Sent Successfully', 'bookingpress-sms');
                                }

                            }
                        }
                    }
                    
                }else if($bookingpress_selected_sms_gateway == "Movile"){

                    $bookingpress_movile_send_url = "https://api-messaging.movile.com/v1/send-sms";

                    $bookingpress_api_key = !empty($bookingpress_configured_options['movile_api_key']) ? $bookingpress_configured_options['movile_api_key'] : $BookingPress->bookingpress_get_settings('movile_api_key', 'notification_setting');

                    $bookingpress_username = !empty($bookingpress_configured_options['movile_username']) ? $bookingpress_configured_options['movile_username'] : $BookingPress->bookingpress_get_settings('movile_username', 'notification_setting');

                    $bookingpress_movile_params = array(
                        'timeout' => 5000,
                        'headers' => array(
                            'authenticationtoken' => $bookingpress_api_key,
                            'username' => $bookingpress_username,
                        ),
                        'body' => json_encode(array(
                            'destination' => $to_number,
                            'messageText' => $to_message,
                        )),
                    );

                    $bookingpress_send_sms_res = wp_remote_post($bookingpress_movile_send_url, $bookingpress_movile_params);

                    do_action('bookingpress_integration_log_entry', 'sms_debug_logs', '', 'SMS Addon', 'Movile Gateway send data', $bookingpress_movile_params, $bookingpress_debug_integration_log_id);

                    do_action('bookingpress_integration_log_entry', 'sms_debug_logs', '', 'SMS Addon', 'Movile Gateway received response', $bookingpress_send_sms_res, $bookingpress_debug_integration_log_id);

                    if(!is_wp_error($bookingpress_send_sms_res)){
                        $bookingpress_return_res['return_type'] = 'success';
                        $bookingpress_return_res['return_res'] = __('SMS Sent Successfully', 'bookingpress-sms');
                    }else{
                        $bookingpress_return_res['return_res'] = $bookingpress_send_sms_res->get_error_message();
                    }
                    
                }else if($bookingpress_selected_sms_gateway == "Twilio"){

                    $bookingpress_account_sid = !empty($bookingpress_configured_options['twilio_account_sid']) ? $bookingpress_configured_options['twilio_account_sid'] : $BookingPress->bookingpress_get_settings('twilio_account_sid', 'notification_setting');

                    $bookingpress_auth_token = !empty($bookingpress_configured_options['twilio_auth_token']) ? $bookingpress_configured_options['twilio_auth_token'] : $BookingPress->bookingpress_get_settings('twilio_auth_token', 'notification_setting');

                    $bookingpress_from_number = !empty($bookingpress_configured_options['twilio_from_number']) ? $bookingpress_configured_options['twilio_from_number'] : $BookingPress->bookingpress_get_settings('twilio_from_number', 'notification_setting');

                    $bookingpress_twilio_send_url = "https://api.twilio.com/2010-04-01/Accounts/".$bookingpress_account_sid."/Messages.json";

                    $bookingpress_twilio_params = array(
                        'timetout' => 4500,
                        'body' => array(
                            'From' => $bookingpress_from_number,
                            'To' => $to_number,
                            'Body' => $to_message
                        ),
                        'headers' => array(
                            'Content-Type' => 'application/x-www-form-urlencoded',
                            'Authorization' =>  'Basic '. base64_encode( $bookingpress_account_sid.':'.$bookingpress_auth_token )
                        )
                    );
                    $bookingpress_send_sms_res = wp_remote_post(
                        $bookingpress_twilio_send_url,
                        $bookingpress_twilio_params
                    );  
                    
                    do_action('bookingpress_integration_log_entry', 'sms_debug_logs', '', 'SMS Addon', 'Twilio Gateway send data', $bookingpress_twilio_params, $bookingpress_debug_integration_log_id);

                    do_action('bookingpress_integration_log_entry', 'sms_debug_logs', '', 'SMS Addon', 'Twilio Gateway received response', $bookingpress_send_sms_res, $bookingpress_debug_integration_log_id);
                    
                    if( !empty( $bookingpress_send_sms_res )){
                        
                        if( !empty($bookingpress_send_sms_res['response']) && ($bookingpress_send_sms_res['response']['code'] != 200 && $bookingpress_send_sms_res['response']['code'] != 201 ) ){

                            $send_sms_res = json_decode( $bookingpress_send_sms_res['body'] ,true );
                            $error_code = $send_sms_res['code'];
                            $error_code_msg = $send_sms_res['message'];
                            $bookingpress_return_res['return_type'] = 'error';
                            $bookingpress_return_res['return_res'] = __('SMS not sent successfully due to error','bookingpress-sms').' '.$error_code.' '. $error_code_msg;
                        } else {
                            $bookingpress_return_res['return_type'] = 'success';
                            $bookingpress_return_res['return_res'] = __('SMS Sent Successfully', 'bookingpress-sms');
                        }
                    }
                    
                }else if($bookingpress_selected_sms_gateway == "Telerivet"){

                    $bookingpress_api_key = !empty($bookingpress_configured_options['telerivet_api_key']) ? $bookingpress_configured_options['telerivet_api_key'] : $BookingPress->bookingpress_get_settings('telerivet_api_key', 'notification_setting');

                    $bookingpress_project_id = !empty($bookingpress_configured_options['telerivet_project_id']) ? $bookingpress_configured_options['telerivet_project_id'] : $BookingPress->bookingpress_get_settings('telerivet_project_id', 'notification_setting');

                    $bookingpress_telerivet_send_url = "https://api.telerivet.com/v1/projects/".$bookingpress_project_id."/messages/send";

                    $bookingpress_telerivet_params = array(
                        'timetout' => 4500,
                        'body' => array(
                            'project_id' => $bookingpress_project_id,
                            'to_number' => $to_number,
                            'content' => $to_message
                        ),
                        'headers' => array(
                            'Content-Type' => 'application/x-www-form-urlencoded',
                            'Authorization' =>  'Basic '. base64_encode( $bookingpress_api_key )
                        )
                    );
                    $bookingpress_send_sms_res = wp_remote_post(
                        $bookingpress_telerivet_send_url,
                        $bookingpress_telerivet_params
                    );

                    do_action('bookingpress_integration_log_entry', 'sms_debug_logs', '', 'SMS Addon', 'Telerivet Gateway send data', $bookingpress_telerivet_params, $bookingpress_debug_integration_log_id);

                    do_action('bookingpress_integration_log_entry', 'sms_debug_logs', '', 'SMS Addon', 'Telerivet Gateway received response', $bookingpress_send_sms_res, $bookingpress_debug_integration_log_id);

                    if( !empty( $bookingpress_send_sms_res )){
                        if( !empty($bookingpress_send_sms_res['response']) && $bookingpress_send_sms_res['response']['code'] != 200 ){

                            $bookingpress_body_res = json_decode( $bookingpress_send_sms_res['body'], true);
                            $error_code = $bookingpress_body_res['error']['code'];
                            $error_message = $bookingpress_body_res['error']['message'];

                            $bookingpress_return_res['return_type'] = 'error';
                            $bookingpress_return_res['return_res'] = __('SMS not sent successfully due to error','bookingpress-sms').' '.$error_code.' '.$error_message;

                        } else {

                            $bookingpress_return_res['return_type'] = 'success';
                            $bookingpress_return_res['return_res'] = __('SMS Sent Successfully', 'bookingpress-sms');
                        }
                    }

                    
                }else if($bookingpress_selected_sms_gateway == "ClickSend"){
                    
                    $bookingpress_clicksend_send_url = "https://rest.clicksend.com/v3/sms/send";

                    $bookingpress_username = !empty($bookingpress_configured_options['clicksend_username']) ? $bookingpress_configured_options['clicksend_username'] : $BookingPress->bookingpress_get_settings('clicksend_username', 'notification_setting');

                    $bookingpress_password = !empty($bookingpress_configured_options['clicksend_password']) ? $bookingpress_configured_options['clicksend_password'] : $BookingPress->bookingpress_get_settings('clicksend_password', 'notification_setting');

                    $bookingpress_pwd = $bookingpress_username.":".$bookingpress_password;

                    $bookingpress_sms_params = array(
                        'messages' => array(
                            array(
                                'source' => 'bookingpress',
                                'body' => $to_message,
                                'to' => $to_number
                            )
                        )
                    );

                    $bookingpress_clicksend_params = json_encode( $bookingpress_sms_params );

                    $bookingpress_sms_send = wp_remote_post(
                        $bookingpress_clicksend_send_url,
                        array(
                            'timeout' => 4500,
                            'headers' => array(
                                'Authorization' => 'Basic '. base64_encode( $bookingpress_pwd ),
                                'Content-Type' => 'application/json'
                            ),
                            'body' => $bookingpress_clicksend_params
                        )
                    );

                    $bookingpress_send_sms_res = wp_remote_retrieve_body( $bookingpress_sms_send );

                    do_action('bookingpress_integration_log_entry', 'sms_debug_logs', '', 'SMS Addon', 'ClickSend Gateway send data', $bookingpress_clicksend_params, $bookingpress_debug_integration_log_id);

                    do_action('bookingpress_integration_log_entry', 'sms_debug_logs', '', 'SMS Addon', 'ClickSend Gateway received response', $bookingpress_send_sms_res, $bookingpress_debug_integration_log_id);

                    if( !empty( $bookingpress_send_sms_res ) ){

                        $bookingpress_send_sms_res = json_decode($bookingpress_send_sms_res,true); 

                        if( !empty($bookingpress_send_sms_res['http_code']) && $bookingpress_send_sms_res['http_code'] != 200 ){
                            $error_code = $bookingpress_send_sms_res['http_code'];
                            $error_message = $bookingpress_send_sms_res['response_msg'];

                            $bookingpress_return_res['return_type'] = 'error';
                            $bookingpress_return_res['return_res'] = __('SMS not sent successfully due to error','bookingpress-sms').' '.$error_code.' ' .$error_message;

                        } else {

                            $bookingpress_return_res['return_type'] = 'success';
                            $bookingpress_return_res['return_res'] = __('SMS Sent Successfully', 'bookingpress-sms');
                        }
                    }

                }
            }

            return $bookingpress_return_res;
        }

        function bookingpress_send_test_sms_func(){
            global $wpdb, $BookingPress;
			$response              = array();
			
            $bpa_check_authorization = $this->bpa_check_authentication( 'bpa_send_test_sms', true, 'bpa_wp_nonce' );
            
            if( preg_match( '/error/', $bpa_check_authorization ) ){
                $bpa_auth_error = explode( '^|^', $bpa_check_authorization );
                $bpa_error_msg = !empty( $bpa_auth_error[1] ) ? $bpa_auth_error[1] : esc_html__( 'Sorry. Something went wrong while processing the request', 'bookingpress-sms');

                $response['variant'] = 'error';
                $response['title'] = esc_html__( 'Error', 'bookingpress-sms');
                $response['msg'] = $bpa_error_msg;

                wp_send_json( $response );
                die;
            }

            $bookingpress_to_number = !empty($_POST['bookingpress_test_to_number']) ? esc_html($_POST['bookingpress_test_to_number']) : ''; //phpcs:ignore
            $bookingpress_to_msg = !empty($_POST['bookingpress_test_message']) ? $_POST['bookingpress_test_message'] : ''; //phpcs:ignore

			$bookingpress_posted_fields_data = !empty($_POST['bookingpress_posted_fields_data']) ? array_map( array( $BookingPress, 'appointment_sanatize_field' ), $_POST['bookingpress_posted_fields_data'] ) : array(); // phpcs:ignore

            $bookingpress_send_sms_res = $this->bookingpress_send_sms_function($bookingpress_to_number, $bookingpress_to_msg, 1, 0, $bookingpress_posted_fields_data);

            echo json_encode($bookingpress_send_sms_res);
            exit;
        }

        function bookingpress_add_setting_dynamic_vue_methods_func(){
            ?>
                bookingpress_sms_gateway_change(){

                    const vm = this
                    if( vm.notification_setting_form.bookingpress_selected_sms_gateway == 'SMS API'){

                        if( "undefined" == typeof vm.notification_setting_form.bookingpress_selected_sms_api_endpoint || '' == vm.notification_setting_form.bookingpress_selected_sms_api_endpoint){
                            vm.notification_setting_form.bookingpress_selected_sms_api_endpoint = 'api.smsapi.com';
                            
                        }
                    }
                    if( vm.notification_setting_form.bookingpress_selected_sms_gateway == 'RingCaptcha'){

                        if( "undefined" == typeof vm.notification_setting_form.bookingpress_selected_ringcaptcha_locale || '' == vm.notification_setting_form.bookingpress_selected_ringcaptcha_locale){
                            vm.notification_setting_form.bookingpress_selected_ringcaptcha_locale = 'en';
                            
                        }
                    }

                },
                bookingpress_send_test_sms(test_sms_form){
                    const vm = this
                    vm.$refs[test_sms_form].validate((valid) => {
                        if(valid) {
                            vm.is_display_send_sms_loader = '1'
                            var postdata = {}
                            postdata.action = 'bookingpress_send_test_sms'
                            postdata.bookingpress_posted_fields_data = vm.notification_setting_form
                            postdata.bookingpress_test_to_number = vm.bookingpress_test_sms_form.test_to_number
                            postdata.bookingpress_test_message = vm.bookingpress_test_sms_form.test_to_msg
                            postdata._wpnonce = '<?php echo esc_html(wp_create_nonce( 'bpa_wp_nonce' )); ?>'
                            axios.post( appoint_ajax_obj.ajax_url, Qs.stringify( postdata ) )
                            .then( function (response) {
                                console.log(response.data);
                                vm.is_display_send_sms_loader = '0'
                                vm.bookingpress_sms_err_msg = ''
                                vm.bookingpress_sms_success_msg = ''
                                if(response.data.return_type != 'success'){
                                    vm.bookingpress_sms_err_msg = response.data.return_res
                                }else{
                                    vm.bookingpress_sms_success_msg = response.data.return_res
                                }
                            }.bind(this) )
                            .catch( function (error) {
                                console.log(error)
                            });
                        }
                    });
                },
            <?php
        }

        function bookingpress_email_notification_get_data_func(){
            ?>
            vm.bookingpress_sms_notification_msg = bookingpress_return_notification_data.bookingpress_sms_notification_message
            vm.bookingpress_send_sms_notification = bookingpress_return_notification_data.bookingpress_send_sms_notification
            vm.bookingpress_sms_admin_number = bookingpress_return_notification_data.bookingpress_sms_admin_number
            
            <?php
        }

        function bookingpress_get_notifiacation_data_filter_func($bookingpress_exist_record_data) {
            if(isset($bookingpress_exist_record_data['bookingpress_send_sms_notification']) ) {
                $bookingpress_exist_record_data['bookingpress_send_sms_notification'] = $bookingpress_exist_record_data['bookingpress_send_sms_notification'] == '1' ? true : false;
            }
            return $bookingpress_exist_record_data;
        }

        function bookingpress_save_email_notification_data_filter_func($bookingpress_database_modify_data, $posted_data){
            global $BookingPress, $bookingpress_global_options;
            if(!empty($posted_data['sms_notification_data'])){
                $bookingpress_options                  = $bookingpress_global_options->bookingpress_global_options();
                $bookingpress_allow_tag = json_decode( $bookingpress_options['allowed_html'], true );
                $bookingpress_sms_notification_msg = ! empty( $posted_data['sms_notification_data'] ) ? wp_kses( $posted_data['sms_notification_data'], $bookingpress_allow_tag ) : '';

                $bookingpress_database_modify_data['bookingpress_sms_notification_message'] = stripslashes_deep($bookingpress_sms_notification_msg);
            }     
            $bookingpress_database_modify_data['bookingpress_send_sms_notification'] = (!empty($posted_data['bookingpress_send_sms_notification']) && $posted_data['bookingpress_send_sms_notification'] == 'true') ? 1 : 0 ;
            
            $bookingpress_database_modify_data['bookingpress_sms_admin_number'] = (!empty($posted_data['bookingpress_sms_admin_number'])) ? $posted_data['bookingpress_sms_admin_number'] : "" ;

            return $bookingpress_database_modify_data;
        }

        function bookingpress_add_email_notification_data_func(){
            ?>
            bookingpress_save_notification_data.sms_notification_data = vm.bookingpress_sms_notification_msg
            bookingpress_save_notification_data.bookingpress_send_sms_notification = vm.bookingpress_send_sms_notification
            bookingpress_save_notification_data.bookingpress_sms_admin_number = vm.bookingpress_sms_admin_number
            <?php
        }

        function bookingpress_add_dynamic_notification_data_fields_func($bookingpress_notification_vue_methods_data){
            $bookingpress_notification_vue_methods_data['bookingpress_sms_notification_msg']  = '';
            $bookingpress_notification_vue_methods_data['bookingpress_send_sms_notification'] = 0;            
            $bookingpress_notification_vue_methods_data['bookingpress_sms_admin_number'] = '';            
            return $bookingpress_notification_vue_methods_data;
        }

        function bookingpress_add_email_notification_section_func(){
            ?>  
            <el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
                <el-form-item>
                    <div class="bpa-en-status--swtich-row">
                        <label class="bpa-form-label"><?php esc_html_e( 'Send SMS Notification', 'bookingpress-sms' ); ?></label>
                        <el-switch class="bpa-swtich-control" v-model="bookingpress_send_sms_notification"></el-switch>
                    </div>
                </el-form-item>
            </el-col>

            <el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24" v-if="bookingpress_active_email_notification != 'share_appointment' && (bookingpress_send_sms_notification == true && activeTabName == 'employee')"> 
                <el-form-item>
                    <template #label>
                        <span class="bpa-form-label"><?php esc_html_e('Enter phone number ( With Contry code ) to send SMS to extra recipient', 'bookingpress-sms'); ?></span>
                    </template>
                    <el-input class="bpa-form-control" v-model="bookingpress_sms_admin_number"></el-input>
                </el-form-item>
            </el-col>

            <el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24" v-if="bookingpress_send_sms_notification == true">
                <el-form-item>
                    <template #label>
                        <span class="bpa-form-label"><?php esc_html_e( 'SMS Notification Message', 'bookingpress-sms' ); ?></span>
                    </template>
                    <el-input class="bpa-form-control" id="bookingpress_sms_notification" :rows="3" v-model="bookingpress_sms_notification_msg" type="textarea"></el-input>
                </el-form-item>												
            </el-col>
            <?php
        }

        function bookingpress_sms_api_endpoint(){
            $bookingpress_sms_api_endpoint_list = array(
                "default_sms_api" => array(
                    "name" => 'Default (https://api.smsapi.com)',
                    "endpointurl"  => "api.smsapi.com",
                ),
                "polish" => array(
                    "name" => 'Polish (https://api.smsapi.pl)',
                    "endpointurl"  => "api.smsapi.pl",
                ),
                "bulgarian" => array(
                    "name" => 'Bulgarian (https://api.smsapi.bg)',
                    "endpointurl"  => "api.smsapi.bg",
                ),
                "swedish" => array(
                    "name" => 'Swedish (https://api.smsapi.se)',
                    "endpointurl"  => "api.smsapi.se",
                ),
            );
            return $bookingpress_sms_api_endpoint_list;
        }

        function bookingpress_sms_ringcaptcha_locale(){
            $bookingpress_ringcaptcha_sms_locale = array(

                "default_locale" => array(
                    "name" => "en (US English)",
                    "ringcaptchalocale" => "en",
                ),
                "german_locale" => array(
                    "name" => "de (German)",
                    "ringcaptchalocale" => "de",
                ),
                "arabic_locale" => array(
                    "name" => "ar (Arabic)",
                    "ringcaptchalocale" => "ar",
                ),
                "english_uk_locale" => array(
                    "name" => "en_gb (UK English)",
                    "ringcaptchalocale" => "en_gb",
                ),
                "spanish_locale" => array(
                    "name" => "es (Spanish)",
                    "ringcaptchalocale" => "es",
                ),
                "finnish_locale" => array(
                    "name" => "fi (Finnish)",
                    "ringcaptchalocale" => "fi",
                ),
                "french_locale" => array(
                    "name" => "fr (French)",
                    "ringcaptchalocale" => "fr",
                ),
                "greek_locale" => array(
                    "name" => "gr (Greek)",
                    "ringcaptchalocale" => "gr",
                ),
                "italian_locale" => array(
                    "name" => "it (Italian)",
                    "ringcaptchalocale" => "it",
                ),
                "Japanese_locale" => array(
                    "name" => "ja (Japanese)",
                    "ringcaptchalocale" => "ja",
                ),
                "dutch_locale"=> array(
                    "name" => "nl (Dutch)",
                    "ringcaptchalocale" => "nl",
                ),
                "portuguess_locale" => array(
                    "name" => "pt (Portuguese)",
                    "ringcaptchalocale" => "pt",
                ),
                "russian_locale" => array(
                    "name" => "ru (Russian)",
                    "ringcaptchalocale" => "ru",
                ),
                "swedish_locale" => array(
                    "name" => "sv (Swedish)",
                    "ringcaptchalocale" => "sv",
                ),
                "turkish_locale" => array( 
                    "name" => "tr (Turkish)",
                    "ringcaptchalocale" => "tr",
                ),
                "chinese_locale" => array(
                    "name" => "zh (Chinese)",
                    "ringcaptchalocale" => "zh",
                ),

            );
            return $bookingpress_ringcaptcha_sms_locale;
        }

        function bookingpress_sms_gateway_list(){
            $bookingpress_sms_gateway_list = array(
                /******** get ********/
                "clickatell"  => array(
                    "name"    => __("Clickatell", 'bookingpress-sms'),
                    "url"     => "https://platform.clickatell.com/messages/http/send",
                    "group"   => "get",
                ),
                "redoxygen"  => array(
                    "name"    => __("Redoxygen", 'bookingpress-sms'),
                    "url"     => "http://sms1.redoxygen.net/sms.dll?Action=SendSMS",
                    "group"   => "get",
                ),
                "1s2u"  => array(
                    "name"    => __("1s2u", 'bookingpress-sms'),
                    "url"     => "https://api.1s2u.io/bulksms",
                    "group"   => "get",
                ),
                "experttexting"  => array(
                    "name"    => __("Experttexting", 'bookingpress-sms'),
                    "url"     => "https://www.experttexting.com/ExptRestApi/sms/json/Message/Send",
                    "group"   => "get",
                ),
                "bearsms"  => array(
                    "name"    => __("BearSMS", 'bookingpress-sms'),
                    "url"     => "http://app.bearsms.com/index.php?app=ws&op=pv",
                    "group"   => "get",
                ),
                "spirius"  => array(
                    "name"    => __("Spirius", 'bookingpress-sms'),
                    "url"     => "https://get.spiricom.spirius.com:55001/cgi-bin/sendsms",
                    "group"   => "get",
                ),
                "d7sms"  => array(
                    "name"    => __("D7 SMS", 'bookingpress-sms'),
                    "url"     => "http://smsc.d7networks.com:1401/send",
                    "group"   => "get",
                ),
                /******** post ********/
                "eztexting"  => array(
                    "name"    => __("EZTexting", 'bookingpress-sms'),
                    "url"     => "https://app.eztexting.com/sending/messages?format={BOOKINGPRESS_SMS_FORMAT}",
                    "data_type" => "json",
                    "group"   => "post",
                ),
                "ringcaptcha"  => array(
                    "name"    => __("RingCaptcha", 'bookingpress-sms'),
                    "url"     => "https://api.ringcaptcha.com/{BOOKINGPRESS_APP_KEY}/sms",
                    "group"   => "post",
                ),
                "bulksms"  => array(
                    "name"    => __("Bulksms", 'bookingpress-sms'),
                    "url"     => "https://api.bulksms.com/v1/messages",
                    "group"   => "post",
                    "data_type" => "json",
                    "content_type" => "application/json",
                    "auth_type"    => "Bearer",
                ),
                "exotel"  => array(
                    "name"    => __("Exotel", 'bookingpress-sms'),
                    "url"     => "https://{BOOKINGPRESS_APP_KEY}:{BOOKINGPRESS_API_TOKEN}{BOOKINGPRESS_SUB_DOMAIN}/v1/Accounts/{BOOKINGPRESS_ACCOUNT_SID}/Sms/send",
                    "group"   => "post",
                ),
                /******** get_post ********/
                "text_local"  => array(
                    "name"    => __("Text Local", 'bookingpress-sms'),
                    "url"     => "https://api.textlocal.in/send",
                    "group"   => "get_post",
                ),
                "nexmo"  => array(
                    "name"    => __("Nexmo", 'bookingpress-sms'),
                    "url"     => "https://rest.nexmo.com/sms/{BOOKINGPRESS_SMS_FORMAT}",
                    "group"   => "get_post",
                    "data_type" => "json",
                ),
                "msg91"  => array(
                    "name"    => __("MSG91", 'bookingpress-sms'),
                    "url"     => "http://api.msg91.com/api/sendhttp.php",
                    "group"   => "get_post",
                ),
                "textbelt"  => array(
                    "name"    => __("Textbelt", 'bookingpress-sms'),
                    "url"     => "https://textbelt.com/text",
                    "group"   => "get_post",
                ),
                "vianett"  => array(
                    "name"    => __("Vianett", 'bookingpress-sms'),
                    "url"     => "https://smsc.vianett.no/v3/send",
                    "group"   => "get_post",
                ),
                "smsglobal"  => array(
                    "name"    => __("SMS Global", 'bookingpress-sms'),
                    "url"     => "https://api.smsglobal.com/http-api.php",
                    "group"   => "get_post",
                ),
                "textmarketer"  => array(
                    "name"    => __("Text Marketer", 'bookingpress-sms'),
                    "url"     => "https://api.textmarketer.co.uk/gateway",
                    "group"   => "get_post",
                ),
                "swiftsmsgateway"  => array(
                    "name"      => __("Swift SMS Gateway", 'bookingpress-sms'),
                    "url"       => "http://smsgateway.ca/services/message.svc/{BOOKINGPRESS_ACCOUNT_KEY}/{BOOKINGPRESS_DESTINATION}",
                    "group"     => "get_post",
                    "data_type" => "json",
                ),
                "msgclub"  => array(
                    "name"    => __("MSG Club", 'bookingpress-sms'),
                    "url"     => "http://msg.msgclub.net/rest/services/sendSMS/sendGroupSms",
                    "group"   => "get_post",
                ),
                "easysendsms"  => array(
                    "name"    => __("Easy Send Sms", 'bookingpress-sms'),
                    "url"     => "https://www.easysendsms.com/sms/bulksms-api/bulksms-api",
                    "group"   => "get_post",
                ),
                "cheapglobalsms"  => array(
                    "name"    => __("Cheap Global SMS", 'bookingpress-sms'),
                    "url"     => "http://cheapglobalsms.com/api_v1?action=send_sms",
                    "group"   => "get_post",
                ),
                "messente"  => array(
                    "name"         => __("Messente", 'bookingpress-sms'),
                    "url"          => "https://api2.messente.com/send_sms",
                    "group"        => "get_post",
                ),
                /******** post_header ********/
                "messagebird"   => array(
                    "name"         => __("Message Bird", 'bookingpress-sms'),
                    "url"          => "https://rest.messagebird.com/messages",
                    "group"        => "post_header",
                    "auth_key"     => "Authorization",
                    "auth_type"    => "AccessKey",
                    "encode"       => 0,
                    "header"       => array(
                        "account_sid" => array(
                            "label"            => __("Authorization Key", 'bookingpress-sms'),
                            "slug"             => "authorization",
                            "show_in_settings" => 1,
                            "required"         => 1,
                            "id"               => "bookingpress_authorization",
                            "type"             => "text",
                            "empty_msg"        => __("Authorization key can not be left blank", 'bookingpress-sms'),
                        )
                    ),
                ),
                "telnyx"  => array(
                    "name"    => __("Telnyx", 'bookingpress-sms'),
                    "url"     => "https://sms.telnyx.com/messages",
                    "group"   => "post_header",
                    "auth_key"     => "x-profile-secret",
                    "auth_type"    => "",
                    "encode"       => 0,
                    "header"  => array(
                        "account_sid" => array(
                            "label"            => __("Profile Secret", 'bookingpress-sms'),
                        "slug"             => "x-profile-secret",
                            "show_in_settings" => 1,
                            "required"         => 1,
                            "id"               => "bookingpress_profile-secret",
                            "type"             => "text",
                            "empty_msg"        => __("Profile Secret can not be left blank", 'bookingpress-sms'),
                        )
                    ),
                ),
                "routee"  => array(
                    "name"      => __("Routee", 'bookingpress-sms'),
                    "url"       => "https://connect.routee.net/sms",
                    "group"     => "post_header",
                    "data_type" => "json",
                    "auth_key"  => "authorization",
                    "auth_type" => "Bearer",
                    "encode"    => 0,
                    "header"    => array(
                        "account_sid" => array(
                            "label"            => __("Authorization", 'bookingpress-sms'),
                        "slug"             => "authorization",
                            "show_in_settings" => 1,
                            "required"         => 1,
                            "id"               => "bookingpress_authorization",
                            "type"             => "text",
                            "empty_msg"        => __("Authorization can not be left blank", 'bookingpress-sms'),
                        )
                    ),
                ),
                "wavecell"  => array(
                    "name"         => __("Wavecell", 'bookingpress-sms'),
                    "url"          => "https://api.wavecell.com/sms/v1/{BOOKINGPRESS_SUBACCOUNT_ID}/single",
                    "group"        => "post_header",
                    "data_type"    => "json",
                    "content_type" => "application/json",
                    "auth_key"     => "authorization",
                    "auth_type"    => "Bearer",
                    "encode"       => 0,
                    "header"       => array(
                        "account_sid" => array(
                            "label"            => __("Authorization", 'bookingpress-sms'),
                        "slug"             => "authorization",
                            "show_in_settings" => 1,
                            "required"         => 1,
                            "id"               => "bookingpress_authorization",
                            "type"             => "text",
                            "empty_msg"        => __("Authorization can not be left blank", 'bookingpress-sms'),
                        )
                    ),
                ),
                "engagespark"  => array(
                    "name"      => __("EngageSPARK", 'bookingpress-sms'),
                    "url"       => "https://api.engagespark.com/v1/sms/phonenumber",
                    "group"     => "post_header",
                    "data_type" => "json",
                    "auth_key"  => "Authorization",
                    "auth_type" => "Token",
                    "encode"    => 0,
                    "header"    => array(
                        "account_sid" => array(
                            "label"            => __("Authorization", 'bookingpress-sms'),
                            "slug"             => "Authorization",
                            "show_in_settings" => 1,
                            "required"         => 1,
                            "id"               => "bookingpress_authorization",
                            "type"             => "text",
                            "empty_msg"        => __("Authorization can not be left blank", 'bookingpress-sms'),
                        )
                    ),
                ),
                /******** get_post_header ********/
                "smsgatewaycenter"  => array(
                    "name"      => __("SMS Gateway Center", 'bookingpress-sms'),
                    "url"       => "http://enterprise.smsgatewaycenter.com/SMSApi/rest/send",
                    "group"     => "get_post_header",
                    "auth_key"  => "apiKey",
                    "auth_type" => "",
                    "encode"    => 0,
                    "header"    => array(
                        "account_sid" => array(
                            "label"            => __("API Key", 'bookingpress-sms'),
                            "slug"             => "apiKey",
                            "show_in_settings" => 1,
                            "required"         => 1,
                            "id"               => "bookingpress_apikey",
                            "type"             => "text",
                            "empty_msg"        => __("API Key can not be left blank", 'bookingpress-sms'),
                        )
                    ),
                ),
                "smsapi"  => array(
                    "name"      => __("SMS API", 'bookingpress-sms'),
                    "url"       => "https://api.smsapi.com/sms.do",
                    "group"     => "get_post_header",
                    "auth_key"  => "Authorization",
                    "auth_type" => "Bearer",
                    "data_type" => "json",
                    "encode"    => 0,
                    "header"    => array(
                        "account_sid" => array(
                            "label"            => __("Access Token", 'bookingpress-sms'),
                            "slug"             => "access_token",
                            "show_in_settings" => 1,
                            "required"         => 1,
                            "id"               => "bookingpress_apikey",
                            "type"             => "text",
                            "empty_msg"        => __("API Key can not be left blank", 'bookingpress-sms'),
                        )
                    ),
                ),
                "fast2sms"  => array(
                    "name"      => __("Fast 2 SMS", 'bookingpress-sms'),
                    "url"       => "https://www.fast2sms.com/dev/bulk",
                    "group"     => "get_post_header",
                    "auth_key"  => "authorization",
                    "auth_type" => "",
                    //"data_type" => "json",
                    "encode"    => 0,
                    "header"    => array(
                        "account_sid" => array(
                            "label"            => __("API Key", 'bookingpress-sms'),
                            "slug"             => "api_key",
                            "show_in_settings" => 1,
                            "required"         => 1,
                            "id"               => "bookingpress_api_key",
                            "type"             => "text",
                            "empty_msg"        => __("API Key can not be left blank", 'bookingpress-sms'),
                        )
                    ),
                ),
                "alcodes"  => array(
                    "name"         => __("Alcodes", 'bookingpress-sms'),
                    "url"          => "https://www.alcodes.com/api/sms-compose",
                    "group"        => "get_post_header",
                    "auth_key"     => "authorization",
                    "auth_type"    => "",
                    "data_type"    => "json",
                    "encode"       => 0,
                    "header"       => array(
                        "account_sid" => array(
                            "label"            => __("API Key", 'bookingpress-sms'),
                            "slug"             => "api_key",
                            "show_in_settings" => 1,
                            "required"         => 1,
                            "id"               => "bookingpress_api_key",
                            "type"             => "text",
                            "empty_msg"        => __("API Key can not be left blank", 'bookingpress-sms'),
                        )
                    ),
                ),
                "movile"  => array(
                    "name"         => __("Movile", 'bookingpress-sms'),
                    "url"          => "https://api-messaging.movile.com/v1/send-sms",
                    "group"        => "post_header_user",
                    "content_type" => "application/json",
                    "data_type"    => "json",
                    "header"       => array(
                        "authenticationtoken" => array(
                            "label"            => __("API Key", 'bookingpress-sms'),
                            "slug"             => "authenticationtoken",
                            "show_in_settings" => 1,
                            "required"         => 1,
                            "id"               => "bookingpress_api_key",
                            "type"             => "text",
                            "empty_msg"        => __("API Key can not be left blank", 'bookingpress-sms'),
                        ),
                        "username" => array(
                            "label"            => __("Username", 'bookingpress-sms'),
                            "slug"             => "username",
                            "show_in_settings" => 1,
                            "required"         => 1,
                            "id"               => "bookingpress_username",
                            "type"             => "text",
                            "empty_msg"        => __("Username can not be left blank", 'bookingpress-sms'),
                        ),
                    ),
                ),
                /******** Usdpwd ********/
                "twilio"  => array(
                    "name"    => __("Twilio", 'bookingpress-sms'),
                    "url"     => "https://api.twilio.com/2010-04-01/Accounts/{BOOKINGPRESS_ACCOUNT_NUMNER}/Messages.json",
                    "group"   => "post_usdpwd",
                    "content_type" => "application/x-www-form-urlencoded",
                    "header"  => array(
                        "account_sid" => array(
                            "label"            => __("Account SID", 'bookingpress-sms'),
                            "slug"             => "account_sid",
                            "show_in_settings" => 1,
                            "required"         => 1,
                            "id"               => "bookingpress_account_sid",
                            "type"             => "text",
                            "empty_msg"        => __("Account SID can not be left blank", 'bookingpress-sms'),
                        ),
                        "auth_token" => array(
                            "label"            => __("Auth Token", 'bookingpress-sms'),
                            "slug"             => "auth_token",
                            "show_in_settings" => 1,
                            "required"         => 0,
                            "id"               => "bookingpress_auth_token",
                            "type"             => "text",
                        )
                    ),
                ),
                "telerivet"  => array(
                    "name"    => __("Telerivet", 'bookingpress-sms'),
                    "url"     => "https://api.telerivet.com/v1/projects/{BOOKINGPRESS_PROJECT_ID}/messages/send",
                    "group"   => "post_usdpwd",
                    "data_type" => "json",
                    "content_type" => "application/json",
                    "header"  => array(
                        "account_sid" => array(
                            "label"            => __("API Key", 'bookingpress-sms'),
                            "slug"             => "api_key",
                            "show_in_settings" => 1,
                            "required"         => 1,
                            "id"               => "bookingpress_api_key",
                            "type"             => "text",
                            "empty_msg"        => __("API Key can not be left blank", 'bookingpress-sms'),
                        )
                    ),
                ),
                "clicksend"  => array(
                    "name"         => __("ClickSend", 'bookingpress-sms'),
                    "url"          => "https://rest.clicksend.com/v3/sms/send",
                    "group"        => "post_usdpwd",
                    "data_type"    => "json",
                    "content_type" => "application/json",
                    "header"       => array(
                        "account_sid" => array(
                            "label"            => __("Username", 'bookingpress-sms'),
                            "slug"             => "account_sid",
                            "show_in_settings" => 1,
                            "required"         => 1,
                            "id"               => "bookingpress_account_sid",
                            "type"             => "text",
                            "empty_msg"        => __("Username can not be left blank", 'bookingpress-sms'),
                        ),
                        "auth_token" => array(
                            "label"            => __("Password", 'bookingpress-sms'),
                            "slug"             => "auth_token",
                            "show_in_settings" => 1,
                            "required"         => 0,
                            "id"               => "bookingpress_auth_token",
                            "type"             => "text",
                        )
                    )
                ),
            );
            return $bookingpress_sms_gateway_list;
        }

        function sms_routee_func( $bookingpress_routee_application_id, $bookingpress_routee_application_secret ){

            global $BookingPress;

            $bookingpress_routee_send_auth_url = "https://auth.routee.net/oauth/token";

            $bookingpress_routee_access_token_params = array(
                'timeout' => 5000,
                'headers' => array(
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Authorization' => 'Basic '. base64_encode("$bookingpress_routee_application_id:$bookingpress_routee_application_secret"),
                ),
                'body' => array(
                    'grant_type' => 'client_credentials',
                ),
            );

            $bookingpress_sms_response_token = wp_remote_post($bookingpress_routee_send_auth_url, $bookingpress_routee_access_token_params);

            if(is_wp_error($bookingpress_sms_response_token)){
        
                $bookingpress_return_res['return_res'] = $bookingpress_send_sms_res->get_error_message();
            } else {
                if( !empty($bookingpress_sms_response_token)){

                    $bookingpress_sms_response_token = !empty( $bookingpress_sms_response_token['body'] ) ? json_decode( $bookingpress_sms_response_token['body']) : array();
                    $bookingpress_routee_access_token = $bookingpress_sms_response_token->access_token;
                    $bookingpress_routee_access_expire_time = $bookingpress_sms_response_token->expires_in;

                    if(!empty($bookingpress_routee_access_token)){
                        $BookingPress->bookingpress_update_settings('bookingpress_routee_sms_token', 'notification_setting', $bookingpress_routee_access_token);
                    } 
                    
                    if( !empty($bookingpress_routee_access_expire_time)){   
                        
                        $bookingpress_routee_access_expire_and_current_time =  $bookingpress_routee_access_expire_time + current_time('timestamp');
                        $BookingPress->bookingpress_update_settings('bookingpress_routee_sms_token_expire_time', 'notification_setting', $bookingpress_routee_access_expire_and_current_time );
                    }
                }
            }


            return $bookingpress_routee_access_token;
        }

        function bookingpress_get_appointment_advanced_field_data() {
            global $wpdb,$tbl_bookingpress_form_fields;
			$bookingpress_field_list_data = $wpdb->get_results( $wpdb->prepare( 'SELECT bookingpress_field_label,bookingpress_field_meta_key,bookingpress_field_is_default,bookingpress_form_field_name FROM ' . $tbl_bookingpress_form_fields . ' WHERE bookingpress_is_customer_field = %d AND ( bookingpress_field_type = %s OR bookingpress_field_type = %s ) order by bookingpress_form_field_id',0,'text','phone'), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared --Reason $tbl_bookingpress_form_fields is a table name. false alarm.            
            $bookingpress_field_final_list_data = array();
			if ( ! empty( $bookingpress_field_list_data ) ) {				
				foreach ( $bookingpress_field_list_data as $field_data ) {
                    $bookingpress_field_final_list_data[] = array(
                        'bookingpress_field_label' => $field_data['bookingpress_field_label'],
                        'bookingpress_field_meta_key' => $field_data['bookingpress_field_meta_key'],
                    );
				}
			}
			return $bookingpress_field_final_list_data;            
        }

        function bookingpress_add_setting_dynamic_data_fields_func($bookingpress_settings_vue_data_fields){
            global $BookingPress, $bookingpress_customize;

            $bookingpress_settings_vue_data_fields['bookingpress_sms_gateways'] = $this->bookingpress_sms_gateway_list();
            $bookingpress_settings_vue_data_fields['notification_setting_form']['bookingpress_selected_sms_gateway'] = 'select_sms_gateway';

            $bookingpress_settings_vue_data_fields['bookingpress_sms_api_endpoint'] = $this->bookingpress_sms_api_endpoint();
            $bookingpress_settings_vue_data_fields['notification_setting_form']['bookingpress_selected_sms_api_endpoint'] = 'Default (https://api.smsapi.com)';
            
            $bookingpress_settings_vue_data_fields['bookingpress_sms_ringcaptcha_locale'] = $this->bookingpress_sms_ringcaptcha_locale();
            $bookingpress_settings_vue_data_fields['notification_setting_form']['bookingpress_selected_ringcaptcha_locale'] = 'en (US English)';

            $bookingpress_settings_vue_data_fields['notification_setting_form']['clickatell_api_key'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['clickatell_api_id'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['redoxygen_account_id'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['redoxygen_email'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['redoxygen_password'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['sms_1s2u_username'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['sms_1s2u_password'] = '';            
            $bookingpress_settings_vue_data_fields['notification_setting_form']['experttexting_username'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['experttexting_password'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['experttexting_api_key'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['experttexting_sender_id'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['bearsms_username'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['bearsms_password'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['bearsms_sender_id'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['spirius_username'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['spirius_password'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['spirius_from_number'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['d7sms_username'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['d7sms_password'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['d7sms_sender_name'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['eztexting_user'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['eztexting_password'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['ringcaptcha_app_key'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['ringcaptcha_api_key'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['bulksms_username'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['bulksms_password'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['exotel_api_key'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['exotel_api_token'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['exotel_sub_domain'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['exotel_account_sid'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['exotel_sender_id'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['textlocal_api_key'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['textlocal_sender_name'] = '';            
            $bookingpress_settings_vue_data_fields['notification_setting_form']['nexmo_api_key'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['nexmo_api_secret'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['nexmo_sender_id'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['msg91_auth_key'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['msg91_sender_id'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['textbelt_key'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['vianett_username'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['vianett_password'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['smsglobal_user'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['smsglobal_password'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['smsglobal_sender_id'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['textmarketer_username'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['textmarketer_password'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['textmarketer_originator'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['swiftsms_account_key'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['msgclub_auth_key'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['msgclub_sender_id'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['easysendsms_username'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['easysendsms_password'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['easysendsms_sender_name'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['cheapglobalsms_account_no'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['cheapglobalsms_account_password'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['cheapglobalsms_sender_id'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['messente_username'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['messente_password'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['messente_sender_name'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['messagebird_authorization_key'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['messagebird_originator'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['telnyx_profile_secret'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['telnyx_from_number'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['routee_authorization'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['routee_sender_name'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['wavecell_authorization'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['wavecell_sub_account_id'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['wavecell_source'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['engagespark_authorization'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['engagespark_orgid'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['engagespark_senderid'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['smsgatewaycenter_api_key'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['smsgatewaycenter_user_id'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['smsgatewaycenter_password'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['smsgatewaycenter_sender_id'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['smsapi_access_token'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['smsapi_from_number'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['fast2sms_api_key'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['fast2sms_sender_id'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['alcodes_api_key'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['alcodes_sender_id'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['movile_api_key'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['movile_username'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['twilio_account_sid'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['twilio_auth_token'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['twilio_from_number'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['telerivet_api_key'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['telerivet_project_id'] = '';            
            $bookingpress_settings_vue_data_fields['notification_setting_form']['clicksend_username'] = '';
            $bookingpress_settings_vue_data_fields['notification_setting_form']['clicksend_password'] = '';            

            $bookingpress_form_fields_arr = $this->bookingpress_get_appointment_advanced_field_data();
            $bookingpress_settings_vue_data_fields['bookingpress_form_fields_data'] = $bookingpress_form_fields_arr;
            $bookingpress_settings_vue_data_fields['notification_setting_form']['bookingpress_selected_phone_number_field'] = '';

            $bookingpress_settings_vue_data_fields['is_display_send_sms_loader'] = '0';

            $bookingpress_settings_vue_data_fields['bookingpress_test_sms_form'] = array(
                'test_to_number' => '',
                'test_to_msg' => '',
            );

            $bookingpress_settings_vue_data_fields['bookingpress_test_sms_rules'] = array(
                'test_to_number' => array(
                    array( 'required' => true, 'message' => esc_html__('This field is required', 'bookingpress-sms'), 'trigger' => 'blur'  ),
                ),
                'test_to_msg' => array(
                    array( 'required' => true, 'message' => esc_html__('This field is required', 'bookingpress-sms'), 'trigger' => 'blur'  ),
                ),
            );

            $bookingpress_settings_vue_data_fields['bookingpress_sms_err_msg'] = '';
            $bookingpress_settings_vue_data_fields['bookingpress_sms_success_msg'] = '';

            $bookingpress_settings_vue_data_fields['debug_log_setting_form']['sms_debug_logs'] = false;

            return $bookingpress_settings_vue_data_fields;
        }

        function bookingpress_add_notification_settings_section_func(){
            require(BOOKINGPRESS_SMS_DIR.'/core/views/bookingpress_sms_settings.php');
        }

        public static function install(){
			global $wpdb, $BookingPress, $bookingpress_sms_version, $tbl_bookingpress_notifications, $tbl_bookingpress_form_fields;
            $bookingpress_sms_addon_version = get_option('bookingpress_sms_gateway');
            if (!isset($bookingpress_sms_addon_version) || $bookingpress_sms_addon_version == '') {
                // phpcs:ignore
                update_option('bookingpress_sms_new_installment',1);
		update_option('bookingpress_sms_new_installment_chk',1);

                // activate license for this addon
                $posted_license_key = trim( get_option( 'bkp_license_key' ) );
			    $posted_license_package = '4872';

                $myaddon_name = "bookingpress-sms/bookingpress-sms.php";

                $api_params = array(
                    'edd_action' => 'activate_license',
                    'license'    => $posted_license_key,
                    'item_id'  => $posted_license_package,
                    //'item_name'  => urlencode( BOOKINGPRESS_ITEM_NAME ), // the name of our product in EDD
                    'url'        => home_url()
                );

                // Call the custom API.
                $response = wp_remote_post( BOOKINGPRESS_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

                //echo "<pre>";print_r($response); echo "</pre>"; exit;

                // make sure the response came back okay
                $message = "";
                if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
                    $message =  ( is_wp_error( $response ) && ! empty( $response->get_error_message() ) ) ? $response->get_error_message() : __( 'An error occurred, please try again.','bookingpress-sms' );
                } else {
                    $license_data = json_decode( wp_remote_retrieve_body( $response ) );
                    $license_data_string = wp_remote_retrieve_body( $response );
                    if ( false === $license_data->success ) {
                        switch( $license_data->error ) {
                            case 'expired' :
                                $message = sprintf(
                                    __( 'Your license key expired on %s.','bookingpress-sms' ),
                                    date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
                                );
                                break;
                            case 'revoked' :
                                $message = __( 'Your license key has been disabled.','bookingpress-sms' );
                                break;
                            case 'missing' :
                                $message = __( 'Invalid license.','bookingpress-sms' );
                                break;
                            case 'invalid' :
                            case 'site_inactive' :
                                $message = __( 'Your license is not active for this URL.','bookingpress-sms' );
                                break;
                            case 'item_name_mismatch' :
                                $message = __('This appears to be an invalid license key for your selected package.','bookingpress-sms');
                                break;
                            case 'invalid_item_id' :
                                    $message = __('This appears to be an invalid license key for your selected package.','bookingpress-sms');
                                    break;
                            case 'no_activations_left':
                                $message = __( 'Your license key has reached its activation limit.','bookingpress-sms');
                                break;
                            default :
                                $message = __( 'An error occurred, please try again.','bookingpress-sms' );
                                break;
                        }

                    }

                }

                if ( ! empty( $message ) ) {
                    update_option( 'bkp_sms_license_data_activate_response', $license_data_string );
                    update_option( 'bkp_sms_license_status', $license_data->license );
                    deactivate_plugins($myaddon_name, FALSE);

                    $redirect_url = network_admin_url('plugins.php?deactivate=true&bkp_license_deactivate=true&bkp_deactivate_plugin='.$myaddon_name);
                    $bpa_dact_message = __('Please activate license of BookingPress premium plugin to use BookingPress SMS Add-on', 'bookingpress-sms');
					$bpa_link = sprintf( __('Please %s Click Here %s to Continue', 'bookingpress-sms'), '<a href="javascript:void(0)" onclick="window.location.href=\'' . $redirect_url . '\'">', '</a>');
					wp_die('<p>'.$bpa_dact_message.'<br/>'.$bpa_link.'</p>');
                    die;
                }
                
                if($license_data->license === "valid")
                {
                    update_option( 'bkp_sms_license_key', $posted_license_key );
                    update_option( 'bkp_sms_license_package', $posted_license_package );
                    update_option( 'bkp_sms_license_status', $license_data->license );
                    update_option( 'bkp_sms_license_data_activate_response', $license_data_string );

                    

                } 




               $wpdb->query("ALTER TABLE {$tbl_bookingpress_notifications} ADD bookingpress_sms_notification_message TEXT NULL DEFAULT NULL AFTER bookingpress_notification_message"); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_notifications is table name defined globally. False Positive alarm
               $wpdb->query("ALTER TABLE {$tbl_bookingpress_notifications} ADD bookingpress_send_sms_notification INT(1) DEFAULT 0 AFTER bookingpress_notification_duration_unit");  // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_notifications is table name defined globally. False Positive alarm
               $wpdb->query("ALTER TABLE {$tbl_bookingpress_notifications} ADD bookingpress_sms_admin_number VARCHAR(60) NULL DEFAULT NULL AFTER bookingpress_send_sms_notification");  // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_notifications is table name defined globally. False Positive alarm

                update_option('bookingpress_sms_gateway', $bookingpress_sms_version);
            }

            //Get default phone number meta key
            $bookingpress_default_phone_number_details = $wpdb->get_row($wpdb->prepare("SELECT bookingpress_field_meta_key FROM {$tbl_bookingpress_form_fields} WHERE bookingpress_form_field_name = %s", 'phone_number'), ARRAY_A); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_form_fields is a table name. false alarm
            if(!empty($bookingpress_default_phone_number_details['bookingpress_field_meta_key'])){
                $bookingpress_field_meta_key = $bookingpress_default_phone_number_details['bookingpress_field_meta_key'];
                $BookingPress->bookingpress_update_settings('bookingpress_selected_phone_number_field', 'notification_setting', $bookingpress_field_meta_key);
            }
		}

        public static function uninstall(){            
            global $wpdb,$tbl_bookingpress_notifications;
            delete_option('bookingpress_sms_gateway');


            delete_option('bkp_sms_license_key');
            delete_option('bkp_sms_license_package');
            delete_option('bkp_sms_license_status');
            delete_option('bkp_sms_license_data_activate_response');


            $wpdb->query( "ALTER TABLE {$tbl_bookingpress_notifications} DROP COLUMN bookingpress_sms_notification_message" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_notifications is a table name. false alarm
            $wpdb->query( "ALTER TABLE {$tbl_bookingpress_notifications} DROP COLUMN bookingpress_send_sms_notification" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_notifications is a table name. false alarm
            $wpdb->query( "ALTER TABLE {$tbl_bookingpress_notifications} DROP COLUMN bookingpress_sms_admin_number" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_notifications is a table name. false alarm
        }
    }

    global $bookingpress_sms;
	$bookingpress_sms = new bookingpress_sms;
}
?>