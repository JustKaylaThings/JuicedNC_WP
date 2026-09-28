<?php
/**
 * Shared template helpers for the Juiced! theme.
 *
 * Homepage copy is stored as ACF fields on the static front page (see
 * inc/acf-homepage.php). juiced_field() reads those fields with a fallback so
 * every section renders correctly even before the client fills anything in.
 *
 * @package Juiced
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ID of the page set as the static front page (Settings → Reading).
 *
 * @return int
 */
function juiced_front_id() {
	return (int) get_option( 'page_on_front' );
}

/**
 * Read an ACF field from the front page, with a fallback default.
 *
 * Used for singular homepage/site copy (hero text, order-ahead URL, etc.) so it
 * is editable in one place — the Home page — and readable from any template
 * (e.g. the header, which renders site-wide).
 *
 * @param string $name    ACF field name.
 * @param mixed  $default Value to use when the field is empty or ACF is absent.
 * @return mixed
 */
function juiced_field( $name, $default = '' ) {
	if ( function_exists( 'get_field' ) ) {
		$value = get_field( $name, juiced_front_id() );
		if ( null !== $value && '' !== $value && false !== $value && array() !== $value ) {
			return $value;
		}
	}
	return $default;
}

/**
 * Read a site-wide business detail (location names, addresses, phones, hours,
 * parking, directions links, Place IDs, business email), with a fallback.
 *
 * Managed in Appearance → Customize → Business Info (inc/customizer.php) and
 * stored as theme mods. These used to be ACF fields on the Home page, so a
 * setting that has *never* been saved in the Customizer (mod === false) falls
 * back to that old location — un-migrated environments keep rendering what was
 * entered. A saved-but-empty setting means the client cleared it on purpose,
 * so it gets the default without resurrecting the old ACF value.
 *
 * @param string $name    Setting name (matches the old ACF field name).
 * @param mixed  $default Value to use when the setting is empty.
 * @return mixed
 */
function juiced_business_field( $name, $default = '' ) {
	$value = get_theme_mod( $name );
	if ( false === $value ) {
		return juiced_field( $name, $default );
	}
	return '' !== $value ? $value : $default;
}

/**
 * Read an ACF field from the page currently being viewed, with a fallback.
 *
 * The sibling of juiced_field(): that one always reads the front page (site-wide
 * copy), this one reads whatever page is being rendered. Used by the Events page
 * template-parts, whose fields hang on the Events page itself.
 *
 * @param string $name    ACF field name.
 * @param mixed  $default Value to use when the field is empty or ACF is absent.
 * @return mixed
 */
function juiced_page_field( $name, $default = '' ) {
	if ( function_exists( 'get_field' ) ) {
		$value = get_field( $name, get_queried_object_id() );
		if ( null !== $value && '' !== $value && false !== $value && array() !== $value ) {
			return $value;
		}
	}
	return $default;
}

/**
 * URL of the page using the Events Page template.
 *
 * The Event CPT has no archive (see the juiced-cpt plugin), so "all events"
 * links resolve to that page instead. Falls back to /events — the slug the page
 * is expected to use — so links still point somewhere sane before it exists.
 *
 * @return string
 */
function juiced_events_page_url() {
	$pages = get_posts(
		array(
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'no_found_rows'  => true,
			'meta_key'       => '_wp_page_template',
			'meta_value'     => 'page-templates/events.php',
		)
	);

	return $pages ? get_permalink( $pages[0] ) : home_url( '/events' );
}

/**
 * URL of the page using the Herb Library Page template.
 *
 * The Herb CPT has no archive (see the juiced-cpt plugin), so "herb library"
 * links resolve to that page instead. Falls back to /herbs — the slug the page
 * is expected to use — so links still point somewhere sane before it exists.
 *
 * @return string
 */
function juiced_herbs_page_url() {
	$pages = get_posts(
		array(
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'no_found_rows'  => true,
			'meta_key'       => '_wp_page_template',
			'meta_value'     => 'page-templates/herbs.php',
		)
	);

	return $pages ? get_permalink( $pages[0] ) : home_url( '/herbs' );
}

