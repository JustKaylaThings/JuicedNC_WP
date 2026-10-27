<?php
/**
 * Shared page hero — a compact band like the Events hero: two-tone headline
 * with the intro under it, over an optional full-bleed photo that fades into
 * the deep-green "Garden" background.
 *
 * Same construction as the Contact, About and Events heroes (the header's leaf
 * pattern runs on behind it; -mt-px hides the seam), pulled into one partial
 * for the Menu page. Pass the copy as $args:
 *
 *   get_template_part( 'template-parts/page-hero', null, array(
 *       'lead'    => 'Host Your Event', // cream line
 *       'accent'  => 'at Juiced!',      // orange line
 *       'subtext' => '…',
 *       'image'   => $acf_image,        // ACF image array, ID or URL; optional
 *       'one_line' => true,             // lead and accent on one line; optional
 *   ) );
 *
 * "Juiced!" in either line shows as the logo (juiced_logo_text()).
 *
 * @package Juiced
 */

$lead    = isset( $args['lead'] ) ? $args['lead'] : '';
$accent  = isset( $args['accent'] ) ? $args['accent'] : '';
$subtext = isset( $args['subtext'] ) ? $args['subtext'] : '';
$line    = ! empty( $args['one_line'] ) ? 'inline' : 'block';
$image   = juiced_image_url( isset( $args['image'] ) ? $args['image'] : '', 'juiced_hero' );
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

	<!-- Same compact band as the Events hero (template-parts/events/hero.php),
		 whose long headline pushes the subtext onto its own row under it. Here
		 the subtext always sits under the headline so the two pages match, and
		 the min-heights hold the band to the Events height (256px phone /
		 246px desktop) when the headline is short. -->
	<div class="mx-auto flex min-h-[256px] max-w-7xl flex-col justify-end gap-6 px-4 py-12 md:min-h-[246px] md:py-16">
		<h1 class="font-display font-bold leading-[.92] tracking-[-.03em] text-[clamp(2.75rem,7vw,4.5rem)]">
			<?php if ( $lead ) : ?>
				<span class="<?php echo esc_attr( $line ); ?>"><?php echo juiced_logo_text( $lead ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in the helper. ?></span>
			<?php endif; ?>
			<?php if ( $accent ) : ?>
				<span class="<?php echo esc_attr( $line ); ?> text-brand-gold"><?php echo juiced_logo_text( $accent ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in the helper. ?></span>
			<?php endif; ?>
		</h1>

		<?php if ( $subtext ) : ?>
			<p class="max-w-md text-lg md:text-xl font-medium leading-relaxed text-brand-mint md:max-w-none">
				<?php echo esc_html( $subtext ); ?>
			</p>
		<?php endif; ?>
	</div>
</section>
