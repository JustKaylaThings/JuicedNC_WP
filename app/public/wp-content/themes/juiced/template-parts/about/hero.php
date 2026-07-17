<?php
/**
 * About page Section 1 — Hero.
 *
 * Two-tone headline and an intro paragraph over a full-bleed photo of the shop
 * that fades into the dark background from the left — the same construction as
 * the events and herbs heroes, so the pages stay visually of a piece. No search
 * or CTA here; the mockup's hero is purely the statement. Copy is ACF-editable
 * on the About page (inc/acf-about-page.php); defaults below match the approved
 * mockup so the section renders before anything is set. With no image the
 * gradient collapses to solid ink, which still reads correctly.
 *
 * @package Juiced
 */

$lead    = juiced_page_field( 'about_hero_lead', 'More Than a Juice Bar.' );
$accent  = juiced_page_field( 'about_hero_accent', 'We\'re a Community.' );
$subtext = juiced_page_field( 'about_hero_subtext', 'Juiced! Juice Bar was created to nourish more than just the body. We\'re here to bring people together, inspire healthy habits, and uplift Raleigh — one smoothie, one smile, and one connection at a time.' );
$image   = juiced_image_url( juiced_page_field( 'about_hero_image', '' ), 'juiced_hero' );
?>
<section class="relative isolate bg-brand-ink text-brand-cream overflow-hidden bg-grain">

	<?php if ( $image ) : ?>
		<div class="absolute inset-0 -z-10" aria-hidden="true">
			<img src="<?php echo esc_url( $image ); ?>" alt=""
				 class="h-full w-full object-cover object-center" loading="eager" fetchpriority="high" />
			<!-- Left-to-right scrim: solid behind the copy, clearing by the right
				 edge. Heavier on mobile, where the text sits over the image. -->
			<div class="absolute inset-0 bg-gradient-to-r from-brand-ink via-brand-ink/85 to-brand-ink/65 md:from-28% md:via-brand-ink/55 md:via-60% md:to-brand-ink/5"></div>
		</div>
	<?php endif; ?>

	<div class="mx-auto max-w-7xl px-4 py-16 md:py-20 min-h-[380px] md:min-h-[440px] flex items-center">
		<div class="max-w-xl">

			<h1 class="font-display text-5xl md:text-6xl leading-[1.03]">
				<span class="block text-white"><?php echo esc_html( $lead ); ?></span>
				<span class="block text-brand-gold"><?php echo esc_html( $accent ); ?></span>
			</h1>

			<?php if ( $subtext ) : ?>
				<p class="mt-6 max-w-md text-base md:text-lg text-brand-cream/80 leading-relaxed">
					<?php echo esc_html( $subtext ); ?>
				</p>
			<?php endif; ?>

		</div>
	</div>
</section>