/**
 * Normalize an ACF image field (array | id | url) to a usable URL.
 *
 * @param mixed  $image ACF image field value.
 * @param string $size  Image size for array/id values.
 * @return string URL or empty string.
 */
function juiced_image_url( $image, $size = 'large' ) {
	if ( empty( $image ) ) {
		return '';
	}
	if ( is_array( $image ) ) {
		return isset( $image['sizes'][ $size ] ) ? $image['sizes'][ $size ] : ( $image['url'] ?? '' );
	}
	if ( is_numeric( $image ) ) {
		$src = wp_get_attachment_image_src( (int) $image, $size );
		return $src ? $src[0] : '';
	}
	return (string) $image; // Already a URL.
}

/**
 * Inline SVG for a hero benefit chip. Icons are fixed; labels are ACF-editable.
 *
 * @param int $index 0 = leaf, 1 = heart, 2 = community.
 * @return string SVG markup.
 */
function juiced_benefit_icon( $index ) {
	$icons = array(
		// Leaf (fresh ingredients) — brand green.
		'<svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="#5cc070" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"/><path d="M2 21c0-3 1.85-5.36 5.08-6"/></svg>',
		// Heart (healthy options) — magenta accent.
		'<svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="#e0559a" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>',
		// People (community focused) — brand gold.
		'<svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="#f2811f" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
	);
	return isset( $icons[ $index ] ) ? $icons[ $index ] : '';
}

/**
 * Accent color + inline icon for a menu section tab (Section 4).
 *
 * Keyed by the menu_section term slug so the client can add sections and still
 * get a sensible colored icon; anything unrecognized falls back to a generic cup
 * in brand green. Colors mirror the approved mockup (smoothies=orange, etc.) and
 * are returned as literal Tailwind classes so the @source scan compiles them.
 *
 * @param string $slug menu_section term slug.
 * @return array{color:string,icon:string} Tailwind text-color class + SVG markup.
 */
function juiced_menu_section_style( $slug ) {
	$cup   = '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 8h12l-1 12a2 2 0 0 1-2 2H9a2 2 0 0 1-2-2L6 8Z"/><path d="M9 8V5a3 3 0 0 1 6 0v3"/></svg>';
	$bottle = '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M10 2h4v3l1.5 2.5A4 4 0 0 1 16 9.6V20a2 2 0 0 1-2 2h-4a2 2 0 0 1-2-2V9.6a4 4 0 0 1 .5-2.1L10 5V2Z"/><path d="M8 12h8"/></svg>';
	$bowl  = '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 11h18a9 9 0 0 1-18 0Z"/><path d="M8 11a4 4 0 0 1 8 0"/><path d="M12 3v4"/></svg>';
	$shot  = '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M8 3h8l-1 4H9L8 3Z"/><path d="M9 7l.7 12a2 2 0 0 0 2 1.9h.6a2 2 0 0 0 2-1.9L15 7"/><path d="M9.4 13h5.2"/></svg>';
	$leaf  = '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"/><path d="M2 21c0-3 1.85-5.36 5.08-6"/></svg>';

	$map = array(
		'smoothies'      => array( 'color' => 'text-brand-gold', 'icon' => $cup ),
		'juices'         => array( 'color' => 'text-brand-green', 'icon' => $bottle ),
		'bowls'          => array( 'color' => 'text-[#e0559a]', 'icon' => $bowl ),
		'wellness-shots' => array( 'color' => 'text-brand-gold', 'icon' => $shot ),
		'wellness'       => array( 'color' => 'text-brand-gold', 'icon' => $shot ),
		'extras'         => array( 'color' => 'text-[#3aa0e0]', 'icon' => $leaf ),
	);

	return isset( $map[ $slug ] ) ? $map[ $slug ] : array( 'color' => 'text-brand-green', 'icon' => $cup );
}

