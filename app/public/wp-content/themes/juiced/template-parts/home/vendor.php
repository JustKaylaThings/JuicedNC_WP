<?php
/**
 * Homepage Section — Book Juiced! as a vendor (Oct 2026).
 *
 * For businesses and event organizers who want Juiced! at *their* event —
 * festivals, markets, corporate wellness days, private parties. A warm paper
 * band between the top five and the two neighborhoods: the pitch, a row of
 * event-type chips and a booking button on the left, a pop-up photo
 * gallery on the right (stacked on phones).
 *
 * Different from the Events page "Host Your Event" band, which sells renting
 * the shop itself. The button defaults to the Contact page with the
 * vendor topic preselected (?topic=…, read by template-parts/contact/connect.php).
 *
 * The gallery is one large photo with a row of thumbnails along its bottom
 * edge; a thumbnail swaps the large photo (Alpine), and the photos cycle on
 * their own every 5s — paused for hover, focus, a hidden tab or reduced
 * motion, like the Events page carousel. The large photos are
 * stacked layers with the first on top, so without JS photo 1 still shows.
 * Photos are twelve fixed slots (ACF free has no Repeater); the thumbnail row
 * scrolls sideways when they don't fit, and is dropped with only one photo. Alt text comes from the Media Library. Clearing the
 * heading hides the section.
 *
 * @package Juiced
 */

$juiced_v_heading = juiced_field( 'vendor_heading', 'Bring Juiced! to' );
$juiced_v_accent  = juiced_field( 'vendor_heading_accent', 'your event' );

if ( ! $juiced_v_heading && ! $juiced_v_accent ) {
	return;
}

$juiced_v_eyebrow = juiced_field( 'vendor_eyebrow', 'Pop-ups & vending' );
$juiced_v_body    = juiced_field( 'vendor_body', 'Fresh-pressed juices and smoothies, made on site. We set up at festivals, markets, office wellness days and private celebrations across the Triangle.' );
$juiced_v_types   = array_filter( array_map( 'trim', explode( "\n", (string) juiced_field(
	'vendor_types',
	"Festivals & markets\nCorporate wellness days\nSchool & community events\nPrivate parties"
) ) ) );
$juiced_v_label   = juiced_field( 'vendor_cta_label', 'Book Us as a Vendor' );
$juiced_v_url     = juiced_field(
	'vendor_cta_url',
	add_query_arg( 'topic', rawurlencode( 'Book Us as a Vendor' ), juiced_contact_page_url() ) . '#message'
);

