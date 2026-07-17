<?php
/**
 * Template Name: Contact Page
 *
 * The /contact page — built section by section from the approved mockup, the
 * same way the About, Events and Locations pages are assembled. Each section is
 * a partial under template-parts/contact/ and pulls its copy from ACF fields
 * attached to this page (inc/acf-contact-page.php). The message form posts to
 * the handler in inc/contact-form.php.
 *
 * The orange newsletter band and the footer below this page's last section are
 * site-wide and live in footer.php.
 *
 * @package Juiced
 */

get_header();

get_template_part( 'template-parts/contact/hero' );

get_template_part( 'template-parts/contact/connect' );

get_template_part( 'template-parts/contact/visit' );

get_footer();
