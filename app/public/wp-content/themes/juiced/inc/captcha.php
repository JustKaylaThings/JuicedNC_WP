<?php
/**
 * Spam protection — Cloudflare Turnstile on every public form.
 *
 * Guards the Contact, Host an Event and Careers forms and both newsletter
 * sign-ups (footer band + popup), on top of each form's honeypot. Turnstile is
 * free, needs no Google account, and in "interaction-only" mode is invisible
 * to most visitors — a checkbox only appears when Cloudflare is unsure.
 *
 * Keys: Appearance → Customize → Spam Protection (from the Cloudflare
 * dashboard → Turnstile → Add site). Constants in wp-config.php win over the
 * Customizer, which keeps the secret out of the database if preferred:
 *
 *   define( 'JUICED_TURNSTILE_SITE_KEY', '…' );
 *   define( 'JUICED_TURNSTILE_SECRET_KEY', '…' );
 *
 * Until BOTH keys are set the whole thing is off — no widget, no check — so
 * the forms keep working on a fresh install or staging copy.
 *
 * @package Juiced
 */

/**
 * Turnstile site key (public) — constant first, then the Customizer.
 *
 * @return string
 */
function juiced_captcha_site_key() {
	return defined( 'JUICED_TURNSTILE_SITE_KEY' ) ? (string) JUICED_TURNSTILE_SITE_KEY : (string) get_theme_mod( 'juiced_turnstile_site_key', '' );
}

/**
 * Turnstile secret key — constant first, then the Customizer.
 *
 * @return string
 */
function juiced_captcha_secret_key() {
	return defined( 'JUICED_TURNSTILE_SECRET_KEY' ) ? (string) JUICED_TURNSTILE_SECRET_KEY : (string) get_theme_mod( 'juiced_turnstile_secret_key', '' );
}

/**
 * Is spam protection switched on (both keys present)?
 *
 * @return bool
 */
function juiced_captcha_enabled() {
	return '' !== juiced_captcha_site_key() && '' !== juiced_captcha_secret_key();
}

/**
 * Print the Turnstile widget inside a form. It adds a hidden
 * cf-turnstile-response input that the handler checks.
 *
 * @param string $class Extra classes for the wrapper (layout in the host form).
 */
function juiced_captcha_field( $class = '' ) {
	if ( ! juiced_captcha_enabled() ) {
		return;
	}
	printf(
		'<div class="cf-turnstile %1$s" data-sitekey="%2$s" data-appearance="interaction-only" data-theme="light"></div>',
		esc_attr( $class ),
		esc_attr( juiced_captcha_site_key() )
	);
}

/**
 * Check the posted Turnstile token with Cloudflare.
 *
 * Off (no keys) → always passes. A rejected or missing token fails. If
 * Cloudflare itself can't be reached, the submission is let through (and
 * logged) rather than losing a real customer's message — the honeypot still
 * applies.
 *
 * @return bool
 */
function juiced_captcha_verify() {
	if ( ! juiced_captcha_enabled() ) {
		return true;
	}

	// phpcs:ignore WordPress.Security.NonceVerification.Missing -- each handler checks its own nonce.
	$token = isset( $_POST['cf-turnstile-response'] ) ? sanitize_text_field( wp_unslash( $_POST['cf-turnstile-response'] ) ) : '';
	if ( '' === $token ) {
		return false;
	}

	$response = wp_remote_post(
		'https://challenges.cloudflare.com/turnstile/v0/siteverify',
		array(
			'timeout' => 10,
			'body'    => array(
				'secret'   => juiced_captcha_secret_key(),
				'response' => $token,
				'remoteip' => isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '',
			),
		)
	);

	if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
		error_log( 'Juiced! Turnstile: verification request failed — submission allowed.' ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
		return true;
	}

	$result = json_decode( wp_remote_retrieve_body( $response ), true );
	return ! empty( $result['success'] );
}

/**
 * Load Turnstile's script site-wide when switched on — the footer newsletter
 * band puts a form on every page.
 */
function juiced_captcha_assets() {
	if ( ! juiced_captcha_enabled() ) {
		return;
	}
	wp_enqueue_script(
		'cf-turnstile',
		'https://challenges.cloudflare.com/turnstile/v0/api.js',
		array(),
		null, // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion -- Cloudflare serves the current build.
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);
}
add_action( 'wp_enqueue_scripts', 'juiced_captcha_assets' );

/**
 * Customizer: Appearance → Customize → Spam Protection.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager.
 */
function juiced_captcha_customize_register( $wp_customize ) {
	$wp_customize->add_section(
		'juiced_spam_protection',
		array(
			'title'       => __( 'Spam Protection', 'juiced' ),
			'description' => __( 'Cloudflare Turnstile keys for the site\'s forms (Contact, Host an Event, Careers, newsletter). Get them free at dash.cloudflare.com → Turnstile → Add site. Leave either blank to switch protection off.', 'juiced' ),
			'priority'    => 26, // Right below Business Info.
		)
	);

	$keys = array(
		'juiced_turnstile_site_key'   => __( 'Site key', 'juiced' ),
		'juiced_turnstile_secret_key' => __( 'Secret key', 'juiced' ),
	);
	foreach ( $keys as $name => $label ) {
		$wp_customize->add_setting(
			$name,
			array(
				'type'              => 'theme_mod',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'postMessage', // Nothing to preview; skip the refresh.
			)
		);
		$wp_customize->add_control(
			$name,
			array(
				'section' => 'juiced_spam_protection',
				'label'   => $label,
				'type'    => 'text',
			)
		);
	}
}
add_action( 'customize_register', 'juiced_captcha_customize_register' );
