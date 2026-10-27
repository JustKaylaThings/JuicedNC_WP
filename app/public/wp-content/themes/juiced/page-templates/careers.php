<?php
/**
 * Template Name: Careers Page
 *
 * The /careers page: one split section — the pitch on the deep-green panel
 * beside the three-step application form (template-parts/careers/apply.php).
 * Copy comes from ACF fields on this page (inc/acf-careers-page.php); the
 * form posts to the handler in inc/page-forms.php.
 *
 * @package Juiced
 */

get_header();

get_template_part( 'template-parts/careers/apply' );

get_footer();
