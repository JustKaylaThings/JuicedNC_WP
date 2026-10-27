<?php
/**
 * Homepage Section 3 — Coming up ("Garden" redesign, Sep 2026).
 *
 * Cream band with three parts:
 *   1. Heading row: eyebrow, two-tone heading, "Full events calendar" button.
 *   2. Featured banner: one big one-off event (e.g. the Oct 30 festival) on a
 *      green band with a live "days to go" count and a tickets button. The event is
 *      picked on the Home page (events_featured); left blank, it falls back to
 *      the soonest upcoming one-off event. No banner when there is none.
 *   3. Agenda: the next upcoming events (the featured one excluded) as plain
 *      rows, no flyers (client: mixed poster styles looked cluttered) — day,
 *      title, time + place, price and a CTA.
 *
 * Events come from the Event CPT via juiced_upcoming_events()
 * (inc/event-dates.php), so past one-offs drop off on their own. Section copy
 * is ACF-editable on the Home page (inc/acf-homepage.php).
 *
 * @package Juiced
 */

$juiced_rows = function_exists( 'juiced_upcoming_events' ) ? juiced_upcoming_events( 20 ) : array();

if ( empty( $juiced_rows ) ) {
	return;
}

$juiced_eyebrow    = juiced_field( 'events_eyebrow', 'Coming up' );
$juiced_heading    = juiced_field( 'events_heading', 'Pull up a chair.' );
$juiced_accent     = juiced_field( 'events_heading_accent', 'Every week.' );
$juiced_link_label = juiced_field( 'events_link_label', 'Full events calendar' );
$juiced_cal_url    = juiced_field( 'events_link_url', juiced_events_page_url() );

// --- Featured event --------------------------------------------------------
// The chosen event if it is still upcoming, else the soonest one-off.
$juiced_featured = juiced_featured_event( $juiced_rows, juiced_field( 'events_featured', 0 ) );

// --- Agenda rows -------------------------------------------------------------
$juiced_cards = array();
foreach ( $juiced_rows as $row ) {
	if ( $juiced_featured && $row['post']->ID === $juiced_featured['post']->ID ) {
		continue;
	}
	$juiced_cards[] = $row;
	if ( count( $juiced_cards ) === 4 ) {
		break;
	}
}

/**
 * Short "when" label for a row: "Mondays", "Last Fri", "Oct 30".
 */
$juiced_when_tag = static function ( $row ) {
	$id = $row['post']->ID;
	if ( ! $row['recurring'] ) {
		return $row['when']->format( 'M j' );
	}
	$days = juiced_normalize_days( get_field( 'day_of_week', $id ) );
	$join = static function ( $items ) {
		$last = array_pop( $items );
		return $items ? implode( ', ', $items ) . ' & ' . $last : $last;
	};
	$ordinals = array_filter( (array) get_field( 'monthly_ordinals', $id ) );
	if ( $ordinals ) {
		$names = array( '1' => '1st', '2' => '2nd', '3' => '3rd', '4' => '4th', '5' => '5th', 'last' => 'Last' );
		$picked = array();
		foreach ( $ordinals as $token ) {
			$token = strtolower( (string) $token );
			if ( isset( $names[ $token ] ) ) {
				$picked[] = $names[ $token ];
			}
		}
		return $join( $picked ) . ' ' . $join( array_map( static fn( $d ) => substr( $d, 0, 3 ), $days ) );
	}
	return $join( array_map( static fn( $d ) => $d . 's', $days ) );
};

/**
 * "2:00 PM – 11:00 PM", or just the start, or ''.
 */
$juiced_time_range = static function ( $id ) {
	$start = juiced_format_event_time( get_field( 'start_time', $id ) );
	$end   = juiced_format_event_time( get_field( 'end_time', $id ) );
	return $start && $end ? $start . ' – ' . $end : $start;
};

/**
 * CTA label: tickets for paid events with a sign-up link, sign up for free
 * ones, otherwise the event page.
 */
