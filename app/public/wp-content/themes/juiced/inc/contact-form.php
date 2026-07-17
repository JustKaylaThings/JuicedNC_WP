<?php
/**
 * Contact form handler.
 *
 * The Send Us a Message form (template-parts/contact/connect.php) posts to
 * admin-post.php with action=juiced_contact; this delivers it with wp_mail()
 * and redirects back to the contact page with ?contact=sent|error, which the
 * template turns into a notice. No plugin: the site has no form plugin
 * installed, and one form doesn't justify adding one.
 *
 * Spam defence is a nonce plus a honeypot field — submissions that fill the
 * honeypot are silently "accepted" (redirected to the success notice) so bots
 * get no signal to adapt to.
 *
 * @package Juiced
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * URL of the page using the Contact Page template — the redirect target after
 * a submission. Sibling of juiced_events_page_url() (inc/helpers.php); falls
 * back to /contact, the slug the page is expected to use.
 *
 * @return string
 */
function juiced_contact_page_url() {
	$pages = get_posts(
		array(
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'no_found_rows'  => true,
			'meta_key'       => '_wp_page_template',
			'meta_value'     => 'page-templates/contact.php',
		)
	);

	return $pages ? get_permalink( $pages[0] ) : home_url( '/contact' );
}

/**
 * Handle a contact form submission (logged-in and anonymous visitors alike).
 */
function juiced_handle_contact_form() {
	$back = juiced_contact_page_url();

	/**
	 * Redirect back to the contact page with a result flag and stop.
	 *
	 * @param string $flag 'sent' or 'error'.
	 */
	$finish = static function ( $flag ) use ( $back ) {
		wp_safe_redirect( add_query_arg( 'contact', $flag, $back ) . '#message' );
		exit;
	};

	if ( ! isset( $_POST['juiced_contact_nonce'] )
		|| ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['juiced_contact_nonce'] ) ), 'juiced_contact' ) ) {
		$finish( 'error' );
	}

	// Honeypot filled → a bot. Pretend it worked.
	if ( ! empty( $_POST['juiced_website'] ) ) {
		$finish( 'sent' );
	}

	$first   = isset( $_POST['first_name'] ) ? sanitize_text_field( wp_unslash( $_POST['first_name'] ) ) : '';
	$last    = isset( $_POST['last_name'] ) ? sanitize_text_field( wp_unslash( $_POST['last_name'] ) ) : '';
	$email   = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$phone   = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';
	$topic   = isset( $_POST['topic'] ) ? sanitize_text_field( wp_unslash( $_POST['topic'] ) ) : '';
	$message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';

	if ( ! $first || ! is_email( $email ) || ! $message ) {
		$finish( 'error' );
	}

	// Deliver to the ACF-set recipient on the contact page, else the admin email.
	$to = '';
	if ( function_exists( 'get_field' ) ) {
		$page_id = url_to_postid( $back );
		$to      = $page_id ? sanitize_email( (string) get_field( 'contact_form_recipient', $page_id ) ) : '';
	}
	if ( ! is_email( $to ) ) {
		$to = get_option( 'admin_email' );
	}

	$name    = trim( $first . ' ' . $last );
	$subject = sprintf(
		/* translators: 1: topic or fallback label, 2: sender name. */
		__( '[Juiced! Contact] %1$s — %2$s', 'juiced' ),
		$topic ? $topic : __( 'New message', 'juiced' ),
		$name
	);

	$lines = array(
		__( 'Name:', 'juiced' ) . ' ' . $name,
		__( 'Email:', 'juiced' ) . ' ' . $email,
	);
	if ( $phone ) {
		$lines[] = __( 'Phone:', 'juiced' ) . ' ' . $phone;
	}
	if ( $topic ) {
		$lines[] = __( 'Topic:', 'juiced' ) . ' ' . $topic;
	}
	$lines[] = '';
	$lines[] = $message;

	$sent = wp_mail(
		$to,
		$subject,
		implode( "\n", $lines ),
		array( 'Reply-To: ' . $name . ' <' . $email . '>' )
	);

	$finish( $sent ? 'sent' : 'error' );
}
add_action( 'admin_post_juiced_contact', 'juiced_handle_contact_form' );
add_action( 'admin_post_nopriv_juiced_contact', 'juiced_handle_contact_form' );
