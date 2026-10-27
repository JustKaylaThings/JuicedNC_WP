<?php
/**
 * Homepage Section 2 — Hero ("Garden" redesign, Sep 2026).
 *
 * Deep-green band that continues the header: a huge three-line headline
 * ("Blending / Health & / Community.", mint, cream, orange), a short
 * intro with two buttons (top right on wide screens, under the headline below
 * that), a row of four captioned community photos along the bottom edge, and
 * a scrolling orange ticker of weekly events. The faint leaf pattern (.bg-garden,
 * src/tailwind.css) continues from the header above; -mt-px tucks the hero
 * under the header so no hairline of the page background shows at the seam.
 * Full width, like the homepage header bar (header.php): no 1280px column.
 * The two buttons are "stickers" (ink outline, hard ink shadow that presses
 * down on hover) so they hold up against the green.
 *
 * All copy, photos and ticker items are ACF-editable on the Home page
 * (inc/acf-homepage.php); defaults match the approved mockup. Photo slots with
 * no image show a soft green placeholder so the row keeps its shape.
 *
 * @package Juiced
 */

$pre       = juiced_field( 'hero_headline_pre', 'Blending' );
$lead      = juiced_field( 'hero_headline_lead', 'Health &' );
$accent    = juiced_field( 'hero_headline_accent', 'Community.' );
$subtext   = juiced_field( 'hero_subtext', 'Your neighborhood juice bar. Serving our Southeast Raleigh community with fresh juices, smoothies, herbal teas, and much more.' );
$primary   = juiced_field( 'hero_primary_label', 'Join us this week' );
$primary_u = juiced_field( 'hero_primary_url', juiced_events_page_url() );
$second    = juiced_field( 'hero_secondary_label', 'Order' );

$default_captions = array( 'Regulars', 'Open Mic Thursdays', 'Game Night', 'Meet the owners' );
// Staggered heights (mobile / desktop) so the photo tops step like the mockup.
// From lg they scale with the window height (vh), with the headline, so the
// whole hero (photos and ticker included) fits on a laptop screen.
$heights = array( 'h-48 lg:h-[clamp(180px,25vh,260px)]', 'h-56 lg:h-[clamp(210px,31vh,330px)]', 'h-52 lg:h-[clamp(195px,27.5vh,290px)]', 'h-60 lg:h-[clamp(220px,33vh,350px)]' );
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

$ticker = juiced_field( 'hero_ticker', "Open Mic Thursdays\nIsekai Nights\nWork Remote Mondays\nBoard Game Nights\nFresh Juice\nSea Moss" );
$ticker = array_values( array_filter( array_map( 'trim', preg_split( '/\R/', (string) $ticker ) ) ) );
?>
<section class="relative -mt-px bg-brand-forest bg-garden text-brand-cream overflow-hidden">
	<div class="px-4 md:px-14 lg:px-20 xl:px-28 2xl:px-[max(10rem,calc(50vw-40rem))] pt-6 md:pt-10">

		<div class="xl:relative">
			<h1 class="font-display font-bold leading-[.86] tracking-[-.03em] [text-shadow:0_.025em_0_var(--color-brand-ink)] text-[clamp(3.5rem,13vw,11.75rem)] lg:text-[clamp(4.5rem,min(10.6vw,12.5vh),9.75rem)]">
				<?php if ( $pre ) : ?>
					<span class="block text-brand-mint"><?php echo esc_html( $pre ); ?></span>
				<?php endif; ?>
				<span class="block"><?php echo esc_html( $lead ); ?></span>
				<span class="block text-brand-gold"><?php echo esc_html( $accent ); ?></span>
			</h1>

			<div class="mt-8 flex flex-col gap-6 max-w-xl xl:mt-0 xl:max-w-none xl:absolute xl:top-1/2 xl:-translate-y-1/2 xl:right-0 xl:w-[min(540px,33vw)]">
				<?php if ( $subtext ) : ?>
					<p class="text-lg md:text-xl leading-relaxed font-medium"><?php echo esc_html( $subtext ); ?></p>
				<?php endif; ?>
				<div class="flex flex-wrap gap-3">
					<?php if ( $primary ) : ?>
						<a href="<?php echo esc_url( $primary_u ); ?>"
						   class="inline-flex items-center min-h-14 rounded-full px-7 text-xl text-brand-ink bg-brand-gold btn-sticker">
							<?php echo esc_html( $primary ); ?>
						</a>
					<?php endif; ?>
					<?php if ( $second ) : ?>
						<?php juiced_order_button( $second, 'inline-flex items-center gap-2 min-h-14 rounded-full px-7 text-xl text-brand-ink bg-brand-cream btn-sticker' ); ?>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<!-- Community photos: a swipeable row on phones, a 4-up grid from lg. -->
		<div class="mt-10 lg:mt-8 -mx-4 px-4 lg:mx-0 lg:px-0 flex items-end gap-3 overflow-x-auto no-scrollbar snap-x lg:grid lg:grid-cols-4 lg:gap-4 lg:overflow-visible">
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
