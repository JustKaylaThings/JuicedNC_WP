<?php
/**
 * Customizer — site-wide Business Info.
 *
 * The business details (location names, addresses, phones, hours, parking,
 * directions links, ordering links, business email) are entered once in
 * Appearance → Customize → Business Info — right below Site Identity, where the
 * logo lives — and read everywhere via juiced_business_field() (inc/helpers.php):
 * the homepage locations cards, the Locations page, the Contact
 * page and the footer. The Customizer's live preview shows edits on whichever
 * page is open before publishing.
 *
 * Values are stored as theme mods. These fields used to be ACF fields on the
 * Home page; juiced_business_field() still falls back to those for any value
 * that hasn't been saved here yet.
 *
 * A second section, Google Reviews & Maps, holds the homepage reviews strip
 * settings, each shop's Google Place ID (see inc/google-reviews.php) and the
 * Locations page's live maps (juiced_location_map_url() in inc/helpers.php).
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
			'section'     => 'juiced_google_reviews',
			'label'       => "{$loc} — " . __( 'Google Place ID', 'juiced' ),
			'description' => __( 'Which Google listing this shop\'s reviews come from. Leave blank to use the real Juiced! listing already built in; only change it if the shop moves or gets a new listing.', 'juiced' ) . juiced_review_status_note( $i ),
			'type'        => 'text',
			'priority'    => 30,
			'sanitize'    => 'sanitize_text_field',
		);
		// Ordering links: the "Order Ahead" picker (template-parts/order-picker.php)
		// lists each app this shop has a link for; a blank one is left out.
		foreach ( juiced_order_apps() as $app => $meta ) {
			$business_fields[] = array(
				'name'        => "loc{$i}_{$app}",
				/* translators: %s: delivery app name, e.g. DoorDash. */
				'label'       => "{$loc} — " . sprintf( __( '%s link', 'juiced' ), $meta['label'] ),
				'description' => 'doordash' === $app ? __( 'Store links for the "Order Ahead" buttons. Leave an app blank if this shop isn\'t on it.', 'juiced' ) : '',
				'type'        => 'url',
				'sanitize'    => 'esc_url_raw',
			);
		}
	}

	// Google Reviews: the homepage reviews strip (inc/google-reviews.php). The
	// two Place IDs above are added to this section too.
	$wp_customize->add_section(
		'juiced_google_reviews',
		array(
			'title'       => __( 'Google Reviews & Maps', 'juiced' ),
			'description' => __( 'Live reviews from each shop\'s Google listing, shown in the strip at the bottom of the homepage\'s "More Than a Juice Bar" section, and the live maps on the Locations page. Reviews refresh once a day; saving here refreshes them right away.', 'juiced' ),
			'priority'    => 26, // Right below Business Info.
		)
	);

	$business_fields[] = array(
		'name'        => 'reviews_show',
		'section'     => 'juiced_google_reviews',
		'label'       => __( 'Show Google reviews on the homepage', 'juiced' ),
		'type'        => 'checkbox',
		'default'     => true,
		'priority'    => 10,
		'sanitize'    => 'wp_validate_boolean',
	);
	$business_fields[] = array(
		'name'        => 'google_places_api_key',
		'section'     => 'juiced_google_reviews',
		'label'       => __( 'Google Places API key', 'juiced' ),
		'description' => defined( 'JUICED_GOOGLE_PLACES_API_KEY' )
			? __( 'A key is already set in wp-config.php, and that one is used instead of this field.', 'juiced' )
			: __( 'Needed to load the reviews. In Google Cloud Console: create a project, turn on billing, enable "Places API (New)", then Credentials → Create credentials → API key. Restrict the key to the Places API (New). Google\'s monthly free credit covers a site this size, since reviews are only fetched once a day.', 'juiced' ),
		'type'        => 'password',
		'priority'    => 20,
		'sanitize'    => 'sanitize_text_field',
	);
	$business_fields[] = array(
		'name'        => 'reviews_min_rating',
		'section'     => 'juiced_google_reviews',
		'label'       => __( 'Lowest star rating to show', 'juiced' ),
		'description' => __( 'Reviews below this are left out of the strip. Reviews with no written text are always left out.', 'juiced' ),
		'type'        => 'select',
		'choices'     => array(
			'5' => __( '5 stars only', 'juiced' ),
			'4' => __( '4 stars and up', 'juiced' ),
			'3' => __( '3 stars and up', 'juiced' ),
			'1' => __( 'All reviews', 'juiced' ),
		),
		'default'     => '4',
		'priority'    => 40,
		'sanitize'    => 'absint',
	);
	$business_fields[] = array(
		'name'        => 'reviews_count',
		'section'     => 'juiced_google_reviews',
		'label'       => __( 'Most reviews to show', 'juiced' ),
		'description' => __( 'Google shares up to 5 recent reviews per shop, so 10 is the most there can be across both shops.', 'juiced' ),
		'type'        => 'number',
		'input_attrs' => array( 'min' => 1, 'max' => 10 ),
		'default'     => 8,
		'priority'    => 50,
		'sanitize'    => 'absint',
	);

	$business_fields[] = array(
		'name'        => 'locations_live_map',
		'section'     => 'juiced_google_reviews',
		'label'       => __( 'Show live maps on the Locations page', 'juiced' ),
		'description' => __( 'A Google map of each shop that visitors can zoom and tap for directions. Turned off, the map images picked on the Locations page show instead.', 'juiced' ),
		'type'        => 'checkbox',
		'default'     => true,
		'priority'    => 60,
		'sanitize'    => 'wp_validate_boolean',
	);
	$business_fields[] = array(
		'name'        => 'google_maps_embed_key',
		'section'     => 'juiced_google_reviews',
		'label'       => __( 'Maps Embed API key (optional)', 'juiced' ),
		'description' => __( 'The maps work without a key. With one, each map pins the exact Google listing (the Place IDs above) instead of searching the address. Use a separate key from the reviews one: enable "Maps Embed API" (free), and restrict the key to websites: your domain/*. This key is visible in the page, which is normal for map keys.', 'juiced' ),
		'type'        => 'text',
		'priority'    => 70,
		'sanitize'    => 'sanitize_text_field',
	);

	foreach ( $business_fields as $field ) {
		$wp_customize->add_setting(
			$field['name'],
			array(
				'type'              => 'theme_mod',
				'default'           => isset( $field['default'] ) ? $field['default'] : '',
				'sanitize_callback' => $field['sanitize'],
			)
		);
		$wp_customize->add_control(
			$field['name'],
			array(
				'section'     => isset( $field['section'] ) ? $field['section'] : 'juiced_business_info',
				'choices'     => isset( $field['choices'] ) ? $field['choices'] : array(),
				'input_attrs' => isset( $field['input_attrs'] ) ? $field['input_attrs'] : array(),
				'priority'    => isset( $field['priority'] ) ? $field['priority'] : 10,
				'label'       => $field['label'],
				'description' => isset( $field['description'] ) ? $field['description'] : '',
				'type'        => $field['type'],
			)
		);
	}
}
add_action( 'customize_register', 'juiced_customize_register' );
