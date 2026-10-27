<?php
/**
 * Single Herb — /herbs/{slug}.
 *
 * Where every herb card's "Learn More" lands. Assembled from section parts in
 * template-parts/herbs/, mirroring how the pages are built, because each band
 * of the approved mockup is its own self-contained block: dark hero, then
 * overview + key benefits, then the usage / nutrition / fun-fact row, then
 * related herbs.
 *
 * Everything renders from the Herb's own fields (plugin: juiced-cpt) — title,
 * excerpt, featured image, herb_type / tagline ACF text, and its herb_tag
 * terms. No page-local ACF group; there is no "the herb page" to hang one on.
 *
 * @package Juiced
 */

get_header();

while ( have_posts() ) :
	the_post();

	get_template_part( 'template-parts/herbs/single-hero' );
	get_template_part( 'template-parts/herbs/single-overview' );
	get_template_part( 'template-parts/herbs/single-related' );

endwhile;

get_footer();
