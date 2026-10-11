<?php
/**
 * Host an Event and Careers form handlers.
 *
 * Both forms post to admin-post.php (action=juiced_host_event /
 * juiced_job_application), are delivered with wp_mail() and redirect back to
 * their page with ?host=sent|error or ?apply=sent|error, which the template
 * turns into a notice. Same approach as inc/contact-form.php: a nonce plus a
 * honeypot, no form plugin. A filled honeypot is silently "accepted".
 *
 * The job form also takes an optional résumé (PDF or Word, 5 MB max). It is
 * moved to a private temp folder, attached to the email, then deleted, so
 * applications never sit in the public uploads folder.
 *
 * @package Juiced
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Largest résumé the Careers form accepts, in bytes. */
const JUICED_RESUME_MAX_BYTES = 5 * MB_IN_BYTES;

/**
 * URL of the first published page using a given page template, else
 * home_url( $slug ) — the slug the page is expected to use.
 *
 * @param string $template Template path, e.g. 'page-templates/careers.php'.
 * @param string $slug     Fallback slug.
 * @return string
 */
function juiced_template_page_url( $template, $slug ) {
	$pages = get_posts(
		array(
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'no_found_rows'  => true,
			'meta_key'       => '_wp_page_template',
			'meta_value'     => $template,
		)
	);

	return $pages ? get_permalink( $pages[0] ) : home_url( '/' . $slug . '/' );
}

/** @return string URL of the Host an Event page. */
function juiced_host_event_page_url() {
	return juiced_template_page_url( 'page-templates/host-event.php', 'host-an-event' );
}

/** @return string URL of the Careers page. */
function juiced_careers_page_url() {
	return juiced_template_page_url( 'page-templates/careers.php', 'careers' );
}

/**
 * Classes shared by the text inputs, selects and textareas on the Host an Event
 * and Careers forms (the same look as the Contact form).
 *
 * @return string
 */
function juiced_form_input_classes() {
	return 'w-full rounded-xl border border-brand-forest/20 bg-brand-cream px-4 py-3 text-[15px] text-brand-ink placeholder:text-brand-ink/45 focus:border-brand-forest focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-forest/20';
}

/**
 * The two shop names from Business Info, for the forms' location dropdowns.
 * Empty when neither is set, and the forms then skip the question.
 *
 * @return string[]
 */
function juiced_location_names() {
	return array_values(
		array_filter(
			array(
				trim( (string) juiced_business_field( 'loc1_name', '' ) ),
				trim( (string) juiced_business_field( 'loc2_name', '' ) ),
			)
		)
	);
}

/**
 * Read one posted text field.
 *
 * @param string $key       Field name.
 * @param bool   $multiline Keep line breaks.
 * @return string
 */
function juiced_posted( $key, $multiline = false ) {
	// phpcs:ignore WordPress.Security.NonceVerification.Missing -- callers verify the nonce first.
	if ( ! isset( $_POST[ $key ] ) ) {
		return '';
	}
	// phpcs:ignore WordPress.Security.NonceVerification.Missing
	$value = wp_unslash( $_POST[ $key ] );
	return $multiline ? sanitize_textarea_field( $value ) : sanitize_text_field( $value );
}

/**
 * Redirect back to a page with a result flag and stop.
 *
 * @param string $url    Page URL.
 * @param string $param  Query arg, e.g. 'host'.
 * @param string $flag   'sent' or 'error'.
 * @param string $anchor Fragment to land on.
 */
function juiced_form_finish( $url, $param, $flag, $anchor ) {
	wp_safe_redirect( add_query_arg( $param, $flag, $url ) . '#' . $anchor );
	exit;
}

/**
 * Recipient for a form: the ACF email field on its page, else the admin email.
 *
 * @param string $page_url Page the form lives on.
 * @param string $field    ACF field name.
 * @return string
 */