/**
 * Accent color + inline icon for an event category pill (Events page Section 2).
 *
 * Sibling of juiced_menu_section_style(). The pills are driven by whatever
 * event_category terms exist, so this maps the slugs we know — the mockup's five
 * plus the terms currently in use — and falls back for anything the client adds
 * later. The fallback picks a palette color from a hash of the slug rather than
 * a fixed default, so a page of unmapped terms still looks varied instead of
 * uniformly green, and each term keeps the same color between page loads.
 *
 * Classes are returned as literal strings so Tailwind's @source scan compiles
 * the arbitrary hex values.
 *
 * @param string $slug event_category term slug.
 * @return array{text:string,bg:string,soft:string,icon:string} Tailwind classes + SVG.
 */
function juiced_event_category_style( $slug ) {
	// Shared palette — the same accents used by the homepage events strip.
	// 'soft' is the 10% tint, for pills that carry the color without shouting.
	$palette = array(
		array( 'text' => 'text-brand-green', 'bg' => 'bg-brand-green', 'soft' => 'bg-brand-green/10' ), // 0 green
		array( 'text' => 'text-[#6c4fd6]', 'bg' => 'bg-[#6c4fd6]', 'soft' => 'bg-[#6c4fd6]/10' ),       // 1 purple
		array( 'text' => 'text-brand-gold', 'bg' => 'bg-brand-gold', 'soft' => 'bg-brand-gold/10' ),    // 2 gold
		array( 'text' => 'text-[#e0559a]', 'bg' => 'bg-[#e0559a]', 'soft' => 'bg-[#e0559a]/10' ),       // 3 pink
		array( 'text' => 'text-[#3aa0e0]', 'bg' => 'bg-[#3aa0e0]', 'soft' => 'bg-[#3aa0e0]/10' ),       // 4 blue
	);

	$svg = static function ( $paths ) {
		return '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $paths . '</svg>';
	};

	$icons = array(
		'leaf'     => $svg( '<path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"/><path d="M2 21c0-3 1.85-5.36 5.08-6"/>' ),
		'music'    => $svg( '<circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/><path d="M9 18V5l12-2v13"/>' ),
		'dice'     => $svg( '<rect x="3" y="3" width="18" height="18" rx="3"/><circle cx="8.5" cy="8.5" r="1.1" fill="currentColor" stroke="none"/><circle cx="15.5" cy="15.5" r="1.1" fill="currentColor" stroke="none"/><circle cx="15.5" cy="8.5" r="1.1" fill="currentColor" stroke="none"/><circle cx="8.5" cy="15.5" r="1.1" fill="currentColor" stroke="none"/>' ),
		'heart'    => $svg( '<path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/>' ),
		'tools'    => $svg( '<path d="M14.7 6.3a4 4 0 0 0 5 5l-9.4 9.4a2.1 2.1 0 0 1-3-3l9.4-9.4a4 4 0 0 0-2-2Z"/><path d="M5 5l3 3"/>' ),
		'mic'      => $svg( '<rect x="9" y="2" width="6" height="11" rx="3"/><path d="M5 11a7 7 0 0 0 14 0"/><path d="M12 18v4"/>' ),
		'sparkle'  => $svg( '<path d="M12 3l1.9 5.1L19 10l-5.1 1.9L12 17l-1.9-5.1L5 10l5.1-1.9Z"/><path d="M19 16l.8 2.2L22 19l-2.2.8L19 22l-.8-2.2L16 19l2.2-.8Z"/>' ),
		'cup'      => $svg( '<path d="M6 8h12l-1 12a2 2 0 0 1-2 2H9a2 2 0 0 1-2-2L6 8Z"/><path d="M9 8V5a3 3 0 0 1 6 0v3"/>' ),
		'calendar' => $svg( '<rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>' ),
	);

	// slug => [palette index, icon key]. The first five are the mockup's
	// categories; the rest are the terms the client is currently using.
	$map = array(
		'wellness'          => array( 0, 'leaf' ),
		'music-arts'        => array( 1, 'music' ),
		'games-fun'         => array( 2, 'dice' ),
		'community'         => array( 3, 'heart' ),
		'workshops'         => array( 4, 'tools' ),
		'music'             => array( 1, 'music' ),
		'anime'             => array( 1, 'sparkle' ),
		'entertainment'     => array( 2, 'mic' ),
		'festival'          => array( 3, 'sparkle' ),
		'smoothie-specials' => array( 0, 'cup' ),
	);

	if ( isset( $map[ $slug ] ) ) {
		list( $index, $icon ) = $map[ $slug ];
	} else {
		$index = crc32( (string) $slug ) % count( $palette );
		$icon  = 'calendar';
	}

	return array(
		'text' => $palette[ $index ]['text'],
		'bg'   => $palette[ $index ]['bg'],
		'soft' => $palette[ $index ]['soft'],
		'icon' => $icons[ $icon ],
	);
}

