<?php
/**
 * Events page Section 3c — the sidebar "Event Calendar" widget.
 *
 * A month at a glance with a colored dot on every day something is on, and
 * arrows to walk forward through the months PHP precomputed. Dots take their
 * color from the event's category (juiced_event_category_style()), matching its
 * pill in the filter bar and its tag on the cards.
 *
 * Days come from juiced_event_calendar_months() (inc/event-dates.php), which
 * tests each day against the same one-off / recurring rules the listing uses —
 * so a weekly event dots every matching day of the month, not just its next
 * occurrence. The grid itself is drawn client-side from that data.
 *
 * Being client-drawn, the widget is hidden without JS (x-cloak) rather than
 * degrading to a static month. That's deliberate: a calendar whose arrows do
 * nothing is worse than no calendar, and the event list beside it carries the
 * same dates in a form that needs no JS at all.
 *
 * @package Juiced
 */

$juiced_months = function_exists( 'juiced_event_calendar_months' ) ? juiced_event_calendar_months( 12 ) : array();

if ( empty( $juiced_months ) ) {
	return;
}

$juiced_heading = juiced_page_field( 'events_calendar_heading', 'Event Calendar' );

// Sun..Sat, matching the 'lead' offset PHP sends (0 = Sunday, like date('w')).
$juiced_dow = array(
	__( 'Sun', 'juiced' ),
	__( 'Mon', 'juiced' ),
	__( 'Tue', 'juiced' ),
	__( 'Wed', 'juiced' ),
	__( 'Thu', 'juiced' ),
	__( 'Fri', 'juiced' ),
	__( 'Sat', 'juiced' ),
);
?>
<div x-cloak x-data="eventsCalendar(<?php echo esc_attr( wp_json_encode( $juiced_months ) ); ?>)"
	 class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-black/5">

	<h2 class="font-display text-xl text-brand-bark">
		<?php echo esc_html( $juiced_heading ); ?>
	</h2>

	<!-- Month nav -->
	<div class="mt-4 flex items-center justify-between gap-2">
		<button type="button" @click="i--" :disabled="!canPrev"
				class="grid h-8 w-8 shrink-0 cursor-pointer place-items-center rounded-full text-brand-gold transition-colors hover:bg-brand-gold/10 disabled:cursor-default disabled:opacity-25 disabled:hover:bg-transparent"
				aria-label="<?php esc_attr_e( 'Previous month', 'juiced' ); ?>">
			<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
				<path d="M15 18l-6-6 6-6" />
			</svg>
		</button>

		<span class="font-display text-base font-bold text-brand-bark" aria-live="polite" x-text="month.label"></span>

		<button type="button" @click="i++" :disabled="!canNext"
				class="grid h-8 w-8 shrink-0 cursor-pointer place-items-center rounded-full text-brand-gold transition-colors hover:bg-brand-gold/10 disabled:cursor-default disabled:opacity-25 disabled:hover:bg-transparent"
				aria-label="<?php esc_attr_e( 'Next month', 'juiced' ); ?>">
			<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
				<path d="M9 18l6-6-6-6" />
			</svg>
		</button>
	</div>

	<!-- Weekday header -->
	<div class="mt-4 grid grid-cols-7 gap-1 text-center">
		<?php foreach ( $juiced_dow as $juiced_label ) : ?>
			<span class="text-[0.65rem] font-semibold uppercase tracking-wide text-brand-bark/45">
				<?php echo esc_html( $juiced_label ); ?>
			</span>
		<?php endforeach; ?>
	</div>

	<!-- Days -->
	<div class="mt-2 grid grid-cols-7 gap-1 text-center">
		<template x-for="(cell, index) in cells" :key="month.key + '-' + index">
			<div class="py-0.5">
				<!-- A dotted day links to its event; everything else is plain text.
					 Days running several events carry a count badge and link to the
					 first — the tooltip names them all, and the list beside the
					 calendar is where the rest are reachable. -->
				<template x-if="cell.dot">
					<a :href="cell.dot.u" :title="label(cell)"
					   class="relative mx-auto grid h-7 w-7 place-items-center rounded-full text-xs font-bold text-white transition-transform hover:scale-110"
					   :class="cell.dot.c">
						<span x-text="cell.n"></span>
						<span x-show="cell.dot.n > 1" x-text="cell.dot.n"
							  class="absolute -right-1 -top-1 grid h-3.5 w-3.5 place-items-center rounded-full bg-brand-ink text-[0.55rem] font-bold leading-none text-white ring-1 ring-white"></span>
						<span class="sr-only" x-text="', ' + label(cell)"></span>
					</a>
				</template>

				<template x-if="!cell.dot">
					<span class="mx-auto grid h-7 w-7 place-items-center rounded-full text-xs"
						  :class="cell.muted
							? 'text-brand-bark/25'
							: (cell.today ? 'font-bold text-brand-bark ring-1 ring-brand-gold/60' : 'text-brand-bark/70')"
						  x-text="cell.n"></span>
				</template>
			</div>
		</template>
	</div>
</div>
