<?php
/**
 * Single Community Spotlight — /community/{slug}.
 *
 * Three bands, built from the spotlight's own data (juiced-cpt plugin):
 *
 *  1. Green "Garden" hero — breadcrumb and back link, the month it ran, type
 *     badge, name, and role, with their photo framed on the right.
 *  2. Cream story band — the post content, beside a sticky aside with the
 *     pull quote and their website/Instagram button.
 *  3. "More Spotlights" — up to three others: this month's partner first,
 *     then the most recent.
 *
 * Anything unset drops out: no photo → the hero copy runs full width; no
 * quote or link → the aside goes and the story takes the column.
 *
 * @package Juiced
 */

get_header();

while ( have_posts() ) :
	the_post();

	$juiced_card       = juiced_spotlight_card_data( get_post() );
	$juiced_link_url   = get_field( 'spotlight_link_url' );
	$juiced_link_label = get_field( 'spotlight_link_label' );
	$juiced_link_label = $juiced_link_label ? $juiced_link_label : __( 'Visit Their Website', 'juiced' );
	$juiced_has_aside  = $juiced_card['quote'] || $juiced_link_url;
	$juiced_back       = juiced_community_page_url();

	// This month's partner first, then the newest others, three in all.
	$juiced_partner = get_posts(
		array(
			'post_type'      => 'spotlight',
			'posts_per_page' => 1,
			'no_found_rows'  => true,
			'post__not_in'   => array( get_the_ID() ),
			'date_query'     => array(
				array(
					'year'  => (int) get_the_date( 'Y' ),
					'month' => (int) get_the_date( 'n' ),
				),
			),
		)
	);
	$juiced_more = array_merge(
		$juiced_partner,
		get_posts(
			array(
				'post_type'      => 'spotlight',
				'posts_per_page' => 3 - count( $juiced_partner ),
				'no_found_rows'  => true,
				'post__not_in'   => array_merge( array( get_the_ID() ), wp_list_pluck( $juiced_partner, 'ID' ) ),
			)
		)
	);
	?>

	<!-- 1. Hero -->
	<section class="relative isolate -mt-px overflow-hidden bg-brand-forest bg-garden text-brand-cream">
		<div class="mx-auto max-w-7xl px-4 pt-6 pb-14 md:pb-16">

			<div class="flex flex-wrap items-center justify-between gap-x-6 gap-y-2">
				<nav aria-label="<?php esc_attr_e( 'Breadcrumb', 'juiced' ); ?>" class="flex flex-wrap items-center gap-2 text-sm">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="text-brand-cream/60 transition-colors hover:text-brand-gold"><?php esc_html_e( 'Home', 'juiced' ); ?></a>
					<span class="text-brand-cream/40" aria-hidden="true">&rsaquo;</span>
					<a href="<?php echo esc_url( $juiced_back ); ?>" class="text-brand-cream/60 transition-colors hover:text-brand-gold"><?php esc_html_e( 'Community', 'juiced' ); ?></a>
					<span class="text-brand-cream/40" aria-hidden="true">&rsaquo;</span>
					<span class="font-semibold text-white" aria-current="page"><?php the_title(); ?></span>
				</nav>

				<a href="<?php echo esc_url( $juiced_back ); ?>"
				   class="inline-flex items-center gap-2 text-sm font-medium text-brand-cream/80 transition-colors hover:text-brand-gold">
					<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
						<path d="M19 12H5M11 18l-6-6 6-6" />
					</svg>
					<?php esc_html_e( 'All Spotlights', 'juiced' ); ?>
				</a>
			</div>

			<div class="mt-10 grid items-center gap-10 md:mt-12 <?php echo $juiced_card['image'] ? 'md:grid-cols-[3fr_2fr] lg:gap-16' : ''; ?>">
				<div class="max-w-2xl">
					<p class="text-sm font-bold uppercase tracking-[.14em] text-brand-gold">
						<?php
						/* translators: %s: month and year, e.g. October 2026 */
						printf( esc_html__( '%s Spotlight', 'juiced' ), esc_html( $juiced_card['month'] ) );
						?>
					</p>

					<h1 class="mt-3 font-display font-bold leading-[.95] tracking-[-.02em] text-[clamp(2.75rem,7vw,4.5rem)]">
						<?php the_title(); ?>
					</h1>

					<?php if ( $juiced_card['role'] ) : ?>
						<p class="mt-3 font-display text-2xl text-brand-mint md:text-3xl"><?php echo esc_html( $juiced_card['role'] ); ?></p>
					<?php endif; ?>

					<span class="mt-6 inline-flex items-center gap-1.5 rounded-full bg-brand-gold px-3.5 py-1.5 text-sm font-semibold text-brand-ink">
						<?php echo juiced_spotlight_icon( $juiced_card['type'] ); // phpcs:ignore WordPress.Security.EscapeOutput -- static inline SVG. ?>
						<?php echo esc_html( $juiced_card['badge'] ); ?>
					</span>
				</div>

				<?php if ( $juiced_card['image'] ) : ?>
					<img src="<?php echo esc_url( $juiced_card['image'] ); ?>"
						 alt="<?php echo esc_attr( get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true ) ); ?>"
						 class="aspect-[4/5] w-full max-w-md rounded-3xl object-cover shadow-xl ring-4 ring-brand-gold/80 md:justify-self-end"
						 loading="eager" fetchpriority="high" />
				<?php endif; ?>
			</div>
		</div>
	</section>

	<!-- 2. Story -->
	<section class="bg-brand-cream text-brand-ink">
		<div class="mx-auto grid max-w-7xl gap-12 px-4 py-14 md:py-20 <?php echo $juiced_has_aside ? 'lg:grid-cols-12' : ''; ?>">

			<div class="entry-content text-lg <?php echo $juiced_has_aside ? 'lg:col-span-7' : 'mx-auto max-w-3xl'; ?>">
				<?php the_content(); ?>
			</div>

			<?php if ( $juiced_has_aside ) : ?>
				<aside class="lg:col-span-4 lg:col-start-9">
					<div class="space-y-6 lg:sticky lg:top-28">
						<?php if ( $juiced_card['quote'] ) : ?>
							<figure class="rounded-3xl bg-brand-forest bg-garden p-8 text-brand-cream">
								<span class="block font-display text-6xl leading-none text-brand-gold" aria-hidden="true">&ldquo;</span>
								<blockquote class="-mt-2 font-display text-2xl leading-snug">
									<?php echo esc_html( $juiced_card['quote'] ); ?>
								</blockquote>
								<figcaption class="mt-4 text-sm font-semibold text-brand-mint">&mdash; <?php the_title(); ?></figcaption>
							</figure>
						<?php endif; ?>

						<?php if ( $juiced_link_url ) : ?>
							<a href="<?php echo esc_url( $juiced_link_url ); ?>" target="_blank" rel="noopener"
							   class="flex items-center justify-center gap-2 rounded-full bg-brand-gold px-6 py-4 font-bold text-brand-ink transition-colors hover:bg-brand-forest hover:text-brand-cream">
								<?php echo esc_html( $juiced_link_label ); ?>
								<span aria-hidden="true">&rarr;</span>
							</a>
						<?php endif; ?>
					</div>
				</aside>
			<?php endif; ?>

		</div>
	</section>

	<!-- 3. More spotlights -->
	<?php if ( $juiced_more ) : ?>
		<section class="bg-brand-paper text-brand-ink" aria-labelledby="spotlight-more-h">
			<div class="mx-auto max-w-7xl px-4 py-14 md:py-20">
				<div class="flex flex-wrap items-end justify-between gap-4">
					<h2 id="spotlight-more-h" class="font-display text-3xl font-bold leading-tight text-brand-forest md:text-4xl">
						<?php esc_html_e( 'More Spotlights', 'juiced' ); ?>
					</h2>
					<a href="<?php echo esc_url( $juiced_back . '#spotlight-archive' ); ?>"
					   class="inline-flex items-center gap-1.5 font-bold text-brand-rust transition-colors hover:text-brand-green">
						<?php esc_html_e( 'See the Archive', 'juiced' ); ?>
						<span aria-hidden="true">&rarr;</span>
					</a>
				</div>
				<div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
					<?php foreach ( $juiced_more as $juiced_other ) : ?>
						<?php get_template_part( 'template-parts/community/card', null, array( 'card' => juiced_spotlight_card_data( $juiced_other ) ) ); ?>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php
endwhile;

get_footer();