/**
 * event_category terms that have at least one published event, for the filter
 * pills. Empty terms are hidden — a pill that always yields nothing is noise.
 *
 * @return WP_Term[] Terms, or empty array on failure.
 */
function juiced_event_categories() {
	$terms = get_terms(
		array(
			'taxonomy'   => 'event_category',
			'hide_empty' => true,
			'orderby'    => 'name',
			'order'      => 'ASC',
		)
	);

	return ( is_array( $terms ) && ! is_wp_error( $terms ) ) ? $terms : array();
}

/**
 * Everything an event card renders, resolved once.
 *
 * The Events page draws each event twice — as a poster in grid view and as a
 * row in list view (template-parts/events/card-poster.php and card-row.php) —
 * and the two must agree on every value. Reading the fields here rather than in
 * each partial means a field can't be formatted one way in one view and another
 * way in the other, and adding a field is one edit rather than three.
 *
 * The accent rotates by position through the same purple/green/pink palette as
 * the homepage strip (template-parts/home/events.php), so the two event
 * surfaces read as one system. Class names are literal strings so Tailwind's
 * @source scan picks up the arbitrary hex values.
 *
 * @param array $row   A juiced_upcoming_events() row: { post, when, recurring }.
 * @param int   $index Position in the listing; picks the accent color.
 * @return array Card fields, or empty array if $row is unusable.
 */
function juiced_event_card_data( $row, $index = 0 ) {
	if ( empty( $row['post'] ) || empty( $row['when'] ) ) {
		return array();
	}

	$palette = array(
		array(
			'bg'     => 'bg-[#6c4fd6]',
			'text'   => 'text-[#6c4fd6]',
			'soft'   => 'bg-[#6c4fd6]/10',
			'border' => 'border-[#6c4fd6]',
			'hover'  => 'hover:bg-[#6c4fd6]',
		), // purple
		array(
			'bg'     => 'bg-brand-green',
			'text'   => 'text-brand-green',
			'soft'   => 'bg-brand-green/10',
			'border' => 'border-brand-green',
			'hover'  => 'hover:bg-brand-green',
		), // green
		array(
			'bg'     => 'bg-[#e0559a]',
			'text'   => 'text-[#e0559a]',
			'soft'   => 'bg-[#e0559a]/10',
			'border' => 'border-[#e0559a]',
			'hover'  => 'hover:bg-[#e0559a]',
		), // pink
	);

	$id   = $row['post']->ID;
	$when = $row['when'];

	$terms = get_the_terms( $id, 'event_category' );
	$terms = ( $terms && ! is_wp_error( $terms ) ) ? $terms : array();

	// "Free" is the honest default: events here are free unless the client says
	// otherwise, and a card with no price at all reads as an oversight.
	$cost = get_field( 'cost', $id );

	return array(
		'id'        => $id,
		'color'     => $palette[ (int) $index % count( $palette ) ],
		'title'     => get_the_title( $id ),
		'permalink' => get_permalink( $id ),
		'weekday'   => strtoupper( $when->format( 'D' ) ), // TUE
		'day'       => $when->format( 'd' ),               // 15
		'month'     => strtoupper( $when->format( 'M' ) ), // JUL
		'start'     => juiced_format_event_time( get_field( 'start_time', $id ) ),
		'end'       => juiced_format_event_time( get_field( 'end_time', $id ) ),
		'excerpt'   => wp_strip_all_tags( get_the_excerpt( $id ) ),
		'image'     => get_the_post_thumbnail_url( $id, 'juiced_card' ),
		'audience'  => get_field( 'audience', $id ),
		'cost'      => $cost ? $cost : __( 'Free', 'juiced' ),
		'term'      => $terms ? $terms[0] : null,
	);
}