function juiced_form_recipient( $page_url, $field ) {
	$to = '';
	if ( function_exists( 'get_field' ) ) {
		$page_id = url_to_postid( $page_url );
		$to      = $page_id ? sanitize_email( (string) get_field( $field, $page_id ) ) : '';
	}
	return is_email( $to ) ? $to : get_option( 'admin_email' );
}

/**
 * Format "Label: value" lines for the email body, skipping empty values.
 *
 * @param array $pairs Label => value.
 * @return string[]
 */
function juiced_form_lines( $pairs ) {
	$lines = array();
	foreach ( $pairs as $label => $value ) {
		if ( '' !== $value ) {
			$lines[] = $label . ' ' . $value;
		}
	}
	return $lines;
}

/**
 * Handle a Host an Event submission.
 */
function juiced_handle_host_event_form() {
	$back = juiced_host_event_page_url();

	if ( ! isset( $_POST['juiced_host_nonce'] )
		|| ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['juiced_host_nonce'] ) ), 'juiced_host_event' ) ) {
		juiced_form_finish( $back, 'host', 'error', 'event-form' );
	}

	if ( juiced_posted( 'juiced_website' ) ) {
		juiced_form_finish( $back, 'host', 'sent', 'event-form' );
	}

	if ( ! juiced_captcha_verify() ) {
		juiced_form_finish( $back, 'host', 'error', 'event-form' );
	}

	$first   = juiced_posted( 'first_name' );
	$last    = juiced_posted( 'last_name' );
	$email   = sanitize_email( juiced_posted( 'email' ) );
	$details = juiced_posted( 'details', true );

	if ( ! $first || ! is_email( $email ) || ! $details ) {
		juiced_form_finish( $back, 'host', 'error', 'event-form' );
	}

	$name = trim( $first . ' ' . $last );
	$type = juiced_posted( 'event_type' );

	$lines   = juiced_form_lines(
		array(
			__( 'Name:', 'juiced' )            => $name,
			__( 'Email:', 'juiced' )           => $email,
			__( 'Phone:', 'juiced' )           => juiced_posted( 'phone' ),
			__( 'Organization:', 'juiced' )    => juiced_posted( 'organization' ),
			__( 'Event type:', 'juiced' )      => $type,
			__( 'Location:', 'juiced' )        => juiced_posted( 'location' ),
			__( 'Preferred date:', 'juiced' )  => juiced_posted( 'event_date' ),
			__( 'Expected guests:', 'juiced' ) => juiced_posted( 'guests' ),
		)
	);
	$lines[] = '';
	$lines[] = $details;

	$sent = wp_mail(
		juiced_form_recipient( $back, 'host_form_recipient' ),
		sprintf(
			/* translators: 1: event type or fallback label, 2: sender name. */
			__( '[Juiced! Event idea] %1$s — %2$s', 'juiced' ),
			$type ? $type : __( 'New event idea', 'juiced' ),
			$name
		),
		implode( "\n", $lines ),
		array( 'Reply-To: ' . $name . ' <' . $email . '>' )
	);

	juiced_form_finish( $back, 'host', $sent ? 'sent' : 'error', 'event-form' );
}
add_action( 'admin_post_juiced_host_event', 'juiced_handle_host_event_form' );
add_action( 'admin_post_nopriv_juiced_host_event', 'juiced_handle_host_event_form' );

/**
 * Move an uploaded résumé into a private temp folder under its original
 * (sanitized) name so the email attachment reads sensibly.
 *
 * @return string|WP_Error|null Temp file path, an error, or null when no file was sent.
 */
