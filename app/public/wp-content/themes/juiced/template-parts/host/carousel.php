<?php
/**
 * Host an Event — photo carousel of the Hill Street space.
 *
 * Sits at the top of the left column, above Good to Know (pulled in by
 * template-parts/host/form.php). Same build as the Events page carousel
 * (template-parts/events/community.php): a native scroll-snap track, so it
 * swipes on touch and works with no JS; Alpine adds the arrows, dots and a
 * slow autoplay that stops for hover, focus or reduced motion. Colored for
 * the cream band rather than green.
 *
 * Photos are fixed slots (ACF free has no Repeater) on the Host an Event page
 * (inc/acf-host-event-page.php); alt text and captions come from the Media
 * Library. No photos → prints nothing.
 *
 * @package Juiced
 */

$photos = array();
for ( $i = 1; $i <= 8; $i++ ) {
	$img = juiced_page_field( "host_space_photo{$i}", '' );
	$src = juiced_image_url( $img, 'large' );
	if ( $src ) {
		$photos[] = array(
			'src'     => $src,
			'alt'     => is_array( $img ) ? ( $img['alt'] ?? '' ) : '',
			'caption' => is_array( $img ) ? ( $img['caption'] ?? '' ) : '',
		);
	}
}

$count = count( $photos );
if ( ! $count ) {
	return;
}
?>
<div class="mb-12 min-w-0"
	 role="region" aria-roledescription="carousel" aria-label="<?php esc_attr_e( 'Photos of the space', 'juiced' ); ?>"
	 x-data="{
		i: 0,
		n: <?php echo (int) $count; ?>,
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
		class="flex snap-x snap-mandatory overflow-x-auto rounded-3xl [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
		<?php foreach ( $photos as $n => $photo ) : ?>
			<li class="relative w-full shrink-0 snap-start"
				aria-roledescription="slide"
				aria-label="<?php echo esc_attr( sprintf( '%d of %d', $n + 1, $count ) ); ?>">
				<img src="<?php echo esc_url( $photo['src'] ); ?>"
					 alt="<?php echo esc_attr( $photo['alt'] ); ?>"
					 class="aspect-[4/3] w-full object-cover"
					 <?php echo $n ? 'loading="lazy"' : ''; ?> />
				<?php if ( $photo['caption'] ) : ?>
					<p class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-brand-ink/80 to-transparent px-5 pb-4 pt-12 text-sm font-semibold text-brand-cream">
						<?php echo esc_html( wp_strip_all_tags( $photo['caption'] ) ); ?>
					</p>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
	</ul>

	<?php if ( $count > 1 ) : ?>
		<div class="mt-4 flex items-center justify-between gap-4">
			<div class="flex flex-wrap gap-2">
				<?php for ( $d = 0; $d < $count; $d++ ) : ?>
					<button type="button" @click="go(<?php echo (int) $d; ?>)"
							class="h-2.5 cursor-pointer rounded-full transition-all"
							:class="i === <?php echo (int) $d; ?> ? 'w-8 bg-brand-gold' : 'w-2.5 bg-brand-forest/25 hover:bg-brand-forest/50'"
							:aria-current="i === <?php echo (int) $d; ?>"
							aria-label="<?php echo esc_attr( sprintf( 'Show photo %d', $d + 1 ) ); ?>"></button>
				<?php endfor; ?>
			</div>

			<div class="flex gap-2">
				<button type="button" @click="go(i - 1)" aria-label="<?php esc_attr_e( 'Previous photo', 'juiced' ); ?>"
						class="grid size-11 cursor-pointer place-items-center rounded-full text-brand-forest ring-1 ring-brand-forest/30 transition-colors hover:bg-brand-forest hover:text-brand-cream hover:ring-brand-forest">
					<span aria-hidden="true">&larr;</span>
				</button>
				<button type="button" @click="go(i + 1)" aria-label="<?php esc_attr_e( 'Next photo', 'juiced' ); ?>"
						class="grid size-11 cursor-pointer place-items-center rounded-full text-brand-forest ring-1 ring-brand-forest/30 transition-colors hover:bg-brand-forest hover:text-brand-cream hover:ring-brand-forest">
					<span aria-hidden="true">&rarr;</span>
				</button>
			</div>
		</div>
	<?php endif; ?>
</div>