/**
 * Preferred display order for menu sections, matching the mockup. Terms whose
 * slug isn't listed sort after these, alphabetically by name.
 *
 * @param WP_Term[] $terms Menu section terms.
 * @return WP_Term[] Reordered terms.
 */
function juiced_sort_menu_sections( $terms ) {
	$order = array( 'smoothies', 'juices', 'bowls', 'wellness-shots', 'wellness', 'extras' );
	usort(
		$terms,
		static function ( $a, $b ) use ( $order ) {
			$ia = array_search( $a->slug, $order, true );
			$ib = array_search( $b->slug, $order, true );
			$ia = ( false === $ia ) ? PHP_INT_MAX : $ia;
			$ib = ( false === $ib ) ? PHP_INT_MAX : $ib;
			if ( $ia === $ib ) {
				return strcasecmp( $a->name, $b->name );
			}
			return $ia <=> $ib;
		}
	);
	return $terms;
}

/**
 * Format a menu item price for display. A bare number gets "$" + 2 decimals;
 * anything non-numeric (ranges, "MP") is shown as-entered. Empty stays empty.
 *
 * @param string $price Raw ACF price value.
 * @return string
 */
function juiced_format_menu_price( $price ) {
	$price = trim( (string) $price );
	if ( '' === $price ) {
		return '';
	}
	return is_numeric( $price ) ? '$' . number_format( (float) $price, 2 ) : $price;
}

/**
 * Extract the "@handle" from an Instagram profile URL. Returns '' if the URL has
 * no usable username segment. E.g. "https://instagram.com/juicednc/" → "juicednc".
 *
 * @param string $url Instagram profile URL.
 * @return string Bare handle without the leading "@", or ''.
 */
function juiced_instagram_handle( $url ) {
	$path = trim( (string) wp_parse_url( (string) $url, PHP_URL_PATH ), '/' );
	if ( '' === $path ) {
		return '';
	}
	$segment = explode( '/', $path )[0];
	return ltrim( sanitize_text_field( $segment ), '@' );
}

/**
 * Inline map-pin SVG in a given color, for the locations section.
 *
 * @param string $color Any CSS color (hex). Defaults to brand green.
 * @return string SVG markup.
 */
function juiced_map_pin( $color = '#38a54c' ) {
	$color = esc_attr( $color );
	return '<svg viewBox="0 0 24 24" width="40" height="40" fill="' . $color . '" aria-hidden="true"><path d="M12 2a7 7 0 0 0-7 7c0 4.5 7 13 7 13s7-8.5 7-13a7 7 0 0 0-7-7Z"/><circle cx="12" cy="9" r="2.6" fill="#fff"/></svg>';
}

/**
 * Render a 0–5 star rating as inline gold SVGs (filled up to the rounded rating,
 * faint for the rest). Used by the testimonials section on the dark background.
 *
 * @param float|int $rating Star rating (rounded to a whole star).
 * @param int       $size   Pixel size per star.
 * @return string SVG markup.
 */
function juiced_stars( $rating, $size = 18 ) {
	$filled = max( 0, min( 5, (int) round( (float) $rating ) ) );
	$star   = 'M12 2.2l2.9 6 6.6.6-5 4.4 1.5 6.4L12 16.9 5.9 19.6l1.5-6.4-5-4.4 6.6-.6z';
	$out     = '<span class="inline-flex items-center gap-0.5" role="img" aria-label="' . esc_attr( sprintf( '%d out of 5 stars', $filled ) ) . '">';
	for ( $i = 1; $i <= 5; $i++ ) {
		$color = $i <= $filled ? '#f5b301' : 'rgba(255,255,255,0.22)';
		$out  .= '<svg viewBox="0 0 24 24" width="' . (int) $size . '" height="' . (int) $size . '" fill="' . $color . '" aria-hidden="true"><path d="' . $star . '"/></svg>';
	}
	return $out . '</span>';
}

