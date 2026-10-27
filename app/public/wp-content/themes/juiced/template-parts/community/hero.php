<?php
/**
 * Community page Section 1 — Hero.
 *
 * Deep-green "Garden" band (the header's leaf pattern runs on behind it;
 * -mt-px hides the seam) with the two-tone headline and subtext, over an
 * optional photo that fades in from the right — the same construction as the
 * Events and Locations heroes, so the page sits with the rest of the site.
 *
 * @package Juiced
 */

$lead    = juiced_page_field( 'community_hero_lead', 'Raleigh Is' );
$accent  = juiced_page_field( 'community_hero_accent', 'Our Community.' );
$subtext = juiced_page_field( 'community_hero_subtext', 'Every month we spotlight the people and local businesses who make this city feel like home. Pull up a chair and meet your neighbors.' );
$image   = juiced_image_url( juiced_page_field( 'community_hero_image', '' ), 'juiced_hero' );
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

	<div class="mx-auto flex min-h-[340px] max-w-7xl items-center px-4 py-14 md:min-h-[400px] md:py-16">
		<div class="max-w-xl">
			<h1 class="font-display font-bold leading-[.92] tracking-[-.03em] text-[clamp(2.75rem,7vw,4.5rem)]">
				<span class="block"><?php echo esc_html( $lead ); ?></span>
				<span class="block text-brand-gold"><?php echo esc_html( $accent ); ?></span>
			</h1>

			<?php if ( $subtext ) : ?>
				<p class="mt-6 max-w-md text-lg md:text-xl font-medium leading-relaxed text-brand-mint">
					<?php echo esc_html( $subtext ); ?>
				</p>
			<?php endif; ?>
		</div>
	</div>
</section>
