<?php
/**
 * Locations page Section 1 — Hero.
 *
 * Dark band opening the page: two-tone headline ("Two Locations." white,
 * "One Community." orange), an intro paragraph, and a row of small icon
 * badges — with the two storefront photos bleeding to the viewport edge on
 * the right, cut along parallel diagonals (clip-path) like the mockup, each
 * wearing a location pill (slot 1 green, slot 2 pink).
 *
 * ACF is free (no repeaters), so the badges are a textarea, one per line;
 * the icon is keyword-matched from the line via the local map below
 * (fresh/ingredients → leaf, health → heart, community → people). The photos
 * are two fixed slots; a slot with no image is skipped, and with neither
 * photo the copy stands alone on solid ink — same doctrine as the other
 * heroes.
 *
 * Copy is ACF-editable on the Locations page (inc/acf-locations-page.php);
 * defaults below match the approved mockup.
 *
 * @package Juiced
 */

$lead    = juiced_page_field( 'locations_hero_lead', 'Two Locations.' );
$accent  = juiced_page_field( 'locations_hero_accent', 'One Community.' );
$subtext = juiced_page_field( 'locations_hero_subtext', 'We\'re proud to call Raleigh home. Stop by one of our juice bars to sip, connect, and feel your best.' );

$badges_raw = juiced_page_field(
	'locations_hero_badges',
	"Fresh Ingredients\nHealthy Options\nCommunity Focused"
);

$badges = array();
foreach ( preg_split( '/\r\n|\r|\n/', (string) $badges_raw ) as $line ) {
	$line = trim( $line );
	if ( '' !== $line ) {
		$badges[] = $line;
	}
}

// Storefront photos — one fixed slot per location, pill colors from the
// mockup (North Raleigh green, Downtown Raleigh pink).
$pill_colors = array(
	1 => 'bg-brand-green',
	2 => 'bg-[#dd1f69]',
);

$photos = array();
foreach ( range( 1, 2 ) as $i ) {
	$image = juiced_image_url( juiced_page_field( "locations_hero_image{$i}", '' ), 'juiced_hero' );
	if ( ! $image ) {
		continue;
	}
	$photos[] = array(
		'image' => $image,
		'label' => juiced_page_field( "locations_hero_label{$i}", 1 === $i ? 'North Raleigh' : 'Downtown Raleigh' ),
		'pill'  => $pill_colors[ $i ],
	);
}

// Diagonal cuts and pill anchors, chosen by how many photos are set. The
// clip-path values are literal strings so the Tailwind scanner sees them;
// all the edges share the same slope so the cuts read as parallel. With two
// photos the sliver of dark background left between the polygons is the
// diagonal seam.
if ( 2 === count( $photos ) ) {
	$photos[0]['clip']  = '[clip-path:polygon(0_0,54%_0,46%_100%,0_100%)] md:[clip-path:polygon(12%_0,56%_0,46%_100%,2%_100%)]';
	$photos[0]['pos']   = 'left-4 md:left-[5%]';
	// Slide the image left within its clipped panel: the panel only shows the
	// box's left ~56%, so the translate pulls the hidden right side of the
	// photo into view (object-position can't — cover leaves no horizontal
	// overflow for this image's aspect ratio).
	$photos[0]['focus'] = '-translate-x-[15%]';
	$photos[1]['clip'] = '[clip-path:polygon(55%_0,100%_0,100%_100%,47%_100%)] md:[clip-path:polygon(57%_0,100%_0,100%_100%,47%_100%)]';
	$photos[1]['pos']  = 'left-[50%]';
} elseif ( $photos ) {
	$photos[0]['clip'] = 'md:[clip-path:polygon(12%_0,100%_0,100%_100%,2%_100%)]';
	$photos[0]['pos']  = 'left-4 md:left-[5%]';
}

/**
 * Icon for a hero badge, keyword-matched from its text (first hit wins) —
 * the same convention as the About page's We Believe In cards.
 */
