<?php
/**
 * Homepage Section 2 — Hero ("Garden" redesign, Sep 2026).
 *
 * Deep-green band that continues the header: a huge two-tone headline, a short
 * intro with two buttons (top right on wide screens, under the headline below
 * that), a row of four captioned community photos along the bottom edge, and
 * a scrolling orange ticker of weekly events.
 *
 * All copy, photos and ticker items are ACF-editable on the Home page
 * (inc/acf-homepage.php); defaults match the approved mockup. Photo slots with
 * no image show a soft green placeholder so the row keeps its shape.
 *
 * @package Juiced
 */

$lead      = juiced_field( 'hero_headline_lead', 'Health &' );
$accent    = juiced_field( 'hero_headline_accent', 'Community.' );
$subtext   = juiced_field( 'hero_subtext', 'Two Raleigh neighborhoods. One Juiced! family. Fresh juice, real people, and something happening every week.' );
$primary   = juiced_field( 'hero_primary_label', 'Join us this week' );
$primary_u = juiced_field( 'hero_primary_url', juiced_events_page_url() );
$second    = juiced_field( 'hero_secondary_label', 'Order' );
$second_u  = juiced_field( 'order_ahead_url', '#' );

$default_captions = array( 'Regulars', 'Open Mic Thursdays', 'Game Night', 'Meet the owners' );
// Staggered heights (mobile / desktop) so the photo tops step like the mockup.
$heights = array( 'h-48 lg:h-[260px]', 'h-56 lg:h-[330px]', 'h-52 lg:h-[290px]', 'h-60 lg:h-[350px]' );
$photos  = array();
foreach ( $default_captions as $i => $default_caption ) {
	$n     = $i + 1;
	$image = juiced_field( "hero_photo_{$n}", '' );
	$photos[] = array(
		'url'     => juiced_image_url( $image, 'juiced_card' ),
		'alt'     => is_array( $image ) ? ( $image['alt'] ?? '' ) : '',
		'caption' => juiced_field( "hero_caption_{$n}", $default_caption ),
		'height'  => $heights[ $i ],
	);
}

$ticker = juiced_field( 'hero_ticker', "Open Mic Thursdays\nIsekai Nights\nWork Remote Mondays\nBoard Game Nights\nFresh Juice\nSea Moss\nHerb Library" );
$ticker = array_values( array_filter( array_map( 'trim', preg_split( '/\R/', (string) $ticker ) ) ) );
?>
<section class="relative bg-brand-forest text-brand-cream overflow-hidden">
	<div class="mx-auto max-w-7xl px-4 pt-6 md:pt-10">

		<div class="xl:relative">
			<h1 class="font-display font-bold leading-[.86] tracking-[-.03em] text-[clamp(3.5rem,13vw,11.75rem)] xl:text-[min(10.5rem,11.5vw)]">
				<span class="block"><?php echo esc_html( $lead ); ?></span>
				<span class="block text-brand-gold"><?php echo esc_html( $accent ); ?></span>
			</h1>

			<div class="mt-8 flex flex-col gap-5 max-w-xl xl:mt-0 xl:absolute xl:top-8 xl:right-0 xl:w-[360px]">
				<?php if ( $subtext ) : ?>
					<p class="text-lg md:text-xl leading-relaxed font-medium"><?php echo esc_html( $subtext ); ?></p>
				<?php endif; ?>
				<div class="flex flex-wrap gap-3">
					<?php if ( $primary ) : ?>
						<a href="<?php echo esc_url( $primary_u ); ?>"
						   class="inline-flex items-center rounded-full bg-brand-gold px-6 py-4 font-bold text-brand-ink hover:bg-brand-cream transition-colors">
							<?php echo esc_html( $primary ); ?>
						</a>
					<?php endif; ?>
					<?php if ( $second ) : ?>
						<a href="<?php echo esc_url( $second_u ); ?>"
						   class="inline-flex items-center rounded-full border-2 border-brand-cream px-6 py-3.5 font-semibold text-brand-cream hover:bg-brand-cream hover:text-brand-forest transition-colors">
							<?php echo esc_html( $second ); ?>
						</a>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<!-- Community photos: a swipeable row on phones, a 4-up grid from lg. -->
		<div class="mt-10 lg:mt-16 -mx-4 px-4 lg:mx-0 lg:px-0 flex items-end gap-3 overflow-x-auto no-scrollbar snap-x lg:grid lg:grid-cols-4 lg:gap-4 lg:overflow-visible">
			<?php foreach ( $photos as $photo ) : ?>
				<figure class="relative m-0! w-[62%] sm:w-[40%] shrink-0 snap-start lg:w-auto overflow-hidden rounded-t-3xl border-4 border-b-0 border-white <?php echo esc_attr( $photo['height'] ); ?>">
					<?php if ( $photo['url'] ) : ?>
						<img src="<?php echo esc_url( $photo['url'] ); ?>" alt="<?php echo esc_attr( $photo['alt'] ); ?>"
							 class="h-full w-full object-cover" loading="eager" />
					<?php else : ?>
						<div class="h-full w-full bg-brand-mint/20" aria-hidden="true"></div>
					<?php endif; ?>
					<?php if ( $photo['caption'] ) : ?>
						<figcaption class="absolute left-3.5 bottom-3.5 rounded-full bg-brand-gold px-3 py-1.5 text-[13px] font-bold text-brand-ink">
							<?php echo esc_html( $photo['caption'] ); ?>
						</figcaption>
					<?php endif; ?>
				</figure>
			<?php endforeach; ?>
		</div>

	</div>

	<?php if ( $ticker ) : ?>
		<!-- Ticker: the list is printed twice so the CSS loop is seamless; the
			 copy is hidden from screen readers. -->
		<div class="bg-brand-gold text-brand-ink overflow-hidden">
			<div class="juiced-marquee flex w-max">
				<?php for ( $copy = 0; $copy < 2; $copy++ ) : ?>
					<ul class="flex shrink-0 items-center gap-7 py-4 md:py-5 pl-7 font-display font-semibold text-xl md:text-[26px] whitespace-nowrap" <?php echo $copy ? 'aria-hidden="true"' : ''; ?>>
						<?php foreach ( $ticker as $k => $item ) : ?>
							<li><?php echo esc_html( $item ); ?></li>
							<li aria-hidden="true" class="<?php echo $k % 2 ? 'text-brand-cream' : 'text-brand-forest'; ?>">&#9679;</li>
						<?php endforeach; ?>
					</ul>
				<?php endfor; ?>
			</div>
		</div>
	<?php endif; ?>
</section>
