<?php
if ( ! class_exists( 'bookingpress_pro_settings' ) ) {
	class bookingpress_pro_settings Extends BookingPress_Core {

		function __construct() {
			add_filter( 'bookingpress_modify_settings_view_file_path', array( $this, 'bookingpress_modify_settings_view_file_path_func' ), 10 );
			add_filter( 'bookingpress_add_setting_dynamic_data_fields', array( $this, 'bookingpress_add_setting_dynamic_data_fields_func' ), 10 );
			add_action( 'boookingpress_after_save_settings_data', array( $this, 'boookingpress_after_save_settings_data' ) );
			add_action( 'bookingpress_settings_add_dynamic_on_load_method', array( $this, 'bookingpress_settings_add_dynamic_on_load_method_func' ) );
			add_action( 'bookingpress_dynamic_get_settings_data', array( $this, 'bookingpress_dynamic_get_settings_data_func' ) );
			add_action( 'bookingpress_add_setting_dynamic_vue_methods', array( $this, 'bookingpress_add_setting_dynamic_vue_methods_func' ) );

			//add_action( 'bookingpress_dynamic_add_method_for_pagination_size_change', array( $this, 'bookingpress_dynamic_add_method_for_pagination_size_change_func' ) );
			//add_action( 'bookingpress_dynamic_add_method_for_pagination_length_change', array( $this, 'bookingpress_dynamic_add_method_for_pagination_length_change_func' ) );

			//add_action( 'wp_ajax_bookingpress_view_debug_integration_log', array( $this, 'bookingpress_view_debug_integration_log_func' ), 10 );
			add_action( 'wp_ajax_bookingpress_save_default_special_days', array( $this, 'bookingpress_save_default_special_days_func' ), 10 );
			add_action( 'wp_ajax_bookingpress_get_default_special_day_details', array( $this, 'bookingpress_get_default_special_day_details_func' ) );
			add_action( 'bookingpress_add_settings_more_postdata', array( $this, 'bookingpress_add_settings_more_postdata_func' ), 10 );
			add_filter( 'bookingpress_modify_get_settings_data', array( $this, 'bookingpress_modify_get_settings_data_func' ), 10, 2 );
			add_action( 'wp_ajax_bookingpress_validate_special_days', array( $this, 'bookingpress_validate_special_days_func' ), 10, 2 );

			// Hook for check special day added or not for daysoff
			add_filter('bookingpress_modify_default_daysoff_details', array($this, 'bookingpress_modify_default_daysoff_for_specialdays_func'), 10, 3);

			add_action( 'wp_ajax_bookingpress_format_special_days_data', array( $this, 'bookingpress_format_special_days_data_func' ), 10 );

			//add_action( 'bookingpress_customer_setting_selected_tab', array( $this, 'bookingpress_customer_setting_selected_tab_func') );
			add_action( 'bookingpress_get_settings_details_response', array( $this, 'bookingpress_customer_settings_ajax_response_data' ) );
			add_action( 'bookingpress_settings_response', array( $this, 'bookingpress_settings_response_fuc' ) );

			add_action( 'wp_ajax_verify_customer_field_meta_key', array( $this, 'verify_customer_field_meta_key_callback' ) );
			add_action( 'wp_ajax_bookingpress_check_field_deletion', array( $this, 'bookingpress_check_field_deletion_callback' ) );

			add_action( 'bookingpress_general_daysoff_validation',array($this,'bookingpress_general_daysoff_validation_func') ); 

			add_action('bookingpress_settings_dynamic_on_load_methods',array($this,'bookingpress_settings_dynamic_on_load_methods_func'));

			//add_action( 'bookingpress_license_dynamic_view_load', array( $this, 'bookingpress_load_license_view_func' ) );

			//add_action( 'admin_init',array($this,'edd_sample_register_option') );

			add_action( 'wp_ajax_bpa_validate_and_activate_license_key',array($this,'bpa_validate_and_activate_license_key') );

			add_action( 'wp_ajax_bpa_dectivate_license_key',array($this,'bpa_dectivate_license_key') );

			add_action( 'wp_ajax_bpa_refresh_license_key',array($this,'bpa_refresh_license_key') );

			add_action('wp_ajax_bookingpress_upload_company_icon', array( $this, 'bookingpress_upload_company_icon_func' ), 10);

			add_filter('bookingpress_modify_save_setting_data',array($this,'bookingpress_modify_save_setting_data_func'),10,2);
			
			//add_action( 'init',array($this,'bpa_validate_license_key') );

		}

		function bookingpress_upload_company_icon_func(){
			$return_data = array(
                'error'            => 0,
                'msg'              => '',
                'upload_url'       => '',
                'upload_file_name' => '',
            );

            $bpa_check_authorization = $this->bpa_check_authentication( 'upload_company_icon', true, 'bookingpress_upload_company_icon' );
            
            if( preg_match( '/error/', $bpa_check_authorization ) ){
                $bpa_auth_error = explode( '^|^', $bpa_check_authorization );
                $bpa_error_msg = !empty( $bpa_auth_error[1] ) ? $bpa_auth_error[1] : esc_html__( 'Sorry. Something went wrong while processing the request', 'bookingpress-appointment-booking');

                $response['variant'] = 'error';
                $response['title'] = esc_html__( 'Error', 'bookingpress-appointment-booking');
                $response['msg'] = $bpa_error_msg;

                wp_send_json( $response );
                die;
            }
         
            $bookingpress_fileupload_obj = new bookingpress_fileupload_class($_FILES['file']); // phpcs:ignore

            if (! $bookingpress_fileupload_obj ) {
                $return_data['error'] = 1;
                $return_data['msg']   = $bookingpress_fileupload_obj->error_message;
            }

            $bookingpress_fileupload_obj->check_cap          = true;
            $bookingpress_fileupload_obj->check_nonce        = true;
            $bookingpress_fileupload_obj->nonce_data         = isset($_REQUEST['_wpnonce']) ? sanitize_text_field($_REQUEST['_wpnonce']) : '';
            $bookingpress_fileupload_obj->nonce_action       = isset($_REQUEST['action']) ? sanitize_text_field($_REQUEST['action']) : '';
            $bookingpress_fileupload_obj->check_only_image   = true;
            $bookingpress_fileupload_obj->check_specific_ext = false;
            $bookingpress_fileupload_obj->allowed_ext        = array();

            $file_name                = current_time('timestamp') . '_' . isset($_FILES['file']['name']) ? sanitize_file_name($_FILES['file']['name']) : '';
            $upload_dir               = BOOKINGPRESS_TMP_IMAGES_DIR . '/';
            $upload_url               = BOOKINGPRESS_TMP_IMAGES_URL . '/';
            $bookingpress_destination = $upload_dir . $file_name;

            $upload_file = $bookingpress_fileupload_obj->bookingpress_process_upload($bookingpress_destination);
            if ($upload_file == false ) {
                $return_data['error'] = 1;
                $return_data['msg']   = ! empty($upload_file->error_message) ? $upload_file->error_message : esc_html__('Something went wrong while updating the file', 'bookingpress-appointment-booking');
            } else {
                $return_data['error']            = 0;
                $return_data['msg']              = '';
                $return_data['upload_url']       = $upload_url . $file_name;
                $return_data['upload_file_name'] = isset($_FILES['file']['name']) ? sanitize_file_name($_FILES['file']['name']) : '';
            }

            echo json_encode($return_data);
            exit();
		}

		function bookingpress_settings_dynamic_on_load_methods_func(){
			$selected_tab_name  = ! empty($_REQUEST['setting_page']) ? sanitize_text_field($_REQUEST['setting_page']) : 'general_settings';                        			
			?>
			if(vm.bookingpress_tab_list != undefined && vm.bookingpress_tab_list != '') {
				vm.bookingpress_tab_list.forEach(function(item,index,arr) {				
					if(index == 0) {
						vm.bpa_integration_active_tab = item.tab_value;
					}
				});
			}
			if(vm.bookingpress_optin_tab_list != undefined && vm.bookingpress_optin_tab_list != '') {
				vm.bookingpress_optin_tab_list.forEach(function(item,index,arr) {				
					if(index == 0) {
						vm.bpa_optin_active_tab = item.tab_value;
					}
				});
			}			
			<?php						
			if($selected_tab_name == 'optin_settings')  {
				$setting_tab  = ! empty($_REQUEST['setting_tab']) ? sanitize_text_field($_REQUEST['setting_tab']) : '';;
				if(!empty($setting_tab)) {					
					?>
					vm.bpa_optin_active_tab = '<?php echo esc_html( $setting_tab ) ?>'; 
					<?php
                }
			}
            if($selected_tab_name == 'integration_settings')  {
                $setting_tab  = ! empty($_REQUEST['setting_tab']) ? sanitize_text_field($_REQUEST['setting_tab']) : '';;
                if(!empty($setting_tab)) {					
					?>
					vm.bpa_integration_active_tab = '<?php echo esc_html( $setting_tab ) ?>'; 
					<?php
                }
            }

			if( $selected_tab_name == "staffmembers_settings" ){
			?>
				vm.selected_tab_name = "staffmembers_settings";
				vm.getSettingsData('staffmember_setting', 'staffmembers_settings_form');
			<?php
			}
		}

		function bpa_refresh_license_key()
		{
			$response = array();
			$bpa_check_authorization = $this->bpa_check_authentication( 'refresh_license_key', true, 'bpa_license_verify_nonce' );           
			if( preg_match( '/error/', $bpa_check_authorization ) ){
				$bpa_auth_error = explode( '^|^', $bpa_check_authorization );
				$bpa_error_msg = !empty( $bpa_auth_error[1] ) ? $bpa_auth_error[1] : esc_html__( 'Sorry. Something went wrong while processing the request', 'bookingpress-appointment-booking');

				$response['variant'] = 'error';
				$response['title'] = esc_html__( 'Error', 'bookingpress-appointment-booking');
				$response['msg'] = $bpa_error_msg;

				wp_send_json( $response );
				die;
			}
			$store_url = BOOKINGPRESS_STORE_URL;
			
			$license = trim( get_option( 'bkp_license_key' ) );

			$package = trim( get_option( 'bkp_license_package' ) );
			$api_params = array(
				'edd_action' => 'check_license',
				'license' => $license,
				'item_id'  => $package,
				'url' => home_url()
			);

			$response = wp_remote_post( $store_url, array( 'body' => $api_params, 'timeout' => 15 ) );
			if ( is_wp_error( $response ) ) {
						$response['variant'] = 'error';
						$response['title']   = esc_html__( 'Error', 'bookingpress-appointment-booking' );
						$response['msg']     = esc_html__( 'Something went wrong while processing this request' , 'bookingpress-appointment-booking' );
						echo wp_json_encode( $response );
						die();
			}

			$license_data = json_decode( wp_remote_retrieve_body( $response ) );
			$license_data_string =  wp_remote_retrieve_body( $response );

			$message = '';
			if ( true === $license_data->success ) {

				switch( $license_data->license ) {

					case 'expired' :

						$message = sprintf(
							/* translators: 1. Expiry date for license */
							esc_html__( 'Your license key expired on %s.', 'bookingpress-appointment-booking' ),
							date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
						);
						break;

					case 'disabled' :

						$message = esc_html__( 'Your license key has been disabled.', 'bookingpress-appointment-booking' );
						break;

					case 'key_mismatch' :
						/* translators: 1. Plugin Name */
						$message = sprintf( esc_html__( 'This appears to be an invalid license key for %s.', 'bookingpress-appointment-booking' ), $license_data->item_name );
						break;

					case 'invalid_item_id' :
					case 'invalid' :
					case 'site_inactive' :

						$message = esc_html__( 'Your license is not active for this URL.', 'bookingpress-appointment-booking' );
						break;

					case 'item_name_mismatch' :
						/* translators: 1. Plugin Name */
						$message = sprintf( esc_html__( 'This appears to be an invalid license key for %s.', 'bookingpress-appointment-booking' ), $license_data->item_name );
						break;
				}

			}

			if($message != ""){				
				update_option( 'bkp_license_invalid_license_type', $license_data->license );
				update_option( 'bkp_license_invalid_license_message', $message );
				update_option( 'bkp_license_data_validate_response', $license_data_string );
				
				$response['variant'] = 'error';
				$response['title']   = esc_html__( 'error', 'bookingpress-appointment-booking' );
				$response['msg']     = esc_html__( 'Sorry! It seems that license is not renewed yet.' , 'bookingpress-appointment-booking' );
				echo wp_json_encode( $response );
				die();

			}
			else if($license_data->license == "valid")
			{
				update_option( 'bkp_license_data_activate_response', $license_data_string );

				delete_option( 'bkp_license_invalid_license_type' );
				delete_option( 'bkp_license_invalid_license_message' );
				delete_option( 'bkp_license_data_validate_response' );

				$response['variant'] = 'success';
				$response['title']   = esc_html__( 'success', 'bookingpress-appointment-booking' );
				$response['msg']     = esc_html__( 'License refreshed successfully' , 'bookingpress-appointment-booking' );
				echo wp_json_encode( $response );
				die();
			}

						
		}


		function bpa_validate_license_key()
		{
			global $bookingpress_slugs;
			$license_page_slug = admin_url('admin.php?page='.$bookingpress_slugs->bookingpress_license);

			$store_url = BOOKINGPRESS_STORE_URL;
			
			$license = trim( get_option( 'bkp_license_key' ) );
			$package = trim( get_option( 'bkp_license_package' ) );

			$message = '';	

			if("" === $license || "" === $package)
			{
				$message = sprintf( esc_html__( 'It seems that your BookingPress License is not active, to activate the License, click %shere%s.', 'bookingpress-appointment-booking' ), '<a href="'.$license_page_slug.'">', '</a>' ); //phpcs:ignore
				return $message;
			}

			return $message;

			$api_params = array(
				'edd_action' => 'check_license',
				'license' => $license,
				'item_id'  => $package,
				//'item_name' => urlencode( $item_name ),
				'url' => home_url()
			);
			$response = wp_remote_post( $store_url, array( 'body' => $api_params, 'timeout' => 15 ) );
			if ( is_wp_error( $response ) ) {
				return false;
			}

			$license_data = json_decode( wp_remote_retrieve_body( $response ) );
			$license_data_string =  wp_remote_retrieve_body( $response );					

			if ( true === $license_data->success ) {

				switch( $license_data->license ) {				

					case 'expired' :

						$message = sprintf(
							/* translators: 1. Expiry date of license */
							esc_html__( 'Your license key expired on %s.', 'bookingpress-appointment-booking' ),
							date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
						);
						break;

					case 'disabled' :

						$message = esc_html__( 'Your license key has been disabled.', 'bookingpress-appointment-booking' );
						break;

					case 'key_mismatch' :
						/* translators: 1. Item Name */
						$message = sprintf( esc_html__( 'This appears to be an invalid license key for %s.', 'bookingpress-appointment-booking' ), $license_data->item_name );
						break;

					case 'invalid_item_id' :
					case 'invalid' :
					case 'site_inactive' :

						$message = esc_html__( 'Your license is not active for this URL.', 'bookingpress-appointment-booking' );
						break;

					case 'item_name_mismatch' :
						/* translators: 1. Item Name */
						$message = sprintf( esc_html__( 'This appears to be an invalid license key for %s.', 'bookingpress-appointment-booking' ),$license_data->item_name);
						break;
				}

			}

			if($message != ""){				
				update_option( 'bkp_license_invalid_license_type', $license_data->license );
				update_option( 'bkp_license_invalid_license_message', $message );
				update_option( 'bkp_license_data_validate_response', $license_data_string );
			}
			else if($license_data->license == "valid")
			{
				$message = "";
				update_option( 'bkp_license_data_activate_response', $license_data_string );
			}
			
			return $message;
		}

		function bpa_dectivate_license_key(){
			
			global $wpdb, $bookingpress_global_options, $BookingPress;

			$response                    = array();
			$bpa_check_authorization = $this->bpa_check_authentication( 'deactivate_license_key', true, 'bpa_license_verify_nonce' );           
			if( preg_match( '/error/', $bpa_check_authorization ) ){
				$bpa_auth_error = explode( '^|^', $bpa_check_authorization );
				$bpa_error_msg = !empty( $bpa_auth_error[1] ) ? $bpa_auth_error[1] : esc_html__( 'Sorry. Something went wrong while processing the request', 'bookingpress-appointment-booking');

				$response['variant'] = 'error';
				$response['title'] = esc_html__( 'Error', 'bookingpress-appointment-booking');
				$response['msg'] = $bpa_error_msg;

				wp_send_json( $response );
				die;
			}

			//$posted_license_key = isset( $_REQUEST['license_key'] ) ? sanitize_text_field( $_REQUEST['license_key'] ) : '';

			// retrieve the license from the database
			$posted_license_key = trim( get_option( 'bkp_license_key' ) );

			$package = trim( get_option( 'bkp_license_package' ) );
			
			// data to send in our API request
			$api_params = array(
				'edd_action' => 'deactivate_license',
				'license'    => $posted_license_key,
				'item_id'  => $package,
				//'item_name'  => urlencode( BOOKINGPRESS_ITEM_NAME ), // the name of our product in EDD
				'url'        => home_url()
			);

			// Call the custom API.
			$callback_response = wp_remote_post( BOOKINGPRESS_STORE_URL, array( 'timeout' => 15, 'body' => $api_params ) );

			// make sure the response came back okay
			if ( is_wp_error( $callback_response ) || 200 !== wp_remote_retrieve_response_code( $callback_response ) ) {

				$message =  ( is_wp_error( $callback_response ) && ! empty( $callback_response->get_error_message() ) ) ? $callback_response->get_error_message() : __( 'An error occurred, please try again.', 'bookingpress-appointment-booking' );

			} else {

				$license_data = json_decode( wp_remote_retrieve_body( $callback_response ) );
				$license_data_string = wp_remote_retrieve_body( $callback_response );

				if ( false === $license_data->success ) {

					switch( $license_data->error ) {

						case 'invalid' :
						default :

							$message = __( 'An error occurred, please try again.', 'bookingpress-appointment-booking' );
							break;
					}

					// Check if anything passed on a message constituting a failure
					if ( ! empty( $message ) ) {

						$response['variant'] = 'error';
						$response['title']   = esc_html__( 'Error', 'bookingpress-appointment-booking' );
						$response['msg']     = $message;
						echo wp_json_encode( $response );
						die();
					}
				}
				else
				{
					if($license_data->license === "deactivated")
					{
						delete_option( 'bkp_license_key');
						delete_option( 'bkp_license_package');
						delete_option( 'bkp_license_status');	

						update_option( 'bkp_license_data_deactivate_response', $license_data_string );

						$response['variant'] = 'success';
						$response['title']   = esc_html__( 'Success', 'bookingpress-appointment-booking' );
						$response['msg']     = esc_html__( 'License deactivated successfully.', 'bookingpress-appointment-booking' );
					} else if($license_data->license === "failed")
					{
						update_option( 'bkp_license_data_deactivate_response', $license_data_string );

						$response['variant'] = 'error';
						$response['title']   = esc_html__( 'Error', 'bookingpress-appointment-booking' );
						$response['msg']     = urlencode( $message );
					}	
					echo wp_json_encode( $response );
					exit;
				}

			}
		}



		function bpa_validate_and_activate_license_key(){
			
			global $wpdb, $bookingpress_global_options, $BookingPress;

			$response                    = array();
			
			$bpa_check_authorization = $this->bpa_check_authentication( 'validate_activate_license', true, 'bpa_license_verify_nonce' );           
			if( preg_match( '/error/', $bpa_check_authorization ) ){
				$bpa_auth_error = explode( '^|^', $bpa_check_authorization );
				$bpa_error_msg = !empty( $bpa_auth_error[1] ) ? $bpa_auth_error[1] : esc_html__( 'Sorry. Something went wrong while processing the request', 'bookingpress-appointment-booking');

				$response['variant'] = 'error';
				$response['title'] = esc_html__( 'Error', 'bookingpress-appointment-booking');
				$response['msg'] = $bpa_error_msg;

				wp_send_json( $response );
				die;
			}

			$posted_license_key = isset( $_REQUEST['license_key'] ) ? sanitize_text_field( $_REQUEST['license_key'] ) : '';
			$posted_license_package = isset( $_REQUEST['license_package'] ) ? sanitize_text_field( $_REQUEST['license_package'] ) : '';
			
			//$posted_license_package = '4534'; // STRIPE ADDON PRODUCT ID

			// retrieve the license from the database
			//$license = trim( get_option( 'edd_sample_license_key' ) );

			// data to send in our API request
			$api_params = array(
				'edd_action' => 'activate_license',
				'license'    => $posted_license_key,
				'item_id'  => $posted_license_package,
				//'item_name'  => urlencode( BOOKINGPRESS_ITEM_NAME ), // the name of our product in EDD
				'url'        => home_url()
			);

			// Call the custom API.
			$response = wp_remote_post( BOOKINGPRESS_STORE_URL, array( 'timeout' => 15, 'body' => $api_params ) );

			// make sure the response came back okay
			if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

				$message =  ( is_wp_error( $response ) && ! empty( $response->get_error_message() ) ) ? $response->get_error_message() : __( 'An error occurred, please try again.', 'bookingpress-appointment-booking' );

			} else {

				$license_data = json_decode( wp_remote_retrieve_body( $response ) );
				$license_data_string = wp_remote_retrieve_body( $response );

				if ( false === $license_data->success ) {

					switch( $license_data->error ) {

						case 'expired' :

							$message = sprintf(
								/* translators: Expiry date of license */
								__( 'Your license key expired on %s.', 'bookingpress-appointment-booking' ),
								date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
							);
							break;

						case 'revoked' :

							$message = __( 'Your license key has been disabled.', 'bookingpress-appointment-booking' );
							break;

						case 'missing' :

							$message = __( 'Invalid license.', 'bookingpress-appointment-booking' );
							break;

						case 'invalid' :
						case 'site_inactive' :

							$message = __( 'Your license is not active for this URL.', 'bookingpress-appointment-booking' );
							break;

						case 'item_name_mismatch' :

							$message = __('This appears to be an invalid license key for your selected package.' , 'bookingpress-appointment-booking');
							break;
						
						case 'invalid_item_id' :

								$message = __('This appears to be an invalid license key for your selected package.' , 'bookingpress-appointment-booking');
								break;

						case 'no_activations_left':

							$message = __( 'Your license key has reached its activation limit.', 'bookingpress-appointment-booking' );
							break;

						default :

							$message = __( 'An error occurred, please try again.', 'bookingpress-appointment-booking' );
							break;
					}

				}

			}

			// Check if anything passed on a message constituting a failure
			if ( ! empty( $message ) ) {
				//$base_url = admin_url( 'plugins.php?page=' . EDD_SAMPLE_PLUGIN_LICENSE_PAGE );
				//$redirect = add_query_arg( array( 'sl_activation' => 'false', 'message' => urlencode( $message ) ), $base_url );

				//wp_redirect( $redirect );
				//exit();

				$response['variant'] = 'error';
				$response['title']   = esc_html__( 'Error', 'bookingpress-appointment-booking' );
				$response['msg']     = esc_html__( $message, 'bookingpress-appointment-booking' ); // phpcs:ignore
				echo wp_json_encode( $response );
				die();
			}

			// $license_data->license will be either "valid" or "invalid"

			if($license_data->license === "valid")
			{
				update_option( 'bkp_license_key', $posted_license_key );
				update_option( 'bkp_license_package', $posted_license_package );
				update_option( 'bkp_license_status', $license_data->license );
			}
			
			update_option( 'bkp_license_data_activate_response', $license_data_string );
			update_option( 'bkp_license_status', $license_data->license );
			//wp_redirect( admin_url( 'plugins.php?page=' . EDD_SAMPLE_PLUGIN_LICENSE_PAGE ) );
			//exit();

			$response['variant'] = 'success';
			$response['title']   = esc_html__( 'Success', 'bookingpress-appointment-booking' );
			$response['msg']     = esc_html__( 'License activated successfully.', 'bookingpress-appointment-booking' );
			
			echo wp_json_encode( $response );
			exit;

		}
		function bookingpress_general_daysoff_validation_func() {
			global $bookingpress_notification_duration;
			?>
			if(vm.special_day_data_arr != undefined && vm.special_day_data_arr != '' ) {                            
				vm.special_day_data_arr.forEach(function(item, index, arr){								                            
					if(item.special_day_start_date <= vm.days_off_form.selected_date && item.special_day_end_date >= vm.days_off_form.selected_date) {
						is_exit = 1;
						vm.$notify({
							title: '<?php esc_html_e( 'Error', 'bookingpress-appointment-booking' ); ?>',
							message: '<?php esc_html_e( 'Special day already exists.', 'bookingpress-appointment-booking' ); ?>',
							type: 'error',
							customClass: 'error_notification',
							duration:<?php echo intval( $bookingpress_notification_duration ); ?>,
						});                                
					}
				});
			}
			<?php
		}

		function bookingpress_format_special_days_data_func() {
			global $wpdb, $bookingpress_global_options, $BookingPress;

			$response                    = array();
			$bpa_check_authorization = $this->bpa_check_authentication( 'format_settings_special_days', true, 'bpa_wp_nonce' );           
			if( preg_match( '/error/', $bpa_check_authorization ) ){
				$bpa_auth_error = explode( '^|^', $bpa_check_authorization );
				$bpa_error_msg = !empty( $bpa_auth_error[1] ) ? $bpa_auth_error[1] : esc_html__( 'Sorry. Something went wrong while processing the request', 'bookingpress-appointment-booking');

				$response['variant'] = 'error';
				$response['title'] = esc_html__( 'Error', 'bookingpress-appointment-booking');
				$response['msg'] = $bpa_error_msg;

				wp_send_json( $response );
				die;
			}

			$response['daysoff_details'] = '';
			$bookingpress_global_settings = $bookingpress_global_options->bookingpress_global_options();
			$bookingpress_date_format     = $bookingpress_global_settings['wp_default_date_format'];
			$bookingpress_time_format     = $bookingpress_global_settings['wp_default_time_format'];

			$bookingpress_special_days_data = ! empty( $_POST['special_days_data'] ) ? array_map( array( $BookingPress, 'appointment_sanatize_field' ), $_POST['special_days_data'] ) : array(); // phpcs:ignore
			if ( ! empty( $bookingpress_special_days_data ) && is_array( $bookingpress_special_days_data ) ) {
				foreach ( $bookingpress_special_days_data as $k => $v ) {
					$bookingpress_special_days_data[ $k ]['special_day_formatted_start_date'] = date( $bookingpress_date_format, strtotime( $v['special_day_start_date'] ) );
					$bookingpress_special_days_data[ $k ]['special_day_formatted_end_date']   = date( $bookingpress_date_format, strtotime( $v['special_day_end_date'] ) );
					$bookingpress_special_days_data[ $k ]['formatted_start_time']             = date( $bookingpress_time_format, strtotime( $v['start_time'] ) );
					$bookingpress_special_days_data[ $k ]['formatted_end_time']               = date( $bookingpress_time_format, strtotime( $v['end_time'] ) );

					if ( ! empty( $v['special_day_workhour'] ) ) {
						foreach ( $v['special_day_workhour'] as $k2 => $v2 ) {
							$bookingpress_special_days_data[ $k ]['special_day_workhour'][ $k2 ]['formatted_start_time'] = date( $bookingpress_time_format, strtotime( $v2['start_time'] ) );
							$bookingpress_special_days_data[ $k ]['special_day_workhour'][ $k2 ]['formatted_end_time']   = date( $bookingpress_time_format, strtotime( $v2['end_time'] ) );
						}
					}
				}

				$response['variant']         = 'success';
				$response['title']           = esc_html__( 'Success', 'bookingpress-appointment-booking' );
				$response['msg']             = esc_html__( 'Details formatted successfully', 'bookingpress-appointment-booking' );
				$response['special_days_details'] = $bookingpress_special_days_data;
			}
			echo wp_json_encode( $response );
			exit;
		}

		function bookingpress_modify_default_daysoff_for_specialdays_func( $default_daysoff_details, $booking_date, $booking_time ) {
			global $wpdb, $BookingPress, $tbl_bookingpress_default_special_day;
			$current_date = date('Y-m-d', current_time('timestamp') ) . ' 00:00:00';
			$get_default_special_days = $wpdb->get_results( $wpdb->prepare( "SELECT bookingpress_special_day_start_date FROM {$tbl_bookingpress_default_special_day} WHERE bookingpress_special_day_start_date >= %s", $current_date ) ); // phpcs:ignore 
			
			if( !empty( $get_default_special_days ) ){
				
				foreach( $get_default_special_days as $k => $v ){
					$special_day_date = $v->bookingpress_special_day_start_date;
					$special_day_date_formatted = date('c', strtotime($special_day_date));
					
					if( in_array( $special_day_date_formatted, $default_daysoff_details ) ){
						$special_day_key = array_search( $special_day_date_formatted, $default_daysoff_details );
						if( '' !== $special_day_key ){
							unset( $default_daysoff_details[ $special_day_key ] );
						}
					}
				}
			}

			return $default_daysoff_details;
		}

		function bookingpress_modify_get_settings_data_func( $bookingpress_setting_return_data, $bookingpress_posted_data ) {
		
			if( 'customer_setting' == $bookingpress_posted_data['setting_type'] ){
				global $wpdb, $tbl_bookingpress_form_fields;

				$bookingpress_field_settings_data = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM `{$tbl_bookingpress_form_fields}` WHERE bookingpress_is_customer_field = %d ORDER BY bookingpress_field_position ASC", 1 ), ARRAY_A );  // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_form_fields is table name.


				$bookingpress_field_settings_return_data = array();

				if( !empty( $bookingpress_field_settings_data ) ){
					
					$bookingpress_field_settings_data        = apply_filters('bookingpress_modify_field_data_before_prepare', $bookingpress_field_settings_data, true);
					foreach( $bookingpress_field_settings_data as $bookingpress_field_setting_key => $bookingpress_field_setting_val ){
						$bookingpress_field_type = '';
						if ($bookingpress_field_setting_val['bookingpress_form_field_name'] == 'fullname' ) {
							$bookingpress_field_type = 'Text';
						} elseif ($bookingpress_field_setting_val['bookingpress_form_field_name'] == 'firstname' ) {
							$bookingpress_field_type = 'Text';
						} elseif ($bookingpress_field_setting_val['bookingpress_form_field_name'] == 'lastname' ) {
							$bookingpress_field_type = 'Text';
						} elseif ($bookingpress_field_setting_val['bookingpress_form_field_name'] == 'email_address' ) {
							$bookingpress_field_type = 'Email';
						} elseif ($bookingpress_field_setting_val['bookingpress_form_field_name'] == 'phone_number' ) {
							$bookingpress_field_type = 'Dropdown';
						} elseif ($bookingpress_field_setting_val['bookingpress_form_field_name'] == 'note' ) {
							$bookingpress_field_type = 'Textarea';
						} else {
							$bookingpress_field_type = $bookingpress_field_setting_val['bookingpress_field_type'];
						}

						$bookingpress_draggable_field_setting_fields_tmp                   = array();
						$bookingpress_draggable_field_setting_fields_tmp['id']             = intval($bookingpress_field_setting_val['bookingpress_form_field_id']);
						$bookingpress_draggable_field_setting_fields_tmp['field_name']     = $bookingpress_field_setting_val['bookingpress_form_field_name'];
						$bookingpress_draggable_field_setting_fields_tmp['field_type']     = $bookingpress_field_type;
						$bookingpress_draggable_field_setting_fields_tmp['is_edit']        = false;
						$bookingpress_draggable_field_setting_fields_tmp['is_required']    = ( $bookingpress_field_setting_val['bookingpress_field_required'] == 0 ) ? false : true;
						$bookingpress_draggable_field_setting_fields_tmp['label']          = stripslashes_deep($bookingpress_field_setting_val['bookingpress_field_label']);
						$bookingpress_draggable_field_setting_fields_tmp['placeholder']    = stripslashes_deep($bookingpress_field_setting_val['bookingpress_field_placeholder']);
						$bookingpress_draggable_field_setting_fields_tmp['error_message']  = stripslashes_deep($bookingpress_field_setting_val['bookingpress_field_error_message']);
						$bookingpress_draggable_field_setting_fields_tmp['is_hide']        = ( $bookingpress_field_setting_val['bookingpress_field_is_hide'] == 0 ) ? false : true;
						$bookingpress_draggable_field_setting_fields_tmp['field_position'] = floatval($bookingpress_field_setting_val['bookingpress_field_position']);
						

						$bookingpress_draggable_field_setting_fields_tmp = apply_filters('bookingpress_modify_field_data_before_load', $bookingpress_draggable_field_setting_fields_tmp, $bookingpress_field_setting_val);

						array_push($bookingpress_field_settings_return_data, $bookingpress_draggable_field_setting_fields_tmp);
					}	
				}

				$bookingpress_setting_return_data['customer_field_settings'] = $bookingpress_field_settings_return_data;

			}
			if( 'payment_setting' == $bookingpress_posted_data['setting_type'] && isset($bookingpress_setting_return_data['bookingpress_partial_refund_rules'])){
				global 	$bookingpress_pro_global_options;								
				$bookingpress_setting_return_data['is_disaply_payment_refund_note'] = 0;

				if(!empty($bookingpress_setting_return_data['bookingpress_refund_on_cancellation']) && $bookingpress_setting_return_data['bookingpress_refund_on_cancellation'] == 1) {								
					$bookingpress_setting_return_data['is_disaply_payment_refund_note'] = 1;					
				}
				if(!empty($bookingpress_setting_return_data['bookingpress_partial_refund_rules'])) {					
		            $bookingpress_setting_return_data['bookingpress_partial_refund_rules'] = maybe_unserialize($bookingpress_setting_return_data['bookingpress_partial_refund_rules']);
				} else  {
					$bookingpress_setting_return_data['bookingpress_partial_refund_rules'] = array();
				}
			}			
			return $bookingpress_setting_return_data;
		}

		function bookingpress_customer_settings_ajax_response_data(){
			?>
				if( 'customer_setting' == settingType ){
					if( typeof response.data.data.customer_field_settings != 'undefined' ){
						vm.customer_field_settings = response.data.data.customer_field_settings;

						if( 0 < vm.bpa_deleted_fields.length ){
							vm.bpa_deleted_fields.length = [];
						}
					}
				}
				if('payment_setting' == settingType ){
					vm.is_disaply_payment_refund_note = response.data.data.is_disaply_payment_refund_note;
				}
			<?php
		}

		function bookingpress_settings_response_fuc(){
			?>
			if( 'customer_setting_form' == form_name && 'customer_setting' == setting_type ){
				vm.getSettingsData( setting_type, form_name );
			} else if( 'license_form' == form_name && 'license' == setting_type ){
				vm.getSettingsData( setting_type, form_name );
			}
			<?php
		}

		function bookingpress_add_settings_more_postdata_func() {
			global $bookingpress_notification_duration;
			?>

				if( 'customer_setting_form' == form_name ){
					saveFormData.customer_field_settings = vm.customer_field_settings
					saveFormData.deletedFields = vm.bpa_deleted_fields;
				}
				if( 'payment_setting_form' == form_name ) {
					if(vm.payment_setting_form.bookingpress_refund_mode == 'partial' && vm.payment_setting_form.bookingpress_partial_refund_rules.length == 0) {
						vm.is_disabled = false
						vm.is_display_save_loader = '0'
						vm.$notify({
							title: '<?php esc_html_e('Error', 'bookingpress-appointment-booking'); ?>',
							message: '<?php esc_html_e('Please add at least one rule for partial refund paymemt.', 'bookingpress-appointment-booking'); ?>',
							type: 'error',
							customClass: 'error_notification',
							duration:<?php echo intval($bookingpress_notification_duration); ?>,
						}); 
						return false;
					}
				}
			<?php
		}

		function bookingpress_check_field_deletion_callback(){
			global $wpdb, $tbl_bookingpress_form_fields;

			$response = array();

			$bpa_check_authorization = $this->bpa_check_authentication( 'check_settings_field_deletion', true, 'bpa_wp_nonce' );           
			if( preg_match( '/error/', $bpa_check_authorization ) ){
				$bpa_auth_error = explode( '^|^', $bpa_check_authorization );
				$bpa_error_msg = !empty( $bpa_auth_error[1] ) ? $bpa_auth_error[1] : esc_html__( 'Sorry. Something went wrong while processing the request', 'bookingpress-appointment-booking');

				$response['variant'] = 'error';
				$response['title'] = esc_html__( 'Error', 'bookingpress-appointment-booking');
				$response['msg'] = $bpa_error_msg;

				wp_send_json( $response );
				die;
			}

			$deleted_field_id = !empty( $_POST['del_field_id'] ) ? intval( $_POST['del_field_id'] ) : 0; // phpcs:ignore
			if( empty( $deleted_field_id ) ){
				$response['variant']  = 'success';
				$response['title'] = esc_html__( 'Success', 'bookingpress-appointment-booking' );
				$response['msg'] = esc_html__( 'Field deleted successfully', 'bookingpress-appointment-booking' );
				echo wp_json_encode( $response );
				die;
			}

			$bpa_field_meta_key = $wpdb->get_var( $wpdb->prepare( "SELECT bookingpress_field_meta_key FROM `{$tbl_bookingpress_form_fields}` WHERE bookingpress_form_field_id = %d", $deleted_field_id ) );  // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_form_fields is table name.
			
			$is_appointment_field = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(bookingpress_form_field_id) FROM `{$tbl_bookingpress_form_fields}` WHERE bookingpress_field_meta_key = %s AND bookingpress_is_customer_field = %d", $bpa_field_meta_key, 0 ) );  // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_form_fields is table name.

			if( 0 < $is_appointment_field ){
				$response['variant'] = 'warning';
				$response['title'] = esc_html__( 'Warning', 'bookingpress-appointment-booking' );
				$response['msg'] = esc_html__( 'The field is already mapped with appointment booking form. Do you wish to delete this?', 'bookingpress-appointment-booking' );
				echo wp_json_encode( $response );
				die;
			}

			$response['variant']  = 'success';
			$response['title'] = esc_html__( 'Success', 'bookingpress-appointment-booking' );
			$response['msg'] = esc_html__( 'Field deleted successfully', 'bookingpress-appointment-booking' );
			echo wp_json_encode( $response );
			die;
		}

		function verify_customer_field_meta_key_callback(){
			global $wpdb, $tbl_bookingpress_form_fields;

			$response = array();

			$bpa_check_authorization = $this->bpa_check_authentication( 'verify_settings_customer_field_meta_key', true, 'bpa_wp_nonce' );           
			if( preg_match( '/error/', $bpa_check_authorization ) ){
				$bpa_auth_error = explode( '^|^', $bpa_check_authorization );
				$bpa_error_msg = !empty( $bpa_auth_error[1] ) ? $bpa_auth_error[1] : esc_html__( 'Sorry. Something went wrong while processing the request', 'bookingpress-appointment-booking');

				$response['variant'] = 'error';
				$response['title'] = esc_html__( 'Error', 'bookingpress-appointment-booking');
				$response['msg'] = $bpa_error_msg;

				wp_send_json( $response );
				die;
			}

			$field_meta_key = !empty( $_REQUEST['meta_key'] ) ? sanitize_text_field( $_REQUEST['meta_key'] ) : '';

			if( !empty( $field_meta_key ) ){
				$field_id = !empty( $_REQUEST['field_id'] ) ? intval( $_REQUEST['field_id'] ) : 0;
				$bpa_meta_key_exists = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(bookingpress_form_field_id) FROM `{$tbl_bookingpress_form_fields}` WHERE bookingpress_field_meta_key = %s AND bookingpress_is_customer_field = %d AND bookingpress_form_field_id != %d", $field_meta_key, 1, $field_id ) );  // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_form_fields	 is table name.

				if( 0 < $bpa_meta_key_exists ){
					$response['variant'] = 'error';
					$response['title'] = esc_html__( 'Error', 'bookingpress-appointment-booking' );
					$response['msg'] = esc_html__( 'Meta key already exists. Please select another value for meta key.', 'bookingpress-appointment-booking' );
					echo wp_json_encode( $response );
					die;
				}
			}
			$response['variant']  = 'success';
			$response['title']   = esc_html__( 'Success', 'bookingpress-appointment-booking' );
			$response['msg']     = esc_html__( 'Meta key is available', 'bookingpress-appointment-booking' );
			echo wp_json_encode( $response );
			die;
		}

		function bookingpress_save_default_special_days_func() {
			global $wpdb,$tbl_bookingpress_default_special_day, $tbl_bookingpress_default_special_day_breaks, $BookingPress;
			$response              = array();
			$bpa_check_authorization = $this->bpa_check_authentication( 'save_settings_default_special_days', true, 'bpa_wp_nonce' );           
			if( preg_match( '/error/', $bpa_check_authorization ) ){
				$bpa_auth_error = explode( '^|^', $bpa_check_authorization );
				$bpa_error_msg = !empty( $bpa_auth_error[1] ) ? $bpa_auth_error[1] : esc_html__( 'Sorry. Something went wrong while processing the request', 'bookingpress-appointment-booking');

				$response['variant'] = 'error';
				$response['title'] = esc_html__( 'Error', 'bookingpress-appointment-booking');
				$response['msg'] = $bpa_error_msg;

				wp_send_json( $response );
				die;
			}
			if ( ! empty( $_REQUEST['action'] ) && ( sanitize_text_field( $_REQUEST['action'] ) == 'bookingpress_save_default_special_days' ) ) {
				$wpdb->query( "DELETE FROM {$tbl_bookingpress_default_special_day}" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_default_special_day is a table name. false alarm
				$wpdb->query( "DELETE FROM {$tbl_bookingpress_default_special_day_breaks}" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_default_special_day_breaks is a table name. false alarm
				if ( ! empty( $_REQUEST['special_day_details'] ) ) {
					$bookingpress_special_days_details = ! empty( $_POST['special_day_details'] ) ? array_map( array( $BookingPress, 'appointment_sanatize_field' ), $_POST['special_day_details'] ) : array(); // phpcs:ignore

					if ( ! empty( $bookingpress_special_days_details ) && is_array( $bookingpress_special_days_details ) ) {

						foreach ( $bookingpress_special_days_details as $k => $v ) {
							$bookingpress_special_day_start_date = date('Y-m-d', strtotime($v['special_day_start_date']));
							$bookingpress_special_day_end_date   = date('Y-m-d', strtotime($v['special_day_end_date']));

							$bookingpress_workhour_start_time = $v['start_time'];
							$bookingpress_workhour_end_time   = $v['end_time'];

							$bookingpress_existing_special_day = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$tbl_bookingpress_default_special_day} WHERE bookingpress_special_day_start_date = %s AND bookingpress_special_day_end_date = %s AND bookingpress_special_day_start_time = %s AND bookingpress_special_day_end_time = %s", $bookingpress_special_day_start_date, $bookingpress_special_day_end_date, $bookingpress_workhour_end_time, $bookingpress_workhour_end_time ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_default_special_day is a table name. false alarm

							$bookingpress_existing_special_day_id = 0;
							if ( ! empty( $bookingpress_existing_special_day ) ) {
								$bookingpress_existing_special_day_id = $bookingpress_existing_special_day['bookingpress_special_day_id'];
							}

							$bookingpress_db_fields = array(
								'bookingpress_special_day_start_date' => $bookingpress_special_day_start_date,
								'bookingpress_special_day_end_date' => $bookingpress_special_day_end_date,
								'bookingpress_special_day_start_time' => $bookingpress_workhour_start_time,
								'bookingpress_special_day_end_time' => $bookingpress_workhour_end_time,
							);

							if ( ! empty( $bookingpress_existing_special_day_id ) ) {
								// Update existing special days
								$wpdb->update( $tbl_bookingpress_default_special_day, $bookingpress_db_fields, array( 'bookingpress_special_day_id' => $bookingpress_existing_special_day_id ) );
							} else {
								// Insert new special days
								$wpdb->insert( $tbl_bookingpress_default_special_day, $bookingpress_db_fields );
								$bookingpress_existing_special_day_id = $wpdb->insert_id;
							}

							$bookingpress_special_days_break_times = ! empty( $v['special_day_workhour'] ) ? $v['special_day_workhour'] : array();
							if ( ! empty( $bookingpress_special_days_break_times ) && is_array( $bookingpress_special_days_break_times ) && ! empty( $bookingpress_existing_special_day_id ) ) {

								foreach ( $bookingpress_special_days_break_times as $k2 => $v2 ) {
									if ( ! empty( $v2['start_time'] && ! empty( $v2['end_time'] ) ) ) {
										$bookingpress_special_day_break_fields = array(
											'bookingpress_special_day_id' => $bookingpress_existing_special_day_id,
											'bookingpress_special_day_break_start_time' => $v2['start_time'],
											'bookingpress_special_day_break_end_time' => $v2['end_time'],
										);

										$wpdb->insert( $tbl_bookingpress_default_special_day_breaks, $bookingpress_special_day_break_fields );
									}
								}
							}
						}
					}
				}
				$response['variant'] = 'success';
				$response['title']   = esc_html__( 'Success', 'bookingpress-appointment-booking' );
				$response['msg']     = esc_html__( 'Special days settings updated successfully.', 'bookingpress-appointment-booking' );
			}

			wp_cache_delete( 'bookingpress_all_general_settings' );
			wp_cache_delete( 'bookingpress_all_customize_settings' );

			echo wp_json_encode( $response );
			exit;
		}

		function bookingpress_get_default_special_day_details_func() {
			global $wpdb, $tbl_bookingpress_default_special_day, $bookingpress_global_options, $tbl_bookingpress_default_special_day_breaks;
			$response              = array();
			$bpa_check_authorization = $this->bpa_check_authentication( 'get_settings_default_special_days', true, 'bpa_wp_nonce' );           
			if( preg_match( '/error/', $bpa_check_authorization ) ){
				$bpa_auth_error = explode( '^|^', $bpa_check_authorization );
				$bpa_error_msg = !empty( $bpa_auth_error[1] ) ? $bpa_auth_error[1] : esc_html__( 'Sorry. Something went wrong while processing the request', 'bookingpress-appointment-booking');

				$response['variant'] = 'error';
				$response['title'] = esc_html__( 'Error', 'bookingpress-appointment-booking');
				$response['msg'] = $bpa_error_msg;

				wp_send_json( $response );
				die;
			}
			$response['special_day_data']          = array();
			$response['disabled_special_day_data'] = array();
			$response['msg']                       = esc_html__( 'Something went wrong.', 'bookingpress-appointment-booking' );
			$response['title']                     = esc_html__( 'Error', 'bookingpress-appointment-booking' );
			$response['variant']                   = 'error';

			if ( ! empty( $_REQUEST['action'] ) && sanitize_text_field( $_REQUEST['action'] == 'bookingpress_get_default_special_day_details' ) ) {
				$bookingpress_global_settings = $bookingpress_global_options->bookingpress_global_options();
				$bookingpress_date_format     = $bookingpress_global_settings['wp_default_date_format'];
				$bookingpress_time_format     = $bookingpress_global_settings['wp_default_time_format'];

				$bookingpress_special_day      = array();
				$bookingpress_special_day_data = $wpdb->get_results( "SELECT * FROM {$tbl_bookingpress_default_special_day}", ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_default_special_day is a table name. false alarm

				if ( ! empty( $bookingpress_special_day_data ) ) {

					foreach ( $bookingpress_special_day_data as $special_day_key => $special_day ) {

						$special_day_arr        = $special_days_breaks = array();
						$special_day_date       = ! empty( $special_day['bookingpress_special_day_date'] ) ? sanitize_text_field( $special_day['bookingpress_special_day_date'] ) : '';
						$special_day_start_date = ! empty( $special_day['bookingpress_special_day_start_date'] ) ? sanitize_text_field( $special_day['bookingpress_special_day_start_date'] ) : '';
						$special_day_end_date   = ! empty( $special_day['bookingpress_special_day_end_date'] ) ? sanitize_text_field( $special_day['bookingpress_special_day_end_date'] ) : '';

						$bookingpress_special_day_details = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM ' . $tbl_bookingpress_default_special_day . ' WHERE bookingpress_special_day_start_date LIKE %s AND bookingpress_special_day_end_date LIKE %s ORDER BY bookingpress_special_day_id ASC', '%' . $special_day_start_date . '%', '%' . $special_day_end_date . '%' ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared --Reason: $tbl_bookingpress_default_special_day is a table name. false alarm

						if ( ! empty( $bookingpress_special_day_details ) ) {
							foreach ( $bookingpress_special_day_details as $k => $v ) {
								$special_day_id = ! empty( $v['bookingpress_special_day_id'] ) ? intval( $v['bookingpress_special_day_id'] ) : '';

								$special_day_arr['id']                               = $special_day_id;
								$special_day_arr['special_day_start_date']           = date( 'Y-m-d', strtotime( $special_day_start_date ) );
								$special_day_arr['special_day_formatted_start_date'] = date( $bookingpress_date_format, strtotime( $special_day_start_date ) );
								$special_day_arr['special_day_end_date']             = date( 'Y-m-d', strtotime( $special_day_end_date ) );
								$special_day_arr['special_day_formatted_end_date']   = date( $bookingpress_date_format, strtotime( $special_day_end_date ) );

								$special_day_arr['start_time']           = $v['bookingpress_special_day_start_time'];
								$special_day_arr['formatted_start_time'] = date( $bookingpress_time_format, strtotime( $v['bookingpress_special_day_start_time'] ) );
								$special_day_arr['end_time']             = $v['bookingpress_special_day_end_time'];
								$special_day_arr['formatted_end_time']   = date( $bookingpress_time_format, strtotime( $v['bookingpress_special_day_end_time'] ) );

								// Fetch all breaks associated with special day
								$bookingpress_special_days_break = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$tbl_bookingpress_default_special_day_breaks} WHERE bookingpress_special_day_id = %d", $special_day_id ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_default_special_day_breaks is a table name. false alarm

								if ( ! empty( $bookingpress_special_days_break ) && is_array( $bookingpress_special_days_break ) ) {
									foreach ( $bookingpress_special_days_break as $k3 => $v3 ) {
										$break_start_time = ! empty( $v3['bookingpress_special_day_break_start_time'] ) ? sanitize_text_field( $v3['bookingpress_special_day_break_start_time'] ) : '';
										$break_end_time   = ! empty( $v3['bookingpress_special_day_break_end_time'] ) ? sanitize_text_field( $v3['bookingpress_special_day_break_end_time'] ) : '';

										$special_days_break_data                         = array();
										$special_days_break_data['id']                   = $v3['bookingpress_special_day_break_id'];
										$special_days_break_data['start_time']           = $break_start_time;
										$special_days_break_data['end_time']             = $break_end_time;
										$special_days_break_data['formatted_start_time'] = date( $bookingpress_time_format, strtotime( $break_start_time ) );
										$special_days_break_data['formatted_end_time']   = date( $bookingpress_time_format, strtotime( $break_end_time ) );

										$special_days_breaks[] = $special_days_break_data;
									}
								}
							}
						}

						$special_day_arr['special_day_workhour'] = $special_days_breaks;
						$bookingpress_special_day[]              = $special_day_arr;
					}
				}
				$response['msg']                       = esc_html__( 'Special Day data retrieved successfully.', 'bookingpress-appointment-booking' );
				$response['special_day_data']          = $bookingpress_special_day;
				$response['variant']                   = 'success';
				$response['title']                     = esc_html__( 'Success', 'bookingpress-appointment-booking' );
			}

			echo wp_json_encode( $response );
			exit;
		}

		function bookingpress_validate_special_days_func() {
			global $wpdb,$tbl_bookingpress_appointment_bookings, $bookingpress_settings;
			$response              = array();
			$bpa_check_authorization = $this->bpa_check_authentication( 'validate_settings_special_days', true, 'bpa_wp_nonce' );           
			if( preg_match( '/error/', $bpa_check_authorization ) ){
				$bpa_auth_error = explode( '^|^', $bpa_check_authorization );
				$bpa_error_msg = !empty( $bpa_auth_error[1] ) ? $bpa_auth_error[1] : esc_html__( 'Sorry. Something went wrong while processing the request', 'bookingpress-appointment-booking');

				$response['variant'] = 'error';
				$response['title'] = esc_html__( 'Error', 'bookingpress-appointment-booking');
				$response['msg'] = $bpa_error_msg;

				wp_send_json( $response );
				die;
			}

			if ( ! empty( $_REQUEST['selected_date'] ) ) {
				$bookingpress_search_date = sanitize_text_field( $_REQUEST['selected_date'] );
				$bookingpress_start_date  = date( 'Y-m-d', strtotime( sanitize_text_field( $_REQUEST['selected_date'][0] ) ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated --Reason: data is validated above
				$bookingpress_end_date    = date( 'Y-m-d', strtotime( sanitize_text_field( $_REQUEST['selected_date'][1] ) ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated --Reason: data is validated above

				$bookingpress_status = array( '1', '2' );

				$total_appointments              = 0;
				$bookingpress_search_query_where = 'WHERE 1=1 ';
				if ( ! empty( $bookingpress_start_date ) && ! empty( $bookingpress_end_date ) ) {
					$bookingpress_search_query_where .= " AND (bookingpress_appointment_date BETWEEN '{$bookingpress_start_date}' AND '{$bookingpress_end_date}')";
				}
				if ( ! empty( $bookingpress_status ) && is_array( $bookingpress_status ) ) {
					$bookingpress_search_query_where .= ' AND (';
					$i                                = 0;
					foreach ( $bookingpress_status as $status_key => $status_value ) {
						if ( $i != 0 ) {
							$bookingpress_search_query_where .= ' OR';
						}
						$bookingpress_search_query_where .= " bookingpress_appointment_status ='{$status_value}'";
						$i++;
					}
					$bookingpress_search_query_where .= ' )';
				}

				$total_appointments = $wpdb->get_var( "SELECT COUNT(bookingpress_appointment_booking_id) FROM {$tbl_bookingpress_appointment_bookings} {$bookingpress_search_query_where}" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_appointment_bookings is a table name. false alarm

				if ( $total_appointments > 0 ) {
					$response['variant'] = 'warnning';
					$response['title']   = esc_html__( 'Warning', 'bookingpress-appointment-booking' );
					$response['msg']     = esc_html__( 'one or more appointments are already booked this time duration still you want to add the Special day','bookingpress-appointment-booking' );
				} else {
					$response['variant'] = 'success';
					$response['title']   = esc_html__( 'success', 'bookingpress-appointment-booking' );
					$response['msg']     = '';
				}
			}

			echo wp_json_encode( $response );
			exit;
		}

		function bookingpress_get_total_appointment_by_date( $date = '', $status = array() ) {

			global $wpdb,$tbl_bookingpress_appointment_bookings;

			$total_appointments              = '';
			$bookingpress_search_query_where = 'WHERE 1=1 ';
			if ( ! empty( $date ) ) {
				$bookingpress_search_query_where .= " AND (bookingpress_appointment_date = '{$date}')";
			}
			if ( ! empty( $status ) && is_array( $status ) ) {
				$bookingpress_search_query_where .= ' AND (';
				$i                                = 0;
				foreach ( $status as $status_key => $status_value ) {
					if ( $i != 0 ) {
						$bookingpress_search_query_where .= ' OR';
					}
					$bookingpress_search_query_where .= " bookingpress_appointment_status ='{$status_value}'";
					$i++;
				}
				$bookingpress_search_query_where .= ' )';
			}

			$total_appointments = $wpdb->get_var( "SELECT COUNT(bookingpress_appointment_booking_id) FROM {$tbl_bookingpress_appointment_bookings} {$bookingpress_search_query_where}" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_appointment_bookings is a table name. false alarm

			return $total_appointments;

		}

		function bookingpress_add_setting_dynamic_vue_methods_func() {
			global $bookingpress_notification_duration;
			?> 
				bookingpress_upload_company_icon_func(response, file, fileList){
					const vm2 = this
					if(response != ''){
						vm2.company_setting_form.company_icon_url = response.upload_url
						vm2.company_setting_form.company_icon_img = response.upload_file_name
					}
				},
				bookingpress_company_icon_upload_limit(files, fileList){
					const vm2 = this
					if(files.length >= 1){
						vm2.$notify({
							title: '<?php esc_html_e('Error', 'bookingpress-appointment-booking'); ?>',
							message: '<?php esc_html_e('Multiple files not allowed', 'bookingpress-appointment-booking'); ?>',
							type: 'error',
							customClass: 'error_notification',
							duration:<?php echo intval($bookingpress_notification_duration); ?>,
						});
					}
				},
				bookingpress_company_icon_upload_err(err, file, fileList){
					const vm2 = this
					var bookingpress_err_msg = '<?php esc_html_e('Something went wrong', 'bookingpress-appointment-booking'); ?>';
					if(err != '' || err != undefined){
						bookingpress_err_msg = err
					}
					vm2.$notify({
						title: '<?php esc_html_e('Error', 'bookingpress-appointment-booking'); ?>',
						message: bookingpress_err_msg,
						type: 'error',
						customClass: 'error_notification',
						duration:<?php echo intval($bookingpress_notification_duration); ?>,
					});
				},
				bookingpress_remove_company_icon(){
					const vm = this
					var upload_url = vm.company_setting_form.company_icon_url                     
					var upload_filename = vm.company_setting_form.company_icon_img 

					var postData = { action:'bookingpress_remove_company_avatar',upload_file_url: upload_url,_wpnonce:'<?php echo esc_html(wp_create_nonce('bpa_wp_nonce')); ?>' };
					axios.post( appoint_ajax_obj.ajax_url, Qs.stringify( postData ) )
					.then( function (response) {                    
						vm.company_setting_form.company_icon_url = ''
						vm.company_setting_form.company_icon_img = ''
						vm.$refs.iconRef.clearFiles()
					}.bind(vm) )
					.catch( function (error) {
						console.log(error);
					});
				},
				checkUploadedIconFile(file){
					const vm2 = this
					if(file.type != 'image/jpeg' && file.type != 'image/png' && file.type != 'image/webp'){
						vm2.$notify({
							title: '<?php esc_html_e('Error', 'bookingpress-appointment-booking'); ?>',
							message: '<?php esc_html_e('Please upload jpg/png/webp file only', 'bookingpress-appointment-booking'); ?>',
							type: 'error',
							duration:<?php echo intval($bookingpress_notification_duration); ?>,
						});
						return false
					}else{
						var bpa_image_size = parseInt(file.size / 500000);
						if(bpa_image_size > 1){
							vm2.$notify({
								title: '<?php esc_html_e('Error', 'bookingpress-appointment-booking'); ?>',
								message: '<?php esc_html_e('Please upload maximum 500 KB file only', 'bookingpress-appointment-booking'); ?>',
								type: 'error',
								customClass: 'error_notification',
								duration:<?php echo intval($bookingpress_notification_duration); ?>,
							});                    
							return false
						}
					}
				},
				integration_tab_select(selected_integration){
					const vm = this;
					sessionStorage.setItem("selected_integration", selected_integration);
					vm.bpa_set_read_more_link();
				},
				optins_tab_select(selected_optins){
					const vm = this;
					sessionStorage.setItem("selected_optins", selected_optins);
					vm.bpa_set_read_more_link();
				},
				bookingpress_add_special_day_period(){
					const vm = this;
					var ilength = 1;
					if(vm.special_day_form.special_day_workhour != undefined){
						ilength = parseInt(vm.special_day_form.special_day_workhour.length) + 1;
					}
					let WorkhourData = {};
					Object.assign(WorkhourData, {id: ilength})
					Object.assign(WorkhourData, {start_time: ''})
					Object.assign(WorkhourData, {formatted_start_time: ''})
					Object.assign(WorkhourData, {end_time: ''})
					Object.assign(WorkhourData, {formatted_end_time: ''})
					if(vm.special_day_form.special_day_workhour == undefined){
						vm.special_day_form.special_day_workhour = []
					}
					vm.special_day_form.special_day_workhour.push(WorkhourData)
				},
				delete_special_day_div(special_day_id){
					var vm = this
					vm.special_day_data_arr.forEach(function(item, index, arr)
					{
						if (item.id == special_day_id) {
							vm.special_day_data_arr.splice(index, 1);
						}
					})
				},
				show_edit_special_day_div(special_day_id) {				
					var vm = this
					vm.special_day_data_arr.forEach(function(item, index, arr)
					{
						if (item.id == special_day_id) {
							vm.special_day_form.special_day_date = [ item.special_day_start_date, item.special_day_end_date ]
							vm.special_day_form.start_time = item.start_time
							vm.special_day_form.end_time = item.end_time
							vm.special_day_form.special_day_workhour = item.special_day_workhour
						}
						vm.edit_special_day_id = special_day_id;
					})
				},		
				bookingpress_remove_special_day_period(id){
					const vm = this									
					vm.special_day_form.special_day_workhour.forEach(function(item, index, arr)
					{
						if(id == item.id ){
							vm.special_day_form.special_day_workhour.splice(index,1);
						}	
					})
				},
				change_special_day_date(selected_value){
					const vm = this
					if(selected_value != null) {
						vm.special_day_form.special_day_date[0] = vm.get_formatted_date(vm.special_day_form.special_day_date[0])
						vm.special_day_form.special_day_date[1] = vm.get_formatted_date(vm.special_day_form.special_day_date[1])
					}
				},
				closeSpecialday(){
					const vm = this;
					vm .edit_special_day_id = ''
					vm.special_day_form.special_day_date = '';
					vm.special_day_form.start_time = '';
					vm.special_day_form.end_time = '';
					vm.special_day_form.special_day_workhour = [];
				},
				addSpecialday(special_day_form) {
					const vm = this;	
					this.$refs[special_day_form].validate((valid) => {
						if (valid) {
							const vm = this
							var is_exit = 0;	
							if(vm.days != undefined && vm.days != '' ) {
								vm.days.forEach(function(item, index, arr){								                            
									if(vm.special_day_form.special_day_date[0] == item.id || vm.special_day_form.special_day_date[1] == item.id || ( vm.special_day_form.special_day_date[0] <= item.id && vm.special_day_form.special_day_date[1] >= item.id) ) {				
										is_exit = 1;
										vm.$notify({
											title: '<?php esc_html_e( 'Error', 'bookingpress-appointment-booking' ); ?>',
											message: '<?php esc_html_e( 'Holiday is already exists', 'bookingpress-appointment-booking' ); ?>',
											type: 'error',
											customClass: 'error_notification',
											duration:<?php echo intval( $bookingpress_notification_duration ); ?>,
										});                                
									}
								});
							}
							if(vm.special_day_form.special_day_workhour != undefined && vm.special_day_form.special_day_workhour != '' ) {
								vm.special_day_form.special_day_workhour.forEach(function(item, index, arr){								                            
									if(is_exit == 0 && (item.start_time == '' || item.end_time == '')) {
										is_exit = 1;
										vm.$notify({
											title: '<?php esc_html_e( 'Error', 'bookingpress-appointment-booking' ); ?>',
											message: '<?php esc_html_e( 'Please Enter Start Time and End Time', 'bookingpress-appointment-booking' ); ?>',
											type: 'error',
											customClass: 'error_notification',
											duration:<?php echo intval( $bookingpress_notification_duration ); ?>,
										});                                
									}
								});
							}
							if(vm.special_day_data_arr != undefined && vm.special_day_data_arr != '' ) {						
								vm.special_day_data_arr.forEach(function(item, index, arr) {											
									if((vm.special_day_form.special_day_date[0] == item.special_day_start_date || vm.special_day_form.special_day_date[0] == item.special_day_end_date || ( vm.special_day_form.special_day_date[0] >= item.special_day_start_date && vm.special_day_form.special_day_date[0] <= item.special_day_end_date ) || vm.special_day_form.special_day_date[1] == item.special_day_end_date || vm.special_day_form.special_day_date[1] == item.special_day_start_date || (vm.special_day_form.special_day_date[1] >= item.special_day_start_date && vm.special_day_form.special_day_date[1] <= item.special_day_end_date) || (vm.special_day_form.special_day_date[0] <= item.special_day_start_date && vm.special_day_form.special_day_date[1] >= item.special_day_end_date)) && vm.edit_special_day_id != item.id  ) {
										is_exit = 1;
										vm.$notify({
											title: '<?php esc_html_e( 'Error', 'bookingpress-appointment-booking' ); ?>',
											message: '<?php esc_html_e( 'Special days already exists', 'bookingpress-appointment-booking' ); ?>',
											type: 'error',
											customClass: 'error_notification',
											duration:<?php echo intval( $bookingpress_notification_duration ); ?>,
										});								
									}							
								});	
							}		 

							if(vm.special_day_form != undefined && vm.special_day_form != ''){
								var bookingpress_special_day_breaks = vm.special_day_form.special_day_workhour;
								var bookingpress_is_break_exist = 0;
								var bookingpress_existing_breaks_start_time = [];
								var bookingpress_existing_breaks_end_time = [];
								if(bookingpress_special_day_breaks.length > 0){
									bookingpress_special_day_breaks.forEach(function(currentValue, index, arr){
										if(!bookingpress_existing_breaks_start_time.includes(currentValue.start_time)){
											bookingpress_existing_breaks_start_time.push(currentValue.start_time);
											bookingpress_existing_breaks_start_time.push(currentValue.end_time);
										}else{
											is_exit = 1;
											vm.$notify({
												title: '<?php esc_html_e( 'Error', 'bookingpress-appointment-booking' ); ?>',
												message: '<?php esc_html_e( 'Break time already added', 'bookingpress-appointment-booking' ); ?>',
												type: 'error',
												customClass: 'error_notification',
												duration:<?php echo intval( $bookingpress_notification_duration ); ?>,
											});
										}
									});
								}
							}

							if(is_exit == 0) {
								var postdata = [];
								postdata.action = 'bookingpress_validate_special_days'
								postdata.selected_date= vm.special_day_form.special_day_date;
								postdata._wpnonce = '<?php echo esc_html( wp_create_nonce( 'bpa_wp_nonce' ) ); ?>';
								axios.post( appoint_ajax_obj.ajax_url, Qs.stringify( postdata ) )
								.then(function(response){
									if(response.data.variant != 'undefined' && response.data.variant == 'warnning') {	
										vm.$confirm(response.data.msg, 'Warning', {
										confirmButtonText: '<?php esc_html_e( 'Ok', 'bookingpress-appointment-booking' ); ?>',
										cancelButtonText: '<?php esc_html_e( 'Cancel', 'bookingpress-appointment-booking' ); ?>',
										type: 'warning'
										}).then(() => {					
											if(vm.edit_special_day_id != '' ){
												vm.edit_Special_days();
											} else {
												vm.add_special_days();
											}
										}).catch(err => {
										});

									}else if(response.data.variant != 'undefined' && response.data.variant  == 'success') {
										if(vm.edit_special_day_id != '' ){
											vm.edit_Special_days();
										} else {
											vm.add_special_days();
										}
									}
								}).catch(function(error){
									console.log(error);
									vm.$notify({
										title: '<?php esc_html_e( 'Error', 'bookingpress-appointment-booking' ); ?>',
										message: '<?php esc_html_e( 'Something went wrong..', 'bookingpress-appointment-booking' ); ?>',
										type: 'error_notification',
									});
								});	
							}	
						} else {
							return false;
						}
					});
				},
				bookingpress_format_special_day_time(){
					const vm = this
					var postdata = [];
					postdata.action = 'bookingpress_format_special_days_data'
					postdata.special_days_data= vm.special_day_data_arr;
					postdata._wpnonce = '<?php echo esc_html( wp_create_nonce( 'bpa_wp_nonce' ) ); ?>';
					axios.post( appoint_ajax_obj.ajax_url, Qs.stringify( postdata ) )
					.then(function(response){
						if(response.data.variant == "success"){
							vm.special_day_data_arr = response.data.special_days_details
						}
					}).catch(function(error){
						console.log(error);
					});
				},
				add_special_days(){
					const vm = this;
					var ilength = parseInt(vm.special_day_data_arr.length) + 1;
					let empSpecialDayData = {};
					Object.assign(empSpecialDayData, {id: ilength})
					Object.assign(empSpecialDayData, {special_day_start_date: vm.special_day_form.special_day_date[0]})
					Object.assign(empSpecialDayData, {special_day_end_date: vm.special_day_form.special_day_date[1]})
					Object.assign(empSpecialDayData, {start_time: vm.special_day_form.start_time})
					Object.assign(empSpecialDayData, {end_time: vm.special_day_form.end_time})
					Object.assign(empSpecialDayData, {special_day_workhour: vm.special_day_form.special_day_workhour})
					vm.special_day_data_arr.push(empSpecialDayData)
					vm.closeSpecialday();
					vm.bookingpress_format_special_day_time()
				},
				edit_Special_days(){
					
					var vm = this
					var special_day_id = vm.edit_special_day_id
					var special_day_date = vm.special_day_form.special_day_date					
					var special_day_start_time = vm.special_day_form.start_time													
					var special_day_end_time = vm.special_day_form.end_time																					
					var special_day_workhour = vm.special_day_form.special_day_workhour

					vm.special_day_data_arr.forEach(function(item, index, arr)
					{
						if(item.id == special_day_id)
						{
							item.special_day_start_date = special_day_date[0]
							item.special_day_end_date = special_day_date[1]
							item.start_time = special_day_start_time
							item.end_time = special_day_end_time
							item.special_day_workhour = special_day_workhour							
						}
					}) 
					vm.closeSpecialday();
					vm.bookingpress_format_special_day_time()					
				},	
				saveEmployeeSpecialdays(){
					const vm2 = this
					vm2.is_disabled = true
					vm2.is_display_save_loader = '1'
					var postdata = []
					postdata.action = 'bookingpress_save_default_special_days';
					postdata.special_day_details = vm2.special_day_data_arr;
					postdata._wpnonce = '<?php echo esc_html( wp_create_nonce( 'bpa_wp_nonce' ) ); ?>';				
					axios.post( appoint_ajax_obj.ajax_url, Qs.stringify(postdata))
					.then(function(response){
						vm2.is_disabled = false
						vm2.is_display_save_loader = '0'
						vm2.$notify({
							title: response.data.title,
							message: response.data.msg,
							type: response.data.variant,
							customClass: response.data.variant+'_notification',
							duration:<?php echo intval( $bookingpress_notification_duration ); ?>
						});
						vm2.closeSpecialday();
						vm2.getSpecialdays()
					}).catch(function(error){
						console.log(error);
						vm2.$notify({
							title: '<?php esc_html_e( 'Error', 'bookingpress-appointment-booking' ); ?>',
							message: '<?php esc_html_e( 'Something went wrong..', 'bookingpress-appointment-booking' ); ?>',
							type: 'error',
							customClass: 'error_notification',
							duration:<?php echo intval( $bookingpress_notification_duration ); ?>,
						});
					});
				},
				getSpecialdays(){
					const vm = this
					 var postdata = [];
					postdata.action = 'bookingpress_get_default_special_day_details';
					postdata._wpnonce = '<?php echo esc_html( wp_create_nonce( 'bpa_wp_nonce' ) ); ?>';
					axios.post( appoint_ajax_obj.ajax_url, Qs.stringify(postdata))
					.then(function(response){
						vm.is_disabled = false
						vm.is_display_loader = '0'
						vm.special_day_data_arr = response.data.special_day_data
					}).catch(function(error){
						console.log(error);
						vm.$notify({
							title: '<?php esc_html_e( 'Error', 'bookingpress-appointment-booking' ); ?>',
							message: '<?php esc_html_e( 'Something went wrong..', 'bookingpress-appointment-booking' ); ?>',
							type: 'error',
							customClass: 'error_notification',
							duration:<?php echo intval( $bookingpress_notification_duration ); ?>,
						});
					});	
				},
				deleteCustomerField( index ){

					const vm = this;

					let deleted_field_id = app.customer_field_settings[index].id;

					let postData = [];
					
					postData.action = 'bookingpress_check_field_deletion';
					postData.del_field_id = deleted_field_id;
					postData._wpnonce = '<?php echo esc_html( wp_create_nonce('bpa_wp_nonce') ); ?>';
					
					axios.post( appoint_ajax_obj.ajax_url, Qs.stringify(postData) ).then(function(response){
						
						if( 'success' == response.data.variant ){
							vm.confirmDeleteCustoemrField( index );
						} else if( 'warning' == response.data.variant ){
							vm.$confirm(response.data.msg, 'Warning', {
								confirmButtonText: '<?php esc_html_e( 'Ok', 'bookingpress-appointment-booking' ); ?>',
								cancelButtonText: '<?php esc_html_e( 'Cancel', 'bookingpress-appointment-booking' ); ?>',
								type: 'warning'
							}).then(() => {		
								vm.confirmDeleteCustoemrField( index );
							}).catch(()=>{
								console.log( 'inside cancel' );
							});
						}
						
					}).catch( function(error){
						console.log( error );
					});
				},
				confirmDeleteCustoemrField( index ){

					let deleted_field_id = app.customer_field_settings[index].id;

					document.querySelector( '.bpa-customer-field-container[data-id="' + index + '"]').replaceWith( '' );
					
					app.bpa_deleted_fields.push( deleted_field_id );

					delete app.customer_field_settings[ index ];
					let updated_fields = app.customer_field_settings;
					let finalFields = [];
					let all_fields = document.getElementsByClassName('bpa-customer-field-container');
					let field_index = index;
					
					if( all_fields.length > 0 ){
						let i = 0;
						all_fields.forEach( (element,index) => {
							element.setAttribute( 'data-id', i );
							i++;
						});

						let x = 0;
						updated_fields.forEach( (element, index ) => {
							finalFields[x] = element;
							finalFields[x].field_position = (index + 1);
							x++;
						});
					}
					this.customer_field_settings = finalFields;
					this.$forceUpdate();
				},
				saveCustomerFieldSettings( index ){
					let field_settings = app.customer_field_settings[index];
					/** AJAX Call to check for duplicate meta key */
					
					let field_meta_key = field_settings.meta_key;
					let field_id = field_settings.id;

					let postData = {
						'action': 'verify_customer_field_meta_key',
						'_wpnonce':'<?php echo esc_html( wp_create_nonce( 'bpa_wp_nonce' ) ); ?>',
						'meta_key' : field_meta_key,
						'field_id' : field_id
					};

					axios.post(
						appoint_ajax_obj.ajax_url,
						Qs.stringify( postData )
					).then(
						function(response){
							if( 'success' == response.data.variant ){
								app.customer_field_settings[index].is_edit = false;
							} else {
								this.$notify({
									title: '<?php esc_html_e( 'Error', 'bookingpress-appointment-booking' ); ?>',
									message: response.data.msg,
									type: 'error',
									customClass: 'error_notification',
									duration:<?php echo intval( $bookingpress_notification_duration ); ?>,
								});
								app.customer_field_settings[index].is_edit = true;
							}
						}.bind(this)
					).catch(function(error){
						console.log( 'error' );
						console.log( error );
					});
					
					this.$forceUpdate();
				},
				bpaCustomerDisplayPresetValues( index ){
					app.customer_field_settings[index].enable_preset_fields = true;
					this.$forceUpdate();
				},
				bpaHideCustomerPresetValues( index ){
					app.customer_field_settings[index].enable_preset_fields = false;
					this.$forceUpdate();
				},
				closeCustomerFieldValueBtn( index ){
					app.customer_field_settings[index].is_edit_values = false;
					this.$forceUpdate();
				},
				bpaCustomerAddfieldValue( index ){
					let field_settings = app.customer_field_settings[index];
					let field_values = field_settings.field_values;
					let last_field_values;
					if( field_values.length > 0 ){
						let field_value = field_values[ field_values.length - 1 ];
						last_field_values = {
							'label' : field_value.label,
							'value' : field_value.value
						};
					} else {
						last_field_values = {
							'label' : 'Option 1',
							'value' : 'Option 1'
						}
					}

					app.customer_field_settings[index].field_values.push( last_field_values );
					this.$forceUpdate();
				},
				bpaCustomerRemovefieldValue( key_index, field_index ){
					let field_settings = app.customer_field_settings[field_index];
					if( field_settings.field_values.length > 1 ){
						delete field_settings.field_values[key_index];
						let updated_field_values = [];
						let field_values = field_settings.field_values;
						let i = 0;
						field_values.forEach(function(element,index){
							updated_field_values[i] = element;
							i++;
						});
						
						app.customer_field_settings[field_index].field_values = updated_field_values;
						
					}
					this.$forceUpdate();
				},
				applyCustomerPresetFields( index ){
					let preset_choice = app.customer_field_settings[index].preset_field_choice;

					if( '' == preset_choice ){
						return false;
					}

					let postData = [];
					postData.preset_key = preset_choice;
					postData.action = 'bpa_load_preset_field_data';
					postData._wpnonce = '<?php echo esc_html( wp_create_nonce( 'bpa_wp_nonce' ) ); ?>'
					app.is_display_preset_value_loader = true;
					app.preset_btn_disable = true;
					axios.post( appoint_ajax_obj.ajax_url, Qs.stringify( postData ) ).then(
						function( response ){
							if( response.data.variant == 'error' ){
								this.$notify({
									title: '<?php esc_html_e( 'Error', 'bookingpress-appointment-booking' ); ?>',
									message: '<?php esc_html_e( 'Something went wrong..', 'bookingpress-appointment-booking' ); ?>',
									type: 'error',
									customClass: 'error_notification',
									duration:<?php echo intval( $bookingpress_notification_duration ); ?>,
								});
							} else {
								let preset_values = response.data.preset_values;
								app.customer_field_settings[index].field_values = preset_values;
							}
							app.$forceUpdate();
							app.is_display_preset_value_loader = false;
							app.preset_btn_disable = false;
						}.bind(this)
					).catch( function( error ){
						this.$notify({
							title: '<?php esc_html_e( 'Error', 'bookingpress-appointment-booking' ); ?>',
							message: '<?php esc_html_e( 'Something went wrong..', 'bookingpress-appointment-booking' ); ?>',
							type: 'error',
							customClass: 'error_notification',
							duration:<?php echo intval( $bookingpress_notification_duration ); ?>,
						});
					});
					app.$forceUpdate();
					setTimeout(function(){
						app.is_display_preset_value_loader = false;
						app.preset_btn_disable = false;
					},3000);
				},
				activateLicenseKey()
				{
					const vm = this;
					var license_key = vm.license_form.license_key;
					var license_package = vm.license_form.license_package;

					let postData = [];
					postData.license_key = license_key;
					postData.license_package = license_package;
					postData.action = 'bpa_validate_and_activate_license_key';
					postData._wpnonce = '<?php echo esc_html( wp_create_nonce( 'bpa_license_verify_nonce' ) ); ?>'
					
					axios.post( appoint_ajax_obj.ajax_url, Qs.stringify( postData ) ).then(
						function( response ){
							let response_message = response.data.msg;

							vm.license_form.error_message = '';
							vm.license_form.success_message = '';

							if( response.data.variant == 'error' ){
								vm.license_form.error_message = response_message;
							} else {
								vm.license_form.success_message = response_message;
							}
							app.$forceUpdate();
							app.is_display_save_loader = false;
							setTimeout(function(){
								location.reload();
							}, 1500);
						}.bind(this)
					).catch( function( error ){
						this.$notify({
							title: '<?php esc_html_e( 'Error', 'bookingpress-appointment-booking' ); ?>',
							message: '<?php esc_html_e( 'Something went wrong..', 'bookingpress-appointment-booking' ); ?>',
							type: 'error',
							customClass: 'error_notification',
							duration:<?php echo intval( $bookingpress_notification_duration ); ?>,
						});
					});					
				},
				deactivateLicenseKey()
				{
					const vm = this;
					var license_key = vm.license_form.license_key;

					let postData = [];
					postData.license_key = license_key;
					postData.action = 'bpa_dectivate_license_key';
					postData._wpnonce = '<?php echo esc_html( wp_create_nonce( 'bpa_license_verify_nonce' ) ); ?>'
					
					axios.post( appoint_ajax_obj.ajax_url, Qs.stringify( postData ) ).then(
						function( response ){
							let response_message = response.data.msg;
							if( response.data.variant == 'error' ){
								vm.license_form.error_message = response_message;
							} else {
								vm.license_form.success_message = response_message;
							}
							app.$forceUpdate();
							app.is_display_save_loader = false;
							
						}.bind(this)
					).catch( function( error ){
						this.$notify({
							title: '<?php esc_html_e( 'Error', 'bookingpress-appointment-booking' ); ?>',
							message: '<?php esc_html_e( 'Something went wrong..', 'bookingpress-appointment-booking' ); ?>',
							type: 'error',
							customClass: 'error_notification',
							duration:<?php echo intval( $bookingpress_notification_duration ); ?>,
						});
					});					
				},
				refreshLicenseKey()
				{
					const vm = this;
					var license_key = vm.license_form.license_key;

					let postData = [];
					postData.license_key = license_key;
					postData.action = 'bpa_refresh_license_key';
					postData._wpnonce = '<?php echo esc_html( wp_create_nonce( 'bpa_license_verify_nonce' ) ); ?>'
					
					axios.post( appoint_ajax_obj.ajax_url, Qs.stringify( postData ) ).then(
						function( response ){
							let response_message = response.data.msg;
							if( response.data.variant == 'error' ){
								vm.license_form.error_message = response_message;
							} else {
								vm.license_form.success_message = response_message;
							}
							app.$forceUpdate();
							app.is_display_save_loader = false;
							
						}.bind(this)
					).catch( function( error ){
						this.$notify({
							title: '<?php esc_html_e( 'Error', 'bookingpress-appointment-booking' ); ?>',
							message: '<?php esc_html_e( 'Something went wrong..', 'bookingpress-appointment-booking' ); ?>',
							type: 'error',
							customClass: 'error_notification',
							duration:<?php echo intval( $bookingpress_notification_duration ); ?>,
						});
					});	
				},
				bookingpress_change_country_type(){
					const vm = this;
					vm.bookingpress_tel_input_settings_props.defaultCountry = 'US';
					vm.$refs.bpa_tel_input_settings_field._data.activeCountryCode = 'US';
					vm.general_setting_form.default_phone_country_code = 'US';
				},
				bookingpress_add_refund_rules() {
					const vm = this;										
					is_exit = 0; 
					vm.$refs['refund_setting_form'].validate((valid) => {                        
                        if(valid) {

							if(vm.payment_setting_form.bookingpress_partial_refund_rules != undefined && vm.payment_setting_form.bookingpress_partial_refund_rules != '' ) {								
								vm.payment_setting_form.bookingpress_partial_refund_rules.forEach(function(item, index, arr) {
									if(vm.refund_setting_form.bookingpress_refund_duration == item.rules_duration && vm.refund_setting_form.bookingpress_refund_duration_unit == item.rules_duration_unit && is_exit == 0) {
										is_exit = 1;
										vm.$notify({
											title: '<?php esc_html_e( 'Error', 'bookingpress-appointment-booking' ); ?>',
											message: '<?php esc_html_e( 'Partial refund rules already added', 'bookingpress-appointment-booking' ); ?>',
											type: 'error',
											customClass: 'error_notification',
											duration:<?php echo intval( $bookingpress_notification_duration ); ?>,
										});
									}
								});
							}
							if(vm.refund_setting_form.bookingpress_refund_duration >= 24 && vm.refund_setting_form.bookingpress_refund_duration_unit == 'h' && is_exit == 0) {
								is_exit = 1;
								vm.$notify({
									title: '<?php esc_html_e( 'Error', 'bookingpress-appointment-booking' ); ?>',
									message: '<?php esc_html_e( 'Duration must be less then 24 Hour', 'bookingpress-appointment-booking' ); ?>',
									type: 'error',
									customClass: 'error_notification',
									duration:<?php echo intval( $bookingpress_notification_duration ); ?>,
								});
							}
							if(vm.refund_setting_form.bookingpress_refund_duration >= 365 && vm.refund_setting_form.bookingpress_refund_duration_unit == 'd' && is_exit == 0) {
								is_exit = 1;
								vm.$notify({
									title: '<?php esc_html_e( 'Error', 'bookingpress-appointment-booking' ); ?>',
									message: '<?php esc_html_e( 'Duration must be less then 365 Days', 'bookingpress-appointment-booking' ); ?>',
									type: 'error',
									customClass: 'error_notification',
									duration:<?php echo intval( $bookingpress_notification_duration ); ?>,
								});
							}

							if( is_exit ==  0) {
								var ilength = parseInt(vm.payment_setting_form.bookingpress_partial_refund_rules.length) + 1;
								let refund_rules_data = {};
								Object.assign(refund_rules_data, {id: ilength})					
								Object.assign(refund_rules_data, {rules_duration: vm.refund_setting_form.bookingpress_refund_duration})
								Object.assign(refund_rules_data, {rules_duration_unit: vm.refund_setting_form.bookingpress_refund_duration_unit})
								Object.assign(refund_rules_data, {rules_amount: vm.refund_setting_form.bookingpress_refund_amount})
								Object.assign(refund_rules_data, {rules_amount_unit: vm.refund_setting_form.bookingpress_refund_amount_unit})
								vm.payment_setting_form.bookingpress_partial_refund_rules.push(refund_rules_data);
								vm.bookingpress_reset_refund_rules();
							}
						}
					});
				},
				bookingpress_reset_refund_rules(){
					const vm = this;
					vm.refund_setting_form.bookingpress_refund_duration = 1
					vm.refund_setting_form.bookingpress_refund_duration_unit = 'h';
					vm.refund_setting_form.bookingpress_refund_amount = 1
					vm.refund_setting_form.bookingpress_refund_amount_unit = 'percentage';
				},	
				bookingpress_delete_refund_rules(id) {
					var vm = this
					vm.payment_setting_form.bookingpress_partial_refund_rules.forEach(function(item, index, arr)
					{
						if (item.id == id) {
							vm.payment_setting_form.bookingpress_partial_refund_rules.splice(index, 1);
						}
					})
				},	
			<?php
		}

		function bookingpress_dynamic_get_settings_data_func() {
			global $bookingpress_pro_staff_members,$bookingpress_notification_duration;
			if ( $bookingpress_pro_staff_members->bookingpress_check_staffmember_module_activation() ) {
				?>
				else if( current_tabname == "staffmembers_settings" ) {
					vm.getSettingsData('staffmember_setting', 'staffmembers_settings_form')
				}
				<?php
			}
			?>
			else if(current_tabname == "customer_settings") {
				vm.getSettingsData('customer_setting', 'customer_setting_form')
				setTimeout(function(){
					const bpa_cus_ortable_obj = new BPACSortable();
				},100);
			}
			else if( current_tabname == "specialday_settings" ) {
				vm.loadAttributes();
				vm.getSpecialdays();
			} else if(current_tabname == "license_settings"){
				vm.getSettingsData('license_settings','license_form');
			}
			else if( current_tabname == "integration_settings" ) {
				<?php	
				do_action('bookingpress_load_integration_settings_data');
				?>
			} 
			else if( current_tabname == "optin_settings" ) {
				<?php	
				do_action('bookingpress_load_optin_settings_data');
				?>
            } 
			<?php	
		}
		function bookingpress_settings_add_dynamic_on_load_method_func() {

			global $bookingpress_pro_staff_members;
			?>
			else if(selected_tab_name == "customer_settings"){
				vm.getSettingsData('customer_setting','customer_setting_form');
				setTimeout(function(){
					const bpa_cus_ortable_obj = new BPACSortable();
				},100);
			} else if(selected_tab_name == "specialday_settings"){
				vm.getSpecialdays();				
				vm.loadAttributes();
			}			
			else if( selected_tab_name == "integration_settings" ) {
				<?php	
				do_action('bookingpress_load_integration_settings_data');
				?>
            } 
			else if( selected_tab_name == "optin_settings" ) {
				<?php	
				do_action('bookingpress_load_optin_settings_data');
				?>				
            } else if(selected_tab_name == "license_settings"){
				vm.getSettingsData('license_settings','license_form');
			}
			<?php
			if ( $bookingpress_pro_staff_members->bookingpress_check_staffmember_module_activation() ) {
				?>
				else if( selected_tab_name == "staffmembers_settings" ) {
					vm.getSettingsData('staffmember_setting', 'staffmembers_settings_form')
				}
				<?php
			}
		}

		function bookingpress_modify_settings_view_file_path_func() {
			$bookingpress_settings_view_path = BOOKINGPRESS_PRO_VIEWS_DIR . '/settings/manage_settings.php';
			return $bookingpress_settings_view_path;
		}

		function bookingpress_add_setting_dynamic_data_fields_func( $bookingpress_dynamic_setting_data_fields ) {
			global $wpdb, $bookingpress_pro_staff_members, $BookingPress, $tbl_bookingpress_default_special_day, $tbl_bookingpress_default_special_day_breaks, $bookingpress_global_options,$bookingpress_coupons;
			$global_data = $bookingpress_global_options->bookingpress_global_options();

			$bookingpress_dynamic_setting_data_fields['bpa_integration_active_tab'] = '';						            			
			$bookingpress_dynamic_setting_data_fields['bpa_optin_active_tab'] = '';

			if ( $bookingpress_pro_staff_members->bookingpress_check_staffmember_module_activation() ) {
			
				$bookingpress_staffmember_any_staff_options = $BookingPress->bookingpress_get_settings( 'bookingpress_staffmember_any_staff_options', 'staffmember_setting' );
				$bookingpress_staffmember_access_admin = $BookingPress->bookingpress_get_settings( 'bookingpress_staffmember_access_admin', 'staffmember_setting' );				
				$bookingpress_staffmember_auto_assign_rule  = $BookingPress->bookingpress_get_settings( 'bookingpress_staffmember_auto_assign_rule', 'staffmember_setting' );
				$bookingpress_dynamic_setting_data_fields['hide_staffmember_selection'] = $BookingPress->bookingpress_get_customize_settings('hide_staffmember_selection','booking_form');										

				$bookingpress_dynamic_setting_data_fields['staffmembers_settings_form'] = array(
					'bookingpress'                     => false,
					'bookingpress_calendar'            => false,
					'bookingpress_appointments'        => false,
					'bookingpress_payments'            => false,
					'bookingpress_customers'           => false,
					'bookingpress_staff_members'       => false,
					//'bookingpress_add_appointments'    => false,
					'bookingpress_edit_appointments'   => false,
					'bookingpress_delete_appointments' => false,
					'bookingpress_export_appointments' => false,
					//'bookingpress_add_customers'       => false,
					'bookingpress_edit_customers'      => false,
					'bookingpress_delete_customers'    => false,
					'bookingpress_export_customers'    => false,
					'bookingpress_edit_payments'       => false,
					'bookingpress_delete_payments'     => false,
					'bookingpress_export_payments'     => false,
					'bookingpress_edit_basic_details'  => false,
					'bookingpress_edit_daysoffs'       => false,
					'bookingpress_edit_special_days'   => false,
					'bookingpress_manage_calendar_integration' => false,
					'bookingpress_timesheet'           => false,
					'bookingpress_myservices'          => false,
					'bookingpress_myprofile'           => false,
					'bookingpress_staffmember_auto_assign_rule' => ! empty( $bookingpress_staffmember_auto_assign_rule ) ? $bookingpress_staffmember_auto_assign_rule : 'least_assigned_by_day',
					'bookingpress_staffmember_any_staff_options' => $bookingpress_staffmember_any_staff_options == 'true' ? true : false,
					'bookingpress_staffmember_access_admin'	=> $bookingpress_staffmember_access_admin == 'true' ? true : false,
					'bookingpress_staffmember_module_singular_name' => 'Staff Member',
					'bookingpress_staffmember_module_plural_name' => 'Staff Members',
				);
			}
			$bookingpress_dynamic_setting_data_fields['general_setting_form']              = array(
				'default_minimum_time_for_booking'        => 'disabled',
				'default_minimum_time_for_canceling'      => 'disabled',
				'default_minimum_time_befor_rescheduling' => 'disabled',
				'period_available_for_booking'            => '60',
				'share_quanty_between_timeslots'	  => false,
				'default_country_type'			  => 'fixed_country',			
			);
			$bookingpress_dynamic_setting_data_fields['default_all_appointment_status'] = $global_data['appointment_status'];			

			$bookingpress_dynamic_setting_data_fields['is_staffmember_activate'] = $bookingpress_pro_staff_members->bookingpress_check_staffmember_module_activation();
			$bookingpress_dynamic_setting_data_fields['is_coupon_activate'] = $bookingpress_coupons->bookingpress_check_coupon_module_activation();

			$bookingpress_dynamic_setting_data_fields['message_setting_form']['no_staffmember_selected_for_the_booking'] = '';
			$bookingpress_dynamic_setting_data_fields['message_setting_form']['bookingpress_card_details_error_msg'] = '';
			$bookingpress_dynamic_setting_data_fields['message_setting_form']['payment_token_failure_message'] = '';
			$bookingpress_dynamic_setting_data_fields['message_setting_form']['payment_already_paid_message'] = '';
			$bookingpress_dynamic_setting_data_fields['message_setting_form']['complete_payment_success_message'] = '';
			$bookingpress_dynamic_setting_data_fields['message_setting_form']['refund_policy_message'] = '';
			$bookingpress_dynamic_setting_data_fields['rules_message']['refund_policy_message']= array(
				array(
					'required' => true,
					'message'  => esc_html__('Please enter message', 'bookingpress-appointment-booking'),
					'trigger'  => 'blur',
				),
			);		
			$bookingpress_dynamic_setting_data_fields['is_disaply_payment_refund_note'] = 0;
			$bookingpress_dynamic_setting_data_fields['default_minimum_time_options']      = array(
				array(
					'text'  => __( 'disabled', 'bookingpress-appointment-booking' ),
					'value' => 'disabled',
				),
				array(
					'text'  => __( '5 min', 'bookingpress-appointment-booking' ),
					'value' => '5',
				),
				array(
					'text'  => __( '10 min', 'bookingpress-appointment-booking' ),
					'value' => '10',
				),
				array(
					'text'  => __( '12 min', 'bookingpress-appointment-booking' ),
					'value' => '12',
				),
				array(
					'text'  => __( '15 min', 'bookingpress-appointment-booking' ),
					'value' => '15',
				),
				array(
					'text'  => __( '20 min', 'bookingpress-appointment-booking' ),
					'value' => '20',
				),
				array(
					'text'  => __( '30 min', 'bookingpress-appointment-booking' ),
					'value' => '30',
				),
				array(
					'text'  => __( '45 min', 'bookingpress-appointment-booking' ),
					'value' => '45',
				),
				array(
					'text'  => __( '1 h', 'bookingpress-appointment-booking' ),
					'value' => '60',
				),
				array(
					'text'  => __( '1 h 30 min', 'bookingpress-appointment-booking' ),
					'value' => '90',
				),
				array(
					'text'  => __( '2 h', 'bookingpress-appointment-booking' ),
					'value' => '120',
				),
				array(
					'text'  => __( '3 h', 'bookingpress-appointment-booking' ),
					'value' => '180',
				),
				array(
					'text'  => __( '6 h', 'bookingpress-appointment-booking' ),
					'value' => '360',
				),
				array(
					'text'  => __( '7 h', 'bookingpress-appointment-booking' ),
					'value' => '420',
				),
				array(
					'text'  => __( '8 h', 'bookingpress-appointment-booking' ),
					'value' => '480',
				),
				array(
					'text'  => __( '9 h', 'bookingpress-appointment-booking' ),
					'value' => '540',
				),
				array(
					'text'  => __( '10 h', 'bookingpress-appointment-booking' ),
					'value' => '600',
				),
				array(
					'text'  => __( '11 h', 'bookingpress-appointment-booking' ),
					'value' => '660',
				),
				array(
					'text'  => __( '12 h', 'bookingpress-appointment-booking' ),
					'value' => '720',
				),
				array(
					'text'  => __( '1 day', 'bookingpress-appointment-booking' ),
					'value' => '1440',
				),
				array(
					'text'  => __( '2 days', 'bookingpress-appointment-booking' ),
					'value' => '2880',
				),
				array(
					'text'  => __( '3 days', 'bookingpress-appointment-booking' ),
					'value' => '4320',
				),
				array(
					'text'  => __( '4 days', 'bookingpress-appointment-booking' ),
					'value' => '5760',
				),
				array(
					'text'  => __( '5 days', 'bookingpress-appointment-booking' ),
					'value' => '7200',
				),
				array(
					'text'  => __( '6 days', 'bookingpress-appointment-booking' ),
					'value' => '8640',
				),
				array(
					'text'  => __( '1 week', 'bookingpress-appointment-booking' ),
					'value' => '10080',
				),
				array(
					'text'  => __( '2 week', 'bookingpress-appointment-booking' ),
					'value' => '20160',
				),
				array(
					'text'  => __( '3 week', 'bookingpress-appointment-booking' ),
					'value' => '30240',
				),
				array(
					'text'  => __( '4 week', 'bookingpress-appointment-booking' ),
					'value' => '40320',
				),
				array(
					'text'  => __( '6 month', 'bookingpress-appointment-booking' ),
					'value' => '262800',
				),
			);
			$bookingpress_dynamic_setting_data_fields['staffmember_auto_assign_rule_list'] = array(
				array(
					'text'  => __( 'Least assigned by the day', 'bookingpress-appointment-booking' ),
					'value' => 'least_assigned_by_day',
				),
				array(
					'text'  => __( 'Most assigned by the day', 'bookingpress-appointment-booking' ),
					'value' => 'most_assigned_by_day',
				),
				array(
					'text'  => __( 'Least assigned by the week', 'bookingpress-appointment-booking' ),
					'value' => 'least_assigned_by_week',
				),
				array(
					'text'  => __( 'Most assigned by the week', 'bookingpress-appointment-booking' ),
					'value' => 'most_assigned_by_week',
				),
				array(
					'text'  => __( 'Most expensive', 'bookingpress-appointment-booking' ),
					'value' => 'most_expensive',
				),
				array(
					'text'  => __( 'Least expensive', 'bookingpress-appointment-booking' ),
					'value' => 'least_expensive',
				),
			);
			$bookingpress_dynamic_setting_data_fields['staffmembers_settings']             = array();
			$bookingpress_dynamic_setting_data_fields['rules_refund_setting'] = array(
				'bookingpress_refund_duration' => array(
					array(
						'required' => true,
						'message'  => esc_html__('Please enter refund duration', 'bookingpress-appointment-booking'),
						'trigger'  => 'blur',
					),
				),	
				'bookingpress_refund_amount' => array(
					array(
						'required' => true,
						'message'  => esc_html__('Please enter refund amount', 'bookingpress-appointment-booking'),
						'trigger'  => 'blur',
					),
				),				
			);

			$bookingpress_dynamic_setting_data_fields['search_delimiter_list'] = array(
				array(
					'text'  => __( 'Comma', 'bookingpress-appointment-booking' ) . ' (,)',
					'value' => ',',
				),
				array(
					'text'  => __( 'Semicolon', 'bookingpress-appointment-booking' ) . ' (;)',
					'value' => ';',
				),

			);
			$bookingpress_dynamic_setting_data_fields['general_setting_form']['bookingpress_export_delimeter'] = ',';
			$bookingpress_dynamic_setting_data_fields['general_setting_form']['default_time_format'] = 'g:i a';			
			$bookingpress_dynamic_setting_data_fields['special_day_form']                                      = array(
				'special_day_date'     => '',
				'start_time'           => '',
				'end_time'             => '',
				'special_day_workhour' => array(),
			);
			$bookingpress_dynamic_setting_data_fields['rules_special_day']                                     = array(
				'special_day_date' => array(
					array(
						'required' => true,
						'message'  => __( 'Please select date', 'bookingpress-appointment-booking' ),
						'trigger'  => 'blur',
					),
				),
				'start_time'       => array(
					array(
						'required' => true,
						'message'  => __( 'Please select start time', 'bookingpress-appointment-booking' ),
						'trigger'  => 'blur',
					),
				),
				'end_time'         => array(
					array(
						'required' => true,
						'message'  => __( 'Please select end time', 'bookingpress-appointment-booking' ),
						'trigger'  => 'blur',
					),
				),
			);
			$bookingpress_dynamic_setting_data_fields['special_day_data_arr']                                  = array();
			$bookingpress_dynamic_setting_data_fields['edit_special_day_id']                                   = '';

			$default_start_time    = '00:00:00';
			$default_end_time      = '23:25:00';
			$step_duration_val     = 05;
			$default_break_timings = array();
			$curr_time             = $tmp_start_time = date( 'H:i:s', strtotime( $default_start_time ) );
			$tmp_end_time          = date( 'H:i:s', strtotime( $default_end_time ) );
			do {
				$tmp_time_obj = new DateTime( $curr_time );
				$tmp_time_obj->add( new DateInterval( 'PT' . $step_duration_val . 'M' ) );
				$end_time                = $tmp_time_obj->format( 'H:i:s' );
				$default_break_timings[] = array(
					'start_time'           => $curr_time,
					'formatted_start_time' => date( $global_data['wp_default_time_format'], strtotime( $curr_time ) ),
					'end_time'             => $end_time,
					'formatted_end_time'   => date( $global_data['wp_default_time_format'], strtotime( $end_time ) ),
				);
				$tmp_time_obj            = new DateTime( $curr_time );
				$tmp_time_obj->add( new DateInterval( 'PT' . $step_duration_val . 'M' ) );
				$curr_time = $tmp_time_obj->format( 'H:i:s' );
			} while ( $curr_time <= $default_end_time );

			$bookingpress_dynamic_setting_data_fields['specialday_hour_list'] = $default_break_timings;

			$default_start_time     = '00:00:00';
			$default_end_time       = '23:25:00';
			$step_duration_val      = 05;
			$default_break_timings2 = array();
			$curr_time              = $tmp_start_time = date( 'H:i:s', strtotime( $default_start_time ) );
			$tmp_end_time           = date( 'H:i:s', strtotime( $default_end_time ) );
			do {
				$tmp_time_obj = new DateTime( $curr_time );
				$tmp_time_obj->add( new DateInterval( 'PT' . $step_duration_val . 'M' ) );
				$end_time                 = $tmp_time_obj->format( 'H:i:s' );
				$default_break_timings2[] = array(
					'start_time'           => $curr_time,
					'formatted_start_time' => date( $global_data['wp_default_time_format'], strtotime( $curr_time ) ),
					'end_time'             => $end_time,
					'formatted_end_time'   => date( $global_data['wp_default_time_format'], strtotime( $end_time ) ),
				);
				$tmp_time_obj             = new DateTime( $curr_time );
				$tmp_time_obj->add( new DateInterval( 'PT' . $step_duration_val . 'M' ) );
				$curr_time = $tmp_time_obj->format( 'H:i:s' );
			} while ( $curr_time <= $default_end_time );

			$bookingpress_dynamic_setting_data_fields['specialday_break_hour_list'] = $default_break_timings2;

			$bookingpress_dynamic_setting_data_fields['general_setting_form']['show_bookingslots_in_client_timezone'] = false;

			$bookingpress_all_services = $BookingPress->get_bookingpress_service_data_group_with_category();
			$bookingpress_dynamic_setting_data_fields['bookingpress_all_services'] = $bookingpress_all_services;

			// Debug logs data variables
			$bookingpress_dynamic_setting_data_fields['debug_log_setting_form']['appointment_debug_logs']        = false;
			$bookingpress_dynamic_setting_data_fields['debug_log_setting_form']['email_notification_debug_logs'] = false;

			$default_daysoff_details = $BookingPress->bookingpress_get_default_dayoff_dates();

			if ( ! empty( $default_daysoff_details ) ) {
				$default_daysoff_details                                   = array_map(
					function( $date ) {
						return date( 'Y-m-d', strtotime( $date ) );
					},
					$default_daysoff_details
				);
				$bookingpress_dynamic_setting_data_fields['disabledDates'] = $default_daysoff_details;
			} else {
				$bookingpress_dynamic_setting_data_fields['disabledDates'] = '';
			}
			$bookingpress_dynamic_setting_data_fields['disabledOtherDates'] = '';

			$bookingpress_dynamic_setting_data_fields['bpa_customer_sortable_data'] = array();
			$bookingpress_dynamic_setting_data_fields['customer_field_settings']  = array();
			$bookingpress_dynamic_setting_data_fields['bpa_deleted_fields']  = array();

			$bookingpress_dynamic_setting_data_fields['is_display_preset_value_loader'] = false;
			$bookingpress_dynamic_setting_data_fields['preset_btn_disable']             = false;

			$bookingpress_preset_fields = array(
				array(
					'id'   => 'countries',
					'name' => esc_html__( 'Countries', 'bookingpress-appointment-booking' ),
				),
				array(
					'id'   => 'us_states',
					'name' => esc_html__( 'U.S. States', 'bookingpress-appointment-booking' ),
				),
				array(
					'id'   => 'us_states_abbr',
					'name' => esc_html__( 'U.S. State Abbreviations', 'bookingpress-appointment-booking' ),
				),
				array(
					'id'   => 'age_group',
					'name' => esc_html__( 'Age Group', 'bookingpress-appointment-booking' ),
				),
				array(
					'id'   => 'satisfaction',
					'name' => esc_html__( 'Satisfaction', 'bookingpress-appointment-booking' ),
				),
				array(
					'id'   => 'days',
					'name' => esc_html__( 'Days', 'bookingpress-appointment-booking' ),
				),
				array(
					'id'   => 'week_days',
					'name' => esc_html__( 'Week Days', 'bookingpress-appointment-booking' ),
				),
				array(
					'id'   => 'months',
					'name' => esc_html__( 'Months', 'bookingpress-appointment-booking' ),
				),
				array(
					'id'   => 'years',
					'name' => esc_html__( 'Years', 'bookingpress-appointment-booking' ),
				),
				array(
					'id'   => 'prefix',
					'name' => esc_html__( 'Prefix', 'bookingpress-appointment-booking' ),
				),
				array(
					'id'   => 'telephone_code',
					'name' => esc_html__( 'Telephone Country Code', 'bookingpress-appointment-booking' ),
				),
			);

			$bookingpress_dynamic_setting_data_fields['bookingpress_tab_list'] = array();
			$bookingpress_dynamic_setting_data_fields['bookingpress_optin_tab_list'] = array();
			$bookingpress_dynamic_setting_data_fields['bookingpress_preset_fields'] = $bookingpress_preset_fields;
			$bookingpress_dynamic_setting_data_fields['optin_setting_rule'] = array();
			$bookingpress_dynamic_setting_data_fields['optin_setting_form'] = array();

			$bookingpress_dynamic_setting_data_fields['license_form'] = array('license_key' => '','error_message' => '','success_message' => '','license_package' => '4470');
			
			$bookingpress_dynamic_setting_data_fields['company_setting_form']['company_icon_img'] = '';
			$bookingpress_dynamic_setting_data_fields['company_setting_form']['company_icon_url'] = '';
			$bookingpress_dynamic_setting_data_fields['company_setting_form']['company_icon_list'] = array();

			$bpa_booking_page_id = $BookingPress->bookingpress_get_settings('complete_payment_page_id', 'general_setting');

			//Get all wp pages
			$bpa_new_wp_pages = array();
			$bpa_wp_pages = get_pages();
			if(!empty($bpa_wp_pages)){
				foreach($bpa_wp_pages as $bpa_wp_page_key => $bpa_wp_page_val){
					$bpa_new_wp_pages[] = array(
						'id' => $bpa_wp_page_val->ID,
						'title' => $bpa_wp_page_val->post_title,
						'url' => get_permalink(get_page_by_path($bpa_wp_page_val->post_name)),
					);
				}
			}

			$bookingpress_dynamic_setting_data_fields['complete_payment_pages'] = $bpa_new_wp_pages;
			$bookingpress_dynamic_setting_data_fields['general_setting_form']['complete_payment_page_id'] = $bpa_booking_page_id;

			$bookingpress_dynamic_setting_data_fields['daysoff_timezone'] = 'UTC';

			/* refund policy data */

			$bookingpress_dynamic_setting_data_fields['payment_setting_form']['bookingpress_refund_on_cancellation'] = false;
			$bookingpress_dynamic_setting_data_fields['payment_setting_form']['bookingpress_refund_mode'] = 'full';
			$bookingpress_dynamic_setting_data_fields['payment_setting_form']['bookingpress_refund_on_partial'] = false;

			$bookingpress_dynamic_setting_data_fields['refund_setting_form']['bookingpress_refund_duration']= 1;
			$bookingpress_dynamic_setting_data_fields['refund_setting_form']['bookingpress_refund_duration_unit'] = 'h';
			$bookingpress_dynamic_setting_data_fields['refund_setting_form']['bookingpress_refund_amount'] = 0;
			$bookingpress_dynamic_setting_data_fields['refund_setting_form']['bookingpress_refund_amount_unit'] = 'percentage';

			$bookingpress_dynamic_setting_data_fields['payment_setting_form']['bookingpress_partial_refund_rules'] = array();			

			$bookingpress_payment_deafult_currency   = $BookingPress->bookingpress_get_settings('payment_default_currency', 'payment_setting');
            		$bookingpress_payment_deafult_currency   = $BookingPress->bookingpress_get_currency_symbol($bookingpress_payment_deafult_currency);
			$bookingpress_dynamic_setting_data_fields['bookingpress_payment_deafult_currency'] = $bookingpress_payment_deafult_currency;

			return $bookingpress_dynamic_setting_data_fields;
		}

		function boookingpress_after_save_settings_data( $save_data ) {
			global $BookingPress, $bookingpress_pro_staff_members;

			$bpa_save_settings_data = (array) $save_data; // phpcs:ignore WordPress.Security.NonceVerification
			$bpa_setting_type       = sanitize_text_field($_POST['settingType']); // phpcs:ignore
			
			foreach ( $bpa_save_settings_data as $bookingpress_setting_key => $bookingpress_setting_val ) {
				if ($bookingpress_setting_key == 'company_icon_url' && ! empty($bookingpress_setting_val) ) {
					$bookingpress_avatar_url        = $bookingpress_setting_val;
					$bookingpress_upload_image_name = isset($_POST['company_icon_img']) ? sanitize_file_name($_POST['company_icon_img']) : ''; // phpcs:ignore WordPress.Security.NonceVerification

					$upload_dir                 = BOOKINGPRESS_UPLOAD_DIR . '/';
					$bookingpress_new_file_name = current_time('timestamp') . '_' . $bookingpress_upload_image_name;
					$upload_path                = $upload_dir . $bookingpress_new_file_name;

					$bookingpress_upload_res = new bookingpress_fileupload_class( $bookingpress_avatar_url, true );

					$bookingpress_upload_res->bookingpress_process_upload( $upload_path );

					$bookingpress_setting_val = BOOKINGPRESS_UPLOAD_URL . '/' . $bookingpress_new_file_name;

					$bookingpress_file_name_arr = explode('/', $bookingpress_avatar_url);
					$bookingpress_file_name     = $bookingpress_file_name_arr[ count($bookingpress_file_name_arr) - 1 ];
					if( file_exists( BOOKINGPRESS_TMP_IMAGES_DIR . '/' . $bookingpress_file_name ) ){
						@unlink(BOOKINGPRESS_TMP_IMAGES_DIR . '/' . $bookingpress_file_name);
					}
				}

				if($bookingpress_setting_key == "company_icon_url" || $bookingpress_setting_key == "company_icon_img"){
					$bookingpress_res = $BookingPress->bookingpress_update_settings($bookingpress_setting_key, $bpa_setting_type, $bookingpress_setting_val);
				}
			}

			$bookingpress_setting_type = ! empty( $save_data['settingType'] ) ? sanitize_text_field( $save_data['settingType'] ) : '';

			if($bookingpress_setting_type == 'general_setting' && !empty($save_data['default_country_type']) && $save_data['default_country_type'] == 'indentify_by_ip') {
				$BookingPress->bookingpress_update_settings('default_phone_country_code','general_setting','auto_detect');
			}

			if ( $bookingpress_pro_staff_members->bookingpress_check_staffmember_module_activation() ) {

				unset( $save_data['bookingpress_staffmember_auto_assign_rule'] );
				unset( $save_data['bookingpress_staffmember_any_staff_options'] );
				unset( $save_data['settingType'] );
				unset( $save_data['action'] );
				unset( $save_data['_wpnonce'] );

				if ( $bookingpress_setting_type == 'staffmember_setting' ) {
					 $bookingpress_pro_staff_members->bookingpress_assign_capability( $save_data );
				}
			}

			if( ! empty( $save_data['customer_field_settings'] ) ){
				global $wpdb, $tbl_bookingpress_form_fields;
				$bpa_customer_field_settings = $save_data['customer_field_settings'];
				foreach( $bpa_customer_field_settings as $bpa_customer_field_key => $bpa_customer_field_val ){
					$bpa_customer_db_fields = array(
						'bookingpress_form_field_name' => $bpa_customer_field_val['field_name'],
						'bookingpress_field_required'      => 0,
						'bookingpress_field_label'         => $bpa_customer_field_val['label'],
						'bookingpress_field_placeholder'   => $bpa_customer_field_val['placeholder'],
						'bookingpress_field_is_hide'       => 0,
						'bookingpress_is_customer_field'   => 1,
						'bookingpress_field_position'      => $bpa_customer_field_val['field_position'],
					);

					$bpa_existing_field_id = $bpa_customer_field_val['id'];
					
					if(!empty($bpa_customer_field_val['field_values'])) {
						$bpa_customer_field_val['field_values'] = stripslashes_deep($bpa_customer_field_val['field_values']);						
					}

                    $bpa_customer_db_fields = apply_filters('bookingpress_modify_form_field_data_before_save', $bpa_customer_db_fields, $bpa_customer_field_val);

					$field_exist = $wpdb->get_var($wpdb->prepare("SELECT COUNT(bookingpress_form_field_id) as total FROM {$tbl_bookingpress_form_fields} WHERE bookingpress_form_field_id = %d", $bpa_existing_field_id)); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_form_fields is table name.

					if ($field_exist > 0 ) {
                        
						$wpdb->update($tbl_bookingpress_form_fields, $bpa_customer_db_fields, array( 'bookingpress_form_field_id' => $bpa_existing_field_id ));
						//bookingpress_field_meta_key
						$field_meta_key = !empty( $bpa_customer_db_fields['bookingpress_field_meta_key'] ) ? $bpa_customer_db_fields['bookingpress_field_meta_key'] : $bpa_customer_db_fields['bookingpress_field_meta_key'];

						if( !empty( $field_meta_key ) ){
							$bpa_form_field = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM `{$tbl_bookingpress_form_fields}` WHERE bookingpress_field_meta_key = %s AND bookingpress_is_customer_field = %d", $field_meta_key, 0 ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_form_fields is table name.
							if( !empty( $bpa_form_field ) ){
								$updated_field_data = array(
									'bookingpress_field_label' 		 => $bpa_customer_db_fields['bookingpress_field_label'],
									'bookingpress_field_placeholder' => $bpa_customer_db_fields['bookingpress_field_placeholder'],
									'bookingpress_field_meta_key' 	 => $bpa_customer_db_fields['bookingpress_field_meta_key'],
									'bookingpress_field_values' 	 => $bpa_customer_db_fields['bookingpress_field_values']
								);

								if( 'date' == $bpa_customer_db_fields['bookingpress_field_type']){
									$bpa_field_options = json_decode( $bpa_form_field->bookingpress_field_options, true );
									
									$bpa_field_options['enable_timepicker'] = $bpa_customer_field_val['field_options']['enable_timepicker'];

									$updated_field_data['bookingpress_field_options'] = json_encode( $bpa_field_options );
								}

								$wpdb->update(
									$tbl_bookingpress_form_fields,
									$updated_field_data,
									array(
										'bookingpress_form_field_id' => $bpa_form_field->bookingpress_form_field_id
									)
								);
							}
						}
						
                    } else {
                        $wpdb->insert($tbl_bookingpress_form_fields, $bpa_customer_db_fields);
                        $bpa_existing_field_id = $wpdb->insert_id;
                    }
				}

				do_action( 'bookingpress_delete_removed_fields', $bpa_customer_field_settings );
			}

			if( 'customer_setting' == $bookingpress_setting_type ){
				if( empty( $save_data['customer_field_settings'] ) ){
					do_action( 'bookingpress_delete_removed_fields', array() );
				}
			}
			
		}

		function bookingpress_modify_save_setting_data_func($bookingpress_save_settings_data,$posted_data) {			

			if(!empty($posted_data['settingType']) && $posted_data['settingType'] == 'payment_setting' && isset($bookingpress_save_settings_data['bookingpress_partial_refund_rules'])) {				
				$bookingpress_save_settings_data['bookingpress_partial_refund_rules'] = maybe_serialize($bookingpress_save_settings_data['bookingpress_partial_refund_rules']);
			} elseif(!empty($posted_data['settingType']) && $posted_data['settingType'] == 'payment_setting') {
				$bookingpress_save_settings_data['bookingpress_partial_refund_rules'] = '';
			}

			return $bookingpress_save_settings_data;
		}
	}
}
global $bookingpress_pro_settings;
$bookingpress_pro_settings = new bookingpress_pro_settings();
