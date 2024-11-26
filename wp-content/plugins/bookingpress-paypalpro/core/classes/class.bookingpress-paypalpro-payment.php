<?php
if (!class_exists('bookingpress_paypalpro_payment') && class_exists( 'BookingPress_Core')) {
	class bookingpress_paypalpro_payment {
        var $bookingpress_selected_payment_method;
        var $bookingpress_paypalpro_api_username;
        var $bookingpress_paypalpro_api_password;
        var $bookingpress_paypalpro_vendor;
        var $bookingpress_paypalpro_partner;

        function __construct() {
            if(is_plugin_active('bookingpress-appointment-booking-pro/bookingpress-appointment-booking-pro.php')) {
                add_filter('bookingpress_paypalpro_submit_form_data', array($this, 'bookingpress_paypalpro_submit_form_data_func'), 10, 2);
                add_filter('bookingpress_paypalpro_apply_refund', array($this, 'bookingpress_paypalpro_apply_refund_func'),10,2);

                /* Package Booking Submit Data */
                add_filter('bookingpress_package_order_paypalpro_submit_form_data', array($this, 'bookingpress_package_order_paypalpro_submit_form_data_func'), 10, 2);

                /* paypalpro Payment GateWay Submit Data - Gift Card*/
                add_filter('bookingpress_gift_card_order_paypalpro_submit_form_data', array($this, 'bookingpress_gift_card_order_paypalpro_submit_form_data_func'), 10, 2);
            }
        }
        function bookingpress_gift_card_order_paypalpro_submit_form_data_func($response, $bookingpress_return_data){
            global $wpdb, $BookingPress, $bookingpress_pro_payment_gateways, $bookingpress_debug_payment_log_id;
            $this->bookingpress_init_paypalpro();

            do_action( 'bookingpress_payment_log_entry', 'paypalpro', 'Paypal pro submitted form data', 'bookingpress pro', $bookingpress_return_data, $bookingpress_debug_payment_log_id );

            if(!empty($bookingpress_return_data)){
                $entry_id                          = $bookingpress_return_data['entry_id'];
                $bookingpress_is_cart = !empty($bookingpress_return_data['is_cart']) ? 1 : 0;
                $currency_code                     = strtoupper($bookingpress_return_data['currency_code']);
                $bookingpress_final_payable_amount = isset( $bookingpress_return_data['payable_amount'] ) ? ($bookingpress_return_data['payable_amount']) : 0;
                $bookingpress_final_payable_amount = number_format((float)$bookingpress_final_payable_amount, 2, '.', '');
                $customer_details                  = $bookingpress_return_data['customer_details'];

                $customer_email = ! empty( $customer_details['customer_email'] ) ? $customer_details['customer_email'] : '';
                $customer_firstname = !empty($customer_details['customer_firstname']) ? $customer_details['customer_firstname'] : $customer_email;
                $customer_lastname = !empty($customer_details['customer_lastname']) ? $customer_details['customer_lastname'] : $customer_email;
                $customer_username = !empty($customer_details['customer_username']) ? $customer_details['customer_username'] : $customer_email;
                $customer_phone = !empty($customer_details['customer_phone']) ? $customer_details['customer_phone'] : '';

                
                $bookingpress_service_name = ! empty( $bookingpress_return_data['selected_gift_card_details']['bookingpress_gift_card_title'] ) ? $bookingpress_return_data['selected_gift_card_details']['bookingpress_gift_card_title'] : __( 'Gift Card Purchase', 'bookingpress-paypalpro' );

                $custom_var = $entry_id;
                $bookingpress_notify_url = $bookingpress_return_data['notify_url'];
                $redirect_url = $bookingpress_return_data['approved_appointment_url'];
                
                $bookingpress_appointment_status = $BookingPress->bookingpress_get_settings( 'appointment_status', 'general_setting' );
                if ( $bookingpress_appointment_status == '2' ) {
                    $redirect_url = $bookingpress_return_data['pending_appointment_url'];
                }

                $bookingpress_card_holder_name = !empty($bookingpress_return_data['card_details']['card_holder_name']) ? $bookingpress_return_data['card_details']['card_holder_name'] : '';
                $bookingpress_card_number = !empty($bookingpress_return_data['card_details']['card_number']) ? str_replace(' ', '', $bookingpress_return_data['card_details']['card_number']) : '';
                $bookingpress_expire_month = !empty($bookingpress_return_data['card_details']['expire_month']) ? $bookingpress_return_data['card_details']['expire_month'] : '';
                $bookingpress_expire_year = !empty($bookingpress_return_data['card_details']['expire_year']) ? substr($bookingpress_return_data['card_details']['expire_year'], -2) : '';
                $bookingpress_cvv = !empty($bookingpress_return_data['card_details']['cvv']) ? $bookingpress_return_data['card_details']['cvv'] : '';
                
                $bookingpress_paypalpro_endpoint = !empty($this->bookingpress_selected_payment_method) && ( $this->bookingpress_selected_payment_method == 'sandbox') ? 'https://pilot-payflowpro.paypal.com' : 'https://payflowpro.paypal.com';
                $bookingpress_card_expire = $bookingpress_expire_month . $bookingpress_expire_year;

                //Authorizing card details
                $bookingpress_authorize_card_details = array(
                    'TENDER' => 'C',
                    'TRXTYPE' => 'A',
                    'ACCT' => $bookingpress_card_number,
                    'EXPDATE' => $bookingpress_card_expire,
                    'CVV2' => $bookingpress_cvv,
                    'AMT' => $bookingpress_final_payable_amount,
                    'CURRENCY' => $currency_code,
                    'VERBOSITY' => 'MEDIUM',
                    'NAME' => $bookingpress_card_holder_name,
                );

                do_action( 'bookingpress_payment_log_entry', 'paypalpro', 'Paypal pro card authorization details', 'bookingpress pro', $bookingpress_authorize_card_details, $bookingpress_debug_payment_log_id );

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $bookingpress_paypalpro_endpoint);
                curl_setopt($ch, CURLOPT_VERBOSE, 1);
                curl_setopt($ch, CURLOPT_TIMEOUT, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POST, 1);
                $nvpstr_ = "";

                if (is_array($bookingpress_authorize_card_details)) {
                    foreach ($bookingpress_authorize_card_details as $key => $value) {
                        if (is_array($value)) {
                            foreach ($value as $item) {
                                if (strlen($nvpstr_) > 0)
                                    $nvpstr_ .= "&";
                                $nvpstr_ .= "$key=" . $item;
                            }
                        } else {
                            if (strlen($nvpstr_) > 0)
                                $nvpstr_ .= "&";
                            $nvpstr_ .= "$key=" .$value;
                        }
                    }
                }

                $nvpreq_ = "VENDOR=$this->bookingpress_paypalpro_vendor&PARTNER=$this->bookingpress_paypalpro_partner&PWD=$this->bookingpress_paypalpro_api_password&USER=$this->bookingpress_paypalpro_api_username&$nvpstr_";
                do_action( 'bookingpress_payment_log_entry', 'paypalpro', 'Paypal pro card authorization nvpreq', 'bookingpress pro', $nvpreq_, $bookingpress_debug_payment_log_id );

                curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq_);

                $bookingpress_authorized_card_res = curl_exec($ch);
                curl_close($ch);

                $bookingpress_formatted_response = array();
                $httpResponseAr = explode("&", $bookingpress_authorized_card_res);
                foreach ($httpResponseAr as $i => $value) {
                    $tmpAr = explode("=", urldecode($value));
                    if (count($tmpAr) > 1) {
                        $bookingpress_formatted_response[$tmpAr[0]] = $tmpAr[1];
                    }
                }

                do_action( 'bookingpress_payment_log_entry', 'paypalpro', 'Paypal pro card authorization response', 'bookingpress pro', $bookingpress_formatted_response, $bookingpress_debug_payment_log_id );

                if(isset($bookingpress_formatted_response['RESULT']) && ($bookingpress_formatted_response['RESULT'] == "0") && !empty($bookingpress_formatted_response['PNREF']) ){
                    $bookingpress_paypalpro_arr = array(
                        'TENDER' => 'C',
                        'TRXTYPE' => 'D',
                        'ACCT' => $bookingpress_card_number,
                        'EXPDATE' => $bookingpress_card_expire,
                        'CVV2' => $bookingpress_cvv,
                        'AMT' => $bookingpress_final_payable_amount,
                        'CURRENCY' => $currency_code,
                        'VERBOSITY' => 'MEDIUM',
                        'NAME' => $bookingpress_card_holder_name,
                        'AMT' => $bookingpress_final_payable_amount,
                        'ORIGID' => $bookingpress_formatted_response['PNREF'],
                    );

                    do_action( 'bookingpress_payment_log_entry', 'paypalpro', 'Paypal pro payment params', 'bookingpress pro', $bookingpress_paypalpro_arr, $bookingpress_debug_payment_log_id );

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $bookingpress_paypalpro_endpoint);
                    curl_setopt($ch, CURLOPT_VERBOSE, 1);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 0);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_POST, 1);
                    $nvpstr = "";
                    if (is_array($bookingpress_paypalpro_arr)) {
                        foreach ($bookingpress_paypalpro_arr as $key => $value) {
                            if (is_array($value)) {
                                foreach ($value as $item) {
                                    if (strlen($nvpstr) > 0)
                                        $nvpstr .= "&";
                                    $nvpstr .= "$key=" . $item;
                                }
                            } else {
                                if (strlen($nvpstr) > 0)
                                    $nvpstr .= "&";
                                $nvpstr .= "$key=" . $value;
                            }
                        }
                    }
                    $nvpreq = "VENDOR=$this->bookingpress_paypalpro_vendor&PARTNER=$this->bookingpress_paypalpro_partner&PWD=$this->bookingpress_paypalpro_api_password&USER=$this->bookingpress_paypalpro_api_username&$nvpstr";
                    do_action( 'bookingpress_payment_log_entry', 'paypalpro', 'Paypal pro payment nvpreq', 'bookingpress pro', $nvpreq, $bookingpress_debug_payment_log_id );

                    curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);
                    $bookingpress_payment_res = curl_exec($ch);
                    curl_close($ch);
                    $bookingpress_payment_formatted_res = array();
                    $httpResponseAr = explode("&", $bookingpress_payment_res);
                    foreach ($httpResponseAr as $i => $value) {
                        $tmpAr = explode("=", urldecode($value));
                        if (count($tmpAr) > 1) {
                            $bookingpress_payment_formatted_res[$tmpAr[0]] = $tmpAr[1];
                        }
                    }
                    $bookingpress_payment_formatted_res['AMT'] =$bookingpress_paypalpro_arr['AMT'];
                    $bookingpress_payment_formatted_res['CURRENCY'] =$bookingpress_paypalpro_arr['CURRENCY'];

                    do_action( 'bookingpress_payment_log_entry', 'paypalpro', 'Paypal pro payment formatted res', 'bookingpress pro', $bookingpress_payment_formatted_res, $bookingpress_debug_payment_log_id );

                    if(isset($bookingpress_payment_formatted_res['RESPMSG']) && $bookingpress_payment_formatted_res['RESPMSG'] == "Approved" ){
                        $payment_log_id = $bookingpress_pro_payment_gateways->bookingpress_confirm_booking( $entry_id, $bookingpress_payment_formatted_res, '1', 'PNREF', '', 1, $bookingpress_is_cart );

                        $response['variant'] = 'redirect_url';
                        $response['title']         = '';
                        $response['msg']           = '';
                        $response['is_redirect']   = 1;
                        $response['redirect_data'] = $redirect_url;
                        $response['is_transaction_completed'] = 1;
                        $response['entry_id'] = $entry_id;
                    }else{
                        $bookingpress_err_msg = __('Something went wrong while pay with Paypalpro', 'bookingpress-paypalpro');

                        do_action( 'bookingpress_payment_log_entry', 'paypalpro', 'Paypal pro payment error', 'bookingpress pro', $bookingpress_err_msg, $bookingpress_debug_payment_log_id );
                    
                        $response['variant']       = 'error';
                        $response['title']         = esc_html__( 'Error', 'bookingpress-paypalpro' );
                        $response['msg']           = $bookingpress_err_msg;
                        $response['is_redirect']   = 0;
                        $response['redirect_data'] = '';
                        $response['is_transaction_completed'] = 0;
                        $response['is_spam']       = 0;   
                    }
                }else{
                    $bookingpress_err_msg = !empty($bookingpress_formatted_response['RESPMSG']) ? __('Error returned from paypal pro', 'bookingpress-paypalpro').": ".$bookingpress_formatted_response['RESPMSG'] : __('Something went wrong while pay with Paypalpro','bookingpress-paypalpro');

                    do_action( 'bookingpress_payment_log_entry', 'paypalpro', 'Paypal pro card authorization error', 'bookingpress pro', $bookingpress_err_msg, $bookingpress_debug_payment_log_id );
                    
                    $response['variant']       = 'error';
                    $response['title']         = esc_html__( 'Error', 'bookingpress-paypalpro' );
                    $response['msg']           = $bookingpress_err_msg;
                    $response['is_redirect']   = 0;
                    $response['redirect_data'] = '';
                    $response['is_transaction_completed'] = 0;
                    $response['is_spam']       = 0;
                }
                
            }
            return $response;
        }
        
        /**
         * Function for package booking submit data
         *
         * @param  mixed $response
         * @param  mixed $bookingpress_return_data
         * @return void
         */
        function bookingpress_package_order_paypalpro_submit_form_data_func($response, $bookingpress_return_data){
            global $wpdb, $BookingPress, $bookingpress_pro_payment_gateways, $bookingpress_debug_payment_log_id;
            $this->bookingpress_init_paypalpro();

            do_action( 'bookingpress_payment_log_entry', 'paypalpro', 'Paypal pro submitted form data', 'bookingpress pro', $bookingpress_return_data, $bookingpress_debug_payment_log_id );

            if(!empty($bookingpress_return_data)){
                $entry_id                          = $bookingpress_return_data['entry_id'];
                $bookingpress_is_cart = !empty($bookingpress_return_data['is_cart']) ? 1 : 0;
                $currency_code                     = strtoupper($bookingpress_return_data['currency_code']);
                $bookingpress_final_payable_amount = isset( $bookingpress_return_data['payable_amount'] ) ? ($bookingpress_return_data['payable_amount']) : 0;
                $bookingpress_final_payable_amount = number_format((float)$bookingpress_final_payable_amount, 2, '.', '');
                $customer_details                  = $bookingpress_return_data['customer_details'];

                $customer_email = ! empty( $customer_details['customer_email'] ) ? $customer_details['customer_email'] : '';
                $customer_firstname = !empty($customer_details['customer_firstname']) ? $customer_details['customer_firstname'] : $customer_email;
                $customer_lastname = !empty($customer_details['customer_lastname']) ? $customer_details['customer_lastname'] : $customer_email;
                $customer_username = !empty($customer_details['customer_username']) ? $customer_details['customer_username'] : $customer_email;
                $customer_phone = !empty($customer_details['customer_phone']) ? $customer_details['customer_phone'] : '';

                
                $bookingpress_service_name = ! empty( $bookingpress_return_data['selected_package_details']['bookingpress_package_name'] ) ? $bookingpress_return_data['selected_package_details']['bookingpress_package_name'] : __( 'Appointment Booking', 'bookingpress-paypalpro' );

                $custom_var = $entry_id;
                $bookingpress_notify_url = $bookingpress_return_data['notify_url'];
                $redirect_url = $bookingpress_return_data['approved_appointment_url'];
                
                $bookingpress_appointment_status = $BookingPress->bookingpress_get_settings( 'appointment_status', 'general_setting' );
                if ( $bookingpress_appointment_status == '2' ) {
                    $redirect_url = $bookingpress_return_data['pending_appointment_url'];
                }

                $bookingpress_card_holder_name = !empty($bookingpress_return_data['card_details']['card_holder_name']) ? $bookingpress_return_data['card_details']['card_holder_name'] : '';
                $bookingpress_card_number = !empty($bookingpress_return_data['card_details']['card_number']) ? str_replace(' ', '', $bookingpress_return_data['card_details']['card_number']) : '';
                $bookingpress_expire_month = !empty($bookingpress_return_data['card_details']['expire_month']) ? $bookingpress_return_data['card_details']['expire_month'] : '';
                $bookingpress_expire_year = !empty($bookingpress_return_data['card_details']['expire_year']) ? substr($bookingpress_return_data['card_details']['expire_year'], -2) : '';
                $bookingpress_cvv = !empty($bookingpress_return_data['card_details']['cvv']) ? $bookingpress_return_data['card_details']['cvv'] : '';
                
                $bookingpress_paypalpro_endpoint = !empty($this->bookingpress_selected_payment_method) && ( $this->bookingpress_selected_payment_method == 'sandbox') ? 'https://pilot-payflowpro.paypal.com' : 'https://payflowpro.paypal.com';
                $bookingpress_card_expire = $bookingpress_expire_month . $bookingpress_expire_year;

                //Authorizing card details
                $bookingpress_authorize_card_details = array(
                    'TENDER' => 'C',
                    'TRXTYPE' => 'A',
                    'ACCT' => $bookingpress_card_number,
                    'EXPDATE' => $bookingpress_card_expire,
                    'CVV2' => $bookingpress_cvv,
                    'AMT' => $bookingpress_final_payable_amount,
                    'CURRENCY' => $currency_code,
                    'VERBOSITY' => 'MEDIUM',
                    'NAME' => $bookingpress_card_holder_name,
                );

                do_action( 'bookingpress_payment_log_entry', 'paypalpro', 'Paypal pro card authorization details', 'bookingpress pro', $bookingpress_authorize_card_details, $bookingpress_debug_payment_log_id );

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $bookingpress_paypalpro_endpoint);
                curl_setopt($ch, CURLOPT_VERBOSE, 1);
                curl_setopt($ch, CURLOPT_TIMEOUT, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POST, 1);
                $nvpstr_ = "";

                if (is_array($bookingpress_authorize_card_details)) {
                    foreach ($bookingpress_authorize_card_details as $key => $value) {
                        if (is_array($value)) {
                            foreach ($value as $item) {
                                if (strlen($nvpstr_) > 0)
                                    $nvpstr_ .= "&";
                                $nvpstr_ .= "$key=" . $item;
                            }
                        } else {
                            if (strlen($nvpstr_) > 0)
                                $nvpstr_ .= "&";
                            $nvpstr_ .= "$key=" .$value;
                        }
                    }
                }

                $nvpreq_ = "VENDOR=$this->bookingpress_paypalpro_vendor&PARTNER=$this->bookingpress_paypalpro_partner&PWD=$this->bookingpress_paypalpro_api_password&USER=$this->bookingpress_paypalpro_api_username&$nvpstr_";
                do_action( 'bookingpress_payment_log_entry', 'paypalpro', 'Paypal pro card authorization nvpreq', 'bookingpress pro', $nvpreq_, $bookingpress_debug_payment_log_id );

                curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq_);

                $bookingpress_authorized_card_res = curl_exec($ch);
                curl_close($ch);

                $bookingpress_formatted_response = array();
                $httpResponseAr = explode("&", $bookingpress_authorized_card_res);
                foreach ($httpResponseAr as $i => $value) {
                    $tmpAr = explode("=", urldecode($value));
                    if (count($tmpAr) > 1) {
                        $bookingpress_formatted_response[$tmpAr[0]] = $tmpAr[1];
                    }
                }

                do_action( 'bookingpress_payment_log_entry', 'paypalpro', 'Paypal pro card authorization response', 'bookingpress pro', $bookingpress_formatted_response, $bookingpress_debug_payment_log_id );

                if(isset($bookingpress_formatted_response['RESULT']) && ($bookingpress_formatted_response['RESULT'] == "0") && !empty($bookingpress_formatted_response['PNREF']) ){
                    $bookingpress_paypalpro_arr = array(
                        'TENDER' => 'C',
                        'TRXTYPE' => 'D',
                        'ACCT' => $bookingpress_card_number,
                        'EXPDATE' => $bookingpress_card_expire,
                        'CVV2' => $bookingpress_cvv,
                        'AMT' => $bookingpress_final_payable_amount,
                        'CURRENCY' => $currency_code,
                        'VERBOSITY' => 'MEDIUM',
                        'NAME' => $bookingpress_card_holder_name,
                        'AMT' => $bookingpress_final_payable_amount,
                        'ORIGID' => $bookingpress_formatted_response['PNREF'],
                    );

                    do_action( 'bookingpress_payment_log_entry', 'paypalpro', 'Paypal pro payment params', 'bookingpress pro', $bookingpress_paypalpro_arr, $bookingpress_debug_payment_log_id );

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $bookingpress_paypalpro_endpoint);
                    curl_setopt($ch, CURLOPT_VERBOSE, 1);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 0);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_POST, 1);
                    $nvpstr = "";
                    if (is_array($bookingpress_paypalpro_arr)) {
                        foreach ($bookingpress_paypalpro_arr as $key => $value) {
                            if (is_array($value)) {
                                foreach ($value as $item) {
                                    if (strlen($nvpstr) > 0)
                                        $nvpstr .= "&";
                                    $nvpstr .= "$key=" . $item;
                                }
                            } else {
                                if (strlen($nvpstr) > 0)
                                    $nvpstr .= "&";
                                $nvpstr .= "$key=" . $value;
                            }
                        }
                    }
                    $nvpreq = "VENDOR=$this->bookingpress_paypalpro_vendor&PARTNER=$this->bookingpress_paypalpro_partner&PWD=$this->bookingpress_paypalpro_api_password&USER=$this->bookingpress_paypalpro_api_username&$nvpstr";
                    do_action( 'bookingpress_payment_log_entry', 'paypalpro', 'Paypal pro payment nvpreq', 'bookingpress pro', $nvpreq, $bookingpress_debug_payment_log_id );

                    curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);
                    $bookingpress_payment_res = curl_exec($ch);
                    curl_close($ch);
                    $bookingpress_payment_formatted_res = array();
                    $httpResponseAr = explode("&", $bookingpress_payment_res);
                    foreach ($httpResponseAr as $i => $value) {
                        $tmpAr = explode("=", urldecode($value));
                        if (count($tmpAr) > 1) {
                            $bookingpress_payment_formatted_res[$tmpAr[0]] = $tmpAr[1];
                        }
                    }
                    $bookingpress_payment_formatted_res['AMT'] =$bookingpress_paypalpro_arr['AMT'];
                    $bookingpress_payment_formatted_res['CURRENCY'] =$bookingpress_paypalpro_arr['CURRENCY'];

                    do_action( 'bookingpress_payment_log_entry', 'paypalpro', 'Paypal pro payment formatted res', 'bookingpress pro', $bookingpress_payment_formatted_res, $bookingpress_debug_payment_log_id );

                    if(isset($bookingpress_payment_formatted_res['RESPMSG']) && $bookingpress_payment_formatted_res['RESPMSG'] == "Approved" ){
                        $payment_log_id = $bookingpress_pro_payment_gateways->bookingpress_confirm_booking( $entry_id, $bookingpress_payment_formatted_res, '1', 'PNREF', '', 1, $bookingpress_is_cart );

                        $response['variant'] = 'redirect_url';
                        $response['title']         = '';
                        $response['msg']           = '';
                        $response['is_redirect']   = 1;
                        $response['redirect_data'] = $redirect_url;
                        $response['is_transaction_completed'] = 1;
                        $response['entry_id'] = $entry_id;
                    }else{
                        $bookingpress_err_msg = __('Something went wrong while pay with Paypalpro', 'bookingpress-paypalpro');

                        do_action( 'bookingpress_payment_log_entry', 'paypalpro', 'Paypal pro payment error', 'bookingpress pro', $bookingpress_err_msg, $bookingpress_debug_payment_log_id );
                    
                        $response['variant']       = 'error';
                        $response['title']         = esc_html__( 'Error', 'bookingpress-paypalpro' );
                        $response['msg']           = $bookingpress_err_msg;
                        $response['is_redirect']   = 0;
                        $response['redirect_data'] = '';
                        $response['is_transaction_completed'] = 0;
                        $response['is_spam']       = 0;   
                    }
                }else{
                    $bookingpress_err_msg = !empty($bookingpress_formatted_response['RESPMSG']) ? __('Error returned from paypal pro', 'bookingpress-paypalpro').": ".$bookingpress_formatted_response['RESPMSG'] : __('Something went wrong while pay with Paypalpro','bookingpress-paypalpro');

                    do_action( 'bookingpress_payment_log_entry', 'paypalpro', 'Paypal pro card authorization error', 'bookingpress pro', $bookingpress_err_msg, $bookingpress_debug_payment_log_id );
                    
                    $response['variant']       = 'error';
                    $response['title']         = esc_html__( 'Error', 'bookingpress-paypalpro' );
                    $response['msg']           = $bookingpress_err_msg;
                    $response['is_redirect']   = 0;
                    $response['redirect_data'] = '';
                    $response['is_transaction_completed'] = 0;
                    $response['is_spam']       = 0;
                }
                
            }
            return $response;
        }

        function bookingpress_init_paypalpro(){
            global $BookingPress;
            $bookingpress_paypalpro_payment_mode = $BookingPress->bookingpress_get_settings('paypalpro_payment_mode', 'payment_setting');

            $this->bookingpress_selected_payment_method = !empty($bookingpress_paypalpro_payment_mode) ? $bookingpress_paypalpro_payment_mode : 'sandbox';

            $paypalpro_api_username = $BookingPress->bookingpress_get_settings('paypalpro_api_username', 'payment_setting');
            $this->bookingpress_paypalpro_api_username = !empty($paypalpro_api_username) ? $paypalpro_api_username : '';

            $paypalpro_api_password = $BookingPress->bookingpress_get_settings('paypalpro_api_password', 'payment_setting');
            $this->bookingpress_paypalpro_api_password = !empty($paypalpro_api_password) ? $paypalpro_api_password : '';

            $paypalpro_vendor = $BookingPress->bookingpress_get_settings('paypalpro_vendor', 'payment_setting');
            $this->bookingpress_paypalpro_vendor = !empty($paypalpro_vendor) ? $paypalpro_vendor : $this->bookingpress_paypalpro_api_username;

            $paypalpro_partner = $BookingPress->bookingpress_get_settings('paypalpro_partner', 'payment_setting');
            $this->bookingpress_paypalpro_partner = !empty($paypalpro_partner) ? $paypalpro_partner : 'PayPal';
        }

        function bookingpress_paypalpro_submit_form_data_func($response, $bookingpress_return_data){
            global $wpdb, $BookingPress, $bookingpress_pro_payment_gateways, $bookingpress_debug_payment_log_id;
            $this->bookingpress_init_paypalpro();

            do_action( 'bookingpress_payment_log_entry', 'paypalpro', 'Paypal pro submitted form data', 'bookingpress pro', $bookingpress_return_data, $bookingpress_debug_payment_log_id );

            if(!empty($bookingpress_return_data)){
                $entry_id                          = $bookingpress_return_data['entry_id'];
                $bookingpress_is_cart = !empty($bookingpress_return_data['is_cart']) ? 1 : 0;
                $currency_code                     = strtoupper($bookingpress_return_data['currency_code']);
                $bookingpress_final_payable_amount = isset( $bookingpress_return_data['payable_amount'] ) ? ($bookingpress_return_data['payable_amount']) : 0;
                $bookingpress_final_payable_amount = number_format((float)$bookingpress_final_payable_amount, 2, '.', '');
                $customer_details                  = $bookingpress_return_data['customer_details'];

                $customer_email = ! empty( $customer_details['customer_email'] ) ? $customer_details['customer_email'] : '';
                $customer_firstname = !empty($customer_details['customer_firstname']) ? $customer_details['customer_firstname'] : $customer_email;
                $customer_lastname = !empty($customer_details['customer_lastname']) ? $customer_details['customer_lastname'] : $customer_email;
                $customer_username = !empty($customer_details['customer_username']) ? $customer_details['customer_username'] : $customer_email;
                $customer_phone = !empty($customer_details['customer_phone']) ? $customer_details['customer_phone'] : '';

                
                $bookingpress_service_name = ! empty( $bookingpress_return_data['service_data']['bookingpress_service_name'] ) ? $bookingpress_return_data['service_data']['bookingpress_service_name'] : __( 'Appointment Booking', 'bookingpress-paypalpro' );

                $custom_var = $entry_id;
                $bookingpress_notify_url = $bookingpress_return_data['notify_url'];
                $redirect_url = $bookingpress_return_data['approved_appointment_url'];
                
                $bookingpress_appointment_status = $BookingPress->bookingpress_get_settings( 'appointment_status', 'general_setting' );
                if ( $bookingpress_appointment_status == '2' ) {
                    $redirect_url = $bookingpress_return_data['pending_appointment_url'];
                }

                $bookingpress_card_holder_name = !empty($bookingpress_return_data['card_details']['card_holder_name']) ? $bookingpress_return_data['card_details']['card_holder_name'] : '';
                $bookingpress_card_number = !empty($bookingpress_return_data['card_details']['card_number']) ? str_replace(' ', '', $bookingpress_return_data['card_details']['card_number']) : '';
                $bookingpress_expire_month = !empty($bookingpress_return_data['card_details']['expire_month']) ? $bookingpress_return_data['card_details']['expire_month'] : '';
                $bookingpress_expire_year = !empty($bookingpress_return_data['card_details']['expire_year']) ? substr($bookingpress_return_data['card_details']['expire_year'], -2) : '';
                $bookingpress_cvv = !empty($bookingpress_return_data['card_details']['cvv']) ? $bookingpress_return_data['card_details']['cvv'] : '';
                
                $bookingpress_paypalpro_endpoint = !empty($this->bookingpress_selected_payment_method) && ( $this->bookingpress_selected_payment_method == 'sandbox') ? 'https://pilot-payflowpro.paypal.com' : 'https://payflowpro.paypal.com';
                $bookingpress_card_expire = $bookingpress_expire_month . $bookingpress_expire_year;

                //Authorizing card details
                $bookingpress_authorize_card_details = array(
                    'TENDER' => 'C',
                    'TRXTYPE' => 'A',
                    'ACCT' => $bookingpress_card_number,
                    'EXPDATE' => $bookingpress_card_expire,
                    'CVV2' => $bookingpress_cvv,
                    'AMT' => $bookingpress_final_payable_amount,
                    'CURRENCY' => $currency_code,
                    'VERBOSITY' => 'MEDIUM',
                    'NAME' => $bookingpress_card_holder_name,
                );

                do_action( 'bookingpress_payment_log_entry', 'paypalpro', 'Paypal pro card authorization details', 'bookingpress pro', $bookingpress_authorize_card_details, $bookingpress_debug_payment_log_id );

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $bookingpress_paypalpro_endpoint);
                curl_setopt($ch, CURLOPT_VERBOSE, 1);
                curl_setopt($ch, CURLOPT_TIMEOUT, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POST, 1);
                $nvpstr_ = "";

                if (is_array($bookingpress_authorize_card_details)) {
                    foreach ($bookingpress_authorize_card_details as $key => $value) {
                        if (is_array($value)) {
                            foreach ($value as $item) {
                                if (strlen($nvpstr_) > 0)
                                    $nvpstr_ .= "&";
                                $nvpstr_ .= "$key=" . $item;
                            }
                        } else {
                            if (strlen($nvpstr_) > 0)
                                $nvpstr_ .= "&";
                            $nvpstr_ .= "$key=" .$value;
                        }
                    }
                }

                $nvpreq_ = "VENDOR=$this->bookingpress_paypalpro_vendor&PARTNER=$this->bookingpress_paypalpro_partner&PWD=$this->bookingpress_paypalpro_api_password&USER=$this->bookingpress_paypalpro_api_username&$nvpstr_";
                do_action( 'bookingpress_payment_log_entry', 'paypalpro', 'Paypal pro card authorization nvpreq', 'bookingpress pro', $nvpreq_, $bookingpress_debug_payment_log_id );

                curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq_);

                $bookingpress_authorized_card_res = curl_exec($ch);
                curl_close($ch);

                $bookingpress_formatted_response = array();
                $httpResponseAr = explode("&", $bookingpress_authorized_card_res);
                foreach ($httpResponseAr as $i => $value) {
                    $tmpAr = explode("=", urldecode($value));
                    if (count($tmpAr) > 1) {
                        $bookingpress_formatted_response[$tmpAr[0]] = $tmpAr[1];
                    }
                }

                do_action( 'bookingpress_payment_log_entry', 'paypalpro', 'Paypal pro card authorization response', 'bookingpress pro', $bookingpress_formatted_response, $bookingpress_debug_payment_log_id );

                if(isset($bookingpress_formatted_response['RESULT']) && ($bookingpress_formatted_response['RESULT'] == "0") && !empty($bookingpress_formatted_response['PNREF']) ){
                    $bookingpress_paypalpro_arr = array(
                        'TENDER' => 'C',
                        'TRXTYPE' => 'D',
                        'ACCT' => $bookingpress_card_number,
                        'EXPDATE' => $bookingpress_card_expire,
                        'CVV2' => $bookingpress_cvv,
                        'AMT' => $bookingpress_final_payable_amount,
                        'CURRENCY' => $currency_code,
                        'VERBOSITY' => 'MEDIUM',
                        'NAME' => $bookingpress_card_holder_name,
                        'AMT' => $bookingpress_final_payable_amount,
                        'ORIGID' => $bookingpress_formatted_response['PNREF'],
                    );

                    do_action( 'bookingpress_payment_log_entry', 'paypalpro', 'Paypal pro payment params', 'bookingpress pro', $bookingpress_paypalpro_arr, $bookingpress_debug_payment_log_id );

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $bookingpress_paypalpro_endpoint);
                    curl_setopt($ch, CURLOPT_VERBOSE, 1);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 0);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_POST, 1);
                    $nvpstr = "";
                    if (is_array($bookingpress_paypalpro_arr)) {
                        foreach ($bookingpress_paypalpro_arr as $key => $value) {
                            if (is_array($value)) {
                                foreach ($value as $item) {
                                    if (strlen($nvpstr) > 0)
                                        $nvpstr .= "&";
                                    $nvpstr .= "$key=" . $item;
                                }
                            } else {
                                if (strlen($nvpstr) > 0)
                                    $nvpstr .= "&";
                                $nvpstr .= "$key=" . $value;
                            }
                        }
                    }
                    $nvpreq = "VENDOR=$this->bookingpress_paypalpro_vendor&PARTNER=$this->bookingpress_paypalpro_partner&PWD=$this->bookingpress_paypalpro_api_password&USER=$this->bookingpress_paypalpro_api_username&$nvpstr";
                    do_action( 'bookingpress_payment_log_entry', 'paypalpro', 'Paypal pro payment nvpreq', 'bookingpress pro', $nvpreq, $bookingpress_debug_payment_log_id );

                    curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);
                    $bookingpress_payment_res = curl_exec($ch);
                    curl_close($ch);
                    $bookingpress_payment_formatted_res = array();
                    $httpResponseAr = explode("&", $bookingpress_payment_res);
                    foreach ($httpResponseAr as $i => $value) {
                        $tmpAr = explode("=", urldecode($value));
                        if (count($tmpAr) > 1) {
                            $bookingpress_payment_formatted_res[$tmpAr[0]] = $tmpAr[1];
                        }
                    }

                    $bookingpress_payment_formatted_res['AMT'] =$bookingpress_paypalpro_arr['AMT'];
                    $bookingpress_payment_formatted_res['CURRENCY'] =$bookingpress_paypalpro_arr['CURRENCY'];

                    do_action( 'bookingpress_payment_log_entry', 'paypalpro', 'Paypal pro payment formatted res', 'bookingpress pro', $bookingpress_payment_formatted_res, $bookingpress_debug_payment_log_id );

                    if(isset($bookingpress_payment_formatted_res['RESPMSG']) && $bookingpress_payment_formatted_res['RESPMSG'] == "Approved" ){
                        $payment_log_id = $bookingpress_pro_payment_gateways->bookingpress_confirm_booking( $entry_id, $bookingpress_payment_formatted_res, '1', 'PNREF', 'AMT', 1, $bookingpress_is_cart, 'CURRENCY' );

                        $response['variant'] = 'redirect_url';
                        $response['title']         = '';
                        $response['msg']           = '';
                        $response['is_redirect']   = 1;
                        $response['redirect_data'] = $redirect_url;
                        $response['is_transaction_completed'] = 1;
                        $response['entry_id'] = $entry_id;
                    }else{
                        $bookingpress_err_msg = __('Something went wrong while pay with Paypalpro', 'bookingpress-paypalpro');

                        do_action( 'bookingpress_payment_log_entry', 'paypalpro', 'Paypal pro payment error', 'bookingpress pro', $bookingpress_err_msg, $bookingpress_debug_payment_log_id );
                    
                        $response['variant']       = 'error';
                        $response['title']         = esc_html__( 'Error', 'bookingpress-paypalpro' );
                        $response['msg']           = $bookingpress_err_msg;
                        $response['is_redirect']   = 0;
                        $response['redirect_data'] = '';
                        $response['is_transaction_completed'] = 0;
                        $response['is_spam']       = 0;   
                    }
                }else{
                    $bookingpress_err_msg = !empty($bookingpress_formatted_response['RESPMSG']) ? __('Error returned from paypal pro', 'bookingpress-paypalpro').": ".$bookingpress_formatted_response['RESPMSG'] : __('Something went wrong while pay with Paypalpro','bookingpress-paypalpro');

                    do_action( 'bookingpress_payment_log_entry', 'paypalpro', 'Paypal pro card authorization error', 'bookingpress pro', $bookingpress_err_msg, $bookingpress_debug_payment_log_id );
                    
                    $response['variant']       = 'error';
                    $response['title']         = esc_html__( 'Error', 'bookingpress-paypalpro' );
                    $response['msg']           = $bookingpress_err_msg;
                    $response['is_redirect']   = 0;
                    $response['redirect_data'] = '';
                    $response['is_transaction_completed'] = 0;
                    $response['is_spam']       = 0;
                }
                
            }
            return $response;
        }
        function bookingpress_paypalpro_apply_refund_func($response,$bookingpress_refund_data) {
            global $bookingpress_debug_payment_log_id;

            $bookingpress_transaction_id = !empty($bookingpress_refund_data['bookingpress_transaction_id']) ? $bookingpress_refund_data['bookingpress_transaction_id'] :'';

            if(!empty($bookingpress_transaction_id )) {
                $this->bookingpress_init_paypalpro();
                $bookingpress_paypalpro_endpoint = !empty($this->bookingpress_selected_payment_method) && ( $this->bookingpress_selected_payment_method == 'sandbox') ? 'https://pilot-payflowpro.paypal.com' : 'https://payflowpro.paypal.com';

                $bookingpres_refund_type = $bookingpress_refund_data['refund_type'] ? $bookingpress_refund_data['refund_type'] : '';
                if($bookingpres_refund_type != 'full') {                    
                    $bookingpres_refund_amount = $bookingpress_refund_data['refund_amount'] ? $bookingpress_refund_data['refund_amount'] : 0;
                } else {
                    $bookingpres_refund_amount = $bookingpress_refund_data['default_refund_amount'] ? $bookingpress_refund_data['default_refund_amount'] : 0;	
                }

                $bookingpress_refund_currency = $bookingpress_refund_data['bookingpress_payment_currency'];
                try{             
                    $headers = array('Content-Type' => 'application/x-www-form-urlencoded');
                    $body_data = array(
                        'USER' => $this->bookingpress_paypalpro_api_username,
                        'PARTNER' => $this->bookingpress_paypalpro_partner,
                        'PWD' => $this->bookingpress_paypalpro_api_password,
                        'VENDOR' => $this->bookingpress_paypalpro_vendor,
                        'TENDER' => 'C',
                        'TRXTYPE'  => 'C',
                        'AMT' => $bookingpres_refund_amount,
                        'ORIGID' => $bookingpress_transaction_id,
                        'CURRENCY' => $bookingpress_refund_currency,
                    );
                    $body_str = '';
                    $i = 0;
                    foreach( $body_data as $key => $value ){
                        if( 'PWD' == $key ){
                            $body_str .= ($i++ > 0 ? '&' : '') . "$key=" . $value;
                        } else {
                            $body_str .= ($i++ > 0 ? '&' : '') . "$key=" . urlencode($value);
                        }
                    }
                    $headers['Content-Length'] = strlen( $body_str );
                    do_action( 'bookingpress_payment_log_entry', 'paypalpro', 'Paypalpro submited refund data', 'bookingpress pro', $body_data, $bookingpress_debug_payment_log_id ); 
                   $bookingpress_create_refund_response =  wp_remote_post(
                        $bookingpress_paypalpro_endpoint,
                        array(
                            'timeout' => 4500,
                            'headers' => $headers,
                            'body' => $body_str,                            
                            'method' => 'POST',
                        )
                    );
                    do_action( 'bookingpress_payment_log_entry', 'paypalpro', 'Paypalpro response of the refund', 'bookingpress pro',$bookingpress_create_refund_response, $bookingpress_debug_payment_log_id);
					$bookingpress_paypalpro_response = wp_remote_retrieve_body($bookingpress_create_refund_response);
                    $bookingpress_paypalpro_formatted_res = array();
                    $httpResponseAr = explode("&", $bookingpress_paypalpro_response);
                    foreach ($httpResponseAr as $i => $value) {
                        $tmpAr = explode("=", urldecode($value));
                        if (count($tmpAr) > 1) {
                            $bookingpress_paypalpro_formatted_res[$tmpAr[0]] = $tmpAr[1];
                        }
                    }
                    if(!empty($bookingpress_paypalpro_formatted_res['RESPMSG'] && $bookingpress_paypalpro_formatted_res['RESPMSG'] == 'Approved')) {
                        $response['title']   = esc_html__( 'Success', 'bookingpress-paypalpro' );
                        $response['variant'] = 'success';
                        $response['bookingpress_refund_response'] = !empty($bookingpress_create_refund_response) ? $bookingpress_create_refund_response : 
                        0;
                    } else {
                        $error_msg = esc_html__('Sorry! refund could not be processed', 'bookingpress-paypalpro');
                        if(!empty($bookingpress_paypalpro_formatted_res['RESPMSG'])) {
                            $error_msg = $bookingpress_paypalpro_formatted_res['RESPMSG'];
                        }
	                    $response['title']   = esc_html__( 'Error', 'bookingpress-paypalpro' );
						$response['variant'] = 'error';
						$response['msg'] = $error_msg;
					}
               } catch (Exception $e){
                    $error_message = $e->getMessage();
                    do_action( 'bookingpress_payment_log_entry', 'paypalpro', 'paypalpro refund resoponse with error', 'bookingpress pro', $error_message, $bookingpress_debug_payment_log_id);                    
                    $response['title']   = esc_html__( 'Error', 'bookingpress-paypalpro' );
                    $response['variant'] = 'error';
                    $response['msg'] = $error_message;
               }
            }            
            return 	$response;
		}
    }

    global $bookingpress_paypalpro_payment;
	$bookingpress_paypalpro_payment = new bookingpress_paypalpro_payment;
}

?>