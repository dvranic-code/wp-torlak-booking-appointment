<?php
/**
 * Main Class for database CRUD
 *
 * @package TorlakBookingAppointment
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Torlak_Booking_Database' ) ) {
	/**
	 * Main Class for database CRUD
	 */
	class Torlak_Booking_Database {
		/**
		 * Get bookings for specific day and service
		 *
		 * @param string $date Date.
		 * @param int    $service_id Service id.
		 *
		 * @return array
		 */
		public static function get_bookings( $date, $service_id ) {
			global $wpdb;
			$table_name = $wpdb->prefix . 'torlak_booking_appointment';
			$sql        = $wpdb->prepare(
				"SELECT * FROM $table_name WHERE booking_date = %s AND service_id = %d", //phpcs:ignore
				$date,
				$service_id
			);
			$bookings = $wpdb->get_results( $sql, ARRAY_A ); //phpcs:ignore

			return $bookings;
		}

		/**
		 * Get all bookings for specific day
		 *
		 * @param string $date Date.
		 *
		 * @return array
		 */
		public static function get_all_bookings( $date ) {
			global $wpdb;
			$table_name = $wpdb->prefix . 'torlak_booking_appointment';
			$sql        = $wpdb->prepare(
				"SELECT * FROM $table_name WHERE booking_date = %s ORDER BY STR_TO_DATE(day_slot, %s)", //phpcs:ignore
				$date,
				'%H:%i'
			);
			$bookings = $wpdb->get_results( $sql, ARRAY_A ); //phpcs:ignore

			return $bookings;
		}

		/**
		 * Set booking
		 *
		 * @param array $data Data.
		 *
		 * @return void
		 */
		public static function set_booking( $data ) {
			global $wpdb;
			$table_name = $wpdb->prefix . 'torlak_booking_appointment';
			$wpdb->insert( $table_name, $data );
		}
	}

	new Torlak_Booking_Database();
}
