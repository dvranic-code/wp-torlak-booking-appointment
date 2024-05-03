<?php
/**
 * Main page for plugin
 *
 * @return void
 * @package TorlakBookingAppointment
 */

// get today date in format day of the week, dd.mm.yyyy.
$today    = date( 'l, d.m.Y' ); //phpcs:ignore
$bookings = Torlak_Booking_Database::get_all_bookings( date( 'd.m.Y' ) ); //phpcs:ignore
?>
<div class="wrap">
	<h1>Torlak Booking Appointment</h1>
	<p>Today is: <?php echo esc_html( $today ); ?></p>
	<p>Change date: <input type="date" class="tor-booking-date-input"></p>
	<div id="todays-booking">
		<h2>Заказане услуге за: <span><?php echo esc_html( $today ); ?></span></h2>
		<table>
			<thead>
				<tr>
					<th>Ред.Бр</th>
					<th>Време</th>
					<th>Име пацијента</th>
					<th>Е-пошта</th>
					<th>Телефон</th>
					<th>ЈМБГ</th>
					<th>Изабране услуге</th>
				</tr>
			</thead>
			<tbody>
				<?php
				if ( ! empty( $bookings ) ) {
					foreach ( $bookings as $key => $booking ) {
						echo '<tr>';
						echo '<td>' . esc_html( $key + 1 ) . '</td>';
						echo '<td>' . esc_html( $booking['day_slot'] ) . '</td>';
						echo '<td>' . esc_html( $booking['pacient'] ) . '</td>';
						echo '<td>' . esc_html( $booking['email'] ) . '</td>';
						echo '<td>' . esc_html( $booking['phone'] ) . '</td>';
						echo '<td>' . esc_html( $booking['jmbg'] ) . '</td>';
						echo '<td>' . esc_html( $booking['choosed_services'] ) . '</td>';
						echo '</tr>';
					}
				} else {
					echo '<tr><td colspan="7">Нема прегледа заказаних за данас.</td></tr>';
				}
				?>
		</table>
	</div>

	<input type="submit" accesskey="p" value="Print Table" class="button button-primary button-large" id="print-todays-table">
	
</div>