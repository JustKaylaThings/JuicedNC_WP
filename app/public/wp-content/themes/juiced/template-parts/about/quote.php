<?php
/**
 * About page Section — Community quote.
 *
 * A dark band closing the page before the newsletter: a big orange quote
 * mark, a short testimonial in white, and a green attribution, over a photo
 * that fades in from the right — the hero's construction mirrored, so the
 * page opens and closes on the same dark note.
 *
 * Copy is ACF-editable on the About page (inc/acf-about-page.php); defaults
 * below match the approved mockup. With no image the gradient collapses to
 * solid ink, which still reads correctly.
 *
 * @package Juiced
 */

$quote       = juiced_page_field( 'about_quote_text', 'Juiced! is more than a place to grab a smoothie. It\'s a place where I feel seen, inspired, and connected.' );
$attribution = juiced_page_field( 'about_quote_attribution', 'Community Member' );
$image       = juiced_image_url( juiced_page_field( 'about_quote_image', '' ), 'juiced_hero' );

if ( ! $quote ) {
	return;
}
?>
<section class="relative isolate bg-brand-ink text-brand-cream overflow-hidden bg-grain">

	<?php if ( $image ) : ?>
		<div class="absolute inset-0 -z-10" aria-hidden="true">
			<img src="<?php echo esc_url( $image ); ?>" alt=""
				 class="h-full w-full object-cover object-right" loading="lazy" />
			<!-- Same scrim as the hero: solid behind the copy, clearing by the
				 right edge where the photo shows through. -->
			<div class="absolute inset-0 bg-gradient-to-r from-brand-ink via-brand-ink/85 to-brand-ink/65 md:from-28% md:via-brand-ink/55 md:via-60% md:to-brand-ink/5"></div>
		</div>
	<?php endif; ?>

	<div class="mx-auto max-w-7xl px-4 py-16 md:py-20 min-h-[280px] flex items-center">
		<figure class="flex max-w-2xl gap-4 md:gap-5">

			<svg viewBox="0 0 24 24" width="36" height="36" fill="currentColor"
				 class="mt-1 shrink-0 text-brand-gold" aria-hidden="true">
				<path d="M10.7 5.9 8.5 4.6C5.6 6.4 3.8 9.2 3.8 12.7c0 3 1.9 5.1 4.5 5.1 2.3 0 4.1-1.7 4.1-4.1 0-2.2-1.6-3.9-3.8-3.9-.3 0-.6 0-.9.1.5-1.6 1.6-3 3-4Z" />
				<path d="M20.9 5.9 18.7 4.6c-2.9 1.8-4.7 4.6-4.7 8.1 0 3 1.9 5.1 4.5 5.1 2.3 0 4.1-1.7 4.1-4.1 0-2.2-1.6-3.9-3.8-3.9-.3 0-.6 0-.9.1.5-1.6 1.6-3 3-4Z" />
			</svg>

			<div>
				<blockquote class="text-xl font-semibold leading-snug text-white md:text-2xl">
					<?php echo esc_html( $quote ); ?>
				</blockquote>
				<?php if ( $attribution ) : ?>
					<figcaption class="mt-5 text-sm font-bold text-brand-green md:text-base">
						&ndash; <?php echo esc_html( $attribution ); ?>
					</figcaption>
				<?php endif; ?>
			</div>

		</figure>
	</div>
</section>
