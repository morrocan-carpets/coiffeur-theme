<?php
if ( ! class_exists( 'bookingpress_bring_anyone_with_you' ) ) {
	class bookingpress_bring_anyone_with_you Extends BookingPress_Core {
		function __construct() {

			if ( $this->bookingpress_check_bring_anyone_module_activation() ) {
				add_filter('bookingpress_frontend_apointment_form_add_dynamic_data',array($this ,'bookingpress_frontend_apointment_form_add_dynamic_data_func'));
				add_filter('bookingpress_customize_add_dynamic_data_fields',array($this,'bookingpress_customize_add_dynamic_data_fields_func'),10);
				add_filter('bookingpress_get_booking_form_customize_data_filter',array($this, 'bookingpress_get_booking_form_customize_data_filter_func'),10,1);
				add_action( 'bookingpress_set_additional_appointment_xhr_data', array( $this, 'bookingpress_set_bawy_appointment_xhr_data') );
				add_filter( 'bookingpress_modify_timeslot_data_for_bawy', array($this, 'bookingpress_calculate_bawy_for_timeslot'), 10, 2 );
				add_filter('bookingpress_front_modify_cart_data_filter',array($this,'bookingpress_front_modify_cart_data_filter_func'),11);

				add_filter( 'bookingpress_dynamic_next_page_request_filter', array( $this, 'bookingpress_set_quantity'), 7, 1 );
			}

		}

		function bookingpress_set_quantity( $bookingpress_dynamic_next_page_request_filter ){

			$bookingpress_dynamic_next_page_request_filter .= '
				if( "summary" == next_tab && "summary" == vm.bookingpress_current_tab && bookingpress_is_validate == 0 ){
					if (typeof vm.appointment_step_form_data.cart_items == "undefined") {
						let selected_service_data = vm.bookingpress_all_services_data[ vm.appointment_step_form_data.selected_service ];
						if( "undefined" != typeof selected_service_data.enable_custom_service_duration && true == selected_service_data.enable_custom_service_duration ){

						} else {
							
							let total_payable_amount = vm.appointment_step_form_data.base_price_without_currency;

							let selected_no_person = vm.appointment_step_form_data.bookingpress_selected_bring_members || 1;

							let calcualted_person_price = parseFloat( total_payable_amount ) * parseInt( selected_no_person );

							vm.appointment_step_form_data.service_price_without_currency = calcualted_person_price;

							vm.appointment_step_form_data.selected_service_price = vm.bookingpress_price_with_currency_symbol( calcualted_person_price );

							vm.use_base_price_for_calculation = false;
						}
					}
				}';

			return $bookingpress_dynamic_next_page_request_filter;
		}
		
		/**
		 * Function for add additional post data to bring anyone with you ajax request
		 *
		 * @return void
		 */
		function bookingpress_set_bawy_appointment_xhr_data(){
			?>
			if( "undefined" != typeof vm.appointment_formdata.selected_bring_members ){
				postData.appointment_data_obj.bookingpress_selected_bring_members = vm.appointment_formdata.selected_bring_members;
			}
			<?php
		}
		
		/**
		 * Function for check bring anyone module active or not
		 *
		 * @return void
		 */
		function bookingpress_check_bring_anyone_module_activation() {
			$is_bring_anyone_with_you_activated = 0;
			$deposit_payment_addon_option_val   = get_option( 'bookingpress_bring_anyone_with_you_module' );
			if ( ! empty( $deposit_payment_addon_option_val ) && ( $deposit_payment_addon_option_val == 'true' ) ) {
				$is_bring_anyone_with_you_activated = 1;
			}
			return $is_bring_anyone_with_you_activated;
		}
				
		/**
		 * Function for add dynamic field to customize page
		 *
		 * @param  mixed $bookingpress_customize_vue_data_fields
		 * @return void
		 */
		function bookingpress_customize_add_dynamic_data_fields_func($bookingpress_customize_vue_data_fields) {
			$bookingpress_customize_vue_data_fields['sevice_container_data']['bring_anyone_title'] = '';
			//$bookingpress_customize_vue_data_fields['sevice_container_data']['number_of_guest_title'] = '';
			$bookingpress_customize_vue_data_fields['sevice_container_data']['number_of_person_title'] = '';			
				
			return $bookingpress_customize_vue_data_fields;
		}
		
		/**
		 * Function for add data variables for customize page
		 *
		 * @param  mixed $booking_form_settings
		 * @return void
		 */
		function bookingpress_get_booking_form_customize_data_filter_func($booking_form_settings){
			$booking_form_settings['service_container_data']['bring_anyone_title'] = __('Select Service Extras', 'bookingpress-appointment-booking');		
			//$booking_form_settings['service_container_data']['number_of_guest_title'] = __( 'Number of guests', 'bookingpress-appointment-booking' );		
			$booking_form_settings['service_container_data']['number_of_person_title'] = __( 'Person', 'bookingpress-appointment-booking');
			
			return $booking_form_settings;
		}
		
		/**
		 * Function for calculate bring anyone with you for timeslots
		 *
		 * @param  mixed $timeslot_data
		 * @param  mixed $slot_key
		 * @return void
		 */
		function bookingpress_calculate_bawy_for_timeslot( $timeslot_data, $slot_key ){

			$total_bawy = !empty( $_POST['appointment_data_obj']['bookingpress_selected_bring_members'] ) ? intval($_POST['appointment_data_obj']['bookingpress_selected_bring_members']) : 0;
			if( !empty( $total_bawy ) && !empty($timeslot_data[$slot_key]) ){ // phpcs:ignore WordPress.Security.NonceVerification.Missing --Reason Nonce already verified from the caller function.
				if( $total_bawy > $timeslot_data[$slot_key]['max_capacity'] ){
					$timeslot_data[$slot_key]['is_booked'] = true;
					if( !empty( $timeslot_data[$slot_key]['reason_for_not_available'] ) ){
						$timeslot_data[$slot_key]['reason_for_not_available'][] = array( 'Total Number of person with guests ' . ( $total_bawy ) );
					} else {
						$timeslot_data[$slot_key]['reason_for_not_available'] = array( 'Total Number of person with guests ' . ( $total_bawy ) );
					}
				}
			}
			return $timeslot_data;

		}
		
		/**
		 * Function for add bring anyone with you fields at frontend form
		 *
		 * @param  mixed $bookingpress_front_vue_data_fields
		 * @return void
		 */
		function bookingpress_frontend_apointment_form_add_dynamic_data_func($bookingpress_front_vue_data_fields){
			global $BookingPress;
			$service_extra_title = $BookingPress->bookingpress_get_customize_settings('bring_anyone_title', 'booking_form');
			//$number_of_guest_title = $BookingPress->bookingpress_get_customize_settings('number_of_guest_title', 'booking_form');
			$number_of_person_title = $BookingPress->bookingpress_get_customize_settings('number_of_person_title', 'booking_form');

			$bookingpress_front_vue_data_fields['bring_anyone_title'] = !empty($service_extra_title) ? stripslashes_deep($service_extra_title) : '';
			//$bookingpress_front_vue_data_fields['number_of_guest_title'] = !empty($number_of_guest_title) ? stripslashes_deep($number_of_guest_title) : '';
			$bookingpress_front_vue_data_fields['number_of_person_title'] = !empty($number_of_person_title) ? stripslashes_deep($number_of_person_title) : '';			

			return $bookingpress_front_vue_data_fields;
		}


		function bookingpress_front_modify_cart_data_filter_func($bookingpress_appointment_details) {
			
			if(!empty($bookingpress_appointment_details['bookingpress_selected_bring_members'])) {				
				$bookingpress_appointment_details['bookingpress_selected_bring_members'] =  intval($bookingpress_appointment_details['bookingpress_selected_bring_members'] );
			}	

			return $bookingpress_appointment_details;
		}
	}
}
global $bookingpress_bring_anyone_with_you;
$bookingpress_bring_anyone_with_you = new bookingpress_bring_anyone_with_you();


