<?php
/**
 * Host an Event Section 1 — hero with The Space beside it (Host an Event
 * option D, reworked).
 *
 * The deep-green "Garden" hero: two-tone headline (cream + orange), the pitch,
 * and a "Pitch Your Event" button down to the form (#event-form). On the
 * right, "The Space" card — the quick facts a group needs before pitching:
 * how many it fits, the best times, cost, and where (events are hosted at the
 * Hill Street shop only), and a "Book on Peerspace" link to the space's
 * Peerspace listing. Stacks under the
 * copy on phones.
 *
 * Facts are fixed slots (ACF free has no Repeater) — a label, a value and a
 * small detail line each; a fact hides when its value is cleared, and the card
 * when all are. Defaults come from the Peerspace listing.
 *
 * Copy is ACF-editable on this page (inc/acf-host-event-page.php).
 *
 * "Juiced!" in the copy shows as the logo (juiced_logo_text()).
 *
 * @package Juiced
 */

$lead    = juiced_page_field( 'host_hero_lead', 'Host Your Event' );
$accent  = juiced_page_field( 'host_hero_accent', 'at Juiced!' );
$subtext = juiced_page_field( 'host_hero_subtext', 'Run clubs, workshops, pop-ups and fundraisers. If it brings neighbors together, we want to hear about it.' );
$primary = juiced_page_field( 'host_hero_primary', 'Pitch Your Event' );

$space_heading = juiced_page_field( 'host_space_heading', 'The Space' );
$space_intro   = juiced_page_field( 'host_space_intro', 'A cozy café and lounge in historic Washington Terrace Shopping Center, made for gathering.' );
$peerspace     = juiced_page_field( 'host_peerspace_url', 'https://www.peerspace.com/pages/listings/66e062ef67ea994f5373c45c' );
$ps_rating     = juiced_page_field( 'host_peerspace_rating', '4.9' );
$ps_reviews    = juiced_page_field( 'host_peerspace_reviews', '26' );

// Label, value, small detail line — defaults from the Peerspace listing
// (Oct 2026). The location detail is the street part of the Hill Street
// address in Business Info.
$hill_address = preg_split( '/,|\n/', (string) juiced_business_field( 'loc2_address', '434 Hill St' ) );
$defaults     = array(
	1 => array( 'Fits', 'Up to 60 people', '500 sq ft' ),
	2 => array( 'Best times', 'Evenings & weekends', 'Mon–Wed & Fri from 6 PM · Sat–Sun from 6 AM · Closed Thu' ),
	3 => array( 'Cost', '$125 / hour', '2-hour minimum' ),
	4 => array( 'Location', 'Hill Street', trim( $hill_address[0] ) ),
);

