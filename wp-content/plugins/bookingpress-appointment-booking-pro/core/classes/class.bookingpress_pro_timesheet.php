<?php
if ( ! class_exists( 'bookingpress_pro_timesheet' ) ) {
	class bookingpress_pro_timesheet Extends BookingPress_Core {
		function __construct() {
			add_action( 'bookingpress_timesheet_dynamic_view_load', array( $this, 'bookingpress_load_timesheet_view_func' ) );
			add_action( 'bookingpress_timesheet_dynamic_data_fields', array( $this, 'bookingpress_timesheet_dynamic_data_fields_func' ) );
			add_action( 'bookingpress_timesheet_dynamic_on_load_methods', array( $this, 'bookingpress_timesheet_dynamic_onload_methods_func' ) );
			add_action( 'bookingpress_timesheet_dynamic_vue_methods', array( $this, 'bookingpress_timesheet_dynamic_vue_methods_func' ) );
			add_action( 'bookingpress_timesheet_dynamic_helper_vars', array( $this, 'bookingpress_timesheet_dynamic_helper_vars_func' ) );

			add_action( 'wp_ajax_bookingpress_timesheet_add_days_off', array( $this, 'bookingpress_timesheet_add_days_off_func' ) );
			add_action( 'wp_ajax_bookingpress_timesheet_get_days_off', array( $this, 'bookingpress_get_timesheet_daysoff_details_func' ) );
			add_action( 'wp_ajax_bookingpress_timesheet_delete_days_off', array( $this, 'bookingpress_delete_daysoff_func' ) );

			add_action( 'wp_ajax_bookingpress_timesheet_add_staffmember_special_day', array( $this,'bookingpress_timesheet_add_staffmember_special_day_func' ) );
			add_action( 'wp_ajax_bookingpress_timesheet_get_special_days', array( $this, 'bookingpress_timesheet_get_special_days_func' ) );
			add_action( 'wp_ajax_bookingpress_timesheet_delete_staffmember_special_day', array( $this, 'bookingpress_timesheet_delete_staffmember_special_day_func' ) );
			add_action( 'wp_ajax_bookingpress_validate_staff_member_special_day', array( $this, 'bookingpress_validate_staff_member_special_day_func' ) );			
		}
		function bookingpress_validate_staff_member_special_day_func() {
			global $wpdb,$tbl_bookingpress_appointment_bookings,$tbl_bookingpress_staffmembers;
			$response              = array();

			$bpa_check_authorization = $this->bpa_check_authentication( 'timesheet_validate_staff_special_days', true, 'bpa_wp_nonce' );           
			if( preg_match( '/error/', $bpa_check_authorization ) ){
				$bpa_auth_error = explode( '^|^', $bpa_check_authorization );
				$bpa_error_msg = !empty( $bpa_auth_error[1] ) ? $bpa_auth_error[1] : esc_html__( 'Sorry. Something went wrong while processing the request', 'bookingpress-appointment-booking');

				$response['variant'] = 'error';
				$response['title'] = esc_html__( 'Error', 'bookingpress-appointment-booking');
				$response['msg'] = $bpa_error_msg;

				wp_send_json( $response );
				die;
			}
			$bookingpress_current_user_id  = get_current_user_id();
			$bookingpress_staffmember_data = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . $tbl_bookingpress_staffmembers . ' WHERE bookingpress_wpuser_id = %d', $bookingpress_current_user_id ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared --Reason: $tbl_bookingpress_staffmembers is a table name. false alarm
			$bookingpress_staffmember_id   = ! empty( $bookingpress_staffmember_data['bookingpress_staffmember_id'] ) ? intval( $bookingpress_staffmember_data['bookingpress_staffmember_id'] ) : 0;
			if ( ! empty( $_REQUEST['selected_date_range'] ) && ! empty( $bookingpress_staffmember_id ) ) { 
				$bookingpress_start_date         = date( 'Y-m-d', strtotime( sanitize_text_field( $_REQUEST['selected_date_range'][0] ) ) );  // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated --Reason: data has been validated above
				$bookingpress_end_date           = date( 'Y-m-d', strtotime( sanitize_text_field( $_REQUEST['selected_date_range'][1] ) ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated --Reason: data has been validated above
				$bookingpress_status             = array( '1', '2' );
				$total_appointments              = 0;
				$bookingpress_search_query_where = 'WHERE 1=1 ';
				if ( ! empty( $bookingpress_start_date ) && ! empty( $bookingpress_end_date ) && ! empty( $bookingpress_staffmember_id ) ) {
						$bookingpress_search_query_where .= " AND (bookingpress_appointment_date BETWEEN '{$bookingpress_start_date}' AND '{$bookingpress_end_date}') AND (bookingpress_staff_member_id = {$bookingpress_staffmember_id})";
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
				$total_appointments = $wpdb->get_var( 'SELECT COUNT(bookingpress_appointment_booking_id) FROM ' . $tbl_bookingpress_appointment_bookings . ' ' . $bookingpress_search_query_where ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared --Reason: $tbl_bookingpress_appointment_bookings is a table name. false alarm
				if ( $total_appointments > 0 ) {
					$response['variant'] = 'warnning';
					$response['title']   = esc_html__( 'Warning', 'bookingpress-appointment-booking' );
					$response['msg']     = esc_html__( 'one or more appointments are already booked this time duration with this staffmember still you want to add the Special day', 'bookingpress-appointment-booking' );
				} else {
					$response['variant'] = 'success';
					$response['title']   = esc_html__( 'success', 'bookingpress-appointment-booking' );
					$response['msg']     = '';
				}
			}
			echo wp_json_encode( $response );
			exit;
		}

		function bookingpress_timesheet_delete_staffmember_special_day_func() {
			global $wpdb,$tbl_bookingpress_staffmembers_special_day, $tbl_bookingpress_staffmembers_special_day_breaks;
			$response            = array();

			$bpa_check_authorization = $this->bpa_check_authentication( 'timesheet_delete_staffmember_special_days', true, 'bpa_wp_nonce' );           
			if( preg_match( '/error/', $bpa_check_authorization ) ){
				$bpa_auth_error = explode( '^|^', $bpa_check_authorization );
				$bpa_error_msg = !empty( $bpa_auth_error[1] ) ? $bpa_auth_error[1] : esc_html__( 'Sorry. Something went wrong while processing the request', 'bookingpress-appointment-booking');

				$response['variant'] = 'error';
				$response['title'] = esc_html__( 'Error', 'bookingpress-appointment-booking');
				$response['msg'] = $bpa_error_msg;

				wp_send_json( $response );
				die;
			}
			
			if ( ! empty( $_POST['delete_id'] ) ) { // phpcs:ignore
				$bookingpress_delete_id = ! empty( $_POST['delete_id'] ) ? intval( $_POST['delete_id'] ) : 0; // phpcs:ignore

				$wpdb->delete( $tbl_bookingpress_staffmembers_special_day, array( 'bookingpress_staffmember_special_day_id' => $bookingpress_delete_id ) );

				$wpdb->delete( $tbl_bookingpress_staffmembers_special_day_breaks, array( 'bookingpress_special_day_id' => $bookingpress_delete_id ) );

				$response['variant'] = 'success';
				$response['title']   = esc_html__( 'Success', 'bookingpress-appointment-booking' );
				$response['msg']     = esc_html__( 'Special Days Deleted Successfully', 'bookingpress-appointment-booking' );
			}

			echo wp_json_encode( $response );
			exit;
		}

		function bookingpress_timesheet_get_special_days_func() {
			global $wpdb, $BookingPress, $tbl_bookingpress_staffmembers, $tbl_bookingpress_staffmembers_special_day, $tbl_bookingpress_staffmembers_special_day_breaks,$bookingpress_global_options,$bookingpress_pro_staff_members;
			$response                = array();

			$bpa_check_authorization = $this->bpa_check_authentication( 'timesheet_get_special_days', true, 'bpa_wp_nonce' );           
			if( preg_match( '/error/', $bpa_check_authorization ) ){
				$bpa_auth_error = explode( '^|^', $bpa_check_authorization );
				$bpa_error_msg = !empty( $bpa_auth_error[1] ) ? $bpa_auth_error[1] : esc_html__( 'Sorry. Something went wrong while processing the request', 'bookingpress-appointment-booking');

				$response['variant'] = 'error';
				$response['title'] = esc_html__( 'Error', 'bookingpress-appointment-booking');
				$response['msg'] = $bpa_error_msg;

				wp_send_json( $response );
				die;
			}

			$response['specialdays'] = array();
		
			// Find bookingpress staffmember id
			$bookingpress_current_user_id  = get_current_user_id();
			$bookingpress_staffmember_data = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . $tbl_bookingpress_staffmembers . ' WHERE bookingpress_wpuser_id = %d', $bookingpress_current_user_id ), ARRAY_A );// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared --Reason: $tbl_bookingpress_staffmembers is a table name. false alarm
			$bookingpress_staffmember_id   = ! empty( $bookingpress_staffmember_data['bookingpress_staffmember_id'] ) ? intval( $bookingpress_staffmember_data['bookingpress_staffmember_id'] ) : 0;

			$bookingpress_staffmember_specialdays_details = array();

			if ( ! empty( $bookingpress_staffmember_id ) && ! empty( $_REQUEST['action'] ) && sanitize_text_field( $_REQUEST['action'] == 'bookingpress_timesheet_get_special_days' ) ) {
				$bookingpress_global_settings  = $bookingpress_global_options->bookingpress_global_options();
				$bookingpress_date_format      = $bookingpress_global_settings['wp_default_date_format'];
				$bookingpress_time_format      = $bookingpress_global_settings['wp_default_time_format'];
				$bookingpress_special_day      = array();
				$bookingpress_special_day_data = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM ' . $tbl_bookingpress_staffmembers_special_day . ' WHERE bookingpress_staffmember_id = %d ', $bookingpress_staffmember_id ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared --Reason: $tbl_bookingpress_staffmembers_special_day is a table name. false alarm
				if ( ! empty( $bookingpress_special_day_data ) ) {
					foreach ( $bookingpress_special_day_data as $special_day_key => $special_day ) {
						$special_day_arr        = $special_days_breaks = array();
						$special_day_start_date = ! empty( $special_day['bookingpress_special_day_start_date'] ) ? sanitize_text_field( $special_day['bookingpress_special_day_start_date'] ) : '';						
						$special_day_end_date   = ! empty( $special_day['bookingpress_special_day_end_date'] ) ? sanitize_text_field( $special_day['bookingpress_special_day_end_date'] ) : '';
						$special_day_service_id = ! empty( $special_day['bookingpress_special_day_service_id'] ) ? explode( ',', $special_day['bookingpress_special_day_service_id'] ) : '';

						$special_day_id                                      = ! empty( $special_day['bookingpress_staffmember_special_day_id'] ) ? intval( $special_day['bookingpress_staffmember_special_day_id'] ) : '';
						$special_day_arr['id']                               = $special_day_id;
						$special_day_arr['special_day_start_date']           =  date( 'Y-m-d', strtotime( $special_day_start_date ) );$special_day_start_date;
						$special_day_arr['special_day_formatted_start_date'] = date( $bookingpress_date_format, strtotime( sanitize_text_field( $special_day_start_date ) ) );
						$special_day_arr['special_day_end_date']             = date( 'Y-m-d', strtotime( $special_day_end_date ) );
						$special_day_arr['special_day_formatted_end_date']   = date( $bookingpress_date_format, strtotime( $special_day_end_date ) );
						$special_day_arr['start_time']                       = sanitize_text_field( $special_day['bookingpress_special_day_start_time'] );

						$special_day_arr['formatted_start_time'] = date( $bookingpress_time_format, strtotime( sanitize_text_field( $special_day['bookingpress_special_day_start_time'] ) ) );
						$special_day_arr['end_time']             = sanitize_text_field( $special_day['bookingpress_special_day_end_time'] );

						$special_day_arr['formatted_end_time']  = date( $bookingpress_time_format, strtotime( sanitize_text_field( $special_day['bookingpress_special_day_end_time'] ) ) );
						$special_day_arr['special_day_service'] = $special_day_service_id;

						// Fetch all breaks associated with special day
						$bookingpress_special_days_break = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM ' . $tbl_bookingpress_staffmembers_special_day_breaks . ' WHERE bookingpress_special_day_id = %d ', $special_day_id ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared --Reason: $tbl_bookingpress_staffmembers_special_day_breaks is a table name. false alarm

						if ( ! empty( $bookingpress_special_days_break ) && is_array( $bookingpress_special_days_break ) ) {
							foreach ( $bookingpress_special_days_break as $k3 => $v3 ) {
								$break_start_time                                = ! empty( $v3['bookingpress_special_day_break_start_time'] ) ? sanitize_text_field( $v3['bookingpress_special_day_break_start_time'] ) : '';
								$break_end_time                                  = ! empty( $v3['bookingpress_special_day_break_end_time'] ) ? sanitize_text_field( $v3['bookingpress_special_day_break_end_time'] ) : '';
								$special_days_break_data                         = array();
								$special_days_break_data['id']                   = intval( $v3['bookingpress_staffmember_special_day_break_id'] );
								$special_days_break_data['start_time']           = $break_start_time;
								$special_days_break_data['end_time']             = $break_end_time;
								$special_days_break_data['formatted_start_time'] = date( $bookingpress_time_format, strtotime( $break_start_time ) );
								$special_days_break_data['formatted_end_time']   = date( $bookingpress_time_format, strtotime( $break_end_time ) );
								$special_days_breaks[]                           = $special_days_break_data;
							}
						}
						$special_day_arr['special_day_workhour'] = $special_days_breaks;
						$bookingpress_special_day[]              = $special_day_arr;
					}
				}

				$response['msg']                       = esc_html__( 'Staff member Special Day data retrieved successfully.', 'bookingpress-appointment-booking' );
				$response['special_day_data']          = $bookingpress_special_day;
				$response['disabled_special_day_data'] = '';
				$response['variant']                   = 'success';
				$response['title']                     = esc_html__( 'Success', 'bookingpress-appointment-booking' );
			}

			echo wp_json_encode( $response );
			exit;
		}

		function bookingpress_timesheet_add_staffmember_special_day_func() {
			global $wpdb, $BookingPress, $tbl_bookingpress_staffmembers, $tbl_bookingpress_staffmembers_special_day, $tbl_bookingpress_staffmembers_special_day_breaks;

			$response = array();

			$bpa_check_authorization = $this->bpa_check_authentication( 'timesheet_add_staffmember_special_days', true, 'bpa_wp_nonce' );           
			if( preg_match( '/error/', $bpa_check_authorization ) ){
				$bpa_auth_error = explode( '^|^', $bpa_check_authorization );
				$bpa_error_msg = !empty( $bpa_auth_error[1] ) ? $bpa_auth_error[1] : esc_html__( 'Sorry. Something went wrong while processing the request', 'bookingpress-appointment-booking');

				$response['variant'] = 'error';
				$response['title'] = esc_html__( 'Error', 'bookingpress-appointment-booking');
				$response['msg'] = $bpa_error_msg;

				wp_send_json( $response );
				die;
			}
			
			// Find bookingpress staffmember id
			$bookingpress_current_user_id  = get_current_user_id();
			$bookingpress_staffmember_data = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . $tbl_bookingpress_staffmembers . ' WHERE bookingpress_wpuser_id = %d', $bookingpress_current_user_id ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared --Reason: $tbl_bookingpress_staffmembers is a table name. false alarm
			$bookingpress_staffmember_id   = ! empty( $bookingpress_staffmember_data['bookingpress_staffmember_id'] ) ? intval( $bookingpress_staffmember_data['bookingpress_staffmember_id'] ) : 0;

			$bookingpress_specialday_id = ! empty( $_POST['specialday_updateid'] ) ? intval( $_POST['specialday_updateid'] ) : 0; // phpcs:ignore

			if ( ! empty( $_POST['specialdays_details'] ) && ! empty( $bookingpress_staffmember_id ) ) { // phpcs:ignore
				$special_day = ! empty( $_REQUEST['specialdays_details'] ) ? array_map(array( $BookingPress, 'appointment_sanatize_field' ), $_REQUEST['specialdays_details']) : array();  //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized --Reason $_REQUEST['specialdays_details'] has already been sanitized.

				$bookingpress_special_day_start_date = ! empty( $special_day['special_day_date'][0] ) ?  $special_day['special_day_date'][0]  : '';
				$bookingpress_special_day_end_date   = ! empty( $special_day['special_day_date'][1] ) ?  $special_day['special_day_date'][1]  : '';
				$special_day_selected_service        = ( ! empty( $special_day['special_day_service'] ) && is_array( $special_day['special_day_service'] ) ) ? implode( ',', $special_day['special_day_service'] ) : '';
				$special_day_workhour_arr            = ! empty( $special_day['special_day_workhour'] ) ? $special_day['special_day_workhour'] : array();

				$start_time = ! empty( $special_day['start_time'] ) ?  $special_day['start_time']  : '';
				$end_time   = ! empty( $special_day['end_time'] ) ?  $special_day['end_time']  : '';

				$args_special_day = array(
					'bookingpress_staffmember_id'         => $bookingpress_staffmember_id,
					'bookingpress_special_day_start_date' => $bookingpress_special_day_start_date,
					'bookingpress_special_day_end_date'   => $bookingpress_special_day_end_date,
					'bookingpress_special_day_start_time' => $start_time,
					'bookingpress_special_day_end_time'   => $end_time,
					'bookingpress_special_day_service_id' => $special_day_selected_service,
					'bookingpress_created_at'             => current_time( 'mysql' ),
				);

				if ( ! empty( $bookingpress_specialday_id ) ) {
					$wpdb->update( $tbl_bookingpress_staffmembers_special_day, $args_special_day, array( 'bookingpress_staffmember_special_day_id' => $bookingpress_specialday_id ) );
					$wpdb->delete( $tbl_bookingpress_staffmembers_special_day_breaks, array( 'bookingpress_special_day_id' => $bookingpress_specialday_id ) );
					$bookingpress_special_day_reference_id = $bookingpress_specialday_id;
				} else {
					$wpdb->insert( $tbl_bookingpress_staffmembers_special_day, $args_special_day );
					$bookingpress_special_day_reference_id = $wpdb->insert_id;
				}

				if ( ! empty( $special_day_workhour_arr ) ) {
					foreach ( $special_day_workhour_arr as $special_day_workhour_key => $special_day_workhour_val ) {
						$start_time         = ! empty( $special_day_workhour_val['start_time'] ) ?  $special_day_workhour_val['start_time']  : '';
						$end_time           = ! empty( $special_day_workhour_val['end_time'] ) ?  $special_day_workhour_val['end_time']  : '';
						$args_extra_details = array(
							'bookingpress_special_day_id' => intval( $bookingpress_special_day_reference_id ),
							'bookingpress_special_day_break_start_time' => $start_time,
							'bookingpress_special_day_break_end_time' => $end_time,
							'bookingpress_created_at'     => current_time( 'mysql' ),
						);

						if ( empty( $bookingpress_update_id ) ) {
							$wpdb->insert( $tbl_bookingpress_staffmembers_special_day_breaks, $args_extra_details );
						} else {
							$wpdb->update( $tbl_bookingpress_staffmembers_special_day_breaks, $args_extra_details, array( 'bookingpress_special_day__id' => $bookingpress_specialday_id ) );
						}
					}
				}

				$response['variant'] = 'success';
				$response['title']   = esc_html__( 'Success', 'bookingpress-appointment-booking' );
				$response['msg']     = esc_html__( 'Specialdays saved successfully', 'bookingpress-appointment-booking' );
			}
			echo wp_json_encode( $response );
			exit;
		}

		function bookingpress_delete_daysoff_func() {
			global $wpdb, $BookingPress, $tbl_bookingpress_staffmembers, $tbl_bookingpress_staffmembers_daysoff;
			$response = array();

			$bpa_check_authorization = $this->bpa_check_authentication( 'delete_timesheet_daysoff', true, 'bpa_wp_nonce' );           
			if( preg_match( '/error/', $bpa_check_authorization ) ){
				$bpa_auth_error = explode( '^|^', $bpa_check_authorization );
				$bpa_error_msg = !empty( $bpa_auth_error[1] ) ? $bpa_auth_error[1] : esc_html__( 'Sorry. Something went wrong while processing the request', 'bookingpress-appointment-booking');

				$response['variant'] = 'error';
				$response['title'] = esc_html__( 'Error', 'bookingpress-appointment-booking');
				$response['msg'] = $bpa_error_msg;

				wp_send_json( $response );
				die;
			}

			if ( ! empty( $_POST['delete_id'] ) ) { // phpcs:ignore
				$bookingpress_delete_id = ! empty( $_POST['delete_id'] ) ? intval( $_POST['delete_id'] ) : 0; // phpcs:ignore
				if ( ! empty( $bookingpress_delete_id ) ) {
					$wpdb->delete( $tbl_bookingpress_staffmembers_daysoff, array( 'bookingpress_staffmember_daysoff_id' => $bookingpress_delete_id ) );

					$response['variant'] = 'success';
					$response['title']   = esc_html__( 'Success', 'bookingpress-appointment-booking' );
					$response['msg']     = esc_html__( 'Days-off deleted successfully', 'bookingpress-appointment-booking' );
				}
			}

			echo wp_json_encode( $response );
			exit;
		}

		function bookingpress_get_timesheet_daysoff_details_func() {
			global $wpdb, $BookingPress, $tbl_bookingpress_staffmembers, $tbl_bookingpress_staffmembers_daysoff,$bookingpress_global_options;
			$response            = array();

			$bpa_check_authorization = $this->bpa_check_authentication( 'get_timesheet_daysoff_details', true, 'bpa_wp_nonce' );           
			if( preg_match( '/error/', $bpa_check_authorization ) ){
				$bpa_auth_error = explode( '^|^', $bpa_check_authorization );
				$bpa_error_msg = !empty( $bpa_auth_error[1] ) ? $bpa_auth_error[1] : esc_html__( 'Sorry. Something went wrong while processing the request', 'bookingpress-appointment-booking');

				$response['variant'] = 'error';
				$response['title'] = esc_html__( 'Error', 'bookingpress-appointment-booking');
				$response['msg'] = $bpa_error_msg;

				wp_send_json( $response );
				die;
			}
			
			$bookingpress_global_options_arr = $bookingpress_global_options->bookingpress_global_options();
			$bookingpress_date_format        = $bookingpress_global_options_arr['wp_default_date_format'];

			$bookingpress_staffmember_daysoff_details = array();

			// Find bookingpress staffmember id
			$bookingpress_current_user_id  = get_current_user_id();
			$bookingpress_staffmember_data = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$tbl_bookingpress_staffmembers} WHERE bookingpress_wpuser_id = %d", $bookingpress_current_user_id ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_staffmembers is a table name. false alarm
			$bookingpress_staffmember_id   = ! empty( $bookingpress_staffmember_data['bookingpress_staffmember_id'] ) ? intval( $bookingpress_staffmember_data['bookingpress_staffmember_id'] ) : 0;

			if ( ! empty( $bookingpress_staffmember_id ) ) {

				// Get days off details
				$bookingpress_staffmember_daysoff_details = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$tbl_bookingpress_staffmembers_daysoff} WHERE bookingpress_staffmember_id = %d", $bookingpress_staffmember_id ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_staffmembers_daysoff is a table name. false alarm

				foreach ( $bookingpress_staffmember_daysoff_details as $key => $value ) {
					$bookingpress_staffmember_daysoff_details[ $key ]['bookingpress_staffmember_daysoff_formated_date'] = date( $bookingpress_date_format, strtotime( $value['bookingpress_staffmember_daysoff_date'] ) );
					$bookingpress_staffmember_daysoff_details[ $key ]['bookingpress_staffmember_daysoff_date'] = $value['bookingpress_staffmember_daysoff_date'];
				}
			}

			$response['variant']         = 'success';
			$response['title']           = esc_html__( 'Success', 'bookingpress-appointment-booking' );
			$response['msg']             = esc_html__( 'Daysoff detailed retrieved successfully', 'bookingpress-appointment-booking' );
			$response['daysoff_details'] = $bookingpress_staffmember_daysoff_details;

			echo wp_json_encode( $response );
			exit;
		}

		function bookingpress_timesheet_add_days_off_func() {
			global $wpdb, $BookingPress, $tbl_bookingpress_staffmembers, $tbl_bookingpress_staffmembers_daysoff;
			$response = array();

			$bpa_check_authorization = $this->bpa_check_authentication( 'timeslot_add_daysoff', true, 'bpa_wp_nonce' );           
			if( preg_match( '/error/', $bpa_check_authorization ) ){
				$bpa_auth_error = explode( '^|^', $bpa_check_authorization );
				$bpa_error_msg = !empty( $bpa_auth_error[1] ) ? $bpa_auth_error[1] : esc_html__( 'Sorry. Something went wrong while processing the request', 'bookingpress-appointment-booking');

				$response['variant'] = 'error';
				$response['title'] = esc_html__( 'Error', 'bookingpress-appointment-booking');
				$response['msg'] = $bpa_error_msg;

				wp_send_json( $response );
				die;
			}

			if ( ! empty( $_POST['daysoff_details'] ) ) { // phpcs:ignore
				$bookingpress_dayoff_name   = ! empty( $_POST['daysoff_details']['dayoff_name'] ) ? sanitize_text_field( $_POST['daysoff_details']['dayoff_name'] ) : ''; // phpcs:ignore
				$bookingpress_dayoff_date   = ! empty( $_POST['daysoff_details']['dayoff_date'] ) ? sanitize_text_field( $_POST['daysoff_details']['dayoff_date'] ) : ''; // phpcs:ignore
				$bookingpress_dayoff_repeat = ( isset( $_POST['daysoff_details']['dayoff_repeat'] ) && ( $_POST['daysoff_details']['dayoff_repeat'] == 'true' ) ) ? 1 : 0; // phpcs:ignore

				// Find bookingpress staffmember id
				$bookingpress_current_user_id  = get_current_user_id();
				$bookingpress_staffmember_data = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$tbl_bookingpress_staffmembers} WHERE bookingpress_wpuser_id = %d", $bookingpress_current_user_id ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_staffmembers is a table name. false alarm
				$bookingpress_staffmember_id   = ! empty( $bookingpress_staffmember_data['bookingpress_staffmember_id'] ) ? intval( $bookingpress_staffmember_data['bookingpress_staffmember_id'] ) : 0;

				$bookingpress_insert_data = array(
					'bookingpress_staffmember_id' => $bookingpress_staffmember_id,
					'bookingpress_staffmember_daysoff_name' => $bookingpress_dayoff_name,
					'bookingpress_staffmember_daysoff_date' => date( 'Y-m-d', strtotime( $bookingpress_dayoff_date ) ),
					'bookingpress_staffmember_daysoff_repeat' => $bookingpress_dayoff_repeat,
				);

				$bookingpress_update_id = ! empty( $_POST['update_id'] ) ? intval( $_POST['update_id'] ) : 0; // phpcs:ignore

				if ( empty( $bookingpress_update_id ) ) {
					$wpdb->insert( $tbl_bookingpress_staffmembers_daysoff, $bookingpress_insert_data );

					$response['msg'] = esc_html__( 'Staff member holiday added successfully', 'bookingpress-appointment-booking' );
				} else {
					$wpdb->update( $tbl_bookingpress_staffmembers_daysoff, $bookingpress_insert_data, array( 'bookingpress_staffmember_daysoff_id' => $bookingpress_update_id ) );

					$response['msg'] = esc_html__( 'Staff member holiday off updated successfully', 'bookingpress-appointment-booking' );
				}

				$response['variant'] = 'success';
				$response['title']   = esc_html__( 'Success', 'bookingpress-appointment-booking' );
			}

			echo wp_json_encode( $response );
			exit;
		}

		function bookingpress_timesheet_dynamic_helper_vars_func() {
			global $bookingpress_global_options;
			$bookingpress_options     = $bookingpress_global_options->bookingpress_global_options();
			$bookingpress_locale_lang = $bookingpress_options['locale'];
			?>
				var lang = ELEMENT.lang.<?php echo esc_html( $bookingpress_locale_lang ); ?>;
				ELEMENT.locale(lang)
			<?php
		}

		function bookingpress_timesheet_dynamic_vue_methods_func() {
			global $bookingpress_notification_duration;
			?>
			change_special_day_date(selected_value){
				const vm = this
				if(selected_value != null) {
					vm.staffmember_special_day_form.special_day_date[0] = vm.get_formatted_date(vm.staffmember_special_day_form.special_day_date[0])
					vm.staffmember_special_day_form.special_day_date[1] = vm.get_formatted_date(vm.staffmember_special_day_form.special_day_date[1])
				}
			},
			change_days_off_date(selected_value) {				
				if(selected_value != null) {
					this.staffmember_dayoff_form.dayoff_date = this.get_formatted_date(this.staffmember_dayoff_form.dayoff_date)				
				}
			},	
			get_formatted_date(iso_date){

				if( true == /(\d{2})\T/.test( iso_date ) ){
					let date_time_arr = iso_date.split('T');
					return date_time_arr[0];
				}
				var __date = new Date(iso_date);
				var __year = __date.getFullYear();
				var __month = __date.getMonth()+1;
				var __day = __date.getDate();
				if (__day < 10) {
					__day = '0' + __day;
				}
				if (__month < 10) {
					__month = '0' + __month;
				}
				var formatted_date = __year+'-'+__month+'-'+__day;
				return formatted_date;
			},
			open_days_off_modal_func(currentElement){
				const vm = this
				vm.bookingpress_reset_daysoff_modal()
				vm.days_off_add_modal = true		
				var dialog_pos = currentElement.target.getBoundingClientRect();
				vm.days_off_modal_pos = (dialog_pos.top - 90)+'px'
				vm.days_off_modal_pos_right = '-'+(dialog_pos.right - 400)+'px';
				
				if( typeof vm.bpa_adjust_popup_position != 'undefined' ){
					vm.bpa_adjust_popup_position( currentElement, 'div#days_off_add_modal .el-dialog.bpa-dialog--days-off');
				}
			},
			bookingpress_reset_daysoff_modal() {
				const vm = this				
				vm.edit_staffmember_dayoff = '';
				vm.staffmember_dayoff_form.dayoff_name = '';
				vm.staffmember_dayoff_form.dayoff_date = '';
				vm.staffmember_dayoff_form.dayoff_repeat = '';
				vm.staffmember_dayoff_form.is_disabled = false;
				setTimeout(function(){
					vm.$refs['staffmember_dayoff_form'].clearValidate();
				},100); 
			},
			closeStaffmemberDayoff() {
				const vm = this;
				vm.bookingpress_reset_daysoff_modal()
				vm.days_off_add_modal = false;				
			},
			bookingpress_add_daysoff(staffmember_dayoff_form){
				const vm = this
				this.$refs[staffmember_dayoff_form].validate((valid) => {
					if (valid) {
						vm.staffmember_dayoff_form.is_disabled = true
						var is_exit = 0;
						if(vm.bookingpress_staffmembers_specialdays_details != '' ) {
							vm.bookingpress_staffmembers_specialdays_details.forEach(function(item, index, arr) {									
								if (item.special_day_start_date == vm.staffmember_dayoff_form.dayoff_date || item.special_day_end_date  == vm.staffmember_dayoff_form.dayoff_date || (item.special_day_start_date < vm.staffmember_dayoff_form.dayoff_date && item.special_day_end_date > vm.staffmember_dayoff_form.dayoff_date)) {
									vm.staffmember_dayoff_form.is_disabled = false
									vm.$notify({
										title: '<?php esc_html_e('Error', 'bookingpress-appointment-booking'); ?>',
										message: '<?php esc_html_e('Special day is already exists.', 'bookingpress-appointment-booking'); ?>',
										type: 'error',
										customClass: 'error_notification',
										duration:<?php echo intval($bookingpress_notification_duration); ?>,
									});
									is_exit = 1;									
								}
							});
						}
						vm.bookingpress_staffmembers_daysoff_details.forEach(function(item,index,arr) {
							if(item.bookingpress_staffmember_daysoff_date == vm.staffmember_dayoff_form.dayoff_date && item.bookingpress_staffmember_daysoff_id != vm.edit_staffmember_dayoff) {
								vm.staffmember_dayoff_form.is_disabled = false
								vm.$notify({
									title: '<?php esc_html_e('Error', 'bookingpress-appointment-booking'); ?>',
									message: '<?php esc_html_e('Holiday is already exists', 'bookingpress-appointment-booking'); ?>',
									type: 'error',
									customClass: 'error_notification',
									duration:<?php echo intval($bookingpress_notification_duration); ?>,									
								});
								is_exit = 1;
							}
						});
						if(is_exit == 0) {
							var postdata = [];
							postdata.action = 'bookingpress_validate_staffmember_daysoff'							
							postdata.selected_date_range= vm.staffmember_dayoff_form.dayoff_date;
							postdata._wpnonce = '<?php echo esc_html( wp_create_nonce( 'bpa_wp_nonce' ) ); ?>';
							axios.post( appoint_ajax_obj.ajax_url, Qs.stringify( postdata ) )
							.then(function(response){
								if(response.data.variant != 'undefined' && response.data.variant == 'warnning') {														
									vm.staffmember_dayoff_form.is_disabled = false
									vm.$confirm(response.data.msg, 'Warning', {
									confirmButtonText: '<?php esc_html_e( 'Ok', 'bookingpress-appointment-booking' ); ?>',
									cancelButtonText: '<?php esc_html_e( 'Cancel', 'bookingpress-appointment-booking' ); ?>',
									type: 'warning'
									}).then(() => {
										vm.bookingpress_save_staffmember_daysoff()
									});				
								}else if(response.data.variant != 'undefined' && response.data.variant  == 'success') {
									vm.bookingpress_save_staffmember_daysoff();
									
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
			bookingpress_save_staffmember_daysoff(){
				const vm = this;
				var postData = { action:'bookingpress_timesheet_add_days_off', daysoff_details: vm.staffmember_dayoff_form, update_id: vm.edit_staffmember_dayoff, _wpnonce:'<?php echo esc_html( wp_create_nonce( 'bpa_wp_nonce' ) ); ?>' };
				axios.post( appoint_ajax_obj.ajax_url, Qs.stringify( postData ) )
				.then( function (response) {
					vm.$notify({
						title: response.data.title,
						message: response.data.msg,
						type: response.data.variant,
						customClass: response.data.variant+'_notification',
					});
					vm.closeStaffmemberDayoff()
					vm.bookingpress_get_all_daysoff_details()
					
				}.bind(this) )
				.catch( function (error) {
					console.log(error);
				});
			},			
			bookingpress_get_all_daysoff_details(){
				const vm = this
				var postData = { action:'bookingpress_timesheet_get_days_off', _wpnonce:'<?php echo esc_html( wp_create_nonce( 'bpa_wp_nonce' ) ); ?>' };
				axios.post( appoint_ajax_obj.ajax_url, Qs.stringify( postData ) )
				.then( function (response) {
					vm.staffmember_dayoff_form.is_disabled = false
					if(response.data.variant == 'success'){
						vm.bookingpress_staffmembers_daysoff_details = response.data.daysoff_details
					}
				}.bind(this) )
				.catch( function (error) {
					console.log(error);
				});
			},
			bookingpress_delete_daysoff(delete_id){
				const vm = this
				var postData = { action:'bookingpress_timesheet_delete_days_off', delete_id: delete_id , _wpnonce:'<?php echo esc_html( wp_create_nonce( 'bpa_wp_nonce' ) ); ?>' };
				axios.post( appoint_ajax_obj.ajax_url, Qs.stringify( postData ) )
				.then( function (response) {
					if(response.data.variant == 'success'){
						vm.$notify({
							title: response.data.title,
							message: response.data.msg,
							type: response.data.variant,
							customClass: response.data.variant+'_notification',
						});
						vm.bookingpress_get_all_daysoff_details()
					}
				}.bind(this) )
				.catch( function (error) {
					console.log(error);
				});
			},
			bookingpress_edit_daysoff(edit_details, currentElement){
				const vm = this
				vm.bookingpress_reset_daysoff_modal()
				vm.edit_staffmember_dayoff = edit_details.bookingpress_staffmember_daysoff_id
				vm.staffmember_dayoff_form.dayoff_name = edit_details.bookingpress_staffmember_daysoff_name;
				vm.staffmember_dayoff_form.dayoff_date = edit_details.bookingpress_staffmember_daysoff_date;
				vm.staffmember_dayoff_form.dayoff_repeat = (edit_details.bookingpress_staffmember_daysoff_repeat == "0") ? false : true ;
				var dialog_pos = currentElement.target.getBoundingClientRect();
				vm.days_off_modal_pos = (dialog_pos.top - 110)+'px'
				vm.days_off_modal_pos_right = '-'+(dialog_pos.right - 510)+'px';
				vm.days_off_add_modal = true;
				if( typeof vm.bpa_adjust_popup_position != 'undefined' ){
					vm.bpa_adjust_popup_position( currentElement, 'div#days_off_add_modal .el-dialog.bpa-dialog--days-off');
				}
			},
			close_special_days_func(){
				const vm = this
				vm.bookingpress_reset_specialdays_modal()
				vm.special_days_add_modal = false
			},
			open_special_days_func(currentElement){
				const vm = this                 				               
				vm.bookingpress_reset_specialdays_modal()
				vm.special_days_add_modal = true
				var dialog_pos = currentElement.target.getBoundingClientRect();
				vm.special_days_modal_pos = (dialog_pos.top - 90)+'px'
				vm.special_days_modal_pos_right = '-'+(dialog_pos.right - 400)+'px';
				if( typeof vm.bpa_adjust_popup_position != 'undefined' ){
					vm.bpa_adjust_popup_position( currentElement, 'div#special_days_add_modal .el-dialog.bpa-dialog--special-days');
				}
			},
			bookingpress_reset_specialdays_modal(){
				const vm = this				
				vm.edit_staffmember_special_day = ''
				vm.staffmember_special_day_form.special_day_date = [];                 
				vm.staffmember_special_day_form.start_time = '';
				vm.staffmember_special_day_form.end_time = '';
				vm.staffmember_special_day_form.special_day_service = '';
				vm.staffmember_special_day_form.is_disabled = false;
				vm.staffmember_special_day_form.special_day_workhour = [];
				setTimeout(function(){
					vm.$refs['staffmember_special_day_form'].clearValidate();
				},100);
			},
			bookingpress_add_special_day_period(){
				const vm = this;
				var ilength = parseInt(vm.staffmember_special_day_form.special_day_workhour.length) + 1;
				let WorkhourData = {};
				Object.assign(WorkhourData, {id: ilength})
				Object.assign(WorkhourData, {start_time: ''})
				Object.assign(WorkhourData, {end_time: ''})					
				vm.staffmember_special_day_form.special_day_workhour.push(WorkhourData)
			},
			bookingpress_remove_special_day_period(id){
				const vm = this
				vm.staffmember_special_day_form.special_day_workhour.forEach(function(item, index, arr)
				{
					if(id == item.id ){
						vm.staffmember_special_day_form.special_day_workhour.splice(index,1);
					}	
				})
			},
			bookingpress_get_staffmember_specialdays(){
				const vm = this
				var postData = { action:'bookingpress_timesheet_get_special_days', _wpnonce:'<?php echo esc_html( wp_create_nonce( 'bpa_wp_nonce' ) ); ?>' };
				axios.post( appoint_ajax_obj.ajax_url, Qs.stringify( postData ) )
				.then( function (response) {
					vm.bookingpress_get_staffmember_specialdays.is_disabled = false
					if(response.data.variant == 'success'){
						vm.bookingpress_staffmembers_specialdays_details = response.data.special_day_data            						
					}
				}.bind(this) )
				.catch( function (error) {
					console.log(error);
				});
			},
			addStaffmemberSpecialday(staffmember_special_day_form) {	                			
				this.$refs[staffmember_special_day_form].validate((valid) => {
					if (valid) {
						const vm = this
						vm.staffmember_special_day_form.is_disabled = true
						var is_exit = 0;
						if(vm.bookingpress_staffmembers_daysoff_details != '') {
							vm.bookingpress_staffmembers_daysoff_details.forEach(function(item, index, arr)
							{	
								if (item.bookingpress_staffmember_daysoff_date >= vm.staffmember_special_day_form.special_day_date[0] && item.bookingpress_staffmember_daysoff_date <= vm.staffmember_special_day_form.special_day_date[1] ) {		
									vm.staffmember_special_day_form.is_disabled = false
									vm.$notify({
										title: '<?php esc_html_e('Error', 'bookingpress-appointment-booking'); ?>',
										message: '<?php esc_html_e('Holiday is already exists', 'bookingpress-appointment-booking'); ?>',
										type: 'error',
										customClass: 'error_notification',
										duration:<?php echo intval($bookingpress_notification_duration); ?>,
									});
									is_exit = 1;									
								}
							});
						}
						if(vm.staffmember_special_day_form.special_day_workhour != undefined && vm.staffmember_special_day_form.special_day_workhour != '' ) {
							vm.staffmember_special_day_form.special_day_workhour.forEach(function(item, index, arr){	                            
								if(is_exit == 0 && (item.start_time == '' || item.end_time == '')) {
									vm.staffmember_special_day_form.is_disabled = false
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
						if(vm.bookingpress_staffmembers_specialdays_details != undefined && vm.bookingpress_staffmembers_specialdays_details != '' ) {
							vm.bookingpress_staffmembers_specialdays_details.forEach(function(item, index, arr) {								
								if((vm.staffmember_special_day_form.special_day_date[0] == item.special_day_start_date || vm.staffmember_special_day_form.special_day_date[0] == item.special_day_end_date || ( vm.staffmember_special_day_form.special_day_date[0] >= item.special_day_start_date && vm.staffmember_special_day_form.special_day_date[0] <= item.special_day_end_date ) || vm.staffmember_special_day_form.special_day_date[1] == item.special_day_end_date || vm.staffmember_special_day_form.special_day_date[1] == item.special_day_start_date || (vm.staffmember_special_day_form.special_day_date[1] >= item.special_day_start_date && vm.staffmember_special_day_form.special_day_date[1] <= item.special_day_end_date) || (vm.staffmember_special_day_form.special_day_date[0] <= item.special_day_start_date && vm.staffmember_special_day_form.special_day_date[1] >= item.special_day_end_date)) && vm.edit_staffmember_special_day != item.id  ) {
									vm.staffmember_special_day_form.is_disabled = false
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
						if(is_exit == 0) {
							var postdata = [];
							postdata.action = 'bookingpress_validate_staff_member_special_day'                                          
							postdata.selected_date_range= vm.staffmember_special_day_form.special_day_date;
							postdata.special_day_workhour= vm.staffmember_special_day_form.special_day_workhour;                                                       
							postdata._wpnonce = '<?php echo esc_html( wp_create_nonce( 'bpa_wp_nonce' ) ); ?>';
							axios.post( appoint_ajax_obj.ajax_url, Qs.stringify( postdata ) )
							.then(function(response){
								if(response.data.variant != 'undefined' && response.data.variant == 'warnning') {                                                               
									vm.staffmember_special_day_form.is_disabled = false
									vm.$confirm(response.data.msg, 'Warning', {
									confirmButtonText:  '<?php esc_html_e( 'Ok', 'bookingpress-appointment-booking' ); ?>',
									cancelButtonText:  '<?php esc_html_e( 'Cancel', 'bookingpress-appointment-booking' ); ?>',
									type: 'warning'
									}).then(() => {
										vm.bookingpress_add_staffmember_special_days()
									});             
								}else if(response.data.variant != 'undefined' && response.data.variant  == 'success') {
									vm.bookingpress_add_staffmember_special_days();   
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
			bookingpress_add_staffmember_special_days() {
				const vm = this;
				var postdata = [];
				postdata.action = 'bookingpress_timesheet_add_staffmember_special_day'
				postdata.specialdays_details = vm.staffmember_special_day_form
				postdata.specialday_updateid = vm.edit_staffmember_special_day
				postdata._wpnonce = '<?php echo esc_html( wp_create_nonce( 'bpa_wp_nonce' ) ); ?>';
				axios.post( appoint_ajax_obj.ajax_url, Qs.stringify( postdata ) )
				.then(function(response){
					vm.$notify({
						title: response.data.title,
						message: response.data.msg,
						type: response.data.variant,
						customClass: response.data.variant+'_notification',
					});
					vm.bookingpress_get_staffmember_specialdays()
					vm.close_special_days_func()
				}).catch(function(error){
					console.log(error);
					vm.$notify({
						title: '<?php esc_html_e( 'Error', 'bookingpress-appointment-booking' ); ?>',
						message: '<?php esc_html_e( 'Something went wrong..', 'bookingpress-appointment-booking' ); ?>',
						type: 'error_notification',
					});
				});	

			},
			bookingpress_delete_special_daysoff(delete_id){
				const vm = this
				var postdata = [];
				postdata.action = 'bookingpress_timesheet_delete_staffmember_special_day'
				postdata.delete_id = delete_id
				postdata._wpnonce = '<?php echo esc_html( wp_create_nonce( 'bpa_wp_nonce' ) ); ?>';
				axios.post( appoint_ajax_obj.ajax_url, Qs.stringify( postdata ) )
				.then(function(response){
					vm.$notify({
						title: response.data.title,
						message: response.data.msg,
						type: response.data.variant,
						customClass: response.data.variant+'_notification',
					});
					vm.bookingpress_get_staffmember_specialdays()
				}).catch(function(error){
					console.log(error);
					vm.$notify({
						title: '<?php esc_html_e( 'Error', 'bookingpress-appointment-booking' ); ?>',
						message: '<?php esc_html_e( 'Something went wrong..', 'bookingpress-appointment-booking' ); ?>',
						type: 'error_notification',
					});
				});
			},
			show_edit_special_day_div(special_day_id, currentElement) {				
				var vm = this
				vm.bookingpress_reset_specialdays_modal()
				vm.bookingpress_staffmembers_specialdays_details.forEach(function(item, index, arr)
				{
					if (item.id == special_day_id) {
						vm.staffmember_special_day_form.special_day_date = [item.special_day_start_date,item.special_day_end_date]
						vm.staffmember_special_day_form.start_time = item.start_time
						vm.staffmember_special_day_form.end_time = item.end_time
						vm.staffmember_special_day_form.special_day_service = item.special_day_service							
						vm.staffmember_special_day_form.special_day_workhour = item.special_day_workhour
					}
					vm.edit_staffmember_special_day = special_day_id;
				})
				var dialog_pos = currentElement.target.getBoundingClientRect();
				vm.special_days_modal_pos = (dialog_pos.top - 100)+'px'
				vm.special_days_modal_pos_right = '-'+(dialog_pos.right - 550)+'px';
				vm.special_days_add_modal = true

				if( typeof vm.bpa_adjust_popup_position != 'undefined' ){
					vm.bpa_adjust_popup_position( currentElement, 'div#special_days_add_modal .el-dialog.bpa-dialog--special-days');
				}
			},
			<?php
		}

		function bookingpress_timesheet_dynamic_onload_methods_func() {
			?>
				this.bookingpress_get_staffmember_specialdays()
			<?php
		}

		function bookingpress_load_timesheet_view_func() {
			$bookingpress_load_file_name = BOOKINGPRESS_PRO_VIEWS_DIR . '/staff_members/staffmember_timesheet.php';
			require $bookingpress_load_file_name;
		}

		function bookingpress_timesheet_dynamic_data_fields_func() {
			global $wpdb, $BookingPress, $tbl_bookingpress_staffmembers, $tbl_bookingpress_staff_member_workhours, $tbl_bookingpress_staffmembers_daysoff, $tbl_bookingpress_default_workhours,$tbl_bookingpress_default_daysoff,$bookingpress_global_options,$bookingpress_pro_staff_members;
			$bookingpress_timesheet_data_fields_arr = array();
			$bookingpress_timesheet_data_fields_arr['is_readonly_input_fields'] = true;
			$bookingpress_global_options_arr = $bookingpress_global_options->bookingpress_global_options();
			$bookingpress_date_format        = $bookingpress_global_options_arr['wp_default_date_format'];
			$bookingpress_time_format        = $bookingpress_global_options_arr['wp_default_time_format'];			
			// Find bookingpress staffmember id
			$bookingpress_current_user_id  = get_current_user_id();
			$bookingpress_staffmember_data = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$tbl_bookingpress_staffmembers} WHERE bookingpress_wpuser_id = %d", $bookingpress_current_user_id ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_staffmembers is a table name. false alarm
			$bookingpress_staffmember_id   = ! empty( $bookingpress_staffmember_data['bookingpress_staffmember_id'] ) ? intval( $bookingpress_staffmember_data['bookingpress_staffmember_id'] ) : 0;
			
			// $bookingpress_get_default_workhours
			$bookingpress_days_arr = array( 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday' );
			$default_start_time = '00:00:00';
			$default_end_time   = '23:00:00';
			$step_duration_val  = 30;
			$bookingpress_workhours_data = array();
			$bookingpress_monday_times   = $bookingpress_tuesday_times = $bookingpress_wednesday_times = $bookingpress_thursday_times = $bookingpress_friday_times = $bookingpress_saturday_times = $bookingpress_sunday_times = array();

			foreach ( $bookingpress_days_arr as $days_key => $days_val ) {
				$selected_staffmembers_timings = $selected_timing_data = $bookingpress_breaks_arr = $bookingpress_get_staffmembers_breakhours = array();
				$selected_staffmembers_timings = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$tbl_bookingpress_staff_member_workhours} WHERE bookingpress_staffmember_workday_key = %s AND bookingpress_staffmember_workhours_is_break = %d AND bookingpress_staffmember_id = %d", ucfirst( $days_val ), 0, $bookingpress_staffmember_id ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_staff_member_workhours is a table name. false alarm

				if(!empty($selected_staffmembers_timings)) {		
					$selected_start_time = esc_html($selected_staffmembers_timings['bookingpress_staffmember_workhours_start_time']);
					$selected_end_time   = esc_html($selected_staffmembers_timings['bookingpress_staffmember_workhours_end_time']);

					$bookingpress_get_staffmembers_breakhours = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$tbl_bookingpress_staff_member_workhours} WHERE bookingpress_staffmember_workday_key = %s AND bookingpress_staffmember_workhours_is_break = %d AND bookingpress_staffmember_id = %d", ucfirst( $days_val ), 1, $bookingpress_staffmember_id ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_staff_member_workhours is a table name. false alarm

					if ( ! empty( $bookingpress_get_staffmembers_breakhours ) ) {
						foreach ( $bookingpress_get_staffmembers_breakhours as $break_workhour_key => $break_workhour_val ) {
							$bookingpress_breaks_arr[] = array(
								'start_time' => date( $bookingpress_time_format, strtotime(esc_html($break_workhour_val['bookingpress_staffmember_workhours_start_time']) ) ),
								'end_time'   => date( $bookingpress_time_format, strtotime( esc_html($break_workhour_val['bookingpress_staffmember_workhours_end_time']) )),
							);
						}
					}	
					
				} else {
					$selected_timing_data = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$tbl_bookingpress_default_workhours} WHERE bookingpress_workday_key = %s AND bookingpress_is_break = 0", $days_val ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_default_workhours is a table name. false alarm
					$selected_start_time = !empty($selected_timing_data['bookingpress_start_time']) ? esc_html($selected_timing_data['bookingpress_start_time']) : '' ;
					$selected_end_time   = !empty($selected_timing_data['bookingpress_end_time']) ? esc_html( $selected_timing_data['bookingpress_end_time']) : '';					
					// Get breaks for current day and add to breaks array
					$bookingpress_get_break_workhours = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$tbl_bookingpress_default_workhours} WHERE bookingpress_workday_key = %s AND bookingpress_is_break = %d", $days_val, 1 ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_default_workhours is a table name. false alarm					 
					if ( ! empty( $bookingpress_get_break_workhours ) ) {
						foreach ( $bookingpress_get_break_workhours as $break_workhour_key => $break_workhour_val ) {
							$bookingpress_breaks_arr[] = array(
								'start_time' => date( $bookingpress_time_format, strtotime( esc_html($break_workhour_val['bookingpress_start_time']))),
								'end_time'   => date( $bookingpress_time_format, strtotime( esc_html($break_workhour_val['bookingpress_end_time']))),
							);
						}
					}
				}
				if ( $selected_start_time == null ) {
					$selected_start_time = 'Off';
				} else {
					$selected_start_time = date( $bookingpress_time_format, strtotime($selected_start_time));
				}
				if ( $selected_end_time == null ) {
					$selected_end_time = 'Off';
				} else {
					$selected_end_time = date( $bookingpress_time_format, strtotime($selected_end_time));
				}
				if ( $days_val == 'monday' ) {
					$bookingpress_monday_times['workhours_start_time'] = $selected_start_time;
					$bookingpress_monday_times['workhours_end_time']   = $selected_end_time;
					$bookingpress_monday_times['break_times']          = $bookingpress_breaks_arr;
				} elseif ( $days_val == 'tuesday' ) {
					$bookingpress_tuesday_times['workhours_start_time'] = $selected_start_time;
					$bookingpress_tuesday_times['workhours_end_time']   = $selected_end_time;
					$bookingpress_tuesday_times['break_times']          = $bookingpress_breaks_arr;
				} elseif ( $days_val == 'wednesday' ) {
					$bookingpress_wednesday_times['workhours_start_time'] = $selected_start_time;
					$bookingpress_wednesday_times['workhours_end_time']   = $selected_end_time;
					$bookingpress_wednesday_times['break_times']          = $bookingpress_breaks_arr;
				} elseif ( $days_val == 'thursday' ) {
					$bookingpress_thursday_times['workhours_start_time'] = $selected_start_time;
					$bookingpress_thursday_times['workhours_end_time']   = $selected_end_time;
					$bookingpress_thursday_times['break_times']          = $bookingpress_breaks_arr;
				} elseif ( $days_val == 'friday' ) {
					$bookingpress_friday_times['workhours_start_time'] = $selected_start_time;
					$bookingpress_friday_times['workhours_end_time']   = $selected_end_time;
					$bookingpress_friday_times['break_times']          = $bookingpress_breaks_arr;
				} elseif ( $days_val == 'saturday' ) {
					$bookingpress_saturday_times['workhours_start_time'] = $selected_start_time;
					$bookingpress_saturday_times['workhours_end_time']   = $selected_end_time;
					$bookingpress_saturday_times['break_times']          = $bookingpress_breaks_arr;
				} elseif ( $days_val == 'sunday' ) {
					$bookingpress_sunday_times['workhours_start_time'] = $selected_start_time;
					$bookingpress_sunday_times['workhours_end_time']   = $selected_end_time;
					$bookingpress_sunday_times['break_times']          = $bookingpress_breaks_arr;
				}
			}

			$bookingpress_timesheet_data_fields_arr['monday_timings']    = $bookingpress_monday_times;
			$bookingpress_timesheet_data_fields_arr['tuesday_timings']   = $bookingpress_tuesday_times;
			$bookingpress_timesheet_data_fields_arr['wednesday_timings'] = $bookingpress_wednesday_times;
			$bookingpress_timesheet_data_fields_arr['thursday_timings']  = $bookingpress_thursday_times;
			$bookingpress_timesheet_data_fields_arr['friday_timings']    = $bookingpress_friday_times;
			$bookingpress_timesheet_data_fields_arr['saturday_timings']  = $bookingpress_saturday_times;
			$bookingpress_timesheet_data_fields_arr['sunday_timings']    = $bookingpress_sunday_times;

			$bookingpress_staffmember_daysoff_details = $bookingpress_staffmember_default_daysoff_details = array();

			if ( ! empty( $bookingpress_staffmember_id ) ) {

				// Get days off details
				$bookingpress_staffmember_daysoff_details = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$tbl_bookingpress_staffmembers_daysoff} WHERE bookingpress_staffmember_id = %d", $bookingpress_staffmember_id ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_staffmembers_daysoff is a table name. false alarm

				foreach ( $bookingpress_staffmember_daysoff_details as $key => $value ) {
					$bookingpress_staffmember_daysoff_details[ $key ]['bookingpress_staffmember_daysoff_formated_date'] = date( $bookingpress_date_format, strtotime( $value['bookingpress_staffmember_daysoff_date'] ) );
					$bookingpress_staffmember_daysoff_details[ $key ]['bookingpress_staffmember_daysoff_date'] = $value['bookingpress_staffmember_daysoff_date'];
				}
			}

			$bookingpress_timesheet_data_fields_arr['bookingpress_staffmembers_daysoff_details'] = $bookingpress_staffmember_daysoff_details;

			$bookingpress_timesheet_data_fields_arr['bookingpress_staffmembers_specialdays_details'] = array();

			$bookingpress_staffmember_default_daysoff_details = $wpdb->get_results( "SELECT `bookingpress_name`,`bookingpress_dayoff_date`,`bookingpress_repeat` FROM {$tbl_bookingpress_default_daysoff}", ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_default_daysoff is a table name. false alarm

			foreach ( $bookingpress_staffmember_default_daysoff_details as $key => $value ) {
				$bookingpress_staffmember_default_daysoff_details[ $key ]['bookingpress_dayoff_date'] = date( $bookingpress_date_format, strtotime( $value['bookingpress_dayoff_date'] ) );
			}
			$bookingpress_timesheet_data_fields_arr['bookingpress_staffmember_default_daysoff_details'] = $bookingpress_staffmember_default_daysoff_details;

			// Days Off data variables
			$bookingpress_timesheet_data_fields_arr['days_off_add_modal']       = false;
			$bookingpress_timesheet_data_fields_arr['days_off_modal_pos']       = '0';
			$bookingpress_timesheet_data_fields_arr['days_off_modal_pos_right'] = '0';
			$bookingpress_timesheet_data_fields_arr['edit_staffmember_dayoff']  = '';
			$bookingpress_timesheet_data_fields_arr['rules_dayoff']             = array(
				'dayoff_name' => array(
					array(
						'required' => true,
						'message'  => __( 'Please enter name', 'bookingpress-appointment-booking' ),
						'trigger'  => 'blur',
					),
				),
				'dayoff_date' => array(
					array(
						'required' => true,
						'message'  => __( 'Please select date', 'bookingpress-appointment-booking' ),
						'trigger'  => 'blur',
					),
				),
			);
			$bookingpress_timesheet_data_fields_arr['staffmember_dayoff_form']  = array(
				'dayoff_name'   => '',
				'dayoff_date'   => '',
				'dayoff_repeat' => false,
				'is_disabled' => false,
			);	
			$bookingpress_timesheet_data_fields_arr['is_mask_display'] = false;

			// Special Days variables
			$bookingpress_timesheet_data_fields_arr['special_days_add_modal']       = false;
			$bookingpress_timesheet_data_fields_arr['special_days_modal_pos']       = '0';
			$bookingpress_timesheet_data_fields_arr['special_days_modal_pos_right'] = '0';
			$bookingpress_timesheet_data_fields_arr['edit_staffmember_special_day'] = '';
			$bookingpress_timesheet_data_fields_arr['rules_special_day']            = array(
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
						'message'  => __( 'Select start time', 'bookingpress-appointment-booking' ),
						'trigger'  => 'blur',
					),
				),
				'end_time'         => array(
					array(
						'required' => true,
						'message'  => __( 'Select end time', 'bookingpress-appointment-booking' ),
						'trigger'  => 'blur',
					),
				),
			);
			$bookingpress_timesheet_data_fields_arr['staffmember_special_day_form'] = array(
				'special_day_date'     => '',
				'special_day_service'  => '',
				'start_time'           => '',
				'end_time'             => '',
				'is_disabled'          => false,
				'special_day_workhour' => array(),
			);
			$bookingpress_timesheet_data_fields_arr['bookingpress_services_list']   = $BookingPress->get_bookingpress_service_data_group_with_category();		
			$bookingpress_timesheet_data_fields_arr['disabledOtherDates'] = '';			
			$bookingpress_timesheet_data_fields_arr['disabledDates'] = '';						
			$default_start_time    = '00:00:00';
			$default_end_time      = '23:25:00';
			$step_duration_val     = 05;
			$default_break_timings = array();
			$curr_time             = $tmp_start_time = date( 'H:i:s', strtotime( $default_start_time ) );
			$tmp_end_time          = date( 'H:i:s', strtotime( $default_end_time ) );

			do {
				$tmp_time_obj = new DateTime( $curr_time );
				$tmp_time_obj->add( new DateInterval( 'PT' . $step_duration_val . 'M' ) );
				$end_time = $tmp_time_obj->format( 'H:i:s' );

					$default_break_timings[] = array(
						'start_time'           => $curr_time,
						'formatted_start_time' => date( $bookingpress_global_options_arr['wp_default_time_format'], strtotime( $curr_time ) ),
						'end_time'             => $end_time,
						'formatted_end_time'   => date( $bookingpress_global_options_arr['wp_default_time_format'], strtotime( $end_time ) ),
					);

					$tmp_time_obj = new DateTime( $curr_time );
					$tmp_time_obj->add( new DateInterval( 'PT' . $step_duration_val . 'M' ) );
					$curr_time = $tmp_time_obj->format( 'H:i:s' );
			} while ( $curr_time <= $default_end_time );

			$bookingpress_timesheet_data_fields_arr['specialday_hour_list'] = $default_break_timings;

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
					'formatted_start_time' => date( $bookingpress_global_options_arr['wp_default_time_format'], strtotime( $curr_time ) ),
					'end_time'             => $end_time,
					'formatted_end_time'   => date( $bookingpress_global_options_arr['wp_default_time_format'], strtotime( $end_time ) ),
				);
				$tmp_time_obj             = new DateTime( $curr_time );
				$tmp_time_obj->add( new DateInterval( 'PT' . $step_duration_val . 'M' ) );
				$curr_time = $tmp_time_obj->format( 'H:i:s' );
			} while ( $curr_time <= $default_end_time );
			$bookingpress_timesheet_data_fields_arr['specialday_break_hour_list'] = $default_break_timings2;			
			echo wp_json_encode( $bookingpress_timesheet_data_fields_arr );
		}

	}
}
global $bookingpress_pro_timesheet;
$bookingpress_pro_timesheet = new bookingpress_pro_timesheet();