/**
 * Render one editorial footer column (Section 8). If the client has assigned a
 * menu to the given nav location it wins; otherwise the passed defaults render,
 * so the footer looks complete out of the box. Both paths share the .footer-links
 * styling (see src/tailwind.css) since wp_nav_menu strips utility classes.
 *
 * @param string                              $location Registered nav menu location.
 * @param string                              $heading  Column heading.
 * @param array<int,array{label:string,url:string}> $defaults Fallback links.
 * @return void Echoes the column markup.
 */
function juiced_footer_column( $location, $heading, $defaults ) {
	echo '<div>';
	echo '<p class="m-0 mb-2.5 font-bold text-brand-cream">' . esc_html( $heading ) . '</p>';

	if ( has_nav_menu( $location ) ) {
		wp_nav_menu(
			array(
				'theme_location' => $location,
				'container'      => false,
				'menu_class'     => 'footer-links',
				'fallback_cb'    => false,
				'depth'          => 1,
			)
		);
	} else {
		echo '<ul class="footer-links">';
		foreach ( $defaults as $link ) {
			printf(
				'<li><a href="%s">%s</a></li>',
				esc_url( $link['url'] ),
				esc_html( $link['label'] )
			);
		}
		echo '</ul>';
	}

	echo '</div>';
}

/**
 * Print the footer copyright line ("© <year> <name>", plus optional Privacy and
 * Terms links). footer.php prints it twice — beside the wordmark from md up,
 * below the link columns on phones — so it lives here.
 *
 * @param string $class     Classes for the <p>.
 * @param string $copyright Text after the year.
 * @param string $privacy   Privacy policy URL, or ''.
 * @param string $terms     Terms URL, or ''.
 */
function juiced_footer_copyright( $class, $copyright, $privacy, $terms ) {
	?>
	<p class="<?php echo esc_attr( $class ); ?>">
		&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( $copyright ); ?>
		<?php if ( $privacy ) : ?>
			<span aria-hidden="true">·</span> <a href="<?php echo esc_url( $privacy ); ?>" class="hover:text-brand-gold transition-colors"><?php esc_html_e( 'Privacy', 'juiced' ); ?></a>
		<?php endif; ?>
		<?php if ( $terms ) : ?>
			<span aria-hidden="true">·</span> <a href="<?php echo esc_url( $terms ); ?>" class="hover:text-brand-gold transition-colors"><?php esc_html_e( 'Terms', 'juiced' ); ?></a>
		<?php endif; ?>
	</p>
	<?php
}

/**
 * Inline social-media icon SVG for the footer, by network. Stroke/fill use
 * currentColor so the caller sets the color via a text-* utility.
 *
 * @param string $network 'instagram' | 'facebook' | 'tiktok'.
 * @return string SVG markup, or '' for an unknown network.
 */
function juiced_social_icon( $network ) {
	$icons = array(
		'instagram' => '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="0.9" fill="currentColor" stroke="none"/></svg>',
		'facebook'  => '<svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor" aria-hidden="true"><path d="M13.5 21v-8h2.7l.4-3.2h-3.1V7.7c0-.9.3-1.6 1.6-1.6h1.7V3.2C16.4 3.1 15.4 3 14.2 3c-2.5 0-4.2 1.5-4.2 4.3v2.5H7.3V13H10v8h3.5Z"/></svg>',
		'tiktok'    => '<svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor" aria-hidden="true"><path d="M16.5 3c.3 2.1 1.5 3.6 3.5 3.9v2.6c-1.3.1-2.5-.2-3.6-.9v5.9a5.6 5.6 0 1 1-5.6-5.6c.3 0 .6 0 .9.1v2.7a2.9 2.9 0 1 0 2 2.8V3h2.8Z"/></svg>',
	);
	return isset( $icons[ $network ] ) ? $icons[ $network ] : '';
}

