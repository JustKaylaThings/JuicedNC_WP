<?php
/**
 * Fallback template. WordPress uses this when no more specific template matches.
 * Specific templates (front-page.php, archive-herb.php, single-herb.php,
 * archive-event.php, single-event.php, page-menu.php, page-visit.php, search.php)
 * are added in Phase 3. This keeps the theme valid and renders a basic loop.
 *
 * @package Juiced
 */

get_header();
?>

<div class="mx-auto max-w-3xl px-4 py-16">
	<?php if ( have_posts() ) : ?>
		<?php while ( have_posts() ) : the_post(); ?>
			<article <?php post_class( 'mb-12' ); ?>>
				<h1 class="font-display text-3xl text-brand-bark"><?php the_title(); ?></h1>
				<div class="prose mt-4 text-brand-bark/80">
					<?php the_content(); ?>
				</div>
			</article>
		<?php endwhile; ?>

		<?php the_posts_pagination(); ?>
	<?php else : ?>
		<h1 class="font-display text-3xl text-brand-bark"><?php esc_html_e( 'Nothing here yet', 'juiced' ); ?></h1>
		<p class="mt-4 text-brand-bark/70"><?php esc_html_e( 'No content found.', 'juiced' ); ?></p>
	<?php endif; ?>
</div>

<?php
get_footer();
