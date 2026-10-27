<?php
/**
 * .ics download for a single event — /event/{slug}/?ics=1.
 *
 * The universal half of "Add to Calendar": Google and Outlook.com users get
 * deep links (built in single-event.php), and everyone else — Apple Calendar,
 * desktop Outlook, Thunderbird — gets this file, which opens in whatever app
 * owns text/calendar on their machine. Recurring events carry a real RRULE,
 * so subscribing to "Every Monday" means every Monday, not just the next one.
 *
 * Times are written as *floating* local time (no Z, no TZID) deliberately:
 * these are in-person events at a Raleigh juice bar, so the wall-clock time
 * is the truth — 7 PM means 7 PM at the door. A TZID would be more correct on
 * paper but requires shipping a VTIMEZONE block, and a UTC conversion would
 * drift recurring events by an hour every DST change. Floating time sidesteps
 * both and renders right for the local audience the events serve.
 *
 * @package Juiced
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Escape text for an iCalendar property value (RFC 5545 §3.3.11).
 *
 * @param string $text Raw text.
 * @return string
 */
function juiced_ics_escape( $text ) {
	$text = str_replace( array( '\\', ';', ',' ), array( '\\\\', '\;', '\,' ), (string) $text );
	return str_replace( array( "\r\n", "\r", "\n" ), '\n', $text );
}

/**
 * Fold an iCalendar content line to RFC 5545's 75-octet limit, splitting on
 * character boundaries so multibyte text can't be cut mid-sequence.
 *
 * @param string $line Unfolded line.
 * @return string Folded line(s), CRLF + space continuations included.
 */
function juiced_ics_fold( $line ) {
	if ( strlen( $line ) <= 73 ) {
		return $line;
	}
	$out     = '';
	$current = '';
	foreach ( preg_split( '//u', $line, -1, PREG_SPLIT_NO_EMPTY ) as $char ) {
		if ( strlen( $current ) + strlen( $char ) > 73 ) {
			$out    .= $current . "\r\n ";
			$current = '';
		}
		$current .= $char;
	}
	return $out . $current;
}

/**
 * Serve the event as an .ics attachment when its permalink is hit with ?ics.
 *
 * @return void
 */
function juiced_event_ics_download() {
	if ( ! is_singular( 'event' ) || ! isset( $_GET['ics'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- public, read-only download.
		return;
	}

	$event_id = get_queried_object_id();
	$span     = juiced_event_occurrence_span( $event_id );

	// Nothing upcoming to put on a calendar — back to the page, which says so.
	if ( ! $span ) {
		wp_safe_redirect( get_permalink( $event_id ) );
		exit;
	}

	$title    = html_entity_decode( get_the_title( $event_id ), ENT_QUOTES, get_bloginfo( 'charset' ) );
	$location = (string) get_field( 'location', $event_id );
	$rrule    = juiced_event_rrule( $event_id );
	$link     = get_permalink( $event_id );

	$summary = wp_strip_all_tags( get_the_excerpt( $event_id ) );
	$details = trim( $summary . "\n\n" . __( 'Details:', 'juiced' ) . ' ' . $link );

	$lines = array(
		'BEGIN:VCALENDAR',
		'VERSION:2.0',
		'PRODID:-//Juiced! Juice Bar//Events//EN',
		'CALSCALE:GREGORIAN',
		'METHOD:PUBLISH',
		'BEGIN:VEVENT',
		'UID:event-' . $event_id . '@' . wp_parse_url( home_url(), PHP_URL_HOST ),
		'DTSTAMP:' . gmdate( 'Ymd\THis\Z' ),
	);

	if ( $span['all_day'] ) {
		$lines[] = 'DTSTART;VALUE=DATE:' . $span['start']->format( 'Ymd' );
		$lines[] = 'DTEND;VALUE=DATE:' . $span['end']->format( 'Ymd' );
	} else {
		$lines[] = 'DTSTART:' . $span['start']->format( 'Ymd\THis' );
		$lines[] = 'DTEND:' . $span['end']->format( 'Ymd\THis' );
	}

	if ( $rrule ) {
		$lines[] = 'RRULE:' . $rrule;
	}

	$lines[] = 'SUMMARY:' . juiced_ics_escape( $title );
	if ( $location ) {
		$lines[] = 'LOCATION:' . juiced_ics_escape( $location );
	}
	$lines[] = 'DESCRIPTION:' . juiced_ics_escape( $details );
	$lines[] = 'URL:' . juiced_ics_escape( $link );
	$lines[] = 'END:VEVENT';
	$lines[] = 'END:VCALENDAR';

	nocache_headers();
	header( 'Content-Type: text/calendar; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename="' . sanitize_file_name( get_post_field( 'post_name', $event_id ) . '.ics' ) . '"' );

	echo implode( "\r\n", array_map( 'juiced_ics_fold', $lines ) ) . "\r\n"; // phpcs:ignore WordPress.Security.EscapeOutput -- text/calendar payload, ICS-escaped above.
	exit;
}
add_action( 'template_redirect', 'juiced_event_ics_download' );
