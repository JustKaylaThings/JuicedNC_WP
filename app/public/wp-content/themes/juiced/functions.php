<?php
/**
 * Juiced! theme setup and asset loading.
 *
 * Registers theme supports, navigation menus, and image sizes, and enqueues the
 * compiled Tailwind stylesheet (assets/theme.css), Google Fonts, Alpine.js, and
 * the theme's own JS. Data-rendering helpers live in inc/ (added in later phases).
 *
 * @package Juiced
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access.
}

define( 'JUICED_VERSION', '0.1.0' );

/**
 * Theme supports + navigation menus.
 */
function juiced_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 96,
			'width'       => 96,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	// Image sizes mirroring the Next.js layouts (cards, hero, detail).
	add_image_size( 'juiced_card', 640, 480, true );
	add_image_size( 'juiced_hero', 1920, 1080, true );

	// Primary nav + the five editorial footer columns from the mockup. Each
	// footer column is its own menu location so the client can curate its links;
	// when a location has no menu assigned, footer.php renders sensible defaults.
	register_nav_menus(
		array(
			'primary'          => __( 'Primary Navigation', 'juiced' ),
			'footer'           => __( 'Footer Navigation (legacy)', 'juiced' ),
			'footer_menu'      => __( 'Footer — Menu column', 'juiced' ),
			'footer_events'    => __( 'Footer — Events column', 'juiced' ),
			'footer_about'     => __( 'Footer — About column', 'juiced' ),
			'footer_locations' => __( 'Footer — Locations column', 'juiced' ),
			'footer_order'     => __( 'Footer — Order column', 'juiced' ),
		)
	);
}
add_action( 'after_setup_theme', 'juiced_setup' );

/**
 * Enqueue styles and scripts.
 */
function juiced_assets() {
	$theme_css = get_theme_file_path( 'assets/theme.css' );

	// Google Fonts — Fredoka (rounded display, variable) + Inter Tight (body).
	wp_enqueue_style(
		'juiced-fonts',
		'https://fonts.googleapis.com/css2?family=Fredoka:wght@300..700&family=Inter+Tight:wght@400;500;600;700&display=swap',
		array(),
		null
	);

	// Compiled Tailwind. Version by file mtime for cache-busting in dev.
	wp_enqueue_style(
		'juiced-theme',
		get_theme_file_uri( 'assets/theme.css' ),
		array( 'juiced-fonts' ),
		file_exists( $theme_css ) ? (string) filemtime( $theme_css ) : JUICED_VERSION
	);

	// Alpine.js powers the interactive islands (nav drawer, sliders, filters).
	// TODO: vendor this locally (assets/js/alpine.min.js) before production so
	// the site has no external runtime dependency. CDN is fine for dev.
	wp_enqueue_script(
		'alpinejs',
		'https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js',
		array(),
		'3',
		array(
			'strategy'  => 'defer',
			'in_footer' => true,
		)
	);

	// Theme JS (progressive enhancement helpers).
	wp_enqueue_script(
		'juiced-main',
		get_theme_file_uri( 'assets/js/main.js' ),
		array(),
		JUICED_VERSION,
		array( 'in_footer' => true )
	);

	// Instagram embed.js — only on the front page, where the community section's
	// live feed lives. It auto-processes any .instagram-media blockquote on load,
	// swapping it for Instagram's hosted card. Loaded in the footer so the
	// blockquote is already in the DOM.
	if ( is_front_page() ) {
		wp_enqueue_script(
			'instagram-embed',
			'https://www.instagram.com/embed.js',
			array(),
			null,
			array( 'in_footer' => true )
		);
	}
}
add_action( 'wp_enqueue_scripts', 'juiced_assets' );

/**
 * Auto-load everything in inc/ (helpers, ACF field groups, event/announcement
 * logic, Google reviews). helpers.php loads first since others depend on it.
 */
$juiced_inc_dir = get_theme_file_path( 'inc' );
require_once $juiced_inc_dir . '/helpers.php';
foreach ( (array) glob( $juiced_inc_dir . '/*.php' ) as $juiced_inc_path ) {
	if ( 'helpers.php' !== basename( $juiced_inc_path ) ) {
		require_once $juiced_inc_path;
	}
}
