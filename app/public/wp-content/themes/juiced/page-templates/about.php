<?php
/**
 * Template Name: About Page
 *
 * The /about page — built section by section from the approved mockup,
 * mirroring how the Events and Herb Library pages are assembled. Each section
 * is a partial under template-parts/about/ and pulls its copy from ACF fields
 * attached to this page (inc/acf-about-page.php).
 *
 * The orange newsletter band and the footer below this page's last section are
 * site-wide and live in footer.php.
 *
 * @package Juiced
 */

get_header();

get_template_part( 'template-parts/about/hero' );

get_template_part( 'template-parts/about/mission' );

get_template_part( 'template-parts/about/story' );

get_template_part( 'template-parts/about/owners' );

get_template_part( 'template-parts/about/partners' );

get_template_part( 'template-parts/about/quote' );

get_footer();
