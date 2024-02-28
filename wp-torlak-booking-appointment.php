<?php
/**
 * Plugin Name: Torlak Booking Appointment
 * GitHub Plugin URI: https://github.com/dvranic-code/wp-torlak-booking-appointment.git
 * Description: Custom plugin for booking appointments.
 * Version: 1.0.1
 * Author: Dejan Rudic Vranic
 * Author URI: https://studioagnis.com/
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: torlak-booking-appointment
 * Domain Path: /languages
 * 
 * @package TorlakBookingAppointment
 */

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
            `time` datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            `name` tinytext NOT NULL,
            `text` text NOT NULL,
            `url` varchar(55) DEFAULT '' NOT NULL,
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