/**
 * Icon + accent color for a herb wellness topic, by term slug.
 *
 * Serves both herb_category terms in the Herb Library sidebar and herb_tag
 * chips on the herb cards, so a category and its related tag ("Immune
 * Support" / "immunity") share one look. Matched by keyword rather than exact
 * slug so client-added terms land on something sensible; anything unmatched
 * falls back to a green leaf.
 *
 * Color classes are the same literal utilities as the event palette in
 * juiced_event_category_style(), so Tailwind has already compiled them.
 *
 * @param string $slug Term slug.
 * @return array { icon: string SVG, text: string color utility }
 */
function juiced_herb_topic_icon( $slug ) {
	$svg = static function ( $paths ) {
		return '<svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $paths . '</svg>';
	};

	// First keyword hit wins, so order specific before general ("skin" before
	// "heart" doesn't matter, but "anti-inflamm…" must beat nothing).
	$topics = array(
		'detox'    => array( $svg( '<path d="M12 2s6 6.5 6 11a6 6 0 0 1-12 0c0-4.5 6-11 6-11Z"/>' ), 'text-[#e0559a]' ),
		'cleanse'  => array( $svg( '<path d="M12 2s6 6.5 6 11a6 6 0 0 1-12 0c0-4.5 6-11 6-11Z"/>' ), 'text-[#e0559a]' ),
		'immun'    => array( $svg( '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/>' ), 'text-[#6c4fd6]' ),
		'energy'   => array( $svg( '<path d="M13 2 3 14h7l-1 8 10-12h-7l1-8Z"/>' ), 'text-brand-gold' ),
		'focus'    => array( $svg( '<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="4"/><circle cx="12" cy="12" r="0.5" fill="currentColor"/>' ), 'text-[#3aa0e0]' ),
		'brain'    => array( $svg( '<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="4"/><circle cx="12" cy="12" r="0.5" fill="currentColor"/>' ), 'text-[#3aa0e0]' ),
		'memory'   => array( $svg( '<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="4"/><circle cx="12" cy="12" r="0.5" fill="currentColor"/>' ), 'text-[#3aa0e0]' ),
		'joint'    => array( $svg( '<circle cx="7" cy="7" r="3"/><circle cx="17" cy="17" r="3"/><path d="M9.2 9.2l5.6 5.6"/>' ), 'text-[#3aa0e0]' ),
		'digest'   => array( $svg( '<path d="M4 10h16v2a8 8 0 0 1-16 0v-2Z"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/>' ), 'text-brand-green' ),
		'nausea'   => array( $svg( '<path d="M3 8h10a3 3 0 1 0-3-3"/><path d="M3 12h15a3 3 0 1 1-3 3"/><path d="M3 16h7"/>' ), 'text-brand-green' ),
		'bloat'    => array( $svg( '<path d="M3 8h10a3 3 0 1 0-3-3"/><path d="M3 12h15a3 3 0 1 1-3 3"/><path d="M3 16h7"/>' ), 'text-brand-green' ),
		'sleep'    => array( $svg( '<path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8Z"/>' ), 'text-[#6c4fd6]' ),
		'stress'   => array( $svg( '<path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/>' ), 'text-[#e0559a]' ),
		'mood'     => array( $svg( '<path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/>' ), 'text-[#e0559a]' ),
		'calm'     => array( $svg( '<path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/>' ), 'text-[#e0559a]' ),
		'skin'     => array( $svg( '<path d="M12 2l2.4 7.6L22 12l-7.6 2.4L12 22l-2.4-7.6L2 12l7.6-2.4L12 2Z"/>' ), 'text-brand-gold' ),
		'heart'    => array( $svg( '<path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/><path d="M4 12h4l2-3 3 6 2-3h5"/>' ), 'text-[#e0559a]' ),
		'circul'   => array( $svg( '<path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/><path d="M4 12h4l2-3 3 6 2-3h5"/>' ), 'text-[#e0559a]' ),
		'blood'    => array( $svg( '<path d="M12 2s6 6.5 6 11a6 6 0 0 1-12 0c0-4.5 6-11 6-11Z"/><path d="M9 13a3 3 0 0 0 3 3"/>' ), 'text-[#e0559a]' ),
		'hormone'  => array( $svg( '<path d="M12 3v18"/><path d="M5 7h14"/><path d="M5 7 3 12a3 3 0 0 0 6 0L7 7"/><path d="M19 7l-2 5a3 3 0 0 0 6 0l-2-5"/>' ), 'text-[#6c4fd6]' ),
		'balance'  => array( $svg( '<path d="M12 3v18"/><path d="M5 7h14"/><path d="M5 7 3 12a3 3 0 0 0 6 0L7 7"/><path d="M19 7l-2 5a3 3 0 0 0 6 0l-2-5"/>' ), 'text-[#6c4fd6]' ),
		'inflamm'  => array( $svg( '<path d="M12 22c-4.4 0-8-3-8-7.5C4 9 9.5 7.5 12 2c2.5 5.5 8 7 8 12.5 0 4.5-3.6 7.5-8 7.5Z"/>' ), 'text-brand-gold' ),
		'antiox'   => array( $svg( '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-4"/>' ), 'text-brand-green' ),
	);

	foreach ( $topics as $keyword => $topic ) {
		if ( false !== strpos( (string) $slug, $keyword ) ) {
			return array(
				'icon' => $topic[0],
				'text' => $topic[1],
			);
		}
	}

	// Leaf — the fallback for topics the map doesn't know.
	return array(
		'icon' => $svg( '<path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"/><path d="M2 21c0-3 1.85-5.36 5.08-6"/>' ),
		'text' => 'text-brand-green',
	);
}