function juiced_take_resume_upload() {
	// phpcs:ignore WordPress.Security.NonceVerification.Missing -- the caller verifies the nonce first.
	if ( empty( $_FILES['resume'] ) || UPLOAD_ERR_NO_FILE === $_FILES['resume']['error'] ) {
		return null;
	}
	// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput
	$file = $_FILES['resume'];

	if ( UPLOAD_ERR_OK !== $file['error'] || $file['size'] > JUICED_RESUME_MAX_BYTES || ! is_uploaded_file( $file['tmp_name'] ) ) {
		return new WP_Error( 'resume' );
	}

	$mimes = array(
		'pdf'  => 'application/pdf',
		'doc'  => 'application/msword',
		'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
	);
	$check = wp_check_filetype_and_ext( $file['tmp_name'], $file['name'], $mimes );
	if ( ! $check['ext'] ) {
		return new WP_Error( 'resume' );
	}

	$dir = trailingslashit( get_temp_dir() ) . 'juiced-resume-' . wp_generate_password( 16, false );
	if ( ! wp_mkdir_p( $dir ) ) {
		return new WP_Error( 'resume' );
	}

	$base = sanitize_file_name( pathinfo( $file['name'], PATHINFO_FILENAME ) );
	$path = $dir . '/' . ( $base ? $base : 'resume' ) . '.' . $check['ext'];

	return move_uploaded_file( $file['tmp_name'], $path ) ? $path : new WP_Error( 'resume' );
}

/**
 * Handle a Careers application.
 */
function juiced_handle_job_application_form() {
	$back = juiced_careers_page_url();

	if ( ! isset( $_POST['juiced_apply_nonce'] )
		|| ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['juiced_apply_nonce'] ) ), 'juiced_job_application' ) ) {
		juiced_form_finish( $back, 'apply', 'error', 'apply' );
	}

	if ( juiced_posted( 'juiced_website' ) ) {
		juiced_form_finish( $back, 'apply', 'sent', 'apply' );
	}

	if ( ! juiced_captcha_verify() ) {
		juiced_form_finish( $back, 'apply', 'error', 'apply' );
	}

	$first        = juiced_posted( 'first_name' );
	$last         = juiced_posted( 'last_name' );
	$email        = sanitize_email( juiced_posted( 'email' ) );
	$phone        = juiced_posted( 'phone' );
	$availability = juiced_posted( 'availability', true );

	if ( ! $first || ! $last || ! is_email( $email ) || ! $phone || ! $availability ) {
		juiced_form_finish( $back, 'apply', 'error', 'apply' );
	}

	$resume = juiced_take_resume_upload();
	if ( is_wp_error( $resume ) ) {
		juiced_form_finish( $back, 'apply', 'resume', 'apply' );
	}

	$name  = trim( $first . ' ' . $last );
	$about = juiced_posted( 'about', true );

	$lines   = juiced_form_lines(
		array(
			__( 'Name:', 'juiced' )        => $name,
			__( 'Email:', 'juiced' )       => $email,
			__( 'Phone:', 'juiced' )       => $phone,
			__( 'Location:', 'juiced' )    => juiced_posted( 'location' ),
			__( 'Can start:', 'juiced' )   => juiced_posted( 'start_date' ),
			__( 'Résumé:', 'juiced' )      => $resume ? __( 'attached', 'juiced' ) : __( 'not included', 'juiced' ),
		)
	);
	$lines[] = '';
	$lines[] = __( 'Availability:', 'juiced' );
	$lines[] = $availability;
	if ( $about ) {
		$lines[] = '';
		$lines[] = __( 'About them:', 'juiced' );
		$lines[] = $about;
	}

	$sent = wp_mail(
		juiced_form_recipient( $back, 'careers_form_recipient' ),
		sprintf(
			/* translators: %s: applicant name. */
			__( '[Juiced! Application] %s', 'juiced' ),
			$name
		),
		implode( "\n", $lines ),
		array( 'Reply-To: ' . $name . ' <' . $email . '>' ),
		$resume ? array( $resume ) : array()
	);

	if ( $resume ) {
		wp_delete_file( $resume );
		@rmdir( dirname( $resume ) ); // phpcs:ignore WordPress.PHP.NoSilencedErrors, WordPress.WP.AlternativeFunctions -- best-effort cleanup of our own temp folder.
	}

	juiced_form_finish( $back, 'apply', $sent ? 'sent' : 'error', 'apply' );
}
add_action( 'admin_post_juiced_job_application', 'juiced_handle_job_application_form' );
add_action( 'admin_post_nopriv_juiced_job_application', 'juiced_handle_job_application_form' );