$juiced_badge_icons = array(
	'fresh'      => array(
		'text' => 'text-brand-green',
		'icon' => '<svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 20v-8" /><path d="M12 12C12 8.5 9.5 6 5.5 6 5.5 9.5 8 12 12 12Z" /><path d="M12 12c0-3.5 2.5-6 6.5-6 0 3.5-2.5 6-6.5 6Z" /></svg>',
	),
	'ingredient' => array(
		'text' => 'text-brand-green',
		'icon' => '<svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 20v-8" /><path d="M12 12C12 8.5 9.5 6 5.5 6 5.5 9.5 8 12 12 12Z" /><path d="M12 12c0-3.5 2.5-6 6.5-6 0 3.5-2.5 6-6.5 6Z" /></svg>',
	),
	'health'     => array(
		'text' => 'text-[#dd1f69]',
		'icon' => '<svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 20.5 4.7 13a5 5 0 1 1 7.3-6.8A5 5 0 1 1 19.3 13Z" /></svg>',
	),
	'communi'    => array(
		'text' => 'text-brand-yellow',
		'icon' => '<svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="9" cy="8" r="3" /><path d="M4 19c0-3 2.2-5 5-5s5 2 5 5" /><circle cx="16.5" cy="9" r="2.5" /><path d="M16.5 14.5c2.4.3 3.5 2 3.5 4.5" /></svg>',
	),
);

$juiced_badge_fallback = array(
	'text' => 'text-brand-green',
	'icon' => '<svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z" /><path d="M2 21c0-3 1.85-5.36 5.08-6" /></svg>',
);
?>
<section class="relative isolate bg-brand-ink text-brand-cream overflow-hidden bg-grain">
	<div class="mx-auto max-w-7xl px-4">

		<div class="py-14 md:flex md:min-h-[480px] md:w-[45%] md:items-center md:py-16">
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

				<?php if ( $badges ) : ?>
					<div class="mt-10 flex flex-wrap gap-y-6">
						<?php foreach ( $badges as $i => $badge ) : ?>
							<?php
							$topic = $juiced_badge_fallback;
							$slug  = sanitize_title( $badge );
							foreach ( $juiced_badge_icons as $keyword => $candidate ) {
								if ( false !== strpos( $slug, $keyword ) ) {
									$topic = $candidate;
									break;
								}
							}
							?>
							<div class="flex flex-col items-center gap-2 px-5 text-center first:pl-0 <?php echo $i > 0 ? 'border-l border-white/15' : ''; ?>">
								<span class="<?php echo esc_attr( $topic['text'] ); ?>">
									<?php echo $topic['icon']; // phpcs:ignore WordPress.Security.EscapeOutput -- static inline SVG. ?>
								</span>
								<span class="text-xs font-bold"><?php echo esc_html( $badge ); ?></span>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>

			</div>
		</div>

	</div>

	<?php if ( $photos ) : ?>
		<!-- Full-bleed on mobile below the copy; from md the strip pins to the
			 band's right edge, full height, cut along the diagonals above. -->
		<div class="relative h-[260px] md:absolute md:inset-y-0 md:right-0 md:h-auto md:w-[55%]">

			<?php foreach ( $photos as $photo ) : ?>
				<div class="absolute inset-0 <?php echo esc_attr( $photo['clip'] ); ?>">
					<img src="<?php echo esc_url( $photo['image'] ); ?>" alt=""
						 class="h-full w-full object-cover <?php echo esc_attr( $photo['focus'] ?? '' ); ?>" loading="eager" fetchpriority="high" />
				</div>
			<?php endforeach; ?>

			<?php foreach ( $photos as $photo ) : ?>
				<?php if ( $photo['label'] ) : ?>
					<span class="absolute bottom-5 <?php echo esc_attr( $photo['pos'] ); ?> whitespace-nowrap rounded-full <?php echo esc_attr( $photo['pill'] ); ?> px-4 py-1.5 text-xs font-bold text-white">
						<?php echo esc_html( $photo['label'] ); ?>
					</span>
				<?php endif; ?>
			<?php endforeach; ?>

		</div>
	<?php endif; ?>

</section>