/**
 * Everything a herb card needs, resolved once per herb.
 *
 * The sibling of juiced_event_card_data(): template-parts/herbs/card.php draws
 * from this, and the listing's Alpine component filters on `slugs` (category)
 * and `haystack` (search) without ever reading the DOM. The haystack folds in
 * the name, description, benefits field and term names, so "search by name or
 * benefit" means exactly that.
 *
 * @param int|WP_Post $post Herb post.
 * @return array|null Null when the post is missing.
 */
function juiced_herb_card_data( $post ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return null;
	}

	$tags       = array();
	$tag_names  = array();
	$herb_tags  = get_the_terms( $post, 'herb_tag' );
	if ( $herb_tags && ! is_wp_error( $herb_tags ) ) {
		foreach ( $herb_tags as $tag ) {
			$tags[]      = array(
				'name'  => $tag->name,
				'style' => juiced_herb_topic_icon( $tag->slug ),
			);
			$tag_names[] = $tag->name;
		}
	}

	$categories = get_the_terms( $post, 'herb_category' );
	$slugs      = array();
	$cat_names  = array();
	if ( $categories && ! is_wp_error( $categories ) ) {
		$slugs     = array_values( wp_list_pluck( $categories, 'slug' ) );
		$cat_names = wp_list_pluck( $categories, 'name' );
	}

	$excerpt  = has_excerpt( $post ) ? get_the_excerpt( $post ) : '';
	$benefits = function_exists( 'get_field' ) ? (string) get_field( 'benefits', $post->ID ) : '';

	$haystack = implode(
		' ',
		array_filter( array( $post->post_title, $excerpt, $benefits, implode( ' ', $tag_names ), implode( ' ', $cat_names ) ) )
	);
	// Entity-decode so texturized quotes and &nbsp; can't hide words from the
	// search box; NBSPs become plain spaces for the same reason.
	$haystack = html_entity_decode( wp_strip_all_tags( $haystack ), ENT_QUOTES, get_bloginfo( 'charset' ) );
	$haystack = str_replace( "\xC2\xA0", ' ', $haystack );

	return array(
		'id'        => $post->ID,
		'title'     => get_the_title( $post ),
		'permalink' => get_permalink( $post ),
		'image'     => get_the_post_thumbnail_url( $post, 'juiced_card' ),
		'excerpt'   => $excerpt,
		'tags'      => $tags,
		'slugs'     => $slugs,
		'haystack'  => mb_strtolower( $haystack ),
	);
}
