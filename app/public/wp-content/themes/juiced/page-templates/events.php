<?php
/**
 * Template Name: Events Page
 *
 * The /events landing page — built section by section from the approved mockup,
 * mirroring how front-page.php is assembled. Each section is a partial under
 * template-parts/events/ and pulls its copy from ACF fields attached to this
 * page (inc/acf-events-page.php) or from the Event CPT for the listings.
 *
 * Assign this template to a Page named "Events" (slug: events). The Event CPT
 * has no archive of its own, so this page is the canonical events index and
 * juiced_events_page_url() resolves "all events" links to it.
 *
 * Every section of the mockup is now here. The orange newsletter band and the
 * footer below this page's last section are site-wide and live in footer.php.
 *
 * @package Juiced
 */

get_header();

get_template_part( 'template-parts/events/hero' );

get_template_part( 'template-parts/events/filters' );

get_template_part( 'template-parts/events/listing' );

get_template_part( 'template-parts/events/community' );

get_footer();
