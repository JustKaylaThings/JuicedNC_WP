<?php
/**
 * Events page Section 1 — Hero.
 *
 * A compact band: the two-tone headline on the left, subtext on the right,
 * over an optional event photo that fades into the deep-green "Garden"
 * background from the left (the header's leaf pattern runs on behind it;
 * -mt-px hides the seam). Kept short so the planner below — the reason people
 * come to this page — starts near the top of the screen. Copy is ACF-editable
 * on the Events page (inc/acf-events-page.php); defaults below match the
 * approved design so the section renders before anything is set.
 *
 * "Juiced!" in the copy shows as the logo (juiced_logo_text()).
 *
 * @package Juiced
 */

$lead    = juiced_page_field( 'events_hero_lead', 'Events that' );
$accent  = juiced_page_field( 'events_hero_accent', 'Fuel Community' );
$subtext = juiced_page_field( 'events_hero_subtext', "Juiced! is where Raleigh comes together for healthy fun." );
$image   = juiced_image_url( juiced_page_field( 'events_hero_image', '' ), 'juiced_hero' );
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

	<div class="mx-auto flex max-w-7xl flex-wrap items-end justify-between gap-x-12 gap-y-6 px-4 py-12 md:py-16">
		<h1 class="font-display font-bold leading-[.92] tracking-[-.03em] text-[clamp(2.75rem,7vw,4.5rem)]">
			<?php echo esc_html( $lead ); ?>
			<span class="text-brand-gold"><?php echo esc_html( $accent ); ?></span>
		</h1>

		<?php if ( $subtext ) : ?>
			<p class="max-w-md text-[17px] font-medium leading-relaxed text-brand-mint">
				<?php echo juiced_logo_text( $subtext, 'inline-block h-[1.9em] w-auto -my-[.5em] align-middle' ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in the helper. ?>
			</p>
		<?php endif; ?>
	</div>
</section>
