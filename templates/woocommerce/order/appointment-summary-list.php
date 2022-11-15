<?php
/**
 * The template for displaying the list of appointments in the summary for customers.
 * It is used in:
 * - templates/order/appointment-display.php
 * - templates/order/admin/appointment-display.php
 * It will display in four places:
 * - After checkout,
 * - In the order confirmation email, and
 * - When customer reviews order in My Account > Orders,
 * - When reviewing a customer order in the admin area.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/order/appointment-summary-list.php.
 *
 * HOWEVER, on occasion we will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @version 4.14.3
 * @since   3.4.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

?>
<ul class="wc-appointment-summary-list" style="padding: 0; list-style: none outside;">

	<?php
	// When?
	// if ( isset( $date ) && $date ) {
	// 	printf(
	// 		'<li%1$s>%2$s: <strong>%3$s</strong></li>',
	// 		esc_html( isset( $is_rtl ) && 'right' === $is_rtl ? ' dir="rtl"' : '' ),
	// 		esc_html__( 'When', 'woocommerce-appointments' ),
	// 		esc_attr( $date )
	// 	);
	// }
	// Duration?
	if ( isset( $duration ) && $duration ) {
		// printf(
		// 	'<li%1$s>%2$s: <strong>%3$s</strong></li>',
		// 	esc_html( isset( $is_rtl ) && 'right' === $is_rtl ? ' dir="rtl"' : '' ),
		// 	esc_html__( 'Duration', 'woocommerce-appointments' ),
		// 	esc_attr( $duration )
		// );

		$duration_split = explode(' ', $duration);
		if(substr( $duration_split[1], 0, 3 ) === 'day') {

			$product_id = $product->get_id();
			$html = '';

			$checkin_date = \DateTime::createFromFormat('F d, Y', $date);
			$checkin_date_value = $checkin_date->format('F d, Y');

			// make sure not bool
			if ($checkin_date instanceof \DateTime) {

				// Fly fishing when set to a day
                if($product_id == 212111) {
                    $html .= '<li><strong>Date:</strong> ' . $checkin_date_value . '</li>';
				// Horseback riding when set to a day
                } else if($product_id == 212112) {
					$html .= '<li><strong>Date:</strong> ' . $checkin_date_value . ' @ 10:00 AM</li>';
                } else {

					// Cabins
					if($product_id == 211976 || $product_id == 216196) {
						$checkin_date_value = '4 PM, ' . $checkin_date_value;
						$checkout_date = $checkin_date->modify('+' . ($duration_split[0]) . ' days');
						$checkout_date_value = '10 AM, ' . $checkout_date->format('F d, Y');
						$start_label = 'Check In:';
						$end_label = 'Check Out:';
						$type = 'type-guest-cabin';
					} else {
						$checkout_date = $checkin_date->modify('+' . ($duration_split[0]-1) . ' days');
						$checkout_date_value = $checkout_date->format('F d, Y');
						$start_label = 'Start Date:';
						$end_label = 'End Date:';
						$type = 'type-other';
					}

                    $html .= '<li><strong>' . $start_label . ' </strong> ' . $checkin_date_value . '</li>';
                    $html .= '<li><strong>' . $end_label . ' </strong> ' . $checkout_date_value . '</li>';
                }

				echo $html;
				// printf(
				// 	'<li%1$s>%2$s: <strong>%3$s</strong></li>',
				// 	esc_html( isset( $is_rtl ) && 'right' === $is_rtl ? ' dir="rtl"' : '' ),
				// 	esc_html__( 'End Date', 'woocommerce-appointments' ),
				// 	esc_attr( $checkout_date->format('F d, Y') )
				// );
			}
		}
		if(substr( $duration_split[1], 0, 4 ) === 'hour') {

			// calculate start time and end time
			$start_datetime = \DateTime::createFromFormat('F d, Y, g:i a', $date);
			$amr_starttime = $start_datetime->format('g:i A');

			$end_datetime = $start_datetime;
			$end_datetime = $end_datetime->modify('+' . $duration);
			$amr_endtime = $end_datetime->format('g:i A');

			printf(
				'<li%1$s>%2$s: <strong>%3$s</strong></li>',
				esc_html( isset( $is_rtl ) && 'right' === $is_rtl ? ' dir="rtl"' : '' ),
				esc_html__( 'Start Time', 'woocommerce-appointments' ),
				esc_attr( $amr_starttime )
			);
			printf(
				'<li%1$s>%2$s: <strong>%3$s</strong></li>',
				esc_html( isset( $is_rtl ) && 'right' === $is_rtl ? ' dir="rtl"' : '' ),
				esc_html__( 'End Time', 'woocommerce-appointments' ),
				esc_attr( $amr_endtime )
			);
		}

		
	}
	
	// Providers?
	// if ( isset( $providers ) && $providers ) {
	// 	printf(
	// 		'<li%1$s>%2$s: <strong>%3$s</strong></li>',
	// 		esc_html( isset( $is_rtl ) && 'right' === $is_rtl ? ' dir="rtl"' : '' ),
	// 		esc_attr( $label ),
	// 		esc_attr( $providers )
	// 	);
	// }
	?>
</ul>
