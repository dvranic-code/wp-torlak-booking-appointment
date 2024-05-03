<?php
/**
 * Main Class for plugin
 *
 * @package TorlakBookingAppointment
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Torlak_Booking_Appointment' ) ) {
	/**
	 * Main Class for plugin
	 */
	class Torlak_Booking_Appointment {
		/**
		 * Constructor
		 */
		public function __construct() {
			// Hook for adding admin menus.
			add_action( 'admin_menu', array( $this, 'add_plugin_main_page' ) );

			// Admin scripts.
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );

			// Ajax action tor_get_free_slots. tor_get_all_today_bookings
			add_action( 'wp_ajax_tor_get_free_slots', array( $this, 'tor_get_free_slots' ) );
			add_action( 'wp_ajax_nopriv_tor_get_free_slots', array( $this, 'tor_get_free_slots' ) );

			// Ajax action tor_get_all_today_bookings.
			add_action( 'wp_ajax_tor_get_all_today_bookings', array( $this, 'tor_get_all_today_bookings' ) );

			// Store booking after form submit.
			add_action( 'gform_after_submission', array( $this, 'tor_store_booking' ), 10, 2 );
		}

		/**
		 * Main page for plugin
		 *
		 * @return void
		 */
		public function add_plugin_main_page() {
			add_menu_page(
				'Torlak Booking Appointment',
				'Torlak Booking Appointment',
				'manage_options',
				'torlak-booking-appointment',
				array( $this, 'torlak_booking_appointment_page' ),
				'dashicons-calendar-alt',
				82
			);
		}

		/**
		 * Main page for plugin
		 *
		 * @return void
		 */
		public function torlak_booking_appointment_page() {
			ob_start();
			include TBA_PLUGIN_PATH . 'admin-parts/main-page.php';
			$output = ob_get_clean();
			// echo wp_kses_post( $output );
			echo $output;
		}

		/**
		 * Admin scripts
		 *
		 * @param string $hook_suffix Hook suffix.
		 *
		 * @return void
		 */
		public function admin_scripts( $hook_suffix ) {
			if ( 'toplevel_page_torlak-booking-appointment' !== $hook_suffix ) {
				return;
			}

			wp_enqueue_script( 'torlak-booking-admin', TBA_PLUGIN_URL . 'js/tor-admin.js', array( 'jquery' ), '1.0', true );
			wp_localize_script(
				'torlak-booking-admin',
				'torlak_booking',
				array(
					'ajax_url' => admin_url( 'admin-ajax.php' ),
				)
			);

			wp_enqueue_style( 'torlak-booking-admin', TBA_PLUGIN_URL . 'css/tor-admin.css' );
		}

		/**
		 * Ajax action for getting free slots
		 *
		 * @return void
		 */
		public function tor_get_free_slots() {
			$date       = sanitize_text_field( $_POST['date'] ); //phpcs:ignore
			$services_text = sanitize_text_field( $_POST['services_text'] ); //phpcs:ignore
			$services_ids = sanitize_text_field( $_POST['services_ids'] ); //phpcs:ignore
			$services_ids  = explode( ',', $services_ids );
			$day = date( 'l', strtotime( $date ) ); //phpcs:ignore

			// Check if date is in the past or today. If it is, return error.
			if ( strtotime( $date ) < strtotime( date( 'Y-m-d' ) ) ) { //phpcs:ignore
				wp_send_json_error( pll__( 'Датум је у прошлости, молим Вас одаберите било који дан од сутра.' ) );
			}

			$relevant_service = $this->get_relevant_service( $services_ids );

			// check if day is in the days array.
			if ( ! in_array( $day, $relevant_service['days'], true ) ) {
				// convert days to string.
				$days = implode( ', ', $relevant_service['days'] );
				wp_send_json_error( pll__( 'Доступни су само следећи дани: ' ) . $days );
			}

			// get all bookings for that day and relevant_service id.
			$bookings = Torlak_Booking_Database::get_bookings( $date, $relevant_service['id'] );

			// from relevant_service get slots.
			$slots = array();

			foreach ( $relevant_service['slots'] as $slot ) {
				$slots[] = $slot['time_slot'];
			}

			// from bookings get booked slots.
			$booked_slots = array();
			if ( ! empty( $bookings ) ) {
				foreach ( $bookings as $booking ) {
					$booked_slots[] = $booking['day_slot'];
				}
			}

			// get free slots.
			$free_slots = array_diff( $slots, $booked_slots );

			wp_send_json_success(
				array(
					'slots'            => $free_slots,
					'relevant_service' => $relevant_service,
				)
			);
		}

		/**
		 * Ajax action for getting all today bookings
		 *
		 * @return void
		 */
		public function tor_get_all_today_bookings() {
			$date     = sanitize_text_field( $_POST['date'] ); //phpcs:ignore
			// change date format to dd.mm.yyyy.
			$date = date( 'd.m.Y', strtotime( $date ) ); //phpcs:ignore
			$bookings = Torlak_Booking_Database::get_all_bookings( $date );

			wp_send_json_success( $bookings );
		}

		/**
		 * Store booking after form submit
		 *
		 * @param array $entry Entry.
		 * @param array $form Form.
		 *
		 * @return void
		 */
		public function tor_store_booking( $entry, $form ) {
			// Check if form has class 'gform-zakazivanje'. If not, return.
			if ( 'gform-zakazivanje' !== $form['cssClass'] ) {
				return;
			}

			$fields = $form['fields'];

			// Fields in DB table.
			$booking_date     = array( 'cssClass' => 'tor-pick-date' );
			$day_slot         = array( 'cssClass' => 'tor-picked-slot' );
			$service_id       = array( 'cssClass' => 'tor-relevant-service-id' );
			$pacient          = array( 'cssClass' => 'tor-pacient' );
			$email            = array( 'cssClass' => 'tor-email' );
			$phone            = array( 'cssClass' => 'tor-phone' );
			$jmbg             = array( 'cssClass' => 'tor-jmbg' );
			$choosed_services = array( 'cssClass' => 'tor-requested-services' );

			foreach ( $fields as $field ) {
				if ( isset( $field['cssClass'] ) ) {
					if ( $field['cssClass'] === $booking_date['cssClass'] ) {
						$booking_date['value'] = $entry[ $field['id'] ];
					}
					if ( $field['cssClass'] === $day_slot['cssClass'] ) {
						$day_slot['value'] = $entry[ $field['id'] ];
					}
					if ( $field['cssClass'] === $service_id['cssClass'] ) {
						$service_id['value'] = $entry[ $field['id'] ];
					}
					if ( $field['cssClass'] === $pacient['cssClass'] ) {
						$pacient['value'] = $entry[ $field['id'] ];
					}
					if ( $field['cssClass'] === $email['cssClass'] ) {
						$email['value'] = $entry[ $field['id'] ];
					}
					if ( $field['cssClass'] === $phone['cssClass'] ) {
						$phone['value'] = $entry[ $field['id'] ];
					}
					if ( $field['cssClass'] === $jmbg['cssClass'] ) {
						$jmbg['value'] = $entry[ $field['id'] ];
					}
					if ( $field['cssClass'] === $choosed_services['cssClass'] ) {
						$choosed_services['value'] = $entry[ $field['id'] ];
					}
				}
			}

			// Get week day from date.
			$week_day = date( 'l', strtotime( $booking_date['value'] ) ); //phpcs:ignore

			// Get date dd.mm.yyyy.
			$booking_date['value'] = date( 'd.m.Y', strtotime( $booking_date['value'] ) ); //phpcs:ignore

			// Data for DB.
			$data = array(
				'booking_date'     => $booking_date['value'],
				'week_day'         => $week_day,
				'day_slot'         => $day_slot['value'],
				'service_id'       => $service_id['value'],
				'pacient'          => $pacient['value'],
				'email'            => $email['value'],
				'phone'            => $phone['value'],
				'jmbg'             => $jmbg['value'],
				'choosed_services' => $choosed_services['value'],
			);

			// Store booking.
			Torlak_Booking_Database::set_booking( $data );
		}

		/**
		 * Get services days and slots
		 *
		 * @param array $services_ids Array of services ids.
		 *
		 * @return array
		 */
		private function get_relevant_service( $services_ids ) {
			$services = array();

			foreach ( $services_ids as $service_id ) {
				$is_custom_booking = get_field( 'is_custom_days_and_hours', $service_id );
				if ( $is_custom_booking ) {
					$custom = true;
					$days   = get_field( 'custom_days', $service_id );
					$slots  = get_field( 'custom_hours', $service_id );
				} else {
					$custom = false;
					$days   = get_field( 'tor_booking_default_days', 'option' );
					$slots  = get_field( 'tor_booking_default_hours', 'option' );
				}
				$services[] = array(
					'id'             => $service_id,
					'custom_booking' => $custom,
					'days'           => $days,
					'slots'          => $slots,
				);
			}

			// Check if in array is custom booking and get that record.
			$relevant_service = null;
			foreach ( $services as $service ) {
				if ( $service['custom_booking'] ) {
					$relevant_service = $service;
					break;
				} else {
					$relevant_service = $service;
				}
			}

			return $relevant_service;
		}
	}

	new Torlak_Booking_Appointment();
}