$juiced_cta = static function ( $id ) {
	$cost   = (string) get_field( 'cost', $id );
	$signup = get_field( 'signup_url', $id );
	if ( $signup ) {
		$free = '' === $cost || false !== stripos( $cost, 'free' );
		return array( $free ? __( 'Sign up', 'juiced' ) : __( 'Get tickets', 'juiced' ), $signup );
	}
	return array( __( 'Details', 'juiced' ), get_permalink( $id ) );
};
?>
<section class="bg-brand-cream text-brand-ink">
	<div class="mx-auto max-w-7xl px-4 py-16 md:py-22 flex flex-col gap-8 md:gap-10">

		<!-- Heading row -->
		<div class="flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
			<div class="flex flex-col gap-3.5">
				<?php if ( $juiced_eyebrow ) : ?>
					<span class="text-xs font-bold uppercase tracking-[.22em] text-brand-forest"><?php echo esc_html( $juiced_eyebrow ); ?></span>
				<?php endif; ?>
				<h2 class="text-drop font-display font-bold leading-none text-brand-forest text-[clamp(2.5rem,6vw,3.75rem)]">
					<?php echo esc_html( $juiced_heading ); ?>
					<span class="text-brand-gold"><?php echo esc_html( $juiced_accent ); ?></span>
				</h2>
			</div>
			<?php if ( $juiced_cal_url && $juiced_link_label ) : ?>
				<a href="<?php echo esc_url( $juiced_cal_url ); ?>"
				   class="self-start md:self-auto shrink-0 rounded-full px-6 py-3.5 bg-brand-cream text-brand-ink btn-sticker">
					<?php echo esc_html( $juiced_link_label ); ?>
				</a>
			<?php endif; ?>
		</div>

		<?php
		if ( $juiced_featured ) :
			$fid       = $juiced_featured['post']->ID;
			$fwhen     = $juiced_featured['when'];
			$flocation = get_field( 'location', $fid );
			$fcost     = get_field( 'cost', $fid );
			$fdetails  = array_filter( array_map( 'trim', array( $juiced_time_range( $fid ), (string) $fcost ) ) );
			list( $fcta, $fcta_url ) = $juiced_cta( $fid );
			$fdays = (int) floor( max( 0, $fwhen->getTimestamp() - time() ) / 86400 );
			?>
			<!-- Featured event: one green band with a days-to-go count. The count
				 is rendered server-side and kept current by Alpine (cached pages). -->
			<div class="relative grid gap-6 rounded-3xl bg-brand-forest p-6 text-brand-cream md:p-8 lg:grid-cols-[minmax(0,1fr)_auto_auto] lg:items-center lg:gap-9 lg:px-9">
				<div class="flex flex-col gap-2">
					<span class="text-xs font-bold uppercase tracking-[.2em] text-brand-gold">
						<?php echo esc_html( strtoupper( $fwhen->format( 'D, M j' ) ) . ( $flocation ? ' · ' . strtoupper( $flocation ) : '' ) ); ?>
					</span>
					<a href="<?php echo esc_url( get_permalink( $fid ) ); ?>" class="font-display font-bold text-3xl md:text-4xl leading-[1.05] hover:text-brand-mint after:absolute after:inset-0">
						<?php echo esc_html( get_the_title( $fid ) ); ?>
					</a>
					<?php if ( $fdetails ) : ?>
						<span class="text-[15px] text-brand-mint"><?php echo esc_html( implode( ' · ', $fdetails ) ); ?></span>
					<?php endif; ?>
				</div>

				<?php if ( $fdays > 0 ) : ?>
					<span class="flex flex-col items-start lg:items-center text-brand-mint" role="timer"
						  x-data="{ end: <?php echo (int) $fwhen->getTimestamp() * 1000; ?>, d: <?php echo (int) $fdays; ?>, tick() { this.d = Math.max(0, Math.floor((this.end - Date.now()) / 86400000)); } }"
						  x-init="tick(); setInterval(() => tick(), 60000)" x-show="d > 0">
						<span class="font-display font-bold text-[56px] leading-none text-brand-cream tabular-nums" x-text="d"><?php echo (int) $fdays; ?></span>
						<span class="mt-1 text-[11px] font-bold uppercase tracking-[.16em]" x-text="1 === d ? '<?php echo esc_js( __( 'day to go', 'juiced' ) ); ?>' : '<?php echo esc_js( __( 'days to go', 'juiced' ) ); ?>'"><?php echo esc_html( 1 === $fdays ? __( 'day to go', 'juiced' ) : __( 'days to go', 'juiced' ) ); ?></span>
					</span>
				<?php endif; ?>

				<a href="<?php echo esc_url( $fcta_url ); ?>"
				   class="relative z-10 justify-self-start whitespace-nowrap rounded-full bg-brand-gold px-6.5 py-4 text-brand-ink btn-sticker"
				   <?php echo $fcta_url !== get_permalink( $fid ) ? 'target="_blank" rel="noopener"' : ''; ?>>
					<?php echo esc_html( $fcta ); ?>
				</a>
			</div>
		<?php endif; ?>

		<?php if ( $juiced_cards ) : ?>
			<!-- Agenda: one row per event, no flyers. Stacks on phones; from md
				 it's day | title + details | price | link. -->
			<ul class="m-0 flex list-none flex-col p-0">
				<?php
				foreach ( $juiced_cards as $row ) :
					$id       = $row['post']->ID;
					$location = get_field( 'location', $id );
					$meta     = array_filter( array( $juiced_time_range( $id ), $location ) );
					$cost     = get_field( 'cost', $id );
					list( $cta, $cta_url ) = $juiced_cta( $id );
					?>
					<li class="relative grid gap-1 border-t border-brand-forest/20 py-6 md:grid-cols-[200px_minmax(0,1fr)_90px_150px] md:items-center md:gap-6">
						<span class="font-display text-xl md:text-[22px] font-semibold text-brand-rust"><?php echo esc_html( $juiced_when_tag( $row ) ); ?></span>
						<div class="flex flex-col gap-1">
							<h3 class="font-display font-semibold text-2xl md:text-[26px] leading-[1.15]">
								<a href="<?php echo esc_url( get_permalink( $id ) ); ?>" class="hover:text-brand-forest after:absolute after:inset-0">
									<?php echo esc_html( get_the_title( $id ) ); ?>
								</a>
							</h3>
							<?php if ( $meta ) : ?>
								<span class="text-[15px] text-[#4a4036]"><?php echo esc_html( implode( ' · ', $meta ) ); ?></span>
							<?php endif; ?>
						</div>
						<span class="mt-2 flex items-center justify-between gap-3 font-bold md:contents">
							<span><?php echo esc_html( $cost ? $cost : __( 'Free', 'juiced' ) ); ?></span>
							<a href="<?php echo esc_url( $cta_url ); ?>" class="relative z-10 md:justify-self-end text-brand-forest hover:text-brand-rust"
							   <?php echo $cta_url !== get_permalink( $id ) ? 'target="_blank" rel="noopener"' : ''; ?>>
								<?php echo esc_html( $cta ); ?> <span aria-hidden="true">&rarr;</span>
							</a>
						</span>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

	</div>
</section>
