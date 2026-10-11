<?php
/**
 * Template Name: Community Page
 *
 * The /community page — the monthly Community Spotlight blog. Each month
 * spotlights up to two people or local businesses (the Spotlight CPT, from the
 * juiced-cpt plugin); the newest month is featured up top and every earlier
 * month sits in the archive below. Copy around the spotlights is ACF-editable
 * (inc/acf-community-page.php).
 *
 * Assign this template to the Page with slug "community". Single spotlights
 * live at /community/{slug} alongside it, the same way herbs sit under /herbs.
 *
 * @package Juiced
 */

get_header();

// The newest month with a spotlight — not the calendar month, so the page
// never goes blank on the 1st while the next post is still being written.
$juiced_latest = get_posts(
	array(
		'post_type'      => 'spotlight',
		'posts_per_page' => 1,
		'no_found_rows'  => true,
	)
);

$juiced_featured = array();

if ( $juiced_latest ) {
	$juiced_featured = get_posts(
		array(
			'post_type'      => 'spotlight',
			'posts_per_page' => 2,
			'no_found_rows'  => true,
			'order'          => 'ASC',
			'date_query'     => array(
				array(
					'year'  => (int) get_the_date( 'Y', $juiced_latest[0] ),
					'month' => (int) get_the_date( 'n', $juiced_latest[0] ),
				),
			),
		)
	);
}

get_template_part( 'template-parts/community/hero' );

get_template_part( 'template-parts/community/featured', null, array( 'posts' => $juiced_featured ) );

get_template_part( 'template-parts/community/archive', null, array( 'exclude' => wp_list_pluck( $juiced_featured, 'ID' ) ) );

get_template_part( 'template-parts/community/nominate' );

get_footer();
