<?php

if (!class_exists('bookingpress_paypalpro') && class_exists( 'BookingPress_Core') ) {
	class bookingpress_paypalpro {
		function __construct() {
            register_activation_hook(BOOKINGPRESS_PAYPALPRO_DIR.'/bookingpress-paypalpro.php', array('bookingpress_paypalpro', 'install'));
            register_uninstall_hook(BOOKINGPRESS_PAYPALPRO_DIR.'/bookingpress-paypalpro.php', array('bookingpress_paypalpro', 'uninstall'));
            
            //Admiin notices
            add_action('admin_notices', array($this, 'bookingpress_admin_notices'));

            if( !function_exists('is_plugin_active') ){
                include_once ABSPATH . 'wp-admin/includes/plugin.php';
            }

            if(is_plugin_active('bookingpress-appointment-booking-pro/bookingpress-appointment-booking-pro.php')) {

                //Hook for add front side payment gateway option
                add_action('bpa_front_add_payment_gateway', array($this, 'bookingpress_add_frontend_payment_gateway'), 10);
                add_filter('bookingpress_frontend_apointment_form_add_dynamic_data', array($this, 'bookingpress_frontend_data_fields_for_paypalpro'), 10);

                //validation message at front side while book appointment
                add_action('bookingpress_validate_booking_form', array($this, 'bookingpress_add_validation_code_before_book_appointment_func'), 10, 1);
                add_filter('bookingpress_after_selecting_payment_method', array($this, 'bookingpress_after_selecting_payment_method_func'), 10, 1);

                //Add debug log section
                add_filter('bookingpress_add_debug_logs_section', array($this, 'bookingpress_add_debug_logs_func'));

                //Validate paypalpro currency
                add_filter('bookingpress_currency_support', array($this, 'bookingpress_currency_support_func'), 10, 2);
                add_filter('bookingpress_pro_validate_currency_before_book_appointment', array($this, 'bookingpress_pro_validate_currency_before_book_appointment_func'), 10, 3);

                //Filter for add payment gateway to revenue filter list
			    add_filter('bookingpress_revenue_filter_payment_gateway_list_add', array($this, 'bookingpress_revenue_filter_payment_gateway_list_add_func'));
                
                add_action('bookingpress_gateway_listing_field',array($this,'bookingpress_gateway_listing_field_func'));
                add_filter('bookingpress_add_setting_dynamic_data_fields',array($this,'bookingpress_add_setting_dynamic_data_fields_func'));
                add_filter('bookingpress_addon_list_data_filter',array($this,'bookingpress_addon_list_data_filter_func'));

                add_filter('bookingpress_modify_customize_data_fields',array($this,'bookingpress_modify_customize_data_fields_func'));
                add_action('bookingpress_add_booking_form_summary_label_data',array($this,'bookingpress_add_booking_form_summary_label_data_func'));

                add_filter('bookingpress_get_booking_form_customize_data_filter',array($this,'bookingpress_get_booking_form_customize_data_filter_func'));

                add_filter('bookingpress_allowed_payment_gateway_for_refund',array($this,'bookingpress_allowed_payment_gateway_for_refund_func'));

                if(is_plugin_active('bookingpress-multilanguage/bookingpress-multilanguage.php')) {
					add_filter('bookingpress_modified_language_translate_fields',array($this,'bookingpress_modified_language_translate_fields_func'),10);
                	add_filter('bookingpress_modified_customize_form_language_translate_fields',array($this,'bookingpress_modified_language_translate_fields_func'),10);
				}

                /* Package order Added Start */

                add_filter('bookingpress_frontend_package_order_form_add_dynamic_data', array($this, 'bookingpress_frontend_package_order_form_add_dynamic_data_func'), 10);
                add_action('bpa_front_package_order_add_payment_gateway', array($this, 'bpa_front_package_order_add_payment_gateway_func'), 10);
                add_filter('bookingpress_after_selecting_payment_method_for_package_order', array($this, 'bookingpress_after_selecting_payment_method_for_package_order_func'), 10, 1);

                add_filter('bookingpress_modified_package_customization_fields', array($this, 'bookingpress_modified_package_customization_fields_func'), 10,1);
                add_filter('bookingpress_customized_package_booking_summary_step_labels_translate', array($this, 'bookingpress_customized_package_booking_summary_step_labels_translate'), 10,1);
                add_action('bookingpress_add_package_label_settings_dynamically',array($this,'bookingpress_add_package_label_settings_dynamically_func'));                

                /* Package order Added Over */

                /* Gift Card Addon Payment GateWay Added Start */

                add_action('bpa_front_gift_card_order_add_payment_gateway', array($this, 'bpa_front_gift_card_order_add_payment_gateway_func'), 10);
                add_filter('bookingpress_frontend_gift_card_order_form_add_dynamic_data', array($this, 'bookingpress_frontend_gift_card_order_form_add_dynamic_data_func'), 10);
                add_filter('bookingpress_after_selecting_payment_method_for_gift_card', array($this, 'bookingpress_after_selecting_payment_method_for_gift_card_func'), 10, 1);

                add_filter('bookingpress_get_gift_card_customize_data_filter', array($this, 'bookingpress_get_gift_card_customize_data_filter_func'), 10,1);
                add_filter('bookingpress_customized_gift_card_booking_summary_step_labels_translate', array($this, 'bookingpress_customized_gift_card_booking_summary_step_labels_translate'), 10,1);
                add_action('bookingpress_add_gift_card_label_settings_dynamically',array($this,'bookingpress_add_gift_card_label_settings_dynamically_func'));
                /* Gift Card Addon Payment GateWay Added Over */

			}
            
            add_action('activated_plugin',array($this,'bookingpress_is_paypalpro_addon_activated'),11,2);
	    add_action( 'admin_init', array( $this, 'bookingpress_paypalpro_upgrade_data' ) );
		}

        
        /**
         * Function for add front Gift Card payment gateway
         *
         * @return void
         */
        function bpa_front_gift_card_order_add_payment_gateway_func(){
            global $BookingPress;
            $bookingpress_is_gateway_enable = $BookingPress->bookingpress_get_settings('paypalpro_payment', 'payment_setting');
            if($bookingpress_is_gateway_enable == 'true'){
            ?>
            <div class="bpgc-front-module--pm-body__item bpa-paypalpro" :class="(gift_card_step_form_data.selected_payment_method == 'paypalpro') ? '__bpgc-is-selected' : ''" @click="select_payment_method('paypalpro')" v-if="paypalpro_payment != 'false' && paypalpro_payment != ''">
                <svg class="bpgc-front-pm-pay-local-icon" xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="#000000"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm-1 14H5c-.55 0-1-.45-1-1v-5h16v5c0 .55-.45 1-1 1zm1-10H4V6h16v2z"/></svg>
                    <p>{{paypalpro_text}}</p>
                    <div class="bpgc-front-si-card--checkmark-icon" v-if="gift_card_step_form_data.selected_payment_method == 'paypalpro'">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zM9.29 16.29 5.7 12.7c-.39-.39-.39-1.02 0-1.41.39-.39 1.02-.39 1.41 0L10 14.17l6.88-6.88c.39-.39 1.02-.39 1.41 0 .39.39.39 1.02 0 1.41l-7.59 7.59c-.38.39-1.02.39-1.41 0z"/></svg>
                    </div>
                </div>
            <?php
            }
        }

        /**
         * Function for payment method Gift Card data add
         *
         * @param  mixed $bookingpress_front_vue_data_fields
         * @return void
         */
        function bookingpress_frontend_gift_card_order_form_add_dynamic_data_func($bookingpress_front_vue_data_fields){
            global $BookingPress;
            $bookingpress_paypalpro_payment_method = $BookingPress->bookingpress_get_settings('paypalpro_payment_method', 'payment_setting');
            $bookingpress_paypalpro_payment_method = !empty($bookingpress_paypalpro_payment_method) ? $bookingpress_paypalpro_payment_method : 'sca_popup';

			$bookingpress_front_vue_data_fields['paypalpro_payment'] = $this->is_addon_activated();
            $bookingpress_front_vue_data_fields['paypalpro_payment_method'] = $bookingpress_paypalpro_payment_method;            
            $bookingpress_is_gateway_enable = $BookingPress->bookingpress_get_settings('paypalpro_payment', 'payment_setting');
            if($bookingpress_is_gateway_enable == 'true'){
                $bookingpress_front_vue_data_fields['is_only_onsite_enabled'] = 0;
                $bookingpress_front_vue_data_fields['bookingpress_activate_gift_card_payment_gateway_counter'] = $bookingpress_front_vue_data_fields['bookingpress_activate_gift_card_payment_gateway_counter'] + 1;
            }
            $bookingpress_front_vue_data_fields['paypalpro_text'] = $BookingPress->bookingpress_get_customize_settings('paypalpro_text', 'gift_card_form');
            return $bookingpress_front_vue_data_fields;
        }

         /**
         * Function for after select payment method
         *
         * @param  mixed $bookingpress_after_selecting_payment_method_data
         * @return void
         */
        function bookingpress_after_selecting_payment_method_for_gift_card_func($bookingpress_after_selecting_payment_method_data){
            $bookingpress_after_selecting_payment_method_data .= '
            if(vm.gift_card_step_form_data.selected_payment_method == "paypalpro"){
                bookingpress_allowed_payment_gateways_for_card_fields.push("paypalpro");
            }';
            return $bookingpress_after_selecting_payment_method_data;
		}

        function bookingpress_get_gift_card_customize_data_filter_func($bookingpress_gift_card_field_settings) {
            global $BookingPress;
            $paypalpro_text = $BookingPress->bookingpress_get_customize_settings('paypalpro_text', 'gift_card_form');
            $bookingpress_gift_card_field_settings['paypalpro_text'] = $paypalpro_text;
            return $bookingpress_gift_card_field_settings;
        }

        function bookingpress_customized_gift_card_booking_summary_step_labels_translate($bookingpress_customized_gift_card_booking_summary_step_labels){
            $bookingpress_customized_gift_card_booking_summary_step_labels['paypalpro_text'] = array('field_type'=>'text','field_label'=>__('Paypalpro payment title', 'bookingpress-paypalpro'),'save_field_type'=>'gift_card_form');            
            return $bookingpress_customized_gift_card_booking_summary_step_labels;
        }

        function bookingpress_add_gift_card_label_settings_dynamically_func() {            
            ?>
            <div class="bpa-sm--item">
                <label class="bpa-form-label"><?php esc_html_e('Paypalpro payment title', 'bookingpress-paypalpro'); ?></label>
                <el-input v-model="gift_card_form_settings.paypalpro_text" class="bpa-form-control"></el-input>
            </div>                 
            <?php            
        }
        
        function bookingpress_add_package_label_settings_dynamically_func() {            
            ?>
            <div class="bpa-sm--item">
                <label class="bpa-form-label"><?php esc_html_e('Paypalpro payment title', 'bookingpress-paypalpro'); ?></label>
                <el-input v-model="package_booking_form_settings.paypalpro_text" class="bpa-form-control"></el-input>
            </div>                 
            <?php            
        }

        function bookingpress_customized_package_booking_summary_step_labels_translate($bookingpress_customized_package_booking_summary_step_labels){
            $bookingpress_customized_package_booking_summary_step_labels['paypalpro_text'] = array('field_type'=>'text','field_label'=>__('Paypalpro payment title', 'bookingpress-paypalpro'),'save_field_type'=>'package_booking_form');            
            return $bookingpress_customized_package_booking_summary_step_labels;
        }


        function bookingpress_modified_package_customization_fields_func($bookingpress_modified_package_customization_fields){            
            global $BookingPress;
            $paypalpro_text = $BookingPress->bookingpress_get_customize_settings('paypalpro_text', 'package_booking_form');
            $bookingpress_modified_package_customization_fields['paypalpro_text'] = $paypalpro_text;
            return $bookingpress_modified_package_customization_fields;
        }

        /**
         * Function for add card option in package booking
         *
         * @param  mixed $bookingpress_after_selecting_payment_method_data
         * @return void
         */
        function bookingpress_after_selecting_payment_method_for_package_order_func($bookingpress_after_selecting_payment_method_data){
			$bookingpress_after_selecting_payment_method_data .= '
            if(vm.package_step_form_data.selected_payment_method == "paypalpro"){
                bookingpress_allowed_payment_gateways_for_card_fields.push("paypalpro");
            }';
            return $bookingpress_after_selecting_payment_method_data;
		}        

        /**
         * Function for package payment method HTML
         *
         * @return void
         */
        function bpa_front_package_order_add_payment_gateway_func(){
            global $BookingPress;

            $bookingpress_is_gateway_enable = $BookingPress->bookingpress_get_settings('paypalpro_payment', 'payment_setting');
            if($bookingpress_is_gateway_enable == 'true'){
            ?>             
                <div class="bpp-front-module--pm-body__item bpa-paypalpro" :class="(package_step_form_data.selected_payment_method == 'paypalpro') ? '__bpa-is-selected' : ''" @click="select_payment_method('paypalpro')" v-if="paypalpro_payment != 'false' && paypalpro_payment != ''">
                    <svg class="bpp-front-pm-pay-local-icon" xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="#000000"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm-1 14H5c-.55 0-1-.45-1-1v-5h16v5c0 .55-.45 1-1 1zm1-10H4V6h16v2z"/></svg>
					<p>{{paypalpro_text}}</p>
					<div class="bpp-front-si-card--checkmark-icon" v-if="package_step_form_data.selected_payment_method == 'paypalpro'">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zM9.29 16.29 5.7 12.7c-.39-.39-.39-1.02 0-1.41.39-.39 1.02-.39 1.41 0L10 14.17l6.88-6.88c.39-.39 1.02-.39 1.41 0 .39.39.39 1.02 0 1.41l-7.59 7.59c-.38.39-1.02.39-1.41 0z"/></svg>
					</div>
				</div>
            <?php
	    }
        }        

        /**
         * Function for add package addon vue data
         *
         * @param  mixed $bookingpress_front_vue_data_fields
         * @return void
         */
        function bookingpress_frontend_package_order_form_add_dynamic_data_func($bookingpress_front_vue_data_fields){

            global $BookingPress;
            $bookingpress_front_vue_data_fields['paypalpro_payment'] = $this->is_addon_activated();            
            $bookingpress_is_gateway_enable = $BookingPress->bookingpress_get_settings('paypalpro_payment', 'payment_setting');
            if($bookingpress_is_gateway_enable == 'true'){            
                $bookingpress_front_vue_data_fields['is_only_onsite_enabled'] = '0';
                $bookingpress_front_vue_data_fields['bookingpress_activate_package_payment_gateway_counter'] = $bookingpress_front_vue_data_fields['bookingpress_activate_package_payment_gateway_counter'] + 1;
            }
            $bookingpress_front_vue_data_fields['paypalpro_text'] = $BookingPress->bookingpress_get_customize_settings('paypalpro_text', 'package_booking_form');
            
            return $bookingpress_front_vue_data_fields;
        }

        /*Multi language translation */
        function bookingpress_modified_language_translate_fields_func($bookingpress_all_language_translation_fields){
            $bookingpress_paypalpro_language_translation_fields = array(                
				'paypalpro_text' => array('field_type'=>'text','field_label'=>__('Paypalpro payment title', 'bookingpress-paypalpro'),'save_field_type'=>'booking_form'),                 
			);  
			$bookingpress_all_language_translation_fields['customized_form_summary_step_labels'] = array_merge($bookingpress_all_language_translation_fields['customized_form_summary_step_labels'], $bookingpress_paypalpro_language_translation_fields);
			return $bookingpress_all_language_translation_fields;
		}

        /**
         * bookingpress_paypalpro_upgrade_data
         *
         * @return void
         */
        function bookingpress_paypalpro_upgrade_data(){
            global $BookingPress;
            $bookingpress_paypalpro_version = get_option('bookingpress_paypalpro_payment_gateway', true);

            if( version_compare( $bookingpress_paypalpro_version, '1.4', '<' ) ){
                $bookingpress_load_paypalpro_update_file = BOOKINGPRESS_PAYPALPRO_DIR . '/core/views/upgrade_latest_data.php';
                include $bookingpress_load_paypalpro_update_file;
                $BookingPress->bookingpress_send_anonymous_data_cron();
            }
        }
           
        /**
         * bookingpress_allowed_payment_gateway_for_refund_func
         *
         * @param  mixed $payment_gateway_data
         * @return void
         */
        function bookingpress_allowed_payment_gateway_for_refund_func($payment_gateway_data) {            
            $payment_gateway_data['paypalpro'] = array(
                'full_status' => 1,
                'partial_status' => 1,
                'allow_days' => 0,
                'is_refund_support' => 1,
            );
            return $payment_gateway_data;
        }
	
        function bookingpress_is_paypalpro_addon_activated($plugin,$network_activation)
        {              
            $myaddon_name = "bookingpress-paypalpro/bookingpress-paypalpro.php";

            if($plugin == $myaddon_name)
            {

                if(!(is_plugin_active('bookingpress-appointment-booking-pro/bookingpress-appointment-booking-pro.php')))
                {
                    deactivate_plugins($plugin, FALSE);
                    $redirect_url = network_admin_url('plugins.php?deactivate=true&bkp_license_deactivate=true&bkp_deactivate_plugin='.$plugin);
                    $bpa_dact_message = __('Please activate license of BookingPress premium plugin to use BookingPress PayPal Pro Payment Gateway Add-on', 'bookingpress-paypalpro');
                    $bpa_link = sprintf( __('Please %s Click Here %s to Continue', 'bookingpress-paypalpro'), '<a href="javascript:void(0)" onclick="window.location.href=\'' . $redirect_url . '\'">', '</a>');
                    wp_die('<p>'.$bpa_dact_message.'<br/>'.$bpa_link.'</p>');
                    die;
                }

                $license = trim( get_option( 'bkp_license_key' ) );
                $package = trim( get_option( 'bkp_license_package' ) );

                if( '' === $license || false === $license ) 
                {
                    deactivate_plugins($plugin, FALSE);
                    $redirect_url = network_admin_url('plugins.php?deactivate=true&bkp_license_deactivate=true&bkp_deactivate_plugin='.$plugin);
                    $bpa_dact_message = __('Please activate license of BookingPress premium plugin to use BookingPress PayPal Pro Payment Gateway Add-on', 'bookingpress-paypalpro');
                    $bpa_link = sprintf( __('Please %s Click Here %s to Continue', 'bookingpress-paypalpro'), '<a href="javascript:void(0)" onclick="window.location.href=\'' . $redirect_url . '\'">', '</a>');
                    wp_die('<p>'.$bpa_dact_message.'<br/>'.$bpa_link.'</p>');
                    die;
                }
                else
                {
                    $store_url = BOOKINGPRESS_PAYPALPRO_STORE_URL;
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
                            deactivate_plugins($plugin, FALSE);
                            $redirect_url = network_admin_url('plugins.php?deactivate=true&bkp_license_deactivate=true&bkp_deactivate_plugin='.$plugin);
                            $bpa_dact_message = __('Please activate license of BookingPress premium plugin to use BookingPress PayPal Pro Payment Gateway Add-on', 'bookingpress-paypalpro');
                            $bpa_link = sprintf( __('Please %s Click Here %s to Continue', 'bookingpress-paypalpro'), '<a href="javascript:void(0)" onclick="window.location.href=\'' . $redirect_url . '\'">', '</a>');
                            wp_die('<p>'.$bpa_dact_message.'<br/>'.$bpa_link.'</p>');
                            die;
                        }

                    }
                    else
                    {
                        deactivate_plugins($plugin, FALSE);
                        $redirect_url = network_admin_url('plugins.php?deactivate=true&bkp_license_deactivate=true&bkp_deactivate_plugin='.$plugin);
                        $bpa_dact_message = __('Please activate license of BookingPress premium plugin to use BookingPress PayPal Pro Payment Gateway Add-on', 'bookingpress-paypalpro');
                        $bpa_link = sprintf( __('Please %s Click Here %s to Continue', 'bookingpress-paypalpro'), '<a href="javascript:void(0)" onclick="window.location.href=\'' . $redirect_url . '\'">', '</a>');
                        wp_die('<p>'.$bpa_dact_message.'<br/>'.$bpa_link.'</p>');
                        die;
                    }
                }
            }
        }

		function bookingpress_get_booking_form_customize_data_filter_func($booking_form_settings) {
			$booking_form_settings['front_label_edit_data']['paypalpro_text'] = '';
			return $booking_form_settings;
		} 

        function bookingpress_modify_customize_data_fields_func($bookingpress_customize_vue_data_fields) {
            $bookingpress_customize_vue_data_fields['front_label_edit_data']['paypalpro_text'] = __('Paypalpro', 'bookingpress-paypalpro');
            return $bookingpress_customize_vue_data_fields;            
        }

        function bookingpress_add_booking_form_summary_label_data_func() {            
            ?>
                <div class="bpa-sm--item">
                    <label class="bpa-form-label"><?php esc_html_e('Paypalpro payment title', 'bookingpress-paypalpro'); ?></label>
                    <el-input v-model="front_label_edit_data.paypalpro_text" class="bpa-form-control"></el-input>
                </div>                 
            <?php            
        }	

        function bookingpress_addon_list_data_filter_func($bookingpress_body_res){
            global $bookingpress_slugs;
            if(!empty($bookingpress_body_res)) {
                foreach($bookingpress_body_res as $bookingpress_body_res_key =>$bookingpress_body_res_val) {
                    $bookingpress_setting_page_url = add_query_arg('page', $bookingpress_slugs->bookingpress_settings, esc_url( admin_url() . 'admin.php?page=bookingpress' ));
                    $bookingpress_config_url = add_query_arg('setting_page', 'payment_settings', $bookingpress_setting_page_url);
                    if($bookingpress_body_res_val['addon_key'] == 'bookingpress_paypalpro_payment_gateway') {
                        $bookingpress_body_res[$bookingpress_body_res_key]['addon_configure_url'] = $bookingpress_config_url;
                    }
                }
            }
            return $bookingpress_body_res;
        }  

        function bookingpress_add_setting_dynamic_data_fields_func($bookingpress_dynamic_setting_data_fields) {            
            $bookingpress_dynamic_setting_data_fields['payment_setting_form']['paypalpro_payment'] = false ;
            $bookingpress_dynamic_setting_data_fields['payment_setting_form']['paypalpro_payment_mode'] = 'sandbox';            
            $bookingpress_dynamic_setting_data_fields['payment_setting_form']['paypalpro_api_username'] = '';
            $bookingpress_dynamic_setting_data_fields['payment_setting_form']['paypalpro_vendor'] = ''; 
            $bookingpress_dynamic_setting_data_fields['payment_setting_form']['paypalpro_partner'] = '';             
            $paypalpro_rules = array(                               
                'paypalpro_api_username'  => array(
                    array(
                        'required' => true,
                        'message'  => __( 'Please enter the api username', 'bookingpress-paypalpro' ),
                        'trigger'  => 'change',
                    ),
                ),
                'paypalpro_api_password' => array(
                    array(
                        'required' => true,
                        'message'  => __( 'Please enter the api password', 'bookingpress-paypalpro' ),
                        'trigger'  => 'change',
                    ),
                ),
                'paypalpro_vendor' => array(
                    array(
                        'required' => true,
                        'message'  => __( 'Please enter the vendor', 'bookingpress-paypalpro' ),
                        'trigger'  => 'change',
                    ),
                ),
                'paypalpro_partner' => array(
                    array(
                        'required' => true,
                        'message'  => __( 'Please enter the partner', 'bookingpress-paypalpro' ),
                        'trigger'  => 'change',
                    ),
                ),
            );
            $bookingpress_dynamic_setting_data_fields['rules_payment'] = array_merge($bookingpress_dynamic_setting_data_fields['rules_payment'],$paypalpro_rules);      

            return $bookingpress_dynamic_setting_data_fields;            
        }

        function bookingpress_gateway_listing_field_func(){
            ?>
            <div class="bpa-pst-is-single-payment-box">
                <el-row type="flex" class="bpa-gs--tabs-pb__cb-item-row">
                    <el-col :xs="12" :sm="12" :md="12" :lg="8" :xl="8" class="bpa-gs__cb-item-left --bpa-is-not-input-control">
                        <h4> <?php esc_html_e('Paypal Pro', 'bookingpress-paypalpro'); ?></h4>
                    </el-col>
                    <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
                        <el-form-item prop="paypalpro_payment">
                            <el-switch class="bpa-swtich-control" v-model="payment_setting_form.paypalpro_payment"></el-switch>
                        </el-form-item>
                    </el-col>
                </el-row>
                <div class="bpa-ns--sub-module__card" v-if="payment_setting_form.paypalpro_payment == true">
                    <el-row type="flex" class="bpa-ns--sub-module__card--row">
                        <el-col :xs="12" :sm="12" :md="12" :lg="8" :xl="8" class="bpa-gs__cb-item-left">
                            <h4> <?php esc_html_e('Payment Mode', 'bookingpress-paypalpro'); ?></h4>
                        </el-col>
                        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16">
                            <el-radio v-model="payment_setting_form.paypalpro_payment_mode" label="sandbox">Sandbox</el-radio>
                            <el-radio v-model="payment_setting_form.paypalpro_payment_mode" label="live">Live</el-radio>
                        </el-col>
                    </el-row>                 
                    <el-row type="flex" class="bpa-ns--sub-module__card--row">
                        <el-col :xs="12" :sm="12" :md="12" :lg="8" :xl="8" class="bpa-gs__cb-item-left">
                            <h4> <?php esc_html_e('API Username', 'bookingpress-paypalpro'); ?></h4>
                        </el-col>
                        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16">
                            <el-form-item prop="paypalpro_api_username">
                                <el-input class="bpa-form-control" v-model="payment_setting_form.paypalpro_api_username" placeholder="<?php esc_html_e('Enter api username', 'bookingpress-paypalpro'); ?>"></el-input>
                            </el-form-item>
                        </el-col>
                    </el-row>
                    <el-row type="flex" class="bpa-ns--sub-module__card--row">
                        <el-col :xs="12" :sm="12" :md="12" :lg="8" :xl="8" class="bpa-gs__cb-item-left">
                            <h4> <?php esc_html_e('API Password', 'bookingpress-paypalpro'); ?></h4>
                        </el-col>
                        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
                            <el-form-item prop="paypalpro_api_password">
                                <el-input class="bpa-form-control" v-model="payment_setting_form.paypalpro_api_password" placeholder="<?php esc_html_e('Enter api password', 'bookingpress-paypalpro'); ?>"></el-input>
                            </el-form-item>    
                        </el-col>
                    </el-row>
                    <el-row type="flex" class="bpa-ns--sub-module__card--row">
                        <el-col :xs="12" :sm="12" :md="12" :lg="8" :xl="8" class="bpa-gs__cb-item-left">
                            <h4> <?php esc_html_e('Vendor', 'bookingpress-paypalpro'); ?></h4>
                        </el-col>
                        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
                            <el-form-item prop="paypalpro_vendor">
                                <el-input class="bpa-form-control" v-model="payment_setting_form.paypalpro_vendor" placeholder="<?php esc_html_e('Enter vendor', 'bookingpress-paypalpro'); ?>"></el-input>
                            </el-form-item>    
                        </el-col>
                    </el-row>
                    <el-row type="flex" class="bpa-ns--sub-module__card--row">
                        <el-col :xs="12" :sm="12" :md="12" :lg="8" :xl="8" class="bpa-gs__cb-item-left">
                            <h4> <?php esc_html_e('Partner', 'bookingpress-paypalpro'); ?></h4>
                        </el-col>
                        <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
                            <el-form-item prop="paypalpro_partner">
                                <el-input class="bpa-form-control" v-model="payment_setting_form.paypalpro_partner" placeholder="<?php esc_html_e('Enter partner', 'bookingpress-paypalpro'); ?>"></el-input>
                            </el-form-item>    
                        </el-col>
                    </el-row>
                </div>
            </div>    
            <?php
        }
            
        function bookingpress_revenue_filter_payment_gateway_list_add_func($bookingpress_revenue_filter_payment_gateway_list){		
            $bookingpress_revenue_filter_payment_gateway_list[] = array(
                'value' => 'paypalpro',
                'text' => 'Paypalpro'
            );

			return $bookingpress_revenue_filter_payment_gateway_list;
		}

        function bookingpress_add_debug_logs_func($bookingpress_debug_log_gateways){			
            $bookingpress_debug_log_gateways['paypalpro'] = 'Paypalpro';
			return $bookingpress_debug_log_gateways;
		}

        function bookingpress_after_selecting_payment_method_func($bookingpress_after_selecting_payment_method_data){
			$bookingpress_after_selecting_payment_method_data .= '
            if(vm.appointment_step_form_data.selected_payment_method == "paypalpro"){
                bookingpress_allowed_payment_gateways_for_card_fields.push("paypalpro");
            }';
            return $bookingpress_after_selecting_payment_method_data;
		}

        function bookingpress_add_validation_code_before_book_appointment_func($posted_data){
            global $wpdb, $BookingPress;
            if(!empty($posted_data) && !empty($posted_data['appointment_data']['selected_payment_method']) && ($posted_data['appointment_data']['selected_payment_method'] == "paypalpro") ){
                $bookingpress_card_number = !empty($posted_data['appointment_data']['card_number']) ? $posted_data['appointment_data']['card_number'] : '';
                $bookingpress_expire_month = !empty($posted_data['appointment_data']['expire_month']) ? $posted_data['appointment_data']['expire_month'] : '';
                $bookingpress_expire_year = !empty($posted_data['appointment_data']['expire_year']) ? $posted_data['appointment_data']['expire_year'] : '';
                $bookingpress_expire_cvv = !empty($posted_data['appointment_data']['cvv']) ? $posted_data['appointment_data']['cvv'] : '';

                $bookingpress_validation_msg = $BookingPress->bookingpress_get_settings('bookingpress_card_details_error_msg','message_setting');
                $bookingpress_validation_msg = !empty($bookingpress_validation_msg) ? stripslashes_deep($bookingpress_validation_msg) : __('Please fill all fields value of card details', 'bookingpress-paypalpro');

                if(empty($bookingpress_card_number) || empty($bookingpress_expire_month) || empty($bookingpress_expire_year) || empty($bookingpress_expire_cvv)){
                    $response['variant'] = 'error';
                    $response['title']   = esc_html__( 'Error', 'bookingpress-paypalpro' );
                    $response['msg'] = $bookingpress_validation_msg;
                    echo json_encode( $response );
                    exit();
                }
            }
        }
        
        function bookingpress_frontend_data_fields_for_paypalpro($bookingpress_front_vue_data_fields){
            global $BookingPress;
            $bookingpress_front_vue_data_fields['paypalpro_payment'] = $this->is_addon_activated();            
            $bookingpress_is_gateway_enable = $BookingPress->bookingpress_get_settings('paypalpro_payment', 'payment_setting');
            if($bookingpress_is_gateway_enable == 'true'){            
                $bookingpress_front_vue_data_fields['is_only_onsite_enabled'] = '0';
                $bookingpress_front_vue_data_fields['bookingpress_activate_payment_gateway_counter'] = $bookingpress_front_vue_data_fields['bookingpress_activate_payment_gateway_counter'] + 1;
            }
            $bookingpress_front_vue_data_fields['paypalpro_text'] = $BookingPress->bookingpress_get_customize_settings('paypalpro_text', 'booking_form');
            
            return $bookingpress_front_vue_data_fields;
        }

        function bookingpress_add_frontend_payment_gateway(){
            global $BookingPress;

            $bookingpress_is_gateway_enable = $BookingPress->bookingpress_get_settings('paypalpro_payment', 'payment_setting');
            if($bookingpress_is_gateway_enable == 'true'){
            ?>             
                <div class="bpa-front-module--pm-body__item bpa-paypalpro" :class="(appointment_step_form_data.selected_payment_method == 'paypalpro') ? '__bpa-is-selected' : ''" @click="select_payment_method('paypalpro')" v-if="paypalpro_payment != 'false' && paypalpro_payment != ''">
                    <svg class="bpa-front-pm-pay-local-icon" xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="#000000"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm-1 14H5c-.55 0-1-.45-1-1v-5h16v5c0 .55-.45 1-1 1zm1-10H4V6h16v2z"/></svg>
					<p>{{paypalpro_text}}</p>
					<div class="bpa-front-si-card--checkmark-icon" v-if="appointment_step_form_data.selected_payment_method == 'paypalpro'">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zM9.29 16.29 5.7 12.7c-.39-.39-.39-1.02 0-1.41.39-.39 1.02-.39 1.41 0L10 14.17l6.88-6.88c.39-.39 1.02-.39 1.41 0 .39.39.39 1.02 0 1.41l-7.59 7.59c-.38.39-1.02.39-1.41 0z"/></svg>
					</div>
				</div>
            <?php
	    }
        }

        function bookingpress_admin_notices(){
            if(!is_plugin_active('bookingpress-appointment-booking-pro/bookingpress-appointment-booking-pro.php')){
                echo "<div class='notice notice-warning'><p>" . __('Bookingpress - Paypalpro plugin requires Bookingpress Premium Plugin installed and active.', 'bookingpress-paypalpro') . "</p></div>";
            }
        }

        public static function install(){
			global $wpdb, $tbl_bookingpress_customize_settings, $bookingpress_paypalpro_version, $BookingPress;
            $bookingpress_paypalpro_addon_version = get_option('bookingpress_paypalpro_payment_gateway');
            $myaddon_name = "bookingpress-paypalpro/bookingpress-paypalpro.php";
            if (!isset($bookingpress_paypalpro_addon_version) || $bookingpress_paypalpro_addon_version == '') {

                // activate license for this addon
                $posted_license_key = trim( get_option( 'bkp_license_key' ) );
			    $posted_license_package = '4778';

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
                    $message =  ( is_wp_error( $response ) && ! empty( $response->get_error_message() ) ) ? $response->get_error_message() : __( 'An error occurred, please try again.','bookingpress-paypalpro' );
                } else {
                    $license_data = json_decode( wp_remote_retrieve_body( $response ) );
                    $license_data_string = wp_remote_retrieve_body( $response );
                    if ( false === $license_data->success ) {
                        switch( $license_data->error ) {
                            case 'expired' :
                                $message = sprintf(
                                    __( 'Your license key expired on %s.','bookingpress-paypalpro' ),
                                    date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
                                );
                                break;
                            case 'revoked' :
                                $message = __( 'Your license key has been disabled.' ,'bookingpress-paypalpro');
                                break;
                            case 'missing' :
                                $message = __( 'Invalid license.','bookingpress-paypalpro' );
                                break;
                            case 'invalid' :
                            case 'site_inactive' :
                                $message = __( 'Your license is not active for this URL.','bookingpress-paypalpro' );
                                break;
                            case 'item_name_mismatch' :
                                $message = __('This appears to be an invalid license key for your selected package.','bookingpress-paypalpro');
                                break;
                            case 'invalid_item_id' :
                                    $message = __('This appears to be an invalid license key for your selected package.','bookingpress-paypalpro');
                                    break;
                            case 'no_activations_left':
                                $message = __( 'Your license key has reached its activation limit.','bookingpress-paypalpro' );
                                break;
                            default :
                                $message = __( 'An error occurred, please try again.','bookingpress-paypalpro' );
                                break;
                        }

                    }

                }

                if ( ! empty( $message ) ) {
                    update_option( 'bkp_paypalpro_license_data_activate_response', $license_data_string );
                    update_option( 'bkp_paypalpro_license_status', $license_data->license );
                    deactivate_plugins($myaddon_name, FALSE);
                    $redirect_url = network_admin_url('plugins.php?deactivate=true&bkp_license_deactivate=true&bkp_deactivate_plugin='.$myaddon_name);
                    $bpa_dact_message = __('Please activate license of BookingPress premium plugin to use BookingPress Paypal Pro Add-on', 'bookingpress-paypalpro');
					$bpa_link = sprintf( __('Please %s Click Here %s to Continue', 'bookingpress-paypalpro'), '<a href="javascript:void(0)" onclick="window.location.href=\'' . $redirect_url . '\'">', '</a>');
					wp_die('<p>'.$bpa_dact_message.'<br/>'.$bpa_link.'</p>');
                    die;
                }
                
                if($license_data->license === "valid")
                {
                    update_option( 'bkp_paypalpro_license_key', $posted_license_key );
                    update_option( 'bkp_paypalpro_license_package', $posted_license_package );
                    update_option( 'bkp_paypalpro_license_status', $license_data->license );
                    update_option( 'bkp_paypalpro_license_data_activate_response', $license_data_string );
                }



                update_option('bookingpress_paypalpro_payment_gateway', $bookingpress_paypalpro_version);

                $bookingpress_get_customize_text = $BookingPress->bookingpress_get_customize_settings('paypalpro_text', 'booking_form');
                if(empty($bookingpress_get_customize_text)){
                    $bookingpress_customize_settings_db_fields = array(
                        'bookingpress_setting_name'  => 'paypalpro_text',
                        'bookingpress_setting_value' => __('Credit Card', 'bookingpress-paypalpro'),
                        'bookingpress_setting_type'  => 'booking_form',
                    );

                    $wpdb->insert($tbl_bookingpress_customize_settings, $bookingpress_customize_settings_db_fields);
                }

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

                    $bookingpress_customize_settings_db_fields = array(
                        'bookingpress_setting_name'  => $key,
                        'bookingpress_setting_value' => $value,
                        'bookingpress_setting_type'  => 'gift_card_form',
                    );
                    $wpdb->insert( $tbl_bookingpress_customize_settings, $bookingpress_customize_settings_db_fields );
                }

            }
		}

        public static function uninstall(){
            delete_option('bookingpress_paypalpro_payment_gateway');

            delete_option('bkp_paypalpro_license_key');
            delete_option('bkp_paypalpro_license_package');
            delete_option('bkp_paypalpro_license_status');
            delete_option('bkp_paypalpro_license_data_activate_response');
        }

        public function is_addon_activated(){
            $bookingpress_paypalpro_module_version = get_option('bookingpress_paypalpro_payment_gateway');
            return !empty($bookingpress_paypalpro_module_version) ? 1 : 0;
        }
      
        function bookingpress_currency_support_func($notAllow, $bookingpress_currency){            
            $bookingpress_paypalpro_currency = $this->bookingpress_paypalpro_supported_currency_list();             
            if (!in_array($bookingpress_currency, $bookingpress_paypalpro_currency)) {
                $notAllow[] = 'paypalpro';
            }
            return $notAllow;
        }

        function bookingpress_pro_validate_currency_before_book_appointment_func($bookingpress_is_support,$bookingpress_selected_payment_method,$bookingpress_currency_name){
            $bookingpress_paypalpro_currency = $this->bookingpress_paypalpro_supported_currency_list(); 
            if ($bookingpress_selected_payment_method == 'paypalpro' && !in_array($bookingpress_currency_name,$bookingpress_paypalpro_currency ) ) {
                $bookingpress_is_support = 0;
            }
            return $bookingpress_is_support;
        }        

        function bookingpress_paypalpro_supported_currency_list() {            
            $bookingpress_currency_list = array('AUD','BRL','CAD','CNY','CZK','Dkk','EUR','HKD','HUF','ILS','JPY','MYR','MXN','NOK','NZD','PHP','PLN','GBP','RUB','SGD','SEK','CHF','TWD','THB','USD');            
            return $bookingpress_currency_list;
        }
    }

    global $bookingpress_paypalpro;
	$bookingpress_paypalpro = new bookingpress_paypalpro;
}