$facts = array();
foreach ( $defaults as $i => $default ) {
	$value = juiced_page_field( "host_space{$i}_value", $default[1] );
	if ( $value ) {
		$facts[] = array(
			'label'  => juiced_page_field( "host_space{$i}_label", $default[0] ),
			'value'  => $value,
			'detail' => juiced_page_field( "host_space{$i}_detail", $default[2] ),
		);
	}
}
?>
<section class="relative isolate -mt-px bg-brand-forest bg-garden text-brand-cream overflow-hidden">
	<div class="mx-auto grid max-w-7xl items-center gap-12 px-4 py-16 md:py-20 min-h-[380px] md:min-h-[440px] <?php echo $facts ? 'lg:grid-cols-[1fr_1fr] lg:gap-16' : ''; ?>">

		<div class="max-w-xl">
			<h1 class="font-display font-bold leading-[.9] tracking-[-.03em] text-[clamp(3rem,9vw,4.5rem)] md:text-[clamp(2.75rem,5.2vw,4.75rem)]">
				<?php if ( $lead ) : ?>
					<span class="block"><?php echo esc_html( $lead ); ?></span>
				<?php endif; ?>
				<?php if ( $accent ) : ?>
					<span class="block text-brand-gold"><?php echo juiced_logo_text( $accent ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in the helper. ?></span>
				<?php endif; ?>
			</h1>

			<?php if ( $subtext ) : ?>
				<p class="mt-6 max-w-md text-lg md:text-xl font-medium leading-relaxed">
					<?php echo esc_html( $subtext ); ?>
				</p>
			<?php endif; ?>

			<?php if ( $primary ) : ?>
				<a href="#event-form"
				   class="mt-8 inline-flex min-h-13 items-center gap-2 rounded-full bg-brand-gold px-7 text-[15px] font-bold text-brand-ink transition-colors hover:bg-brand-cream">
					<?php echo esc_html( $primary ); ?>
					<span aria-hidden="true">&darr;</span>
				</a>
			<?php endif; ?>
		</div>

		<?php if ( $facts ) : ?>
			<div class="rounded-[28px] bg-brand-forest-deep/55 p-7 ring-1 ring-brand-cream/15 backdrop-blur-sm md:p-10" aria-labelledby="host-space-h">
				<?php if ( $space_heading ) : ?>
					<h2 id="host-space-h" class="font-display text-3xl font-bold md:text-4xl"><?php echo esc_html( $space_heading ); ?></h2>
				<?php endif; ?>
				<?php if ( $space_intro ) : ?>
					<p class="mt-2 text-base leading-relaxed text-brand-mint md:text-lg"><?php echo esc_html( $space_intro ); ?></p>
				<?php endif; ?>

				<dl class="mt-7 grid gap-x-8 gap-y-6 sm:grid-cols-2">
					<?php foreach ( $facts as $fact ) : ?>
						<div class="border-t border-brand-cream/15 pt-4">
							<?php if ( $fact['label'] ) : ?>
								<dt class="text-[13px] font-bold uppercase tracking-[.08em] text-brand-gold"><?php echo esc_html( $fact['label'] ); ?></dt>
							<?php endif; ?>
							<dd class="mt-1 font-display text-2xl font-semibold leading-snug"><?php echo esc_html( $fact['value'] ); ?></dd>
							<?php if ( $fact['detail'] ) : ?>
								<dd class="mt-1 text-sm leading-snug text-brand-mint"><?php echo esc_html( $fact['detail'] ); ?></dd>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				</dl>

				<?php if ( $peerspace ) : ?>
					<a href="<?php echo esc_url( $peerspace ); ?>" target="_blank" rel="noopener"
					   class="group mt-8 inline-flex min-h-12 items-center gap-1.5 rounded-full border-2 border-brand-cream/50 px-6 text-sm font-bold text-brand-cream transition-colors hover:border-brand-cream hover:bg-brand-cream hover:text-brand-forest">
						<?php esc_html_e( 'Book on Peerspace', 'juiced' ); ?>
						<?php if ( $ps_rating ) : ?>
							<?php // Peerspace rating, e.g. ★ 4.9 (26) — set on the page, as Peerspace has no public feed. ?>
							<span class="ml-1.5 inline-flex items-center gap-1 border-l border-current/30 pl-3" aria-hidden="true">
								<svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor" class="text-brand-gold"><path d="m12 2.5 2.9 6.1 6.6.8-4.9 4.6 1.3 6.6L12 17.3l-5.9 3.3 1.3-6.6-4.9-4.6 6.6-.8Z"/></svg>
								<?php echo esc_html( $ps_rating ); ?>
								<?php if ( $ps_reviews ) : ?>
									<span class="font-medium opacity-75">(<?php echo esc_html( $ps_reviews ); ?>)</span>
								<?php endif; ?>
							</span>
							<span class="sr-only">
								<?php
								/* translators: 1: rating out of 5, 2: number of reviews. */
								echo esc_html( sprintf( __( '— rated %1$s out of 5 from %2$s reviews', 'juiced' ), $ps_rating, $ps_reviews ? $ps_reviews : '' ) );
								?>
							</span>
						<?php endif; ?>
						<svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 17 17 7M8 7h9v9"/></svg>
						<span class="sr-only"><?php esc_html_e( '(opens in a new tab)', 'juiced' ); ?></span>
					</a>
				<?php endif; ?>
			</div>
		<?php endif; ?>

	</div>
</section>
