<?php
/**
 * Homepage — maps to the Next.js `/` route.
 *
 * Built section by section from the approved mockup. Each section is a partial
 * under template-parts/home/ and pulls from ACF (singular copy) or CPTs (cards).
 * Being rebuilt top to bottom for the 2026 "Garden" redesign (deep green +
 * orange). Done: hero, coming up, more than a juice bar, top five, two neighborhoods.
 *
 * @package Juiced
 */

get_header();

get_template_part( 'template-parts/home/hero' );

get_template_part( 'template-parts/home/events' );

get_template_part( 'template-parts/home/community' );

get_template_part( 'template-parts/home/menu' );

get_template_part( 'template-parts/home/locations' );

get_template_part( 'template-parts/home/testimonials' );

// The "Stay in the Loop" newsletter band + editorial footer are site-wide and
// live in footer.php (rendered by get_footer below).

get_footer();
