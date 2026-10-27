<?php
/**
 * Template Name: Locations Page
 *
 * The /locations page — built section by section from the approved mockup,
 * mirroring how the About and Events pages are assembled. Each section is a
 * partial under template-parts/locations/ and pulls its copy from ACF fields
 * attached to this page (inc/acf-locations-page.php).
 *
 * The orange newsletter band and the footer below this page's last section are
 * site-wide and live in footer.php.
 *
 * @package Juiced
 */

get_header();

get_template_part( 'template-parts/locations/hero' );

get_template_part( 'template-parts/locations/find' );

get_footer();
