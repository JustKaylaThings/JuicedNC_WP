<?php
/**
 * Single Event — /event/{slug}.
 *
 * Built from the approved detail-header mockup: breadcrumb up top, then a
 * two-column header on the same cream ground as the Events page — photo left,
 * details right. The details column runs category pill, title, description,
 * icon meta rows (date / time / location, with the cost as a soft pill on the
 * date row), then the RSVP and Add to Calendar buttons. Below it, a "More
 * Upcoming Events" strip reuses the Events page's poster cards, so clicking
 * around events never changes visual language.
 *
 * The accent color comes from the event's category (juiced_event_category_style),
 * not the listing's positional palette — a card's color depends on where it
 * happened to sit that week, which is nothing to anchor a permalink page to.
 *
 * Everything renders from the fields the client already fills in on the Event
 * itself (plugin: juiced-cpt); there are no page-local ACF fields here. Rows
 * with nothing behind them hide — except cost, which defaults to "Free" to
 * match the cards. RSVP stays hidden until signup_url is set, so it can never
 * lead nowhere; a one-off whose run is over drops both buttons and says so.
 *
 * @package Juiced
 */

get_header();

while ( have_posts() ) :
	the_post();

	$event_id = get_the_ID();

	$terms = get_the_terms( $event_id, 'event_category' );
	$term  = ( $terms && ! is_wp_error( $terms ) ) ? $terms[0] : null;
	$style = juiced_event_category_style( $term ? $term->slug : '' );

	$schedule = juiced_event_schedule_label( $event_id );
	$start    = juiced_format_event_time( get_field( 'start_time', $event_id ) );
	$end      = juiced_format_event_time( get_field( 'end_time', $event_id ) );
	$location = get_field( 'location', $event_id );
	$signup   = get_field( 'signup_url', $event_id );
	$cost     = get_field( 'cost', $event_id );
	$cost     = $cost ? $cost : __( 'Free', 'juiced' );

	// Next concrete occurrence. A one-off shows it as the date row ("Fri, Oct
	// 30, 2026"); a recurring event shows its schedule instead ("Every
	// Thursday"), because the pattern says more than any single date. Null +
	// an event_date means the run is over: the schedule stays visible so the
	// page is honest about what the event was, but the action buttons drop.
	$when     = juiced_event_upcoming( $event_id );
	$is_past  = ! $when && get_field( 'event_date', $event_id );
	$date_row = get_field( 'event_date', $event_id ) && $when ? $when->format( 'D, M j, Y' ) : $schedule;

	$image = get_the_post_thumbnail_url( $event_id, 'large' );

	// "Add to Calendar" targets. The web calendars get deep links; everyone
	// else (Apple Calendar, desktop Outlook, …) gets the .ics endpoint
	// (inc/event-ics.php). All three read the same occurrence span and
	// recurrence rule (inc/event-dates.php), so they can't disagree.
	$span  = juiced_event_occurrence_span( $event_id );
	$rrule = $span ? juiced_event_rrule( $event_id ) : '';
	$gcal    = '';
	$outlook = '';
	$ics     = '';
	if ( $span ) {
		// Decoded: get_the_title() returns entities ("&#8211;" — texturized
		// dashes and quotes included), and the calendars would print them
		// literally in the entry name.
		$cal_title = html_entity_decode( get_the_title(), ENT_QUOTES, get_bloginfo( 'charset' ) );

		$gcal_args = array(
			'action'   => 'TEMPLATE',
			'text'     => $cal_title,
			'dates'    => $span['all_day']
				? $span['start']->format( 'Ymd' ) . '/' . $span['end']->format( 'Ymd' )
				: $span['start']->format( 'Ymd\THis' ) . '/' . $span['end']->format( 'Ymd\THis' ),
			'details'  => get_permalink(),
			'location' => $location,
		);
		if ( $rrule ) {
			$gcal_args['recur'] = 'RRULE:' . $rrule;
		}
		// Only a named zone means anything to Google; the "+00:00" style the
		// site returns while set to a UTC offset would just be rejected.
		if ( false !== strpos( wp_timezone_string(), '/' ) ) {
			$gcal_args['ctz'] = wp_timezone_string();
		}
		$gcal = 'https://calendar.google.com/calendar/render?' . http_build_query( $gcal_args );

		// Outlook.com's compose deep link. No recurrence support — the next
		// occurrence is the best it can take.
		$outlook = 'https://outlook.live.com/calendar/0/deeplink/compose?' . http_build_query(
			array(
				'path'     => '/calendar/action/compose',
				'rru'      => 'addevent',
				'subject'  => $cal_title,
				'startdt'  => $span['all_day'] ? $span['start']->format( 'Y-m-d' ) : $span['start']->format( 'Y-m-d\TH:i:s' ),
				'enddt'    => $span['all_day'] ? $span['end']->format( 'Y-m-d' ) : $span['end']->format( 'Y-m-d\TH:i:s' ),
				'allday'   => $span['all_day'] ? 'true' : 'false',
				'location' => $location,
				'body'     => get_permalink(),
			)
		);

		$ics = add_query_arg( 'ics', '1', get_permalink() );
	}

	// Meta-row icons — same 24-box stroke style as the cards' clock.
	$icon = static function ( $paths ) {
		return '<svg viewBox="0 0 24 24" width="19" height="19" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $paths . '</svg>';
	};
	$icons = array(
		'calendar' => $icon( '<rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>' ),
		'clock'    => $icon( '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>' ),
		'pin'      => $icon( '<path d="M12 21s-7-6.5-7-11.5a7 7 0 0 1 14 0C19 14.5 12 21 12 21Z"/><circle cx="12" cy="9.5" r="2.5"/>' ),
	);

	// "More Upcoming Events": the next few, minus this one, as poster cards.
	$more = array();
	if ( function_exists( 'juiced_upcoming_events' ) ) {
		foreach ( juiced_upcoming_events( 8 ) as $row ) {
			if ( (int) $row['post']->ID === (int) $event_id ) {
				continue;
			}
			$more[] = $row;
			if ( count( $more ) >= 3 ) {
				break;
			}
		}
	}
	?>

	<section class="bg-brand-cream bg-grain">
		<div class="mx-auto max-w-7xl px-4 pt-6 pb-14 md:pt-8 md:pb-16">

			<!-- Breadcrumb -->
			<nav aria-label="<?php esc_attr_e( 'Breadcrumb', 'juiced' ); ?>" class="flex flex-wrap items-center gap-2 text-sm">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="text-brand-bark/60 transition-colors hover:text-brand-green"><?php esc_html_e( 'Home', 'juiced' ); ?></a>
				<span class="text-brand-bark/40" aria-hidden="true">&rsaquo;</span>
				<a href="<?php echo esc_url( juiced_events_page_url() ); ?>" class="text-brand-bark/60 transition-colors hover:text-brand-green"><?php esc_html_e( 'Events', 'juiced' ); ?></a>
				<span class="text-brand-bark/40" aria-hidden="true">&rsaquo;</span>
				<span class="font-semibold text-brand-bark" aria-current="page"><?php the_title(); ?></span>
			</nav>

			<!-- Detail header: photo left, details right -->
			<div class="mt-6 grid items-start gap-8 md:mt-8 <?php echo $image ? 'lg:grid-cols-2 lg:gap-12' : ''; ?>">

				<?php if ( $image ) : ?>
					<img src="<?php echo esc_url( $image ); ?>"
						 alt="<?php echo esc_attr( sprintf( /* translators: %s: event title. */ __( 'Flyer for %s', 'juiced' ), get_the_title() ) ); ?>"
						 class="w-full rounded-2xl shadow-sm ring-1 ring-black/5" loading="eager" fetchpriority="high" />
				<?php endif; ?>

				<div class="<?php echo $image ? '' : 'max-w-2xl'; ?>">

					<?php if ( $term ) : ?>
						<span class="inline-flex items-center gap-1.5 rounded-full <?php echo esc_attr( $style['bg'] ); ?> px-3.5 py-1.5 text-sm font-semibold text-white">
							<?php echo $style['icon']; // phpcs:ignore WordPress.Security.EscapeOutput -- static inline SVG. ?>
							<?php echo esc_html( $term->name ); ?>
						</span>
					<?php endif; ?>

					<h1 class="mt-4 font-display text-4xl leading-[1.05] text-brand-bark md:text-5xl">
						<?php the_title(); ?>
					</h1>

					<?php if ( has_excerpt() ) : ?>
						<p class="mt-3 text-lg font-semibold leading-snug text-brand-bark md:text-xl">
							<?php echo esc_html( wp_strip_all_tags( get_the_excerpt() ) ); ?>
						</p>
					<?php endif; ?>

					<?php if ( get_the_content() ) : ?>
						<div class="entry-content mt-5">
							<?php the_content(); ?>
						</div>
					<?php endif; ?>

					<!-- Meta rows. Cost rides the date row as a soft pill, per the mockup. -->
					<div class="mt-7 space-y-3.5">
						<?php if ( $date_row ) : ?>
							<div class="flex flex-wrap items-center gap-3">
								<span class="inline-flex items-center gap-3 font-medium text-brand-bark">
									<span class="<?php echo esc_attr( $style['text'] ); ?>"><?php echo $icons['calendar']; // phpcs:ignore WordPress.Security.EscapeOutput -- static inline SVG. ?></span>
									<?php echo esc_html( $date_row ); ?>
								</span>
								<?php if ( $is_past ) : ?>
									<!-- Ended events have no action row, so the cost pill rides
										 the date row here instead of sitting beside the buttons. -->
									<span class="ml-auto rounded-xl <?php echo esc_attr( $style['soft'] . ' ' . $style['text'] ); ?> px-4 py-1.5 text-sm font-semibold">
										<?php echo esc_html( $cost ); ?>
									</span>
								<?php endif; ?>
							</div>
						<?php endif; ?>

						<?php if ( $start ) : ?>
							<div class="flex items-center gap-3 font-medium text-brand-bark">
								<span class="<?php echo esc_attr( $style['text'] ); ?>"><?php echo $icons['clock']; // phpcs:ignore WordPress.Security.EscapeOutput -- static inline SVG. ?></span>
								<?php echo esc_html( $start . ( $end ? ' – ' . $end : '' ) ); ?>
							</div>
						<?php endif; ?>

						<?php if ( $location ) : ?>
							<div class="flex items-center gap-3 font-medium text-brand-bark">
								<span class="<?php echo esc_attr( $style['text'] ); ?>"><?php echo $icons['pin']; // phpcs:ignore WordPress.Security.EscapeOutput -- static inline SVG. ?></span>
								<?php echo esc_html( $location ); ?>
							</div>
						<?php endif; ?>
					</div>

					<?php if ( $is_past ) : ?>
						<p class="mt-6 rounded-xl bg-black/[0.04] px-4 py-3 text-sm text-brand-bark/60">
							<?php esc_html_e( 'This event has ended — but there’s always something coming up below.', 'juiced' ); ?>
						</p>
					<?php else : ?>
						<div class="mt-8">
							<div class="flex flex-wrap items-center gap-3">
							<span class="rounded-xl <?php echo esc_attr( $style['soft'] . ' ' . $style['text'] ); ?> px-5 py-2.5 text-lg font-bold">
								<?php echo esc_html( $cost ); ?>
							</span>

							<?php if ( $span ) : ?>
								<!-- A native <details> dropdown: works with JS off, closes on
									 a second click, and needs no Alpine on this template. -->
								<details class="relative">
									<summary class="inline-flex cursor-pointer select-none list-none items-center gap-2 rounded-full border border-brand-bark/20 bg-white px-6 py-3 font-semibold text-brand-bark transition-colors hover:border-brand-green hover:text-brand-green [&::-webkit-details-marker]:hidden">
										<?php echo $icons['calendar']; // phpcs:ignore WordPress.Security.EscapeOutput -- static inline SVG. ?>
										<?php esc_html_e( 'Add to Calendar', 'juiced' ); ?>
										<svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6" /></svg>
									</summary>
									<div class="absolute left-0 top-full z-10 mt-2 w-60 rounded-2xl bg-white p-1.5 shadow-lg ring-1 ring-black/10">
										<a href="<?php echo esc_url( $gcal ); ?>" target="_blank" rel="noopener"
										   class="block rounded-xl px-3.5 py-2.5 text-sm font-medium text-brand-bark transition-colors hover:bg-black/[0.04]">
											<?php esc_html_e( 'Google Calendar', 'juiced' ); ?>
										</a>
										<a href="<?php echo esc_url( $outlook ); ?>" target="_blank" rel="noopener"
										   class="block rounded-xl px-3.5 py-2.5 text-sm font-medium text-brand-bark transition-colors hover:bg-black/[0.04]">
											<?php esc_html_e( 'Outlook.com', 'juiced' ); ?>
										</a>
										<a href="<?php echo esc_url( $ics ); ?>"
										   class="block rounded-xl px-3.5 py-2.5 text-sm font-medium text-brand-bark transition-colors hover:bg-black/[0.04]">
											<?php esc_html_e( 'Apple Calendar & others (.ics)', 'juiced' ); ?>
										</a>
									</div>
								</details>
							<?php endif; ?>
							</div>

							<!-- External RSVP / tickets (Eventbrite, Meetup, …) — the event's
								 signup_url field, on its own line under the row above. Hidden
								 until it's filled in, so it can never lead nowhere. -->
							<?php if ( $signup ) : ?>
								<a href="<?php echo esc_url( $signup ); ?>" target="_blank" rel="noopener"
								   class="mt-4 inline-flex items-center gap-2 rounded-full bg-brand-gold px-7 py-3 font-semibold text-white transition-colors hover:bg-brand-green">
									<?php esc_html_e( 'RSVP', 'juiced' ); ?>
									<span aria-hidden="true">&rarr;</span>
								</a>
							<?php endif; ?>
						</div>
					<?php endif; ?>

				</div>
			</div>
		</div>
	</section>

	<!-- More upcoming events — the Events page's list-view rows, stacked. -->
	<?php if ( $more ) : ?>
		<section class="bg-brand-cream bg-grain">
			<div class="mx-auto max-w-7xl px-4 pb-16 md:pb-24">

				<div class="flex flex-wrap items-end justify-between gap-4">
					<h2 class="font-display text-2xl text-brand-bark md:text-3xl">
						<?php esc_html_e( 'More Upcoming Events', 'juiced' ); ?>
					</h2>
					<a href="<?php echo esc_url( juiced_events_page_url() ); ?>"
					   class="inline-flex items-center gap-1.5 text-sm font-semibold text-brand-gold hover:underline">
						<?php esc_html_e( 'View All Events', 'juiced' ); ?>
						<span aria-hidden="true">&rarr;</span>
					</a>
				</div>

				<div class="mt-6 flex flex-col gap-4">
					<?php foreach ( $more as $i => $row ) : ?>
						<?php
						$card = juiced_event_card_data( $row, $i );
						if ( $card ) {
							get_template_part( 'template-parts/events/card-row', null, array( 'card' => $card, 'flyer' => false ) );
						}
						?>
					<?php endforeach; ?>
				</div>

			</div>
		</section>
	<?php endif; ?>

	<?php
endwhile;

get_footer();
