<?php
/**
 * Events page Section 2 — Featured event, above the planner.
 *
 * One compact green card on cream: a small thumbnail of the event's flyer
 * (its featured image) on the left; beside it the tag with the date and
 * place, the title, time and price, a live "days to go" count and the
 * tickets / details buttons. Kept short so the calendar stays near the top.
 *
 * The event is the one picked on this page (events_featured_event); left
 * blank, it is the one picked for the homepage banner, then the soonest
 * upcoming one-off event (juiced_featured_event()). No section when there is
 * none. Copy is ACF-editable on the Events page (inc/acf-events-page.php).
 *
 * @package Juiced
 */

$juiced_rows = function_exists( 'juiced_upcoming_events' ) ? juiced_upcoming_events( 20 ) : array();
$juiced_pick = (int) juiced_page_field( 'events_featured_event', 0 );
if ( ! $juiced_pick ) {
	$juiced_pick = (int) juiced_field( 'events_featured', 0 );
}
$juiced_feat = $juiced_rows ? juiced_featured_event( $juiced_rows, $juiced_pick ) : null;

if ( ! $juiced_feat ) {
	return;
}

$juiced_eyebrow = juiced_page_field( 'events_featured_eyebrow', 'Featured event' );

$fid      = $juiced_feat['post']->ID;
$when     = $juiced_feat['when'];
$location = get_field( 'location', $fid );
$cost     = (string) get_field( 'cost', $fid );
$signup   = get_field( 'signup_url', $fid );
$start    = juiced_format_event_time( get_field( 'start_time', $fid ) );
$end      = juiced_format_event_time( get_field( 'end_time', $fid ) );
$details  = array_filter( array( $start && $end ? $start . ' – ' . $end : $start, $cost ) );
$flyer    = get_post_thumbnail_id( $fid );
$days     = (int) floor( max( 0, $when->getTimestamp() - time() ) / 86400 );
$free     = '' === trim( $cost ) || false !== stripos( $cost, 'free' );
?>
<section class="bg-brand-cream text-brand-ink" aria-labelledby="events-featured-h">
	<div class="mx-auto max-w-7xl px-4 pt-14 md:pt-20">
		<div class="grid grid-cols-[88px_minmax(0,1fr)] items-start gap-4 overflow-hidden rounded-3xl bg-brand-forest bg-garden p-4 text-brand-cream sm:grid-cols-[150px_minmax(0,1fr)] sm:items-center sm:gap-7 sm:p-6">

			<?php if ( $flyer ) : ?>
				<a href="<?php echo esc_url( get_permalink( $fid ) ); ?>" class="block overflow-hidden rounded-xl ring-1 ring-white/10" tabindex="-1" aria-hidden="true">
					<?php echo wp_get_attachment_image( $flyer, 'medium_large', false, array( 'class' => 'block aspect-[3/4] h-auto w-full object-cover object-top', 'alt' => '', 'loading' => 'lazy' ) ); ?>
				</a>
			<?php endif; ?>

			<div class="flex min-w-0 flex-col gap-2 <?php echo $flyer ? '' : 'col-span-2'; ?>">
				<div class="flex flex-wrap items-center gap-x-3 gap-y-1.5">
					<?php if ( $juiced_eyebrow ) : ?>
						<span class="rounded-full bg-brand-gold px-2.5 py-1 text-[11px] font-bold uppercase tracking-[.16em] text-brand-ink"><?php echo esc_html( $juiced_eyebrow ); ?></span>
					<?php endif; ?>
					<span class="text-xs font-bold uppercase tracking-[.2em] text-brand-gold">
						<?php echo esc_html( strtoupper( wp_date( 'l, M j', $when->getTimestamp() ) ) . ( $location ? ' · ' . strtoupper( $location ) : '' ) ); ?>
					</span>
				</div>

				<h2 id="events-featured-h" class="m-0 font-display text-[clamp(1.5rem,3vw,2.25rem)] font-bold leading-[1.05] [text-wrap:balance]">
					<a href="<?php echo esc_url( get_permalink( $fid ) ); ?>" class="hover:text-brand-mint"><?php echo esc_html( get_the_title( $fid ) ); ?></a>
				</h2>

				<?php if ( $details ) : ?>
					<p class="m-0 text-sm font-semibold text-brand-mint sm:text-base"><?php echo esc_html( implode( ' · ', $details ) ); ?></p>
				<?php endif; ?>

				<div class="mt-2 flex flex-wrap items-center gap-x-6 gap-y-3">
					<?php if ( $days > 0 ) : ?>
						<span class="flex items-baseline gap-2 text-brand-mint" role="timer"
							  x-data="{ end: <?php echo (int) $when->getTimestamp() * 1000; ?>, d: <?php echo (int) $days; ?>, tick() { this.d = Math.max(0, Math.floor((this.end - Date.now()) / 86400000)); } }"
							  x-init="tick(); setInterval(() => tick(), 60000)" x-show="d > 0">
							<span class="font-display text-4xl font-bold leading-none text-brand-cream tabular-nums" x-text="d"><?php echo (int) $days; ?></span>
							<span class="text-[11px] font-bold uppercase tracking-[.16em]" x-text="1 === d ? '<?php echo esc_js( __( 'day to go', 'juiced' ) ); ?>' : '<?php echo esc_js( __( 'days to go', 'juiced' ) ); ?>'"><?php echo esc_html( 1 === $days ? __( 'day to go', 'juiced' ) : __( 'days to go', 'juiced' ) ); ?></span>
						</span>
					<?php endif; ?>

					<div class="flex flex-wrap gap-3">
						<?php if ( $signup ) : ?>
							<a href="<?php echo esc_url( $signup ); ?>" target="_blank" rel="noopener"
							   class="whitespace-nowrap rounded-full bg-brand-gold px-5.5 py-3 text-sm font-bold text-brand-ink transition-colors hover:bg-brand-cream">
								<?php echo esc_html( $free ? __( 'Sign up', 'juiced' ) : __( 'Get tickets', 'juiced' ) ); ?>
							</a>
						<?php endif; ?>
						<a href="<?php echo esc_url( get_permalink( $fid ) ); ?>"
						   class="whitespace-nowrap rounded-full border-2 border-brand-cream/60 px-5 py-2.5 text-sm font-bold text-brand-cream transition-colors hover:border-brand-cream hover:bg-brand-cream hover:text-brand-forest">
							<?php esc_html_e( 'Event details', 'juiced' ); ?>
						</a>
					</div>
				</div>
			</div>

		</div>
	</div>
</section>
