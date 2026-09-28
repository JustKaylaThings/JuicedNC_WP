<?php
/**
 * Homepage Section 3 — Coming up ("Garden" redesign, Sep 2026).
 *
 * Cream band with three parts:
 *   1. Heading row: eyebrow, two-tone heading, "Full events calendar" button.
 *   2. Featured banner: one big one-off event (e.g. the Oct 30 festival) with a
 *      live days / hours / minutes countdown and a tickets button. The event is
 *      picked on the Home page (events_featured); left blank, it falls back to
 *      the soonest upcoming one-off event. No banner when there is none.
 *   3. Poster cards: the next upcoming events (the featured one excluded), each
 *      with its flyer, an orange "when" tag, time + place, price and a CTA.
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
$juiced_featured = null;
$juiced_pick     = (int) juiced_field( 'events_featured', 0 );
foreach ( $juiced_rows as $row ) {
	if ( $juiced_pick && $row['post']->ID === $juiced_pick ) {
		$juiced_featured = $row;
		break;
	}
	if ( ! $juiced_featured && ! $row['recurring'] ) {
		$juiced_featured = $row;
	}
}

// --- Poster cards ------------------------------------------------------------
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
 * Short "when" tag for a card: "Mondays", "Last Fri", "Oct 30".
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
				<h2 class="font-display font-bold leading-none text-brand-forest text-[clamp(2.5rem,6vw,3.75rem)]">
					<?php echo esc_html( $juiced_heading ); ?>
					<span class="text-brand-rust"><?php echo esc_html( $juiced_accent ); ?></span>
				</h2>
			</div>
			<?php if ( $juiced_cal_url && $juiced_link_label ) : ?>
				<a href="<?php echo esc_url( $juiced_cal_url ); ?>"
				   class="self-start md:self-auto shrink-0 rounded-full border-2 border-brand-forest px-6 py-3.5 font-semibold text-brand-forest hover:bg-brand-forest hover:text-brand-cream transition-colors">
					<?php echo esc_html( $juiced_link_label ); ?>
				</a>
			<?php endif; ?>
		</div>

		<?php
		if ( $juiced_featured ) :
			$fid       = $juiced_featured['post']->ID;
			$fwhen     = $juiced_featured['when'];
			$flocation = get_field( 'location', $fid );
			$fblurb    = get_field( 'announcement_text', $fid );
			$fblurb    = $fblurb ? $fblurb : wp_trim_words( wp_strip_all_tags( get_the_excerpt( $fid ) ), 10 );
			$fcost     = get_field( 'cost', $fid );
			$fdetails  = array_filter( array_map( 'trim', array( $fblurb, $juiced_time_range( $fid ), $fcost ) ) );
			list( $fcta, $fcta_url ) = $juiced_cta( $fid );
			$fdiff = max( 0, $fwhen->getTimestamp() - time() );
			?>
			<!-- Featured event + countdown. The numbers are rendered server-side
				 and then kept live by Alpine; they hide once the event starts. -->
			<div class="grid gap-6 rounded-3xl border-2 border-brand-forest bg-white p-6 md:p-8 lg:grid-cols-[minmax(0,1fr)_auto_auto] lg:items-center lg:gap-9 lg:px-9">
				<div class="flex flex-col gap-1.5">
					<span class="text-xs font-bold uppercase tracking-[.2em] text-brand-rust">
						<?php echo esc_html( strtoupper( $fwhen->format( 'D, M j' ) ) . ( $flocation ? ' · ' . strtoupper( $flocation ) : '' ) ); ?>
					</span>
					<a href="<?php echo esc_url( get_permalink( $fid ) ); ?>" class="font-display font-bold text-3xl md:text-4xl leading-[1.05] text-brand-ink hover:text-brand-forest">
						<?php echo esc_html( get_the_title( $fid ) ); ?>
					</a>
					<?php if ( $fdetails ) : ?>
						<span class="text-[15px] text-[#4a4036]"><?php echo esc_html( implode( ' · ', $fdetails ) ); ?></span>
					<?php endif; ?>
				</div>

				<?php if ( $fdiff > 0 ) : ?>
					<div class="flex gap-2.5" role="timer" aria-label="<?php esc_attr_e( 'Countdown to the event', 'juiced' ); ?>"
						 x-data="{ end: <?php echo (int) $fwhen->getTimestamp() * 1000; ?>, d: <?php echo (int) floor( $fdiff / 86400 ); ?>, h: <?php echo (int) floor( $fdiff % 86400 / 3600 ); ?>, m: <?php echo (int) floor( $fdiff % 3600 / 60 ); ?>,
							tick() { const s = Math.max(0, Math.floor((this.end - Date.now()) / 1000)); this.d = Math.floor(s / 86400); this.h = Math.floor(s % 86400 / 3600); this.m = Math.floor(s % 3600 / 60); } }"
						 x-init="tick(); setInterval(() => tick(), 30000)">
						<?php
						foreach ( array(
							'd' => __( 'Days', 'juiced' ),
							'h' => __( 'Hrs', 'juiced' ),
							'm' => __( 'Min', 'juiced' ),
						) as $unit => $label ) :
							$initial = 'd' === $unit ? floor( $fdiff / 86400 ) : ( 'h' === $unit ? floor( $fdiff % 86400 / 3600 ) : floor( $fdiff % 3600 / 60 ) );
							?>
							<span class="flex min-w-[76px] flex-col items-center rounded-2xl bg-brand-forest px-4 py-3 text-brand-mint">
								<span class="font-display font-bold text-[44px] leading-none text-brand-cream tabular-nums"
									  x-text="String(<?php echo esc_attr( $unit ); ?>).padStart(2, '0')"><?php echo esc_html( str_pad( (string) $initial, 2, '0', STR_PAD_LEFT ) ); ?></span>
								<span class="mt-1 text-[11px] font-bold uppercase tracking-[.16em]"><?php echo esc_html( $label ); ?></span>
							</span>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>

				<a href="<?php echo esc_url( $fcta_url ); ?>"
				   class="justify-self-start whitespace-nowrap rounded-full bg-brand-gold px-6.5 py-4 font-bold text-brand-ink hover:bg-brand-forest hover:text-brand-cream transition-colors"
				   <?php echo $fcta_url !== get_permalink( $fid ) ? 'target="_blank" rel="noopener"' : ''; ?>>
					<?php echo esc_html( $fcta ); ?>
				</a>
			</div>
		<?php endif; ?>

		<?php if ( $juiced_cards ) : ?>
			<!-- Poster cards: a swipeable row on phones, a grid from lg. -->
			<div class="-mx-4 px-4 lg:mx-0 lg:px-0 flex gap-4 overflow-x-auto no-scrollbar snap-x snap-mandatory lg:grid lg:gap-5 lg:overflow-visible <?php echo esc_attr( 4 === count( $juiced_cards ) ? 'lg:grid-cols-4' : 'lg:grid-cols-3' ); ?>">
				<?php
				foreach ( $juiced_cards as $row ) :
					$id       = $row['post']->ID;
					$image    = get_the_post_thumbnail_url( $id, 'large' );
					$location = get_field( 'location', $id );
					$meta     = array_filter( array( $juiced_time_range( $id ), $location ) );
					$cost     = get_field( 'cost', $id );
					list( $cta, $cta_url ) = $juiced_cta( $id );
					?>
					<article class="relative flex w-[78%] sm:w-[45%] shrink-0 snap-start flex-col overflow-hidden rounded-3xl border border-brand-forest/15 bg-white lg:w-auto">
						<figure class="relative m-0! aspect-[4/5] overflow-hidden bg-brand-forest">
							<?php if ( $image ) : ?>
								<img src="<?php echo esc_url( $image ); ?>" alt="" class="h-full w-full object-cover object-top" loading="lazy" />
							<?php else : ?>
								<span class="absolute -right-5 -top-8 font-display text-[220px] font-bold leading-none text-[#2a7f3a]" aria-hidden="true">&#9733;</span>
							<?php endif; ?>
							<figcaption class="absolute left-4 top-4 rounded-[14px] bg-brand-gold px-3.5 py-2 font-display text-lg font-bold text-brand-ink">
								<?php echo esc_html( $juiced_when_tag( $row ) ); ?>
							</figcaption>
						</figure>
						<div class="flex flex-1 flex-col gap-2 p-5.5 pb-6">
							<h3 class="font-display font-semibold text-2xl leading-[1.1]">
								<a href="<?php echo esc_url( get_permalink( $id ) ); ?>" class="hover:text-brand-forest after:absolute after:inset-0">
									<?php echo esc_html( get_the_title( $id ) ); ?>
								</a>
							</h3>
							<?php if ( $meta ) : ?>
								<span class="text-[15px] text-[#4a4036]"><?php echo esc_html( implode( ' · ', $meta ) ); ?></span>
							<?php endif; ?>
							<div class="mt-auto flex items-center justify-between gap-3 border-t border-brand-forest/15 pt-3.5 text-[15px] font-bold">
								<span><?php echo esc_html( $cost ? $cost : __( 'Free', 'juiced' ) ); ?></span>
								<a href="<?php echo esc_url( $cta_url ); ?>" class="relative z-10 text-brand-forest hover:text-brand-rust"
								   <?php echo $cta_url !== get_permalink( $id ) ? 'target="_blank" rel="noopener"' : ''; ?>>
									<?php echo esc_html( $cta ); ?> <span aria-hidden="true">&rarr;</span>
								</a>
							</div>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

	</div>
</section>
