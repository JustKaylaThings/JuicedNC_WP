<?php
/**
 * Events page Section 4 — "More Than a Juice Bar. / We're a Community."
 *
 * The dark closing band: two-tone heading, body, and an outlined button, laid
 * over a full-bleed photo mosaic. Structurally this is hero.php — same isolate /
 * absolute -z-10 background, same left-to-right scrim, deliberately so: they're
 * the two dark bands on the page and bookend it, so they should read as a pair.
 * With no photos the mosaic is skipped and the band collapses to solid ink,
 * which still reads correctly, exactly as the hero does.
 *
 * The homepage runs this same heading beside a live Instagram feed
 * (template-parts/home/community.php). The copy fields here are page-local
 * because the two bands say different things below the heading, but the button
 * points at the site-wide community_button_url — there is one About page, and
 * duplicating the field would let the client update one band and leave the
 * other pointing somewhere stale.
 *
 * Photos are five discrete fields rather than a repeater: ACF free has no
 * Repeater, and five is what the design calls for.
 *
 * @package Juiced
 */

$juiced_heading = juiced_page_field( 'events_community_heading', 'More Than a Juice Bar.' );

if ( ! $juiced_heading ) {
	return;
}

$juiced_accent = juiced_page_field( 'events_community_accent', "We're a Community." );
$juiced_body   = juiced_page_field( 'events_community_body', 'We create spaces for people to connect, learn, grow, and thrive together.' );
$juiced_label  = juiced_page_field( 'events_community_cta_label', 'Learn More About Us' );
$juiced_url    = juiced_field( 'community_button_url', '' );

// 'large' (1024w) rather than juiced_card (640w): a tile is up to half the
// container wide, so the card size goes soft on a 2x display, and five hero-size
// files to decorate one band is not a trade worth making.
$juiced_tiles = array();

for ( $juiced_n = 1; $juiced_n <= 5; $juiced_n++ ) {
	$juiced_src = juiced_image_url( juiced_page_field( 'events_community_image_' . $juiced_n, '' ), 'large' );
	if ( $juiced_src ) {
		$juiced_tiles[] = $juiced_src;
	}
}

// Column spans that fill a 6-wide grid exactly at every count, so a client who
// uploads three photos gets three wide tiles rather than the design's layout
// with a black hole where the fourth should be. Literal class strings — Tailwind
// only sees what it can read in the source.
$juiced_layouts = array(
	1 => array( 'col-span-6' ),
	2 => array( 'col-span-3', 'col-span-3' ),
	3 => array( 'col-span-2', 'col-span-2', 'col-span-2' ),
	4 => array( 'col-span-3', 'col-span-3', 'col-span-3', 'col-span-3' ),
	5 => array( 'col-span-2', 'col-span-2', 'col-span-2', 'col-span-3', 'col-span-3' ),
);

$juiced_layout = isset( $juiced_layouts[ count( $juiced_tiles ) ] ) ? $juiced_layouts[ count( $juiced_tiles ) ] : array();
?>
<section class="relative isolate overflow-hidden bg-brand-ink bg-grain text-brand-cream">

	<?php if ( $juiced_layout ) : ?>
		<div class="absolute inset-0 -z-10" aria-hidden="true">
			<!-- auto-rows-fr keeps both rows the same height whatever the band grows to. -->
			<div class="grid h-full w-full auto-rows-fr grid-cols-6 gap-1">
				<?php foreach ( $juiced_tiles as $juiced_i => $juiced_src ) : ?>
					<img src="<?php echo esc_url( $juiced_src ); ?>" alt=""
						 class="h-full w-full object-cover <?php echo esc_attr( $juiced_layout[ $juiced_i ] ); ?>"
						 loading="lazy" />
				<?php endforeach; ?>
			</div>
			<!-- Same scrim as the hero: solid behind the copy, clearing by the right
				 edge. Heavier on mobile, where the text sits over the photos. -->
			<div class="absolute inset-0 bg-gradient-to-r from-brand-ink via-brand-ink/85 to-brand-ink/65 md:from-28% md:via-brand-ink/55 md:via-60% md:to-brand-ink/5"></div>
		</div>
	<?php endif; ?>

	<div class="mx-auto flex min-h-[340px] max-w-7xl items-center px-4 py-16 md:min-h-[420px] md:py-24">
		<div class="max-w-xl">

			<h2 class="font-display text-4xl md:text-5xl leading-[1.05]">
				<span class="block text-white"><?php echo esc_html( $juiced_heading ); ?></span>
				<?php if ( $juiced_accent ) : ?>
					<span class="block text-brand-gold"><?php echo esc_html( $juiced_accent ); ?></span>
				<?php endif; ?>
			</h2>

			<?php if ( $juiced_body ) : ?>
				<p class="mt-6 max-w-md text-base md:text-lg leading-relaxed text-brand-cream/80">
					<?php echo esc_html( $juiced_body ); ?>
				</p>
			<?php endif; ?>

			<?php if ( $juiced_url && $juiced_label ) : ?>
				<a href="<?php echo esc_url( $juiced_url ); ?>"
				   class="mt-8 inline-flex items-center gap-2 rounded-full border border-brand-gold/70 px-6 py-3 text-sm font-semibold text-white transition-colors hover:border-brand-gold hover:bg-brand-gold">
					<?php echo esc_html( $juiced_label ); ?>
					<span aria-hidden="true">&rarr;</span>
				</a>
			<?php endif; ?>

		</div>
	</div>
</section>
