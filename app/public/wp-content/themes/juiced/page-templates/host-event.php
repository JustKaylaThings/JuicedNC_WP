<?php
/**
 * Template Name: Host an Event Page
 *
 * The /host-an-event page, for groups and partners who want to run an event
 * at the shop — option D ("Community Story") from the Host an Event canvas,
 * minus its "Recently at Juiced!" row and quote: a hero with The Space quick
 * facts on its right, then a photo carousel and Good to Know FAQs beside the
 * inquiry form. Copy comes from ACF fields on this page
 * (inc/acf-host-event-page.php); the form posts to the handler in
 * inc/page-forms.php.
 *
 * @package Juiced
 */

get_header();

get_template_part( 'template-parts/host/hero' );

get_template_part( 'template-parts/host/form' );

get_footer();
