<?php

if ( ! class_exists( 'bookingpress_paypal' ) ) {
	class bookingpress_paypal Extends BookingPress_Core {
		var $bookingpress_payment_mode    = '';
		var $bookingpress_is_sandbox_mode = true;
		var $bookingpress_api_username    = '';
		var $bookingpress_api_password    = '';
		var $bookingpress_api_signature   = '';
		var $bookingpress_merchant_email  = '';
		var $bookingpress_gateway_status  = '';

		var $bookingpress_paypal = false;

		function __construct() {
			add_filter( 'bookingpress_paypal_submit_form_data', array( $this, 'bookingpress_submit_form_data' ), 10, 2 );
			add_action( 'wp', array( $this, 'bookingpress_payment_gateway_data' ) );

			//Filter for add payment gateway to revenue filter list
			add_filter('bookingpress_revenue_filter_payment_gateway_list_add', array($this, 'bookingpress_revenue_filter_payment_gateway_list_add_func'));

			add_filter('bookingpress_paypal_apply_refund', array($this, 'bookingpress_paypal_apply_refund_func'),10,2);
		}
		
		/**
		 * Add payment gateway to revenue filter list of Report module
		 *
		 * @param  mixed $bookingpress_revenue_filter_payment_gateway_list
		 * @return void
		 */
		function bookingpress_revenue_filter_payment_gateway_list_add_func($bookingpress_revenue_filter_payment_gateway_list){
			global $BookingPress;

			$bookingpress_is_paypal_enabled = $BookingPress->bookingpress_get_settings('paypal_payment', 'payment_setting');
			if($bookingpress_is_paypal_enabled == '1'){
				$bookingpress_revenue_filter_payment_gateway_list[] = array(
					'value' => 'paypal',
					'text' => esc_html__('PayPal', 'bookingpress-appointment-booking')
				);
			}

			return $bookingpress_revenue_filter_payment_gateway_list;
		}
		
		/**
		 * Initialize paypal configuration
		 *
		 * @return void
		 */
		function arm_init_paypal() {
			$PayPal = false;
			if ( file_exists( BOOKINGPRESS_PRO_LIBRARY_DIR . '/paypal/paypal.class.php' ) ) {
				require_once BOOKINGPRESS_PRO_LIBRARY_DIR . '/paypal/paypal.class.php';

				global $wpdb, $BookingPress;

				$this->bookingpress_payment_mode    = $BookingPress->bookingpress_get_settings( 'paypal_payment_mode', 'payment_setting' );
				$this->bookingpress_is_sandbox_mode = ( $this->bookingpress_payment_mode != 'live' ) ? true : false;
				$this->bookingpress_gateway_status  = $BookingPress->bookingpress_get_settings( 'paypal_payment', 'payment_setting' );
				$this->bookingpress_merchant_email  = $BookingPress->bookingpress_get_settings( 'paypal_merchant_email', 'payment_setting' );
				$this->bookingpress_api_username    = $BookingPress->bookingpress_get_settings( 'paypal_api_username', 'payment_setting' );
				$this->bookingpress_api_password    = $BookingPress->bookingpress_get_settings( 'paypal_api_password', 'payment_setting' );
				$this->bookingpress_api_signature   = $BookingPress->bookingpress_get_settings( 'paypal_api_signature', 'payment_setting' );

				$PayPalConfig = array(
					'Sandbox'      => $this->bookingpress_is_sandbox_mode,
					'APIUsername'  => $this->bookingpress_api_username,
					'APIPassword'  => $this->bookingpress_api_password,
					'APISignature' => $this->bookingpress_api_signature,
				);

				$PayPal = new PayPal( $PayPalConfig );
			}

			return $PayPal;
		}
		
		/**
		 * Function for submit request to payment gateway.
		 *
		 * @param  mixed $return_response
		 * @param  mixed $bookingpress_return_data
		 * @return void
		 */
		function bookingpress_submit_form_data( $return_response, $bookingpress_return_data ) {
			if ( ! empty( $bookingpress_return_data ) ) {
				global $BookingPress,$bookingpress_debug_payment_log_id;

				$paypal_data = $this->arm_init_paypal();

				$entry_id                          = $bookingpress_return_data['entry_id'];
				$currency                          = $bookingpress_return_data['currency'];
				$currency_symbol                   = $BookingPress->bookingpress_get_currency_code( $currency );
				$bookingpress_final_payable_amount = isset( $bookingpress_return_data['payable_amount'] ) ? $bookingpress_return_data['payable_amount'] : 0;
				$customer_details                  = $bookingpress_return_data['customer_details'];
				$customer_email                    = ! empty( $customer_details['customer_email'] ) ? $customer_details['customer_email'] : '';

				$bookingpress_service_name = ! empty( $bookingpress_return_data['service_data']['bookingpress_service_name'] ) ? $bookingpress_return_data['service_data']['bookingpress_service_name'] : __( 'Appointment Booking', 'bookingpress-appointment-booking' );

				$bookingpress_is_cart = !empty($bookingpress_return_data['is_cart']) ? 1 : 0;
				$custom_var = $entry_id."|".$bookingpress_is_cart;

				$sandbox = $this->bookingpress_is_sandbox_mode ? 'sandbox.' : '';

				$notify_url = $bookingpress_return_data['notify_url'];

				$redirect_url = $bookingpress_return_data['approved_appointment_url'];
				$bookingpress_appointment_status = $BookingPress->bookingpress_get_settings( 'appointment_status', 'general_setting' );
				if ( $bookingpress_appointment_status == '2' ) {
					$redirect_url = $bookingpress_return_data['pending_appointment_url'];
				}

				$cancel_url = $bookingpress_return_data['canceled_appointment_url'];

				$booking_form_redirection_mode = !empty($bookingpress_return_data['booking_form_redirection_mode']) ? $bookingpress_return_data['booking_form_redirection_mode'] : 'external_redirection';
		
				$cmd          = '_xclick';
				$paypal_form  = '<form name="_xclick" id="bookingpress_paypal_form" action="https://www.' . $sandbox . 'paypal.com/cgi-bin/webscr" method="post">';
				$paypal_form .= '<input type="hidden" name="cmd" value="' . $cmd . '" />';
				$paypal_form .= '<input type="hidden" name="amount" value="' . $bookingpress_final_payable_amount . '" />';
				$paypal_form .= '<input type="hidden" name="business" value="' . $this->bookingpress_merchant_email . '" />';
				$paypal_form .= '<input type="hidden" name="notify_url" value="' . $notify_url . '" />';
				$paypal_form .= '<input type="hidden" name="cancel_return" value="' . $cancel_url . '" />';
				$paypal_form .= '<input type="hidden" name="return" value="' . $redirect_url . '" />';
				$paypal_form .= '<input type="hidden" name="rm" value="2" />';
				$paypal_form .= '<input type="hidden" name="lc" value="en_US" />';
				$paypal_form .= '<input type="hidden" name="no_shipping" value="1" />';
				$paypal_form .= '<input type="hidden" name="custom" value="' . $custom_var . '" />';
				$paypal_form .= '<input type="hidden" name="on0" value="user_email" />';
				$paypal_form .= '<input type="hidden" name="os0" value="' . $customer_email . '" />';
				$paypal_form .= '<input type="hidden" name="currency_code" value="' . $currency_symbol . '" />';
				$paypal_form .= '<input type="hidden" name="page_style" value="primary" />';
				$paypal_form .= '<input type="hidden" name="charset" value="UTF-8" />';
				$paypal_form .= '<input type="hidden" name="item_name" value="' . $bookingpress_service_name . '" />';
				$paypal_form .= '<input type="hidden" name="item_number" value="1" />';
				$paypal_form .= '<input type="submit" value="Pay with PayPal!" />';
				$paypal_form .= '</form>';				
								
				do_action( 'bookingpress_payment_log_entry', 'paypal', 'payment form redirected data', 'bookingpress pro', $paypal_form, $bookingpress_debug_payment_log_id );

				$paypal_form .= '<script type="text/javascript">window.app.bookingpress_is_display_external_html = false; document.getElementById("bookingpress_paypal_form").submit();</script>';

				$return_response['variant']       = 'redirect';
				$return_response['title']         = '';
				$return_response['msg']           = '';
				$return_response['is_redirect']   = 1;
				$return_response['redirect_data'] = $paypal_form;
				if($booking_form_redirection_mode == "in-built"){
					$return_response['is_transaction_completed'] = 1;
				}
				$return_response['entry_id']      = $entry_id;
			}
			return $return_response;
		}
		
		/**
		 * Paypal webhook URL handle function
		 *
		 * @return void
		 */
		function bookingpress_payment_gateway_data() {
			global $wpdb, $BookingPress, $bookingpress_pro_payment_gateways, $bookingpress_debug_payment_log_id;
			if ( ! empty( $_REQUEST['bookingpress-listener'] ) && ( $_REQUEST['bookingpress-listener'] == 'bpa_pro_paypal_url' ) ) {
				$bookingpress_webhook_data = $_REQUEST;
				do_action( 'bookingpress_payment_log_entry', 'paypal', 'Paypal Webhook Data', 'bookingpress pro', $bookingpress_webhook_data, $bookingpress_debug_payment_log_id );
				if ( ! empty( $bookingpress_webhook_data ) && !empty($_POST['txn_id']) && ! empty( $bookingpress_webhook_data['custom'] ) ) { // phpcs:ignore
					$req = 'cmd=_notify-validate';
                    foreach ($_POST as $key => $value) { // phpcs:ignore
                        $value = urlencode(stripslashes($value));
                        $req .= "&$key=$value";
                    }

                    $request = new WP_Http();
                    /* For HTTP1.0 Request */
                    $requestArr = array(
                        "sslverify" => false,
                        "ssl" => true,
                        "body" => $req,
                        "timeout" => 20,
                    );
                    /* For HTTP1.1 Request */
                    $requestArr_1_1 = array(
                        "httpversion" => '1.1',
                        "sslverify" => false,
                        "ssl" => true,
                        "body" => $req,
                        "timeout" => 20,
                    );
                    $response = array();

                    $bookingpress_payment_mode    = $BookingPress->bookingpress_get_settings('paypal_payment_mode', 'payment_setting');
                    $bookingpress_is_sandbox_mode = ( $bookingpress_payment_mode != 'live' ) ? true : false;

                    if($bookingpress_is_sandbox_mode){
                        $url = "https://www.sandbox.paypal.com/cgi-bin/webscr/";
                        $response_1_1 = $request->post($url, $requestArr_1_1);

                        if (!is_wp_error($response_1_1) && $response_1_1['body'] == 'VERIFIED') {
                            $response = $response_1_1;
                        } else {
                            $response = $request->post($url, $requestArr);
                        }  
                    }else{
                        $url = "https://www.paypal.com/cgi-bin/webscr/";
                        $response_1_0 = $request->post($url, $requestArr);
                        if (!is_wp_error($response_1_0) && $response_1_0['body'] == 'VERIFIED') {
                            $response = $response_1_0;
                        } else {
                            $response = $request->post($url, $requestArr_1_1);
                        }
                    }

                    do_action('bookingpress_payment_log_entry', 'paypal', 'PayPal Webhook Verified Data', 'bookingpress pro', $response, $bookingpress_debug_payment_log_id);

					if (!is_wp_error($response) && $response['body'] == 'VERIFIED' && !empty($_POST['txn_type']) && ($_POST['txn_type'] == 'web_accept') ) { // phpcs:ignore
						$custom_var       = $bookingpress_webhook_data['custom'];
						$custom_var_arr = explode('|', $custom_var);
						$entry_id = !empty($custom_var_arr[0]) ? intval($custom_var_arr[0]) : 0;
						$bookingpress_is_cart = !empty($custom_var_arr[1]) ? intval($custom_var_arr[1]) : 0;
						$payment_status = ! empty( $bookingpress_webhook_data['payment_status'] ) ? strtolower(sanitize_text_field( $bookingpress_webhook_data['payment_status'] )) : '1';					
						
						if($payment_status == 'Completed'){
			                            $payment_status = '1';
			                        }else if($payment_status == 'Pending'){
			                            $payment_status = '2';
			                        }else{
			                            $payment_status = '1';
			                        }

						$payer_email    = ! empty( $bookingpress_webhook_data['payer_email'] ) ? sanitize_email( $bookingpress_webhook_data['payer_email'] ) : '';
						$bookingpress_webhook_data['bookingpress_payer_email'] = $payer_email;
						$bookingpress_webhook_data                             = array_map( array( $BookingPress, 'appointment_sanatize_field' ), $bookingpress_webhook_data );
						$payment_log_id                                        = $bookingpress_pro_payment_gateways->bookingpress_confirm_booking( $entry_id, $bookingpress_webhook_data, $payment_status, 'txn_id', '', 1, $bookingpress_is_cart );
					}
				}
			}
		}
		
		/**
		 * bookingpress_paypal_apply_refund_func
		 *
		 * @return void
		 */
		function bookingpress_paypal_apply_refund_func($response,$bookingpress_refund_data) {
			global $bookingpress_debug_payment_log_id;

		   $bookingpress_transaction_id = !empty($bookingpress_refund_data['bookingpress_transaction_id']) ? $bookingpress_refund_data['bookingpress_transaction_id'] :'';

		   if(!empty($bookingpress_transaction_id ) && !empty($bookingpress_refund_data['refund_type'])) {						   
			   try{
					$payment_data = $this->arm_init_paypal();					
					$bookingpress_send_refund_data = array(
						'RTFields' => array(
							'TRANSACTIONID' => $bookingpress_transaction_id,
						),
					);
					$bookingpres_refund_type = $bookingpress_refund_data['refund_type'] ? $bookingpress_refund_data['refund_type'] : '';
					if($bookingpres_refund_type != 'full') {                    
						$bookingpres_refund_amount = $bookingpress_refund_data['refund_amount'] ? $bookingpress_refund_data['refund_amount'] : 0;
						$bookingpress_send_refund_data['RTFields']['AMT'] = ((float)$bookingpres_refund_amount);
						$bookingpress_send_refund_data['RTFields']['REFUNDTYPE'] = ucfirst($bookingpres_refund_type);
					}
					do_action( 'bookingpress_payment_log_entry', 'paypal', 'Paypal submited refund data', 'bookingpress pro', $bookingpress_send_refund_data, $bookingpress_debug_payment_log_id );

					$bookingpress_create_refund_response = $payment_data->RefundTransaction($bookingpress_send_refund_data);

					do_action( 'bookingpress_payment_log_entry', 'paypal', 'Paypal response of the refund', 'bookingpress pro', $bookingpress_create_refund_response, $bookingpress_debug_payment_log_id);

					if(!is_wp_error( $bookingpress_create_refund_response ) && !empty($bookingpress_create_refund_response['REFUNDTRANSACTIONID']) && !empty( $bookingpress_create_refund_response['ACK'] ) && 'success' == strtolower( $bookingpress_create_refund_response['ACK'] ) ) {
						$response['title']   = esc_html__( 'Success', 'bookingpress-appointment-booking' );
						$response['variant'] = 'success';
						$response['bookingpress_refund_response'] = !empty($bookingpress_create_refund_response) ? $bookingpress_create_refund_response : '';
					} else {
						$response['variant'] = 'error';
						$response['title']   = esc_html__( 'Error', 'bookingpress-appointment-booking' );
						$response['msg'] = !empty( $bookingpress_create_refund_response['L_LONGMESSAGE0'] ) ? esc_html__('Error Code', 'bookingpress-appointment-booking').':'.$bookingpress_create_refund_response['L_ERRORCODE0'] . ' '. $bookingpress_create_refund_response['L_LONGMESSAGE0'] : esc_html__('Sorry! refund could not be processed', 'bookingpress-appointment-booking');
					}
			  } catch (Exception $e){
				   $error_message = $e->getMessage();
				   do_action( 'bookingpress_payment_log_entry', 'paypal', 'Paypal refund resoponse with error', 'bookingpress pro', $error_message, $bookingpress_debug_payment_log_id);                    
				   $response['title']   = esc_html__( 'Error', 'bookingpress-appointment-booking' );
				   $response['variant'] = 'error';
				   $response['msg'] = $error_message;
			  }
		   }            
		   return 	$response;
	   }
	}

	global $bookingpress_paypal;
	$bookingpress_paypal = new bookingpress_paypal();
}
