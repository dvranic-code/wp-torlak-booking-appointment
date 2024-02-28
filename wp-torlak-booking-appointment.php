<?php
/**
 * Plugin Name: Torlak Booking Appointment
 * GitHub Plugin URI: https://github.com/dvranic-code/wp-torlak-booking-appointment.git
 * Description: Custom plugin for booking appointments.
 * Version: 1.0.2
 * Author: Dejan Rudic Vranic
 * Author URI: https://studioagnis.com/
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: torlak-booking-appointment
 * Domain Path: /languages
 *
 * @package TorlakBookingAppointment
 */

define( 'TBA_TEXT_DOMAIN', 'torlak-booking-appointment' );

// Check if ACF PRO is active
require_once ABSPATH . 'wp-admin/includes/plugin.php';
if ( is_plugin_active( 'advanced-custom-fields-pro/acf.php' ) ) {
	// Hook for plugin activation.
	register_activation_hook( __FILE__, 'my_custom_plugin_create_table' );
	/**
	 * Create table for plugin.
	 */
	function my_custom_plugin_create_table() {
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		$table_name      = $wpdb->prefix . 'torlak_booking_appointment';

		if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name ) {
			$sql = "CREATE TABLE `$table_name` (
                `id` mediumint(9) NOT NULL AUTO_INCREMENT,
                `date` date DEFAULT NULL,
                `time` text DEFAULT NULL,
                `service_category` text DEFAULT NULL,
                `patient` text DEFAULT NULL,
                `email` text DEFAULT NULL,
                `phone` text DEFAULT NULL,
                `service_name` text DEFAULT NULL,
                `price` decimal(10,2) DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) $charset_collate;";

			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			dbDelta( $sql );
		}
	}

	// Hook for plugin deactivation.
	register_uninstall_hook( __FILE__, 'my_custom_plugin_delete_table' );
	/**
	 * Delete table for plugin.
	 */
	function my_custom_plugin_delete_table() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'torlak_booking_appointment';
		$sql        = "DROP TABLE IF EXISTS $table_name;";
		$wpdb->query( $sql );
	}

    // Initialize plugin
    require_once plugin_dir_path( __FILE__ ) . 'src/class-torlakbookingappointment.php';

} else {
	function admin_notice_acf_not_active() {
		?>
		<div class="notice notice-error is-dismissible">
			<p><?php _e( 'ACF PRO must be activated before you can use the "Torlak Booking Appointment" plugin.', TBA_TEXT_DOMAIN ); ?></p>
		</div>
		<?php
	}
	add_action( 'admin_notices', 'admin_notice_acf_not_active' );
}