<?php
/**
 * About page Section 1 — Hero.
 *
 * Two-tone headline and an intro paragraph over a full-bleed photo of the shop
 * that fades into the deep-green "Garden" background from the left (the
 * header's leaf pattern runs on behind it) — the same construction as the
 * events hero, so the pages stay visually of a piece. No search
 * or CTA here; the mockup's hero is purely the statement. Copy is ACF-editable
 * on the About page (inc/acf-about-page.php); defaults below match the approved
 * mockup so the section renders before anything is set. With no image the
 * gradient collapses to the plain leafy green, which still reads correctly.
 * The full Juiced! wordmark (assets/images/juiced-logo, 1328px wide, so it
 * stays sharp on Retina) sits centred in the space right of the copy from
 * tablet up; it's left off phones, where the copy fills the width.
 *
 * "Juiced!" in the copy shows as the logo (juiced_logo_text()).
 *
 * @package Juiced
 */

$lead    = juiced_page_field( 'about_hero_lead', 'More Than a Juice Bar.' );
$accent  = juiced_page_field( 'about_hero_accent', 'We\'re a Community.' );
$subtext = juiced_page_field( 'about_hero_subtext', 'Juiced! Juice Bar was created to nourish more than just the body. We\'re here to bring people together, inspire healthy habits, and uplift Raleigh — one smoothie, one smile, and one connection at a time.' );
$image   = juiced_image_url( juiced_page_field( 'about_hero_image', '' ), 'juiced_hero' );
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

	<div class="mx-auto max-w-7xl px-4 py-16 md:py-20 min-h-[380px] md:min-h-[440px] flex items-center gap-12">
		<div class="max-w-xl">

			<h1 class="font-display font-bold leading-[.9] tracking-[-.03em] text-[clamp(3rem,9vw,4.5rem)] md:text-[clamp(2.75rem,5.2vw,4.5rem)]">
				<span class="block"><?php echo esc_html( $lead ); ?></span>
				<span class="block text-brand-gold"><?php echo esc_html( $accent ); ?></span>
			</h1>

			<?php if ( $subtext ) : ?>
				<p class="mt-6 max-w-md text-lg md:text-xl font-medium leading-relaxed">
					<?php echo juiced_logo_text( $subtext, 'inline-block h-[1.9em] w-auto -my-[.5em] align-middle' ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in the helper. ?>
				</p>
			<?php endif; ?>

		</div>

		<div class="hidden md:flex flex-1 justify-center">
			<?php echo juiced_logo( 'block h-auto w-full', true, 'w-full max-w-sm lg:max-w-lg' ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in the helper. ?>
		</div>
	</div>
</section>
