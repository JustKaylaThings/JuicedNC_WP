<?php
/**
 * Email list — stores newsletter sign-ups in WordPress.
 *
 * Both sign-up forms (the popup in template-parts/newsletter-popup.php and the
 * "Join the family" band in footer.php) post to admin-post.php with
 * action=juiced_newsletter. Each address becomes a juiced_subscriber post
 * (title = email, `_juiced_source` = popup|footer), listed under "Email List"
 * in the admin with an Export CSV button, so the list can be imported into an
 * email tool later. No provider account is needed at launch.
 *
 * The popup submits with fetch() and gets JSON back; the band is a plain form
 * that redirects back with ?joined=yes|error.
 *
 * Spam defence is a honeypot only. There is deliberately no nonce: the forms
 * sit on every page, and a page cache would serve stale nonces that make real
 * sign-ups fail. A bot that skips the honeypot can at worst add an address.
 *
 * @package Juiced
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the private subscriber post type. Admins can see and delete
 * entries but not add them by hand (sign-ups only come from the forms).
 */
function juiced_register_subscriber_type() {
	register_post_type(
		'juiced_subscriber',
		array(
			'labels'              => array(
				'name'          => __( 'Email List', 'juiced' ),
				'singular_name' => __( 'Subscriber', 'juiced' ),
				'menu_name'     => __( 'Email List', 'juiced' ),
				'all_items'     => __( 'Email List', 'juiced' ),
				'search_items'  => __( 'Search emails', 'juiced' ),
				'not_found'     => __( 'No sign-ups yet.', 'juiced' ),
			),
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_rest'        => false,
			'exclude_from_search' => true,
			'menu_position'       => 26,
			'menu_icon'           => 'dashicons-email-alt',
			'supports'            => array( 'title' ),
			'capability_type'     => 'post',
			'capabilities'        => array( 'create_posts' => 'do_not_allow' ),
			'map_meta_cap'        => true,
		)
	);
}
add_action( 'init', 'juiced_register_subscriber_type' );

/**
 * Save one sign-up. An address already on the list counts as success.
 *
 * @param string $email  Raw email from the form.
 * @param string $source 'popup' or 'footer'.
 * @return bool False when the email is invalid or the insert failed.
 */
function juiced_add_subscriber( $email, $source ) {
	$email = strtolower( sanitize_email( $email ) );
	if ( ! is_email( $email ) ) {
		return false;
	}

	$existing = get_posts(
		array(
			'post_type'      => 'juiced_subscriber',
			'post_status'    => 'any',
			'title'          => $email,
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'no_found_rows'  => true,
		)
	);
	if ( $existing ) {
		return true;
	}

	$id = wp_insert_post(
		array(
			'post_type'   => 'juiced_subscriber',
			'post_status' => 'publish',
			'post_title'  => $email,
			'meta_input'  => array( '_juiced_source' => in_array( $source, array( 'popup', 'footer' ), true ) ? $source : 'footer' ),
		)
	);

	return $id && ! is_wp_error( $id );
}

/**
 * Handle a sign-up from either form (logged-in and anonymous visitors alike).
 */
