<?php
$bookingpress_geoip_file = BOOKINGPRESS_PRO_LIBRARY_DIR . '/geoip/autoload.php';
require $bookingpress_geoip_file;
use GeoIp2\Database\Reader;

if ( ! class_exists( 'bookingpress_pro_customers' ) ) {
	class bookingpress_pro_customers Extends BookingPress_Core {
		var $bookingpress_global_data;

		function __construct() {
			// global $bookingpress_global_options;
			// $this->bookingpress_global_data = $bookingpress_global_options->bookingpress_global_options();

			add_filter( 'bookingpress_modify_customer_view_file_path', array( $this, 'bookingpress_modify_customer_file_path_func' ), 10 );
			add_filter( 'bookingpress_modify_customer_data_fields', array( $this, 'bookingpress_modify_customer_data_fields_func' ), 10 );
			add_action( 'bookingpress_customer_add_dynamic_vue_methods', array( $this, 'bookingpress_customer_add_dynamic_vue_methods_func' ), 10 );
			add_action( 'wp_ajax_bookingpress_export_customer_data', array( $this, 'bookingpress_export_customer_data_func' ), 10 );

			add_action( 'bookingpress_after_update_customer', array( $this, 'bookingpress_customer_update_meta') );
			add_filter( 'bookingpress_modify_edit_customer_details', array( $this, 'bookingpress_get_customer_edit_field_data' ), 10, 2 );
			add_action( 'bookingpress_customer_edit_details', array( $this, 'bookingpress_customer_edit_details_callback' ) );
			add_action( 'bookingpress_reset_customer_fields_data', array( $this, 'bookingpress_reset_customer_fields_data_callback' ) );
		}
		
		/**
		 * Function for modify customer data variables
		 *
		 * @param  mixed $bookingpress_customer_vue_data_fields
		 * @return void
		 */
		function bookingpress_modify_customer_data_fields_func( $bookingpress_customer_vue_data_fields ) {
			global $wpdb,$tbl_bookingpress_customers, $tbl_bookingpress_form_fields,$BookingPressPro;
			$bookingpress_customer_vue_data_fields['is_export_button_loader']    = '0';
			$bookingpress_customer_vue_data_fields['is_export_button_disabled']  = false;
			$bookingpress_customer_vue_data_fields['ExportCustomer']             = false;
			$bookingpress_customer_vue_data_fields['is_mask_display']            = false;
			$bookingpress_customer_vue_data_fields['export_customer_top_pos']    = '210px';
			$bookingpress_customer_vue_data_fields['export_customer_right_pos']  = '80px';
			$bookingpress_customer_vue_data_fields['export_customer_left_pos']   = 'auto';
			$bookingpress_customer_vue_data_fields['customer_export_field_list'] = array(
				array(
					'name' => 'first_name',
					'text' => __( 'First Name', 'bookingpress-appointment-booking' ),
				),
				array(
					'name' => 'last_name',
					'text' => __( 'Last Name', 'bookingpress-appointment-booking' ),
				),
				array(
					'name' => 'email',
					'text' => __( 'Email', 'bookingpress-appointment-booking' ),
				),
				array(
					'name' => 'phone',
					'text' => __( 'Phone', 'bookingpress-appointment-booking' ),
				),
				array(
					'name' => 'note',
					'text' => __( 'Note', 'bookingpress-appointment-booking' ),
				),
				array(
					'name' => 'last_appointment',
					'text' => __( 'Last Appointment', 'bookingpress-appointment-booking' ),
				),
				array(
					'name' => 'total_appointments',
					'text' => __( 'Total Appointments', 'bookingpress-appointment-booking' ),
				),
			);
			$bookingpress_customer_vue_data_fields['export_checked_field'] = array( 'first_name', 'last_name', 'email', 'phone', 'note', 'last_appointment', 'total_appointments' );

			$bpa_customer_form_fields = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM `{$tbl_bookingpress_form_fields}` WHERE bookingpress_is_customer_field = %d ORDER BY bookingpress_field_position ASC", 1 ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_form_fields is table name.
            $bpa_customer_fields = array();
            if( !empty( $bpa_customer_form_fields ) ){
				foreach( $bpa_customer_form_fields as $x => $cs_form_fields ){
					$bpa_customer_fields[ $x ] = $cs_form_fields;
					$bpa_customer_fields[ $x ]['bookingpress_field_label']  = stripslashes_deep($cs_form_fields['bookingpress_field_label']);
					$bpa_customer_fields[ $x ]['bookingpress_field_error_message']  = stripslashes_deep($cs_form_fields['bookingpress_field_error_message']);
					$bpa_customer_fields[ $x ]['bookingpress_field_placeholder']  = stripslashes_deep($cs_form_fields['bookingpress_field_placeholder']);
                    $bpa_customer_fields[ $x ]['bookingpress_field_values'] = json_decode( $cs_form_fields['bookingpress_field_values'], true );
                    $bpa_customer_fields[ $x ]['bookingpress_field_options'] = json_decode( $cs_form_fields['bookingpress_field_options'], true );
                    $bpa_customer_fields[ $x ]['bookingpress_field_key'] = '';//$cs_form_fields['bookingpress_field_meta_key'];
                    if( 'checkbox' == $cs_form_fields['bookingpress_field_type'] ){
                        $bpa_customer_fields[ $x ]['bookingpress_field_key'] = array();
                        foreach( $bpa_customer_fields[ $x ]['bookingpress_field_values'] as $chk_key => $chk_val ){
                            //$bpa_customer_fields[ $x ]['bookingpress_field_key'][ $chk_key ] = false;
							$bookingpress_customer_vue_data_fields['customer']['bpa_customer_field'][ $cs_form_fields['bookingpress_field_meta_key'] . '_' . $chk_key ] = false;
                        }
                    } else {
						$bookingpress_customer_vue_data_fields['customer']['bpa_customer_field'][$cs_form_fields['bookingpress_field_meta_key']] = $bpa_customer_fields[ $x ]['bookingpress_field_key'];
					}
                }
            }
            $bookingpress_customer_vue_data_fields['bookingpress_customer_fields'] = $bpa_customer_fields;

			if ( ! empty( $bookingpress_customer_vue_data_fields['bookingpress_tel_input_props']['defaultCountry'] ) && $bookingpress_customer_vue_data_fields['bookingpress_tel_input_props']['defaultCountry'] == 'auto_detect' ) {
				// Get visitors ip address
				$bookingpress_ip_address = $BookingPressPro->boookingpress_get_visitor_ip();
				try {
					$bookingpress_country_reader = new Reader( BOOKINGPRESS_PRO_LIBRARY_DIR . '/geoip/inc/GeoLite2-Country.mmdb' );
					$bookingpress_country_record = $bookingpress_country_reader->country( $bookingpress_ip_address );
					if ( ! empty( $bookingpress_country_record->country ) ) {
						$bookingpress_country_name     = $bookingpress_country_record->country->name;
						$bookingpress_country_iso_code = $bookingpress_country_record->country->isoCode;
						$bookingpress_customer_vue_data_fields['bookingpress_tel_input_props']['defaultCountry'] = $bookingpress_country_iso_code;
					}
				} catch ( Exception $e ) {
					$bookingpress_error_message = $e->getMessage();
				}
			}

			return $bookingpress_customer_vue_data_fields;
		}
		
		/**
		 * Function for modify customer view filepath
		 *
		 * @param  mixed $bookingpress_customer_view_path
		 * @return void
		 */
		function bookingpress_modify_customer_file_path_func( $bookingpress_customer_view_path ) {
			$bookingpress_customer_view_path = BOOKINGPRESS_PRO_VIEWS_DIR . '/customers/manage_customers.php';
			return $bookingpress_customer_view_path;
		}
		
		/**
		 * Callback Function for export customer data
		 *
		 * @return void
		 */
		function bookingpress_export_customer_data_func() {
			global $wpdb, $tbl_bookingpress_customers, $tbl_bookingpress_appointment_bookings,$BookingPress, $bookingpress_global_options;
			$response              = array();
			$bpa_check_authorization = $this->bpa_check_authentication( 'export_customer_details', true, 'bpa_wp_nonce' );           
			if( preg_match( '/error/', $bpa_check_authorization ) ){
				$bpa_auth_error = explode( '^|^', $bpa_check_authorization );
				$bpa_error_msg = !empty( $bpa_auth_error[1] ) ? $bpa_auth_error[1] : esc_html__( 'Sorry. Something went wrong while processing the request', 'bookingpress-appointment-booking');

				$response['variant'] = 'error';
				$response['title'] = esc_html__( 'Error', 'bookingpress-appointment-booking');
				$response['msg'] = $bpa_error_msg;

				wp_send_json( $response );
				die;
			}
			$bookingpress_export_field = ! empty( $_REQUEST['export_field'] ) ? array_map( array( $BookingPress, 'appointment_sanatize_field' ), $_REQUEST['export_field'] ) : array();// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized --Reason $_REQUEST contains mixed array and will be sanitized using 'appointment_sanatize_field' function
			$bookingpress_search_data  = ! empty( $_REQUEST['search_data'] ) ? array_map( array( $BookingPress, 'appointment_sanatize_field' ), $_REQUEST['search_data'] ) : array();// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized --Reason $_REQUEST contains mixed array and will be sanitized using 'appointment_sanatize_field' function

			$bookingpress_search_query = $bookingpress_search_query_join = '';

			if ( ! empty( $bookingpress_search_data['search_name'] ) ) {
				$bookingpress_search_customer_name = explode( ' ', $bookingpress_search_data['search_name'] );
				$bookingpress_search_query        .= ' AND (';
				$search_loop_counter               = 1;
				foreach ( $bookingpress_search_customer_name as $bookingpress_search_customer_key => $bookingpress_search_customer_val ) {
					if ( $search_loop_counter > 1 ) {
						$bookingpress_search_query .= ' OR';
					}
					$bookingpress_search_query .= " (bookingpress_user_login LIKE '%{$bookingpress_search_customer_val}%' OR bookingpress_user_email LIKE '%{$bookingpress_search_customer_val}%' OR bookingpress_user_firstname LIKE '%{$bookingpress_search_customer_val}%' OR bookingpress_user_lastname LIKE '%{$bookingpress_search_customer_val}%')";

					$search_loop_counter++;
				}
				$bookingpress_search_query .= ' )';
			}
			if (! empty($bookingpress_search_data['search_date_range']) ) {
                $bookingpress_search_date         = $bookingpress_search_data['search_date_range'];
                $start_date                       = date('Y-m-d', strtotime($bookingpress_search_date[0]));
                $end_date                         = date('Y-m-d', strtotime($bookingpress_search_date[1]));
                $bookingpress_search_query .= " AND (bookingpress_user_created BETWEEN '".$start_date." 00:00:00' AND '".$end_date." 23:59:59')";
            }
			$bookingpress_search_query_join = apply_filters( 'bookingpress_customer_export_join_data_filter', $bookingpress_search_query_join );

			$bookingpress_search_query = apply_filters( 'bookingpress_customer_export_data_filter', $bookingpress_search_query );

			$get_customers          = $wpdb->get_results( 'SELECT cs.* FROM ' . $tbl_bookingpress_customers . ' as cs ' . $bookingpress_search_query_join . ' WHERE cs.bookingpress_user_type = 2 AND cs.bookingpress_user_status = 1 ' . $bookingpress_search_query . ' order by cs.bookingpress_customer_id DESC', ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared --Reason: $tbl_bookingpress_customers is a table name. false alarm
			$bookingpress_customers = array();

			if ( ! empty( $get_customers ) && ! empty( $bookingpress_export_field ) ) {
				$bookingpress_global_options_arr = $bookingpress_global_options->bookingpress_global_options();
				$default_date_time_format        = $bookingpress_global_options_arr['wp_default_date_format'] . ' ' . $bookingpress_global_options_arr['wp_default_time_format'];
				foreach ( $get_customers as $customer ) {
					$bookingpress_customer_tmp_details = array();
					$bookingpress_customer_id          = intval( $customer['bookingpress_customer_id'] );
					if ( in_array( 'first_name', $bookingpress_export_field ) ) {
						$bookingpress_customer_tmp_details['First Name'] = ! empty( $customer['bookingpress_user_firstname'] ) ? '"' . sanitize_text_field( $customer['bookingpress_user_firstname'] ) . '"' : '-';
					}
					if ( in_array( 'last_name', $bookingpress_export_field ) ) {
						$bookingpress_customer_tmp_details['Last Name'] = ! empty( $customer['bookingpress_user_lastname'] ) ? '"' . sanitize_text_field( $customer['bookingpress_user_lastname'] ) . '"' : '-';
					}
					if ( in_array( 'email', $bookingpress_export_field ) ) {
						$bookingpress_customer_tmp_details['Email'] = ! empty( $customer['bookingpress_user_email'] ) ? '"' . sanitize_email( $customer['bookingpress_user_email'] ) . '"' : '-';
					}
					if ( in_array( 'phone', $bookingpress_export_field ) ) {
						$bookingpress_customer_tmp_details['Phone'] = ! empty( $customer['bookingpress_user_phone'] ) ? '"' . sanitize_text_field( $customer['bookingpress_user_phone'] ) . '"' : '-';
					}
					if ( in_array( 'note', $bookingpress_export_field ) ) {
						$bookingpress_customer_note_data           = $BookingPress->get_bookingpress_customersmeta( $customer['bookingpress_customer_id'], 'customer_note' );
						$bookingpress_customer_tmp_details['Note'] = ! empty( $bookingpress_customer_note_data ) ? '"' . sanitize_textarea_field( $bookingpress_customer_note_data ) . '"' : '-';
					}
					if ( in_array( 'last_appointment', $bookingpress_export_field ) ) {
						$last_appointment_data = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$tbl_bookingpress_appointment_bookings} WHERE bookingpress_customer_id = %d ORDER BY bookingpress_appointment_booking_id DESC LIMIT 1", $bookingpress_customer_id ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_appointment_bookings is a table name. false alarm

						$last_appointment_booked_datetime                      = ! empty( $last_appointment_data['bookingpress_created_at'] ) ? date( $default_date_time_format, strtotime( $last_appointment_data['bookingpress_created_at'] ) ) : '-';
						$bookingpress_customer_tmp_details['Last Appointment'] = ! empty( $last_appointment_booked_datetime ) ? '"' . sanitize_text_field( $last_appointment_booked_datetime ) . '"' : '-';
					}
					if ( in_array( 'total_appointments', $bookingpress_export_field ) ) {
						$total_appointments                                      = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(bookingpress_appointment_booking_id) FROM {$tbl_bookingpress_appointment_bookings} WHERE bookingpress_customer_id = %d", $bookingpress_customer_id ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_appointment_bookings is a table name. false alarm
						$bookingpress_customer_tmp_details['Total Appointments'] = ! empty( $total_appointments ) ? '"' . sanitize_text_field( $total_appointments ) . '"' : '0';
					}				
					$bookingpress_customers[] = $bookingpress_customer_tmp_details;
				}
			} else {
				$bookingpress_customers = array();
			}
			$data = array();
			if ( ! empty( $bookingpress_customers ) ) {
				array_push( $data, array_keys( $bookingpress_customers[0] ) );
				foreach ( $bookingpress_customers as $key => $value ) {
					array_push( $data, array_values( $value ) );
				}
			}
			$response['status'] = 'success';
			$response['data']   = $data;
			echo wp_json_encode( $response );
			exit;
		}
		
		/**
		 * Function for add dynamic vue methods for customer
		 *
		 * @return void
		 */
		function bookingpress_customer_add_dynamic_vue_methods_func() {
			global $BookingPress;
			$bookingpress_export_delimeter = $BookingPress->bookingpress_get_settings( 'bookingpress_export_delimeter', 'general_setting' );
			?>
			Bookingpress_export_customer_data( currentElement ){
				const vm = this;
				vm.ExportCustomer = true;

				if( typeof vm.bpa_adjust_popup_position != 'undefined' ){
					vm.bpa_adjust_popup_position( currentElement, 'div#customer_export_model .el-dialog.bpa-dailog__small');
				}
			},
			close_export_customer_model(){
				const vm = this;
				vm.ExportCustomer = false;
				vm.export_checked_field = ['first_name','last_name','email','phone','note','last_appointment','total_appointments','pending_appointments'];
			},
			bookingpress_export_customer(){
				const vm = this;	
				vm.is_export_button_disabled= true;
				vm.is_export_button_loader= '1';
				var bookingpress_search_data = { search_name: vm.customerSearch,search_date_range: vm.customer_search_range }
				var customer_export_data = {
					action:'bookingpress_export_customer_data',
					export_field: vm.export_checked_field,
					search_data : bookingpress_search_data,
					_wpnonce: '<?php echo esc_html( wp_create_nonce( 'bpa_wp_nonce' ) ); ?>'
				}								
				axios.post( appoint_ajax_obj.ajax_url, Qs.stringify( customer_export_data ) )
				.then(function(response) {																		
					vm.is_export_button_disabled= false;
					vm.is_export_button_loader= '0';					
					vm.close_export_customer_model();									
					if(response.data.data != 'undefined') {
						var export_data;
						var csv = ''; 
						if(response.data.data != '') {
							export_data = response.data.data;						
							export_data.forEach(function(row){					    				
								csv += row.join('<?php echo esc_html( $bookingpress_export_delimeter ); ?>');
								   csv += "\n";
							});	 
						}		
						const anchor = document.createElement('a');
						anchor.href = 'data:text/csv;charset=utf-8,' + encodeURIComponent(csv);	
						anchor.target = '_blank';
						anchor.download = 'Bookingpress-export-customer.csv';					    
						anchor.click();
					}					
				}).catch(function(error){
					console.log(error);
					vm.$notify({
						title: '<?php esc_html_e( 'Error', 'bookingpress-appointment-booking' ); ?>',
						message: '<?php esc_html_e( 'Something went wrong..', 'bookingpress-appointment-booking' ); ?>',
						type: 'error',
						customClass: 'error_notification',
					});
				});											
			},			
			bpa_get_customer_formatted_date( event, field_meta_key,is_time_enable ){
				const vm = this;
				if(event != null){
					if(is_time_enable == 'true'){
						let formatted_date = vm.get_formatted_datetime( event );
						vm.customer.bpa_customer_field[ field_meta_key ] = formatted_date;
					} else {
						let formatted_date = vm.get_formatted_date( event );
						vm.customer.bpa_customer_field[ field_meta_key ] = formatted_date;
					}
				}
			},
			<?php
		}
		
		/**
		 * Function for update customer details after updating customer details
		 *
		 * @param  mixed $bookingpress_customer_id
		 * @return void
		 */
		function bookingpress_customer_update_meta( $bookingpress_customer_id ){

			global $wpdb, $tbl_bookingpress_customers, $tbl_bookingpress_customers_meta, $BookingPress;

			$customer_metadata = !empty( $_POST['bpa_customer_field'] ) ? array_map( array( $BookingPress, 'appointment_sanatize_field' ), $_POST['bpa_customer_field'] ) : array();  // phpcs:ignore
			
			if( !empty( $customer_metadata ) ){

				$is_wp_user = $wpdb->get_var( $wpdb->prepare( "SELECT bookingpress_wpuser_id FROM `{$tbl_bookingpress_customers}` WHERE bookingpress_customer_id = %d", $bookingpress_customer_id ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_customers is table name.
				
				foreach( $customer_metadata as $customer_metakey => $customer_metavalue ){
					$meta_val = is_array( $customer_metavalue ) ? json_encode( $customer_metavalue ) : $customer_metavalue;
					
					$BookingPress->update_bookingpress_customersmeta( $bookingpress_customer_id, $customer_metakey, $meta_val );

					if( $is_wp_user > 0 ){
						update_user_meta( $is_wp_user, $customer_metakey, $meta_val );
					}
				}

			}

		}
		
		/**
		 * Function for reset customer fields
		 *
		 * @return void
		 */
		function bookingpress_reset_customer_fields_data_callback(){
			global $wpdb, $tbl_bookingpress_form_fields;
			$all_customer_fields = $wpdb->get_results( $wpdb->prepare( "SELECT bookingpress_field_type, bookingpress_field_values,bookingpress_field_meta_key FROM `{$tbl_bookingpress_form_fields}` WHERE bookingpress_is_customer_field = %d", 1 ), ARRAY_A ); //phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason $tbl_bookingpress_form_fields is a table name.

			$bookingpress_customer_metakeys = array();
			if( !empty( $all_customer_fields ) ){
				foreach( $all_customer_fields as $customer_field_data ){
					$bookingpress_field_type = $customer_field_data['bookingpress_field_type'];
					$bookingpress_field_key = $customer_field_data['bookingpress_field_meta_key'];
					if( 'checkbox' == $bookingpress_field_type ){
						$bookingpress_field_values = json_decode( $customer_field_data['bookingpress_field_values'], true );
						if( !empty( $bookingpress_field_values ) ){
							foreach( $bookingpress_field_values as $k => $v ){
								$customer_meta_key = $bookingpress_field_key.'_'.$k;
								array_push( $bookingpress_customer_metakeys, $customer_meta_key );
							}
						}
					} else {
						array_push( $bookingpress_customer_metakeys, $bookingpress_field_key );
					}
				}
			}

			if( !empty( $bookingpress_customer_metakeys ) ){
				foreach( $bookingpress_customer_metakeys as $metakey ){
					echo 'vm2.customer.bpa_customer_field["' . esc_html( $metakey ) . '"]="";';
				}
			}

		}
		
		/**
		 * Function for get edit customers details
		 *
		 * @param  mixed $bookingpress_edit_customer_details
		 * @param  mixed $bookingpress_customer_id
		 * @return void
		 */
		function bookingpress_get_customer_edit_field_data( $bookingpress_edit_customer_details, $bookingpress_customer_id ){
			global $wpdb, $tbl_bookingpress_customers_meta, $tbl_bookingpress_form_fields, $BookingPress;

			if( !empty( $bookingpress_customer_id ) ){
				//$bpa_customer_metadata = $wpdb->get_results( $wpdb->prepare( "SELECT bookingpress_customersmeta_key, bookingpress_customersmeta_value FROM `{$tbl_bookingpress_customers_meta}` WHERE bookingpress_customer_id = %d", $bookingpress_customer_id ) );

				$all_customer_fields = $wpdb->get_results( $wpdb->prepare( "SELECT bookingpress_field_type, bookingpress_field_values,bookingpress_field_meta_key FROM `{$tbl_bookingpress_form_fields}` WHERE bookingpress_is_customer_field = %d", 1 ), ARRAY_A ); //phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason $tbl_bookingpress_form_fields is a table name.

				$bookingpress_customer_metadata = array();
				if( !empty( $all_customer_fields ) ){
					foreach( $all_customer_fields as $customer_field_data ){
						$bookingpress_field_type = $customer_field_data['bookingpress_field_type'];
						$bookingpress_field_key = $customer_field_data['bookingpress_field_meta_key'];
						if( 'checkbox' == $bookingpress_field_type ){
							$bookingpress_field_values = json_decode( $customer_field_data['bookingpress_field_values'], true );
							if( !empty( $bookingpress_field_values ) ){
								foreach( $bookingpress_field_values as $k => $v ){
									$customer_meta_key = $bookingpress_field_key.'_'.$k;
									$customer_db_data = $BookingPress->get_bookingpress_customersmeta( $bookingpress_customer_id, $customer_meta_key );
									if( !empty( $customer_db_data ) ){
										if( 'true' == $customer_db_data ){
											$bookingpress_edit_customer_details['customer_metadata'][$customer_meta_key] = true;
										} else {
											$bookingpress_edit_customer_details['customer_metadata'][$customer_meta_key] = $customer_db_data;
										}
									} else {
										$bookingpress_edit_customer_details['customer_metadata'][$customer_meta_key] = '';
									}
								}
							}
						} else {
							$customer_db_data = $BookingPress->get_bookingpress_customersmeta( $bookingpress_customer_id, $bookingpress_field_key );
							if( !empty( $customer_db_data ) ){
								$bookingpress_edit_customer_details['customer_metadata'][$bookingpress_field_key] = $customer_db_data;
							} else {
								$bookingpress_edit_customer_details['customer_metadata'][$bookingpress_field_key] = '';
							}
						}
					}
				}
			}

			return $bookingpress_edit_customer_details;
		}
		
		/**
		 * Function for assign edit value
		 *
		 * @return void
		 */
		function bookingpress_customer_edit_details_callback(){
			?>
				if( 'undefined' != typeof edit_customer_details.customer_metadata ){
					for( let meta_key in edit_customer_details.customer_metadata ){
						let meta_value = edit_customer_details.customer_metadata[meta_key];
						vm2.customer['bpa_customer_field'][meta_key] = meta_value;
					}
				}
			<?php
		}

	}
}

global $bookingpress_pro_customers;
$bookingpress_pro_customer = new bookingpress_pro_customers();

