<?php
/**
 * Template Name: Herb Library Page
 *
 * The /herbs landing page — built section by section from the approved mockup,
 * mirroring how the Events page is assembled. Each section is a partial under
 * template-parts/herbs/ and pulls its copy from ACF fields attached to this
 * page (inc/acf-herbs-page.php) or from the Herb CPT for the listings.
 *
 * Assign this template to a Page named "Herb Library" (slug: herbs). The Herb
 * CPT has no archive of its own, so this page is the canonical herb index and
 * juiced_herbs_page_url() resolves "herb library" links to it. Single herbs
 * keep their /herbs/{slug} permalinks alongside it.
 *
 * The orange newsletter band and the footer below this page's last section are
 * site-wide and live in footer.php.
 *
 * @package Juiced
 */

get_header();

get_template_part( 'template-parts/herbs/hero' );

get_template_part( 'template-parts/herbs/listing' );

get_template_part( 'template-parts/herbs/knowledge' );

get_footer();