$juiced_v_photos = array();
for ( $juiced_n = 1; $juiced_n <= 12; $juiced_n++ ) {
	$juiced_img = juiced_field( 'vendor_photo_' . $juiced_n, '' );
	$juiced_src = juiced_image_url( $juiced_img, 'large' );
	if ( $juiced_src ) {
		$juiced_v_photos[] = array(
			'src' => $juiced_src,
			'alt' => is_array( $juiced_img ) ? ( $juiced_img['alt'] ?? '' ) : '',
		);
	}
}
$juiced_v_count = count( $juiced_v_photos );
?>
<section class="bg-brand-paper text-brand-ink" aria-labelledby="home-vendor-h">
	<div class="mx-auto grid max-w-7xl items-center gap-10 px-4 py-16 md:py-22 <?php echo $juiced_v_count ? 'lg:grid-cols-[2fr_3fr] lg:gap-14' : ''; ?>">

		<div class="max-w-xl">
			<?php if ( $juiced_v_eyebrow ) : ?>
				<span class="text-xs font-bold uppercase tracking-[.22em] text-brand-forest"><?php echo esc_html( $juiced_v_eyebrow ); ?></span>
			<?php endif; ?>

			<h2 id="home-vendor-h" class="mt-3.5 font-display font-bold leading-none text-brand-forest text-[clamp(2.5rem,5vw,3.75rem)]">
				<?php echo esc_html( $juiced_v_heading ); ?>
				<span class="text-brand-gold"><?php echo esc_html( $juiced_v_accent ); ?></span>
			</h2>

			<?php if ( $juiced_v_body ) : ?>
				<p class="mt-6 text-lg leading-relaxed text-brand-bark/80"><?php echo esc_html( $juiced_v_body ); ?></p>
			<?php endif; ?>

			<?php if ( $juiced_v_types ) : ?>
				<ul class="mt-6 flex flex-wrap gap-2.5">
					<?php foreach ( $juiced_v_types as $juiced_type ) : ?>
						<li class="rounded-full bg-brand-cream px-4 py-2 text-sm font-semibold text-brand-forest ring-1 ring-brand-forest/15">
							<?php echo esc_html( $juiced_type ); ?>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<?php if ( $juiced_v_url && $juiced_v_label ) : ?>
				<a href="<?php echo esc_url( $juiced_v_url ); ?>"
				   class="mt-8 inline-flex items-center gap-2 rounded-full bg-brand-forest px-6.5 py-4 font-bold text-brand-cream transition-colors hover:bg-brand-rust">
					<?php echo esc_html( $juiced_v_label ); ?>
					<span aria-hidden="true">&rarr;</span>
				</a>
			<?php endif; ?>
		</div>

		<?php if ( $juiced_v_count ) : ?>
			<div class="relative aspect-[5/4] overflow-hidden rounded-[22px] bg-brand-clay"
				 x-data="{
					i: 0,
					n: <?php echo (int) $juiced_v_count; ?>,
					paused: false,
					show(k) {
						this.i = (k + this.n) % this.n;
						// Centre the active thumbnail by scrolling the strip only — never the page.
						const strip = this.$refs.strip, t = strip && strip.children[this.i];
						if (t) strip.scrollTo({ left: t.offsetLeft - (strip.clientWidth - t.offsetWidth) / 2, behavior: 'smooth' });
					},
					init() {
						if (this.n < 2 || window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
						setInterval(() => { if (!this.paused && !document.hidden) this.show(this.i + 1); }, 5000);
					}
				 }"
				 @mouseenter="paused = true" @mouseleave="paused = false"
				 @focusin="paused = true" @focusout="paused = false">
				<?php foreach ( $juiced_v_photos as $juiced_i => $juiced_photo ) : ?>
					<img src="<?php echo esc_url( $juiced_photo['src'] ); ?>"
						 alt="<?php echo esc_attr( $juiced_photo['alt'] ); ?>"
						 class="absolute inset-0 h-full w-full object-cover object-[50%_20%] transition-opacity duration-300"
						 style="z-index:<?php echo (int) ( $juiced_v_count - $juiced_i ); ?>"
						 :class="i === <?php echo (int) $juiced_i; ?> ? 'opacity-100 z-10!' : 'opacity-0'"
						 :aria-hidden="(i !== <?php echo (int) $juiced_i; ?>).toString()"
						 loading="lazy" />
				<?php endforeach; ?>

				<?php if ( $juiced_v_count > 1 ) : ?>
					<div x-ref="strip" class="absolute inset-x-0 bottom-0 z-20 flex gap-2 overflow-x-auto bg-gradient-to-t from-brand-ink/70 to-transparent p-3 pt-12 [scrollbar-width:none] sm:gap-3 sm:p-4 sm:pt-16 [&::-webkit-scrollbar]:hidden">
						<?php foreach ( $juiced_v_photos as $juiced_i => $juiced_photo ) : ?>
							<button type="button" @click="show(<?php echo (int) $juiced_i; ?>)"
									:aria-pressed="(i === <?php echo (int) $juiced_i; ?>).toString()"
									aria-label="<?php echo esc_attr( sprintf( 'Show photo %d', $juiced_i + 1 ) ); ?>"
									class="size-12 shrink-0 overflow-hidden rounded-lg ring-2 ring-inset transition sm:size-16"
									:class="i === <?php echo (int) $juiced_i; ?> ? 'ring-brand-gold' : 'ring-brand-cream/60 opacity-75 hover:opacity-100'">
								<img src="<?php echo esc_url( $juiced_photo['src'] ); ?>" alt="" class="h-full w-full object-cover" loading="lazy" />
							</button>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>

	</div>
</section>
