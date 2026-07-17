<?php
/**
 * Single Herb Section 1 — Hero.
 *
 * Dark header naming the herb: breadcrumb + "Back to All Herbs" up top, then
 * the type badge, name, gold tagline, intro, and a row of benefit icons — the
 * herb's own herb_tag terms, drawn with the same topic icons the library's
 * cards use, so a tag looks identical on the card that promised it and the
 * page that delivers it. The featured image runs full-bleed behind the right
 * side, fading into ink on the left — the same scrim construction as the
 * Events and Herb Library heroes.
 *
 * Everything is the herb's own data: title, excerpt, featured image, the
 * herb_type / tagline ACF fields, and its tags. Anything unset drops out —
 * no badge without a type, no tagline line without a tagline, and with no
 * image the gradient collapses to solid ink.
 *
 * @package Juiced
 */

$herb_id = get_the_ID();

$type    = get_field( 'herb_type', $herb_id );
$tagline = get_field( 'tagline', $herb_id );
$image   = get_the_post_thumbnail_url( $herb_id, 'juiced_hero' );

$tags = get_the_terms( $herb_id, 'herb_tag' );
$tags = ( $tags && ! is_wp_error( $tags ) ) ? $tags : array();
?>
<section class="relative isolate overflow-hidden bg-brand-ink bg-grain text-brand-cream">

	<?php if ( $image ) : ?>
		<div class="absolute inset-0 -z-10" aria-hidden="true">
			<img src="<?php echo esc_url( $image ); ?>" alt=""
				 class="h-full w-full object-cover object-center" loading="eager" fetchpriority="high" />
			<!-- Left-to-right scrim: solid behind the copy, clearing by the right
				 edge. Heavier on mobile, where the text sits over the image. -->
			<div class="absolute inset-0 bg-gradient-to-r from-brand-ink via-brand-ink/85 to-brand-ink/65 md:from-28% md:via-brand-ink/55 md:via-60% md:to-brand-ink/5"></div>
		</div>
	<?php endif; ?>

	<div class="mx-auto max-w-7xl px-4 pt-6 pb-14 md:pb-16">

		<!-- Breadcrumb + back link -->
		<div class="flex flex-wrap items-center justify-between gap-x-6 gap-y-2">
			<nav aria-label="<?php esc_attr_e( 'Breadcrumb', 'juiced' ); ?>" class="flex flex-wrap items-center gap-2 text-sm">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="text-brand-cream/60 transition-colors hover:text-brand-gold"><?php esc_html_e( 'Home', 'juiced' ); ?></a>
				<span class="text-brand-cream/40" aria-hidden="true">&rsaquo;</span>
				<a href="<?php echo esc_url( juiced_herbs_page_url() ); ?>" class="text-brand-cream/60 transition-colors hover:text-brand-gold"><?php esc_html_e( 'Herb Library', 'juiced' ); ?></a>
				<span class="text-brand-cream/40" aria-hidden="true">&rsaquo;</span>
				<span class="font-semibold text-white" aria-current="page"><?php the_title(); ?></span>
			</nav>

			<a href="<?php echo esc_url( juiced_herbs_page_url() ); ?>"
			   class="inline-flex items-center gap-2 text-sm font-medium text-brand-cream/80 transition-colors hover:text-brand-gold">
				<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
					<path d="M19 12H5M11 18l-6-6 6-6" />
				</svg>
				<?php esc_html_e( 'Back to All Herbs', 'juiced' ); ?>
			</a>
		</div>

		<div class="mt-10 max-w-xl md:mt-12">

			<?php if ( $type ) : ?>
				<span class="inline-flex items-center gap-1.5 rounded-full bg-brand-gold px-3.5 py-1.5 text-sm font-semibold text-brand-ink">
					<svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
						<path d="M5 19c8 1 14-5 14-14-9 0-15 6-14 14Z" />
						<path d="M5 19c3-5 7-9 11-11" />
					</svg>
					<?php echo esc_html( $type ); ?>
				</span>
			<?php endif; ?>

			<h1 class="mt-4 font-display text-5xl leading-[1.03] text-white md:text-6xl">
				<?php the_title(); ?>
			</h1>

			<?php if ( $tagline ) : ?>
				<p class="mt-2 font-display text-2xl text-brand-gold md:text-3xl">
					<?php echo esc_html( $tagline ); ?>
				</p>
			<?php endif; ?>

			<?php if ( has_excerpt() ) : ?>
				<p class="mt-5 max-w-md text-base leading-relaxed text-brand-cream/85 md:text-lg">
					<?php echo esc_html( wp_strip_all_tags( get_the_excerpt() ) ); ?>
				</p>
			<?php endif; ?>

			<?php if ( $tags ) : ?>
				<!-- Benefit icons: the herb's tags, same icon map as the library
					 cards (juiced_herb_topic_icon), scaled up for the hero. -->
				<ul class="mt-9 flex flex-wrap divide-x divide-white/15">
					<?php foreach ( $tags as $i => $tag ) : ?>
						<?php $topic = juiced_herb_topic_icon( $tag->slug ); ?>
						<li class="flex max-w-32 flex-col gap-2 pr-6 <?php echo 0 === $i ? '' : 'pl-6'; ?>">
							<span class="<?php echo esc_attr( $topic['text'] ); ?> [&_svg]:h-6 [&_svg]:w-6">
								<?php echo $topic['icon']; // phpcs:ignore WordPress.Security.EscapeOutput -- static inline SVG. ?>
							</span>
							<span class="text-sm font-medium leading-snug text-white"><?php echo esc_html( $tag->name ); ?></span>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

		</div>
	</div>
</section>
