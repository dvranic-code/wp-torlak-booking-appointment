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
define( 'TBA_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'TBA_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Check if ACF PRO is active.
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

		if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) !== $table_name ) { // phpcs:ignore
			$sql = "CREATE TABLE $table_name (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
				booking_date VARCHAR(15) NOT NULL,
				week_day VARCHAR(20) NOT NULL,
				day_slot VARCHAR(150) NOT NULL,
				service_id INT NOT NULL,
				pacient VARCHAR(150) NOT NULL,
				email VARCHAR(250) NOT NULL,
				phone VARCHAR(150),
				jmbg VARCHAR(15),
				choosed_services MEDIUMTEXT,
				created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
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
		$wpdb->query( $sql ); // phpcs:ignore
	}

	// Database class.
	require_once plugin_dir_path( __FILE__ ) . 'src/class-torlak-booking-database.php';

	// Initialize admin page.
	require_once plugin_dir_path( __FILE__ ) . 'src/class-torlak-booking-appointment.php';

	// Initialize ACF PRO controller.
	require_once plugin_dir_path( __FILE__ ) . 'src/class-torlak-booking-acf-controller.php';

} else {
	/**
	 * Admin notice if ACF PRO is not active.
	 */
	function admin_notice_acf_not_active() {
		?>
		<div class="notice notice-error is-dismissible">
			<p><?php esc_html_e( 'ACF PRO must be activated before you can use the "Torlak Booking Appointment" plugin.', TBA_TEXT_DOMAIN ); // phpcs:ignore ?></p>
		</div>
		<?php
	}
	add_action( 'admin_notices', 'admin_notice_acf_not_active' );
}