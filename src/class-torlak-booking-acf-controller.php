<?php
/**
 * Main ACF PRO contorller
 *
 * @package TorlakBookingAppointment
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Torlak_Booking_ACF_Controller' ) ) {
	/**
	 * Main ACF PRO contorller
	 */
	class Torlak_Booking_ACF_Controller {
		/**
		 * Constructor
		 */
		public function __construct() {
			add_action( 'acf/init', array( $this, 'add_options' ) );
			add_action( 'acf/init', array( $this, 'add_groups_and_fields' ) );
		}

		/**
		 * Adds options to the Advanced Custom Fields plugin.
		 *
		 * @return void
		 */
		public function add_options() {
			if ( function_exists( 'acf_add_options_page' ) ) {
				acf_add_options_page(
					array(
						'page_title' => __( 'Torlak Booking Settings', TBA_TEXT_DOMAIN ), //phpcs:ignore
						'menu_title' => __( 'Booking Settings', TBA_TEXT_DOMAIN ), //phpcs:ignore
						'menu_slug'  => 'torlak-booking-appointment-settings',
						'icon_url'   => 'dashicons-calendar-alt',
					)
				);
			}
		}

		/**
		 * Adds groups and fields to the Advanced Custom Fields plugin.
		 *
		 * @return void
		 */
		public function add_groups_and_fields() {
			if ( function_exists( 'acf_add_local_field_group' ) ) {

				// Add group for booking settings.
				acf_add_local_field_group(
					array(
						'key'                   => 'group_tor_opt_default_days_and_hours',
						'title'                 => __( 'Default Days and hours', TBA_TEXT_DOMAIN ), //phpcs:ignore
						'fields'                => array(
							array(
								'key'           => 'field_tor_opt_default_days',
								'label'             => __( 'Defalt Days', TBA_TEXT_DOMAIN ), //phpcs:ignore
								'name'          => 'tor_booking_default_days',
								'type'          => 'checkbox',
								'instructions'      => __( 'Select default days when appointmets can be made', TBA_TEXT_DOMAIN ), //phpcs:ignore
								'choices'       => array(
									'Monday'    => __( 'Monday', TBA_TEXT_DOMAIN ), //phpcs:ignore
									'Tuesday'   => __( 'Tuesday', TBA_TEXT_DOMAIN ), //phpcs:ignore
									'Wednesday' => __( 'Wednesday', TBA_TEXT_DOMAIN ), //phpcs:ignore
									'Thursday'  => __( 'Thursday', TBA_TEXT_DOMAIN ), //phpcs:ignore
									'Friday'    => __( 'Friday', TBA_TEXT_DOMAIN ), //phpcs:ignore
									'Saturday'  => __( 'Saturday', TBA_TEXT_DOMAIN ), //phpcs:ignore
									'Sunday'    => __( 'Sunday', TBA_TEXT_DOMAIN ), //phpcs:ignore
								),
								'required'      => 1,
								'layout'        => 'horizontal',
								'return_format' => 'value',
								'default_value' => array(
									'Monday'    => 'Monday',
									'Tuesday'   => 'Tuesday',
									'Wednesday' => 'Wednesday',
									'Thursday'  => 'Thursday',
									'Friday'    => 'Friday',
								),
							),
							array(
								'key'          => 'field_tor_opt_default_hours',
								'label'             => __( 'Default Hours', TBA_TEXT_DOMAIN ), //phpcs:ignore
								'name'         => 'tor_booking_default_hours',
								'type'         => 'repeater',
								'instructions'      => __( 'Enter deafault day time slots', TBA_TEXT_DOMAIN ), //phpcs:ignore
								'required'     => 1,
								'layout'       => 'table',
								'button_label'      => __( 'Add Slot', TBA_TEXT_DOMAIN ), //phpcs:ignore
								'sub_fields'   => array(
									array(
										'key'          => 'field_tor_opt_default_time_slots',
										'label'         => __( 'Appointment time slot', TBA_TEXT_DOMAIN ), //phpcs:ignore
										'name'         => 'time_slot',
										'type'         => 'text',
										'instructions'  => __( 'Enter slot', TBA_TEXT_DOMAIN ), //phpcs:ignore
										'placeholder'  => '8:00',
									),
								),
							),
						),
						'location'              => array(
							array(
								array(
									'param'    => 'options_page',
									'operator' => '==',
									'value'    => 'torlak-booking-appointment-settings',
								),
							),
						),
						'menu_order'            => 0,
						'position'              => 'normal',
						'style'                 => 'default',
						'label_placement'       => 'top',
						'instruction_placement' => 'label',
						'active'                => 1,
						'description'           => '',
					)
				);

				// Add group for usluge post type.
				acf_add_local_field_group(
					array(
						'key'                   => 'group_tor_usluge_days_and_hours',
						'title'                 => __( 'Custom Days and hours', TBA_TEXT_DOMAIN ), //phpcs:ignore
						'fields'                => array(
							// First and true/false field Is custom days and hours.
							array(
								'key'           => 'field_tor_usluge_custom_days_and_hours',
								'label'             => __( 'Is Custom?', TBA_TEXT_DOMAIN ), //phpcs:ignore
								'name'          => 'is_custom_days_and_hours',
								'type'          => 'true_false',
								'instructions'      => __( 'Check if you want to set custom days and hours for this service', TBA_TEXT_DOMAIN ), //phpcs:ignore
								'required'      => 1,
								'layout'        => 'horizontal',
								'return_format' => 'value',
								'default_value' => 0,
							),
							// Second field for custom days, but only if first is checked.
							array(
								'key'               => 'field_tor_usluge_custom_days',
								'label'             => __( 'Custom Days', TBA_TEXT_DOMAIN ), //phpcs:ignore
								'name'              => 'custom_days',
								'type'              => 'checkbox',
								'instructions'      => __( 'Select custom days when appointmets can be made', TBA_TEXT_DOMAIN ), //phpcs:ignore
								'choices'           => array(
									'Monday'    => __( 'Monday', TBA_TEXT_DOMAIN ), //phpcs:ignore
									'Tuesday'   => __( 'Tuesday', TBA_TEXT_DOMAIN ), //phpcs:ignore
									'Wednesday' => __( 'Wednesday', TBA_TEXT_DOMAIN ), //phpcs:ignore
									'Thursday'  => __( 'Thursday', TBA_TEXT_DOMAIN ), //phpcs:ignore
									'Friday'    => __( 'Friday', TBA_TEXT_DOMAIN ), //phpcs:ignore
									'Saturday'  => __( 'Saturday', TBA_TEXT_DOMAIN ), //phpcs:ignore
									'Sunday'    => __( 'Sunday', TBA_TEXT_DOMAIN ), //phpcs:ignore
								),
								'required'          => 1,
								'layout'            => 'vertical',
								'return_format'     => 'value',
								'conditional_logic' => array(
									array(
										array(
											'field'    => 'field_tor_usluge_custom_days_and_hours',
											'operator' => '==',
											'value'    => '1',
										),
									),
								),
							),
							// Third field for custom hours, but only if first is checked.
							array(
								'key'               => 'field_tor_usluge_custom_hours',
								'label'             => __( 'Custom Hours', TBA_TEXT_DOMAIN ), //phpcs:ignore
								'name'              => 'custom_hours',
								'type'              => 'repeater',
								'instructions'      => __( 'Enter custom day time slots', TBA_TEXT_DOMAIN ), //phpcs:ignore
								'required'          => 1,
								'layout'            => 'table',
								'button_label'      => __( 'Add Slot', TBA_TEXT_DOMAIN ), //phpcs:ignore
								'conditional_logic' => array(
									array(
										array(
											'field'    => 'field_tor_usluge_custom_days_and_hours',
											'operator' => '==',
											'value'    => '1',
										),
									),
								),
								'sub_fields'        => array(
									array(
										'key'          => 'field_tor_usluge_custom_time_slots',
										'label'         => __( 'Appointment time slot', TBA_TEXT_DOMAIN ), //phpcs:ignore
										'name'         => 'time_slot',
										'type'         => 'text',
										'instructions'  => __( 'Enter slot', TBA_TEXT_DOMAIN ), //phpcs:ignore
										'placeholder'  => '8:00',
									),
								),
							),
						),
						'location'              => array(
							array(
								array(
									'param'    => 'post_type',
									'operator' => '==',
									'value'    => 'usluge',
								),
							),
						),
						'menu_order'            => 0,
						'position'              => 'side',
						'style'                 => 'default',
						'label_placement'       => 'top',
						'instruction_placement' => 'label',
						'active'                => 1,
						'description'           => '',
					)
				);
			}
		}
	}

	new Torlak_Booking_ACF_Controller();
}
