<?php
/**
 * Community page Section 3 — the spotlight archive.
 *
 * Every spotlight except the featured month's, newest first, grouped under
 * month headings ("September 2026") with a People / Businesses filter and
 * numbered pages. Both are plain GET params (?spotlight_type=, ?spotlights_page=)
 * rather than /page/2: single spotlights own /community/{slug}, so a paged
 * permalink under /community would be read as a spotlight named "page". Every
 * link carries #spotlight-archive so a reload lands back on the grid.
 *
 * The section renders nothing until there is something to archive — the
 * first month's spotlights are all featured.
 *
 * Expects, via get_template_part()'s $args:
 *   exclude  int[]  The featured spotlights' IDs.
 *
 * @package Juiced
 */

$juiced_exclude = isset( $args['exclude'] ) ? array_map( 'intval', (array) $args['exclude'] ) : array();
$juiced_heading = juiced_page_field( 'community_archive_heading', 'Past Spotlights' );

// phpcs:disable WordPress.Security.NonceVerification.Recommended -- public, read-only filters.
$juiced_type = isset( $_GET['spotlight_type'] ) ? sanitize_key( wp_unslash( $_GET['spotlight_type'] ) ) : '';
$juiced_page = isset( $_GET['spotlights_page'] ) ? max( 1, absint( $_GET['spotlights_page'] ) ) : 1;
// phpcs:enable

if ( ! in_array( $juiced_type, array( 'person', 'business' ), true ) ) {
	$juiced_type = '';
}

$juiced_query_args = array(
	'post_type'      => 'spotlight',
	'posts_per_page' => 12,
	'paged'          => $juiced_page,
	'post__not_in'   => $juiced_exclude,
);

// "person" also matches spotlights saved before the type field existed.
if ( 'business' === $juiced_type ) {
	$juiced_query_args['meta_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery -- small table.
		array(
			'key'   => 'spotlight_type',
			'value' => 'business',
		),
	);
} elseif ( 'person' === $juiced_type ) {
	$juiced_query_args['meta_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery -- small table.
		'relation' => 'OR',
		array(
			'key'     => 'spotlight_type',
			'value'   => 'business',
			'compare' => '!=',
		),
		array(
			'key'     => 'spotlight_type',
			'compare' => 'NOT EXISTS',
		),
	);
}

$juiced_query = new WP_Query( $juiced_query_args );

// Hide the whole section when there's nothing archived at all — but keep it
// when a filter is what emptied it, so the visitor can switch back.
if ( ! $juiced_query->have_posts() && ! $juiced_type && 1 === $juiced_page ) {
	return;
}

// Group this page's posts by month, keeping newest-first order.
$juiced_groups = array();
foreach ( $juiced_query->posts as $juiced_post ) {
	$juiced_card = juiced_spotlight_card_data( $juiced_post );
	$juiced_groups[ $juiced_card['month_key'] ]['label']   = $juiced_card['month'];
	$juiced_groups[ $juiced_card['month_key'] ]['cards'][] = $juiced_card;
}

$juiced_base    = get_permalink();
$juiced_filters = array(
	''         => __( 'All', 'juiced' ),
	'person'   => __( 'People', 'juiced' ),
	'business' => __( 'Businesses', 'juiced' ),
);

$juiced_link = function ( $type, $page = 1 ) use ( $juiced_base ) {
	$url = $juiced_base;
	if ( $type ) {
		$url = add_query_arg( 'spotlight_type', $type, $url );
	}
	if ( $page > 1 ) {
		$url = add_query_arg( 'spotlights_page', $page, $url );
	}
	return $url . '#spotlight-archive';
};
?>
<section id="spotlight-archive" class="scroll-mt-24 bg-brand-paper text-brand-ink" aria-labelledby="community-archive-h">
	<div class="mx-auto max-w-7xl px-4 py-14 md:py-20">

		<div class="flex flex-wrap items-end justify-between gap-6">
			<h2 id="community-archive-h" class="font-display text-3xl font-bold leading-tight text-brand-forest md:text-4xl">
				<?php echo esc_html( $juiced_heading ); ?>
			</h2>

			<nav class="flex flex-wrap gap-2" aria-label="<?php esc_attr_e( 'Filter spotlights', 'juiced' ); ?>">
				<?php foreach ( $juiced_filters as $juiced_key => $juiced_label ) : ?>
					<?php $juiced_on = $juiced_key === $juiced_type; ?>
					<a href="<?php echo esc_url( $juiced_link( $juiced_key ) ); ?>"
					   <?php echo $juiced_on ? 'aria-current="page"' : ''; ?>
					   class="rounded-full border-2 px-5 py-2 text-sm font-bold transition-colors <?php echo $juiced_on ? 'border-brand-forest bg-brand-forest text-brand-cream' : 'border-brand-forest/25 text-brand-forest hover:border-brand-forest'; ?>">
						<?php echo esc_html( $juiced_label ); ?>
					</a>
				<?php endforeach; ?>
			</nav>
		</div>

		<?php if ( ! $juiced_groups ) : ?>
			<p class="mt-10 rounded-3xl bg-white px-6 py-10 text-center text-brand-ink/70 ring-1 ring-brand-forest/15">
				<?php esc_html_e( 'No past spotlights here yet.', 'juiced' ); ?>
			</p>
		<?php endif; ?>

		<?php foreach ( $juiced_groups as $juiced_group ) : ?>
			<div class="mt-10">
				<h3 class="flex items-center gap-4 text-sm font-bold uppercase tracking-[.14em] text-brand-rust">
					<?php echo esc_html( $juiced_group['label'] ); ?>
					<span class="h-px flex-1 bg-brand-forest/15" aria-hidden="true"></span>
				</h3>
				<div class="mt-5 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
					<?php foreach ( $juiced_group['cards'] as $juiced_card ) : ?>
						<?php get_template_part( 'template-parts/community/card', null, array( 'card' => $juiced_card ) ); ?>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endforeach; ?>

		<?php if ( $juiced_query->max_num_pages > 1 ) : ?>
			<nav class="mt-12 flex flex-wrap items-center justify-center gap-2" aria-label="<?php esc_attr_e( 'Spotlight archive pages', 'juiced' ); ?>">
				<?php for ( $juiced_n = 1; $juiced_n <= $juiced_query->max_num_pages; $juiced_n++ ) : ?>
					<?php $juiced_on = $juiced_n === $juiced_page; ?>
					<a href="<?php echo esc_url( $juiced_link( $juiced_type, $juiced_n ) ); ?>"
					   <?php echo $juiced_on ? 'aria-current="page"' : ''; ?>
					   class="grid size-11 place-items-center rounded-full text-sm font-bold transition-colors <?php echo $juiced_on ? 'bg-brand-forest text-brand-cream' : 'bg-white text-brand-forest ring-1 ring-brand-forest/15 hover:ring-brand-forest'; ?>">
						<?php echo (int) $juiced_n; ?>
					</a>
				<?php endfor; ?>
			</nav>
		<?php endif; ?>

	</div>
</section>
<?php
wp_reset_postdata();
