<?php
/**
 * Contact page Section 1 — Hero.
 *
 * Two-tone headline ("We'd Love to / Hear From You!") and an intro paragraph
 * over a full-bleed photo that fades into the deep-green "Garden" background
 * from the left (the header's leaf pattern runs on behind it; -mt-px hides the
 * seam) — the same construction as the About and Events heroes, so the pages
 * stay visually of a piece. Copy is ACF-editable on the Contact page
 * (inc/acf-contact-page.php); defaults below match the approved mockup so the
 * section renders before anything is set. With no image the gradient collapses
 * to the plain leafy green, which still reads correctly.
 *
 * @package Juiced
 */

$lead    = juiced_page_field( 'contact_hero_lead', 'We\'d Love to' );
$accent  = juiced_page_field( 'contact_hero_accent', 'Hear From You!' );
$subtext = juiced_page_field( 'contact_hero_subtext', 'Have a question, suggestion, or just want to say hello? We\'re here for you. Reach out and our team will get back to you soon!' );
$image   = juiced_image_url( juiced_page_field( 'contact_hero_image', '' ), 'juiced_hero' );
?>
<section class="relative isolate -mt-px bg-brand-forest bg-garden text-brand-cream overflow-hidden">

	<?php if ( $image ) : ?>
		<div class="absolute inset-0 -z-10" aria-hidden="true">
			<img src="<?php echo esc_url( $image ); ?>" alt=""
				 class="h-full w-full object-cover object-center" loading="eager" fetchpriority="high" />
			<!-- Left-to-right scrim: solid behind the copy, clearing by the right
				 edge. Heavier on mobile, where the text sits over the image. -->
			<div class="absolute inset-0 bg-gradient-to-r from-brand-forest via-brand-forest/85 to-brand-forest/65 md:from-28% md:via-brand-forest/55 md:via-60% md:to-brand-forest/5"></div>
		</div>
	<?php endif; ?>

	<div class="mx-auto max-w-7xl px-4 py-16 md:py-20 min-h-[380px] md:min-h-[440px] flex items-center">
		<div class="max-w-xl">

			<h1 class="font-display font-bold leading-[.9] tracking-[-.03em] text-[clamp(3rem,9vw,4.5rem)] md:text-[clamp(2.75rem,5.2vw,4.5rem)]">
				<span class="block"><?php echo esc_html( $lead ); ?></span>
				<span class="block text-brand-gold"><?php echo esc_html( $accent ); ?></span>
			</h1>

			<?php if ( $subtext ) : ?>
				<p class="mt-6 max-w-md text-lg md:text-xl font-medium leading-relaxed">
					<?php echo esc_html( $subtext ); ?>
				</p>
			<?php endif; ?>

		</div>
	</div>
</section>
