<?php
/**
 * Homepage Section 7 — Testimonials ("What Our Community is Saying").
 *
 * Dark carousel of live Google reviews. Reviews are pulled from both shops'
 * Google Place IDs (Appearance → Customize → Business Info) via inc/google-reviews.php —
 * fetched with wp_remote_get, cached 24h, filtered to 4-star-and-up, newest
 * first. Alpine.js drives the scroll rail + prev/next arrows.
 *
 * Renders nothing when there are no reviews (no API key configured, API error,
 * or no qualifying reviews yet) — see the key-setup notes in inc/google-reviews.php.
 * Heading copy is ACF-editable on the Home page (inc/acf-homepage.php).
 *
 * @package Juiced
 */

// Place IDs default to the two real Juiced! shops so reviews light up as soon as
// the API key is set; the client can override them in the Customizer.
$juiced_place_ids = array(
	juiced_business_field( 'loc1_place_id', 'ChIJqz-S5g9frIkR7BBvS1h_DvE' ), // Rock Quarry Road
	juiced_business_field( 'loc2_place_id', 'ChIJB2o8cbNfrIkR86nWswGqshk' ), // Hill Street
);

$juiced_reviews = function_exists( 'juiced_community_reviews' ) ? juiced_community_reviews( $juiced_place_ids, 9 ) : array();

if ( empty( $juiced_reviews ) ) {
	return;
}

$juiced_t_heading = juiced_field( 'testimonials_heading', 'What Our' );
$juiced_t_accent  = juiced_field( 'testimonials_accent', 'Community' );
$juiced_t_end     = juiced_field( 'testimonials_heading_end', 'is Saying' );
?>
<section class="relative bg-brand-ink text-brand-cream overflow-hidden bg-grain">
	<div class="mx-auto max-w-7xl px-4 py-16 md:py-24" x-data>

		<!-- Section heading -->
		<h2 class="text-center font-display text-3xl md:text-4xl mb-12 md:mb-14">
			<span class="text-white"><?php echo esc_html( $juiced_t_heading ); ?></span>
			<span class="text-brand-gold"><?php echo esc_html( $juiced_t_accent ); ?></span>
			<span class="text-white"><?php echo esc_html( $juiced_t_end ); ?></span>
		</h2>

		<div class="relative">
			<!-- Prev / next arrows (desktop) -->
			<button type="button" @click="$refs.rail.scrollBy({ left: -360, behavior: 'smooth' })"
					class="hidden md:grid place-items-center absolute -left-4 top-1/2 -translate-y-1/2 z-10 w-10 h-10 rounded-full bg-white/10 text-white backdrop-blur hover:bg-white/20 transition"
					aria-label="<?php esc_attr_e( 'Previous reviews', 'juiced' ); ?>">
				<span aria-hidden="true">&larr;</span>
			</button>
			<button type="button" @click="$refs.rail.scrollBy({ left: 360, behavior: 'smooth' })"
					class="hidden md:grid place-items-center absolute -right-4 top-1/2 -translate-y-1/2 z-10 w-10 h-10 rounded-full bg-white/10 text-white backdrop-blur hover:bg-white/20 transition"
					aria-label="<?php esc_attr_e( 'More reviews', 'juiced' ); ?>">
				<span aria-hidden="true">&rarr;</span>
			</button>

			<!-- Review rail -->
			<div x-ref="rail" class="flex gap-5 md:gap-6 overflow-x-auto snap-x snap-mandatory no-scrollbar pb-2">
				<?php foreach ( $juiced_reviews as $review ) : ?>
					<article class="snap-start shrink-0 w-[82vw] sm:w-[60vw] lg:w-[calc((100%-3rem)/3)] rounded-2xl bg-white/[0.04] ring-1 ring-white/10 p-6 md:p-7">
						<div class="flex items-start gap-4">

							<!-- Avatar -->
							<?php if ( $review['photo'] ) : ?>
								<img src="<?php echo esc_url( $review['photo'] ); ?>" alt=""
									 referrerpolicy="no-referrer" loading="lazy"
									 class="shrink-0 w-14 h-14 rounded-full object-cover bg-white/10" />
							<?php else : ?>
								<div class="shrink-0 w-14 h-14 rounded-full bg-brand-green/30 text-white grid place-items-center font-display font-bold text-lg">
									<?php echo esc_html( strtoupper( mb_substr( $review['author'], 0, 1 ) ) ); ?>
								</div>
							<?php endif; ?>

							<div class="min-w-0">
								<?php echo juiced_stars( $review['rating'], 18 ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in helper. ?>
								<p class="mt-3 text-sm text-brand-cream/80 leading-relaxed">
									<?php echo esc_html( wp_trim_words( $review['text'], 34 ) ); ?>
								</p>
								<p class="mt-3 text-sm font-semibold text-white">
									&ndash; <?php echo esc_html( $review['author'] ); ?>
								</p>
							</div>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		</div>

	</div>

	<!-- Bottom accent line -->
	<div class="h-1.5 w-full bg-brand-gold"></div>
</section>
