<?php
/**
 * Main Class for plugin
 *
 * @package TorlakBookingAppointment
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'TorlakBookingAppointment' ) ) {
	/**
	 * Main Class for plugin
	 */
	class TorlakBookingAppointment {
		/**
		 * Constructor
		 */
		public function __construct() {
			// code here.
		}
	}

	new TorlakBookingAppointment();
}