function juiced_handle_newsletter_signup() {
	// phpcs:disable WordPress.Security.NonceVerification.Missing -- no nonce by design, see file header.
	$ajax   = ! empty( $_POST['ajax'] );
	$source = isset( $_POST['source'] ) ? sanitize_key( wp_unslash( $_POST['source'] ) ) : 'footer';
	$email  = isset( $_POST['email'] ) ? wp_unslash( $_POST['email'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized in juiced_add_subscriber().

	// Honeypot filled → a bot. Pretend it worked. Otherwise the Turnstile
	// check (inc/captcha.php) has to pass before the address is saved.
	$ok = ! empty( $_POST['juiced_website'] ) || ( juiced_captcha_verify() && juiced_add_subscriber( $email, $source ) );
	// phpcs:enable

	if ( $ajax ) {
		if ( $ok ) {
			wp_send_json_success();
		}
		wp_send_json_error( null, 400 );
	}

	$back = wp_get_referer();
	$back = $back ? $back : home_url( '/' );
	wp_safe_redirect( add_query_arg( 'joined', $ok ? 'yes' : 'error', $back ) . '#juiced-join' );
	exit;
}
add_action( 'admin_post_juiced_newsletter', 'juiced_handle_newsletter_signup' );
add_action( 'admin_post_nopriv_juiced_newsletter', 'juiced_handle_newsletter_signup' );

/**
 * Admin list columns: Email, Source, Signed up.
 *
 * @param array $columns Default columns.
 * @return array
 */
function juiced_subscriber_columns( $columns ) {
	return array(
		'cb'            => $columns['cb'],
		'title'         => __( 'Email', 'juiced' ),
		'juiced_source' => __( 'Source', 'juiced' ),
		'date'          => __( 'Signed up', 'juiced' ),
	);
}
add_filter( 'manage_juiced_subscriber_posts_columns', 'juiced_subscriber_columns' );

/**
 * @param string $column  Column key.
 * @param int    $post_id Subscriber ID.
 */
function juiced_subscriber_column_value( $column, $post_id ) {
	if ( 'juiced_source' === $column ) {
		echo esc_html( 'popup' === get_post_meta( $post_id, '_juiced_source', true ) ? __( 'Popup', 'juiced' ) : __( 'Footer form', 'juiced' ) );
	}
}
add_action( 'manage_juiced_subscriber_posts_custom_column', 'juiced_subscriber_column_value', 10, 2 );

/**
 * Entries aren't editable — drop the Edit and Quick Edit row links.
 *
 * @param array   $actions Row actions.
 * @param WP_Post $post    Row post.
 * @return array
 */
function juiced_subscriber_row_actions( $actions, $post ) {
	if ( 'juiced_subscriber' === $post->post_type ) {
		unset( $actions['edit'], $actions['inline hide-if-no-js'] );
	}
	return $actions;
}
add_filter( 'post_row_actions', 'juiced_subscriber_row_actions', 10, 2 );

/**
 * "Export CSV" button above the list.
 *
 * @param array $views Status links.
 * @return array
 */
function juiced_subscriber_export_link( $views ) {
	if ( current_user_can( 'manage_options' ) ) {
		$url             = wp_nonce_url( admin_url( 'admin-post.php?action=juiced_subscribers_csv' ), 'juiced_subscribers_csv' );
		$views['export'] = '<a href="' . esc_url( $url ) . '" class="button" style="margin-left:8px">' . esc_html__( 'Export CSV', 'juiced' ) . '</a>';
	}
	return $views;
}
add_filter( 'views_edit-juiced_subscriber', 'juiced_subscriber_export_link' );

/**
 * Stream every subscriber as CSV (email, source, signed_up).
 */
function juiced_export_subscribers_csv() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Sorry, you are not allowed to export the email list.', 'juiced' ), 403 );
	}
	check_admin_referer( 'juiced_subscribers_csv' );

	$ids = get_posts(
		array(
			'post_type'      => 'juiced_subscriber',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'date',
			'order'          => 'ASC',
			'fields'         => 'ids',
		)
	);

	nocache_headers();
	header( 'Content-Type: text/csv; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename=juiced-email-list-' . gmdate( 'Y-m-d' ) . '.csv' );

	$out = fopen( 'php://output', 'w' );
	fputcsv( $out, array( 'email', 'source', 'signed_up' ) );
	foreach ( $ids as $id ) {
		$email = get_post_field( 'post_title', $id, 'raw' );
		// Spreadsheet apps run cells starting with these as formulas.
		if ( preg_match( '/^[=+\-@]/', $email ) ) {
			$email = "'" . $email;
		}
		fputcsv( $out, array( $email, get_post_meta( $id, '_juiced_source', true ), get_the_date( 'Y-m-d H:i', $id ) ) );
	}
	fclose( $out ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
	exit;
}
add_action( 'admin_post_juiced_subscribers_csv', 'juiced_export_subscribers_csv' );
