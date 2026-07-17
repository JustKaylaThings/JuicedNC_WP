<?php
/**
 * Customizer — site-wide Business Info.
 *
 * The business details (location names, addresses, phones, hours, parking,
 * directions links, Google Place IDs, business email) are entered once in
 * Appearance → Customize → Business Info — right below Site Identity, where the
 * logo lives — and read everywhere via juiced_business_field() (inc/helpers.php):
 * the homepage locations cards and testimonials, the Locations page, the Contact
 * page and the footer. The Customizer's live preview shows edits on whichever
 * page is open before publishing.
 *
 * Values are stored as theme mods. These fields used to be ACF fields on the
 * Home page; juiced_business_field() still falls back to those for any value
 * that hasn't been saved here yet.
 *
 * @package Juiced
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the Business Info section and its settings/controls.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager.
 */
function juiced_customize_register( $wp_customize ) {
	$wp_customize->add_section(
		'juiced_business_info',
		array(
			'title'       => __( 'Business Info', 'juiced' ),
			'description' => __( 'Site-wide business details, entered once — shown on the homepage, the Locations and Contact pages, and the footer.', 'juiced' ),
			'priority'    => 25, // Right below Site Identity (20).
		)
	);

	// One field definition per control; the two locations share the same set,
	// added in a loop below. 'sanitize' is the sanitize_callback.
	$business_fields = array(
		array(
			'name'        => 'business_email',
			'label'       => __( 'Business email', 'juiced' ),
			'description' => __( 'Shown on the Contact page\'s "Let\'s Connect" list.', 'juiced' ),
			'type'        => 'email',
			'sanitize'    => 'sanitize_email',
		),
	);

	foreach ( range( 1, 2 ) as $i ) {
		/* translators: %d: location number. */
		$loc = sprintf( __( 'Location %d', 'juiced' ), $i );

		$business_fields[] = array(
			'name'     => "loc{$i}_name",
			'label'    => "{$loc} — " . __( 'name', 'juiced' ),
			'type'     => 'text',
			'sanitize' => 'sanitize_text_field',
		);
		$business_fields[] = array(
			'name'        => "loc{$i}_address",
			'label'       => "{$loc} — " . __( 'address', 'juiced' ),
			'description' => __( 'Street on one line, city/state/zip on the next.', 'juiced' ),
			'type'        => 'textarea',
			'sanitize'    => 'sanitize_textarea_field',
		);
		$business_fields[] = array(
			'name'     => "loc{$i}_phone",
			'label'    => "{$loc} — " . __( 'phone', 'juiced' ),
			'type'     => 'text',
			'sanitize' => 'sanitize_text_field',
		);
		$business_fields[] = array(
			'name'        => "loc{$i}_hours",
			'label'       => "{$loc} — " . __( 'hours', 'juiced' ),
			'description' => __( 'One line per range, e.g. "Mon – Sat: 7:00 AM – 7:00 PM".', 'juiced' ),
			'type'        => 'textarea',
			'sanitize'    => 'sanitize_textarea_field',
		);
		$business_fields[] = array(
			'name'        => "loc{$i}_parking",
			'label'       => "{$loc} — " . __( 'parking', 'juiced' ),
			'description' => __( 'e.g. "Free parking available". Shown on the Locations page.', 'juiced' ),
			'type'        => 'text',
			'sanitize'    => 'sanitize_text_field',
		);
		$business_fields[] = array(
			'name'        => "loc{$i}_directions",
			'label'       => "{$loc} — " . __( 'directions URL', 'juiced' ),
			'description' => __( 'Google Maps (or similar) link for the "Get Directions" buttons. Left blank, they fall back to a Google Maps search for the address.', 'juiced' ),
			'type'        => 'url',
			'sanitize'    => 'esc_url_raw',
		);
		$business_fields[] = array(
			'name'        => "loc{$i}_place_id",
			'label'       => "{$loc} — " . __( 'Google Place ID', 'juiced' ),
			'description' => __( 'Used to pull this shop\'s live Google reviews into the homepage testimonials.', 'juiced' ),
			'type'        => 'text',
			'sanitize'    => 'sanitize_text_field',
		);
	}

	foreach ( $business_fields as $field ) {
		$wp_customize->add_setting(
			$field['name'],
			array(
				'type'              => 'theme_mod',
				'sanitize_callback' => $field['sanitize'],
			)
		);
		$wp_customize->add_control(
			$field['name'],
			array(
				'section'     => 'juiced_business_info',
				'label'       => $field['label'],
				'description' => isset( $field['description'] ) ? $field['description'] : '',
				'type'        => $field['type'],
			)
		);
	}
}
add_action( 'customize_register', 'juiced_customize_register' );
