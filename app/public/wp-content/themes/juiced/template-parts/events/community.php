<?php
/**
 * Events page Section 4 — "Host Your Event" with a carousel of past events.
 *
 * The green band under the calendar: the host pitch and enquiry button on the left, a
 * swipeable carousel of photos from past events on the right (stacked on
 * mobile). It replaced the "More Than a Juice Bar." band in Oct 2026 — the page
 * already says "community" everywhere else, and the photos do a better job of
 * selling the space than a heading could.
 *
 * The button link reads events_host_cta_url — the field the old host box under
 * the calendar used — so the link already saved there carried over.
 *
 * The carousel is a native scroll-snap track, so it swipes on touch and works
 * with no JS at all; Alpine only adds the arrows, dots, and a slow autoplay
 * that stops for hover, focus, or reduced motion. Photos are discrete fields
 * rather than a repeater — ACF free has no Repeater. The photo field names still say
 * "community" because they predate the redesign and renaming them would orphan
 * photos already uploaded.
 *
 * @package Juiced
 */

$juiced_heading = juiced_page_field( 'events_gallery_heading', 'Host Your Event at Juiced!' );

if ( ! $juiced_heading ) {
	return;
}

$juiced_body  = juiced_page_field( 'events_gallery_body', "Birthdays, workshops, pop-ups, book clubs, and wellness meetups — we've hosted them all." );
$juiced_label = juiced_page_field( 'events_gallery_cta_label', 'Inquire Today' );
$juiced_url   = juiced_page_field( 'events_host_cta_url', '' );

// 'large' (1024w): a slide is up to ~60% of the container, so the card size
// goes soft on a 2x display. Alt and caption come from the Media Library, so
// the client describes each photo once, where they uploaded it.
$juiced_slides = array();

for ( $juiced_n = 1; $juiced_n <= 8; $juiced_n++ ) {
	$juiced_img = juiced_page_field( 'events_community_image_' . $juiced_n, '' );
	$juiced_src = juiced_image_url( $juiced_img, 'large' );
	if ( $juiced_src ) {
		$juiced_slides[] = array(
			'src'     => $juiced_src,
			'alt'     => is_array( $juiced_img ) ? ( $juiced_img['alt'] ?? '' ) : '',
			'caption' => is_array( $juiced_img ) ? ( $juiced_img['caption'] ?? '' ) : '',
		);
	}
}

$juiced_count = count( $juiced_slides );
?>
<section class="bg-brand-forest bg-garden text-brand-cream" aria-labelledby="events-host-h">
	<div class="mx-auto grid max-w-7xl items-center gap-10 px-4 pt-10 pb-16 md:pt-14 md:pb-24 <?php echo $juiced_count ? 'lg:grid-cols-[2fr_3fr] lg:gap-14' : ''; ?>">

		<div class="max-w-xl">
			<h2 id="events-host-h" class="font-display font-bold leading-[1.02] text-[clamp(2.25rem,5vw,3.75rem)]">
				<?php echo esc_html( $juiced_heading ); ?>
			</h2>

			<?php if ( $juiced_body ) : ?>
				<p class="mt-6 max-w-md text-lg md:text-xl font-medium leading-relaxed text-brand-mint">
					<?php echo esc_html( $juiced_body ); ?>
				</p>
			<?php endif; ?>

			<?php if ( $juiced_url && $juiced_label ) : ?>
				<a href="<?php echo esc_url( $juiced_url ); ?>"
				   class="mt-8 inline-flex items-center gap-2 rounded-full bg-brand-gold px-6 py-4 font-bold text-brand-ink transition-colors hover:bg-brand-cream">
					<?php echo esc_html( $juiced_label ); ?>
					<span aria-hidden="true">&rarr;</span>
				</a>
			<?php endif; ?>
		</div>

		<?php if ( $juiced_count ) : ?>
			<div class="min-w-0"
				 role="region" aria-roledescription="carousel" aria-label="Photos from past events"
				 x-data="{
					i: 0,
					n: <?php echo (int) $juiced_count; ?>,
					paused: false,
					go(k) {
						this.i = (k + this.n) % this.n;
						const t = this.$refs.track;
						t.scrollTo({ left: t.children[this.i].offsetLeft - t.offsetLeft, behavior: 'smooth' });
					},
					sync() {
						const t = this.$refs.track;
						this.i = Math.round(t.scrollLeft / t.children[0].offsetWidth);
					},
					init() {
						if (this.n < 2 || window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
						setInterval(() => { if (!this.paused && !document.hidden) this.go(this.i + 1); }, 5000);
					}
				 }"
				 @mouseenter="paused = true" @mouseleave="paused = false"
				 @focusin="paused = true" @focusout="paused = false">

				<ul x-ref="track" @scroll.debounce.100ms="sync()"
					class="flex snap-x snap-mandatory overflow-x-auto rounded-2xl [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
					<?php foreach ( $juiced_slides as $juiced_i => $juiced_slide ) : ?>
						<li class="relative w-full shrink-0 snap-start"
							aria-roledescription="slide"
							aria-label="<?php echo esc_attr( sprintf( '%d of %d', $juiced_i + 1, $juiced_count ) ); ?>">
							<img src="<?php echo esc_url( $juiced_slide['src'] ); ?>"
								 alt="<?php echo esc_attr( $juiced_slide['alt'] ); ?>"
								 class="aspect-[4/3] w-full object-cover"
								 <?php echo $juiced_i ? 'loading="lazy"' : ''; ?> />
							<?php if ( $juiced_slide['caption'] ) : ?>
								<p class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-brand-ink/80 to-transparent px-5 pb-4 pt-12 text-sm font-semibold">
									<?php echo esc_html( wp_strip_all_tags( $juiced_slide['caption'] ) ); ?>
								</p>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>

				<?php if ( $juiced_count > 1 ) : ?>
					<div class="mt-5 flex items-center justify-between gap-4">
						<div class="flex flex-wrap gap-2">
							<?php for ( $juiced_d = 0; $juiced_d < $juiced_count; $juiced_d++ ) : ?>
								<button type="button" @click="go(<?php echo (int) $juiced_d; ?>)"
										class="h-2.5 rounded-full transition-all"
										:class="i === <?php echo (int) $juiced_d; ?> ? 'w-8 bg-brand-gold' : 'w-2.5 bg-brand-cream/40 hover:bg-brand-cream/70'"
										:aria-current="i === <?php echo (int) $juiced_d; ?>"
										aria-label="<?php echo esc_attr( sprintf( 'Show photo %d', $juiced_d + 1 ) ); ?>"></button>
							<?php endfor; ?>
						</div>

						<div class="flex gap-2">
							<button type="button" @click="go(i - 1)" aria-label="Previous photo"
									class="grid size-11 place-items-center rounded-full ring-1 ring-brand-cream/40 transition-colors hover:bg-brand-gold hover:text-brand-ink hover:ring-brand-gold">
								<span aria-hidden="true">&larr;</span>
							</button>
							<button type="button" @click="go(i + 1)" aria-label="Next photo"
									class="grid size-11 place-items-center rounded-full ring-1 ring-brand-cream/40 transition-colors hover:bg-brand-gold hover:text-brand-ink hover:ring-brand-gold">
								<span aria-hidden="true">&rarr;</span>
							</button>
						</div>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>

	</div>
</section>
