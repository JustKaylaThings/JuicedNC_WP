<?php
/**
 * Events page Section 2 — the planner: a big month calendar beside "what's on".
 *
 * Left, a month grid with a dot under every day something is on — green for
 * recurring events, orange for one-offs. Click a day and the panel on the right
 * lists that day's events with their time, place and price. Opens on today.
 * When the picked day is empty, the panel offers a jump to the next day that
 * isn't.
 *
 * Below the panel, "Upcoming Events" lists each event once at its next
 * occurrence (juiced_upcoming_events()). It is rendered server-side, so it is
 * also what a no-JS visitor gets: the calendar and day panel are drawn
 * client-side and carry x-cloak, because a calendar whose days do nothing is
 * worse than none.
 *
 * Under both, the Follow Us block (promos.php) sits in its own row.
 *
 * Data comes from juiced_event_planner_data() (inc/event-dates.php), which tests
 * every day against the same one-off / recurring rules the list uses, so a
 * weekly event dots every matching day of the month, not just its next one.
 * The eventsPlanner component (assets/js/main.js) draws the grid.
 *
 * @package Juiced
 */

$juiced_data = function_exists( 'juiced_event_planner_data' ) ? juiced_event_planner_data( 12 ) : array();
$juiced_next = function_exists( 'juiced_upcoming_events' ) ? juiced_upcoming_events( 6 ) : array();

$juiced_eyebrow = juiced_page_field( 'events_calendar_heading', 'Event Calendar' );
$juiced_heading = juiced_page_field( 'events_list_heading', 'Upcoming Events' );

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

$juiced_arrow = 'grid h-11 w-11 shrink-0 cursor-pointer place-items-center rounded-full border border-brand-forest/25 text-brand-forest transition-colors hover:bg-brand-forest hover:text-brand-cream disabled:cursor-default disabled:opacity-25 disabled:hover:bg-transparent disabled:hover:text-brand-forest';
?>
<section class="bg-brand-cream text-brand-ink">
	<div class="mx-auto max-w-7xl px-4 pt-14 pb-8 md:pt-20 md:pb-12">
		<div class="grid gap-8 lg:grid-cols-12 lg:gap-10"
			<?php if ( ! empty( $juiced_data['months'] ) ) : ?>
				x-data="eventsPlanner(<?php echo esc_attr( wp_json_encode( $juiced_data ) ); ?>)"
			<?php endif; ?>>

			<?php if ( ! empty( $juiced_data['months'] ) ) : ?>
				<!-- Calendar -->
					<div x-cloak class="rounded-3xl bg-white p-4 ring-1 ring-brand-forest/15 sm:p-7 lg:col-span-7 lg:self-start">
						<?php if ( $juiced_eyebrow ) : ?>
							<p class="text-xs font-bold uppercase tracking-[.22em] text-brand-rust"><?php echo esc_html( $juiced_eyebrow ); ?></p>
						<?php endif; ?>

						<div class="mt-3 flex items-center justify-between gap-3">
							<h2 class="font-display text-[28px] font-bold leading-none text-brand-forest md:text-[32px]"
								aria-live="polite" x-text="month.label"></h2>
							<div class="flex gap-2">
								<button type="button" @click="i--" :disabled="!canPrev" class="<?php echo esc_attr( $juiced_arrow ); ?>"
										aria-label="<?php esc_attr_e( 'Previous month', 'juiced' ); ?>">
									<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 18l-6-6 6-6" /></svg>
								</button>
								<button type="button" @click="i++" :disabled="!canNext" class="<?php echo esc_attr( $juiced_arrow ); ?>"
										aria-label="<?php esc_attr_e( 'Next month', 'juiced' ); ?>">
									<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 18l6-6-6-6" /></svg>
								</button>
							</div>
						</div>

						<!-- Weekday header -->
						<div class="mt-6 grid grid-cols-7 gap-1 text-center sm:gap-1.5">
							<?php foreach ( $juiced_dow as $juiced_label ) : ?>
								<span class="text-[11px] font-bold uppercase tracking-[.12em] text-brand-ink/50"><?php echo esc_html( $juiced_label ); ?></span>
							<?php endforeach; ?>
						</div>

						<!-- Days -->
						<div class="mt-2 grid grid-cols-7 gap-1 sm:gap-1.5">
							<template x-for="(cell, index) in cells" :key="month.key + '-' + index">
								<div>
									<template x-if="cell.muted">
										<span class="grid h-12 place-items-center text-sm text-brand-ink/20 sm:h-16" x-text="cell.n"></span>
									</template>
									<template x-if="!cell.muted">
										<button type="button" @click="pick(cell.n)"
												:aria-pressed="cell.on" :aria-label="cell.label"
												class="flex h-12 w-full cursor-pointer flex-col items-center justify-center gap-1 rounded-xl font-display text-base font-semibold transition-colors sm:h-16 sm:text-[17px]"
												:class="cell.on
													? 'bg-brand-forest text-brand-cream'
													: (cell.today
														? 'text-brand-forest ring-2 ring-inset ring-brand-gold hover:bg-brand-forest/5'
														: 'text-brand-ink hover:bg-brand-forest/5')">
											<span x-text="cell.n"></span>
											<span class="flex h-1.5 gap-1" aria-hidden="true">
												<template x-for="(special, k) in cell.dots" :key="k">
													<span class="h-1.5 w-1.5 rounded-full"
														  :class="special ? 'bg-brand-gold' : (cell.on ? 'bg-brand-mint' : 'bg-brand-forest')"></span>
												</template>
											</span>
										</button>
									</template>
								</div>
							</template>
						</div>

						<!-- Legend -->
						<div class="mt-5 flex flex-wrap gap-x-5 gap-y-2 text-[13px] text-brand-ink/70">
							<span class="flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-brand-forest"></span><?php esc_html_e( 'Weekly & monthly', 'juiced' ); ?></span>
							<span class="flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-brand-gold"></span><?php esc_html_e( 'Special event', 'juiced' ); ?></span>
							<span class="flex items-center gap-2"><span class="h-3 w-3 rounded-[4px] ring-2 ring-inset ring-brand-gold"></span><?php esc_html_e( 'Today', 'juiced' ); ?></span>
						</div>
					</div>

			<?php endif; ?>

			<div class="flex flex-col gap-10 lg:col-span-5">
				<?php if ( ! empty( $juiced_data['months'] ) ) : ?>
					<!-- The picked day -->
					<div x-cloak class="flex flex-col gap-4" aria-live="polite">
						<p class="text-xs font-bold uppercase tracking-[.22em] text-brand-rust" x-text="selLabel"></p>

						<template x-for="(ev, k) in selEvents" :key="sel.i + '-' + sel.d + '-' + k">
							<article class="flex flex-col gap-2 rounded-3xl p-6"
									 :class="ev.s ? 'bg-brand-forest bg-garden text-brand-cream' : 'bg-white ring-1 ring-brand-forest/15'">
								<p class="text-xs font-bold uppercase tracking-[.18em]"
								   :class="ev.s ? 'text-brand-gold' : 'text-brand-forest'"
								   x-text="ev.s ? '<?php echo esc_js( __( 'Special event', 'juiced' ) ); ?>' : ev.k"></p>
								<h3 class="font-display text-[28px] font-bold leading-[1.05]">
									<a :href="ev.u" class="hover:underline" x-text="ev.t"></a>
								</h3>
								<p class="text-[15px]" :class="ev.s ? 'text-brand-mint' : 'text-brand-ink/70'" x-text="meta(ev)"></p>
								<div class="mt-3 flex flex-wrap gap-2.5">
									<template x-if="ev.g">
										<a :href="ev.g" target="_blank" rel="noopener"
										   class="inline-flex items-center rounded-full bg-brand-gold px-5 py-2.5 text-sm font-bold text-brand-ink transition-colors hover:bg-brand-cream">
											<?php esc_html_e( 'Sign up', 'juiced' ); ?>
										</a>
									</template>
									<a :href="ev.u"
									   class="inline-flex items-center gap-1.5 rounded-full border-2 px-5 py-2 text-sm font-bold transition-colors"
									   :class="ev.s ? 'border-brand-cream text-brand-cream hover:bg-brand-cream hover:text-brand-forest' : 'border-brand-forest text-brand-forest hover:bg-brand-forest hover:text-brand-cream'">
										<?php esc_html_e( 'Details', 'juiced' ); ?> <span aria-hidden="true">&rarr;</span>
									</a>
								</div>
							</article>
						</template>

						<template x-if="!selEvents.length">
							<div class="rounded-3xl border border-dashed border-brand-forest/30 p-6 text-[15px] text-brand-ink/70">
								<p><?php esc_html_e( "Nothing on the calendar this day — the juice bar's still open, so come say hi.", 'juiced' ); ?></p>
								<template x-if="nextDay">
									<button type="button" @click="jump()"
											class="mt-4 inline-flex cursor-pointer items-center gap-1.5 rounded-full border-2 border-brand-forest px-5 py-2 text-sm font-bold text-brand-forest transition-colors hover:bg-brand-forest hover:text-brand-cream">
										<span x-text="'<?php echo esc_js( __( 'Next event:', 'juiced' ) ); ?> ' + nextDay.label"></span>
										<span aria-hidden="true">&rarr;</span>
									</button>
								</template>
							</div>
						</template>
					</div>
				<?php endif; ?>

			<!-- Upcoming: each event once, at its next date. Server-rendered, so
				 it stands in for the calendar with JS off. -->
			<?php if ( $juiced_next ) : ?>
				<div>
					<h2 class="font-display text-2xl font-bold text-brand-forest"><?php echo esc_html( $juiced_heading ); ?></h2>
					<ul class="mt-3">
						<?php foreach ( $juiced_next as $juiced_row ) : ?>
							<li>
								<a href="<?php echo esc_url( get_permalink( $juiced_row['post'] ) ); ?>"
								   class="group flex items-center gap-4 border-t border-brand-forest/15 py-3.5">
									<span class="w-16 shrink-0 font-display text-lg font-bold text-brand-rust"><?php echo esc_html( wp_date( 'M j', $juiced_row['when']->getTimestamp() ) ); ?></span>
									<span class="flex-1 font-semibold text-brand-ink group-hover:text-brand-forest"><?php echo esc_html( get_the_title( $juiced_row['post'] ) ); ?></span>
									<span class="font-bold text-brand-forest transition-transform group-hover:translate-x-1" aria-hidden="true">&rarr;</span>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php elseif ( empty( $juiced_data['months'] ) ) : ?>
				<p class="rounded-3xl bg-white px-6 py-10 text-center text-brand-ink/70 ring-1 ring-brand-forest/15">
					<?php esc_html_e( 'New events are on the way — check back soon.', 'juiced' ); ?>
				</p>
			<?php endif; ?>
			</div>

		</div>

		<?php
		// Follow us. Buffered so the row (and its top margin) disappears
		// when there is nothing to show.
		ob_start();
		get_template_part( 'template-parts/events/promos' );
		$juiced_promos = trim( preg_replace( '/<!--.*?-->/s', '', ob_get_clean() ) );
		?>
		<?php if ( $juiced_promos ) : ?>
			<div class="mt-14 md:mt-20">
				<?php echo $juiced_promos; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in promos.php. ?>
			</div>
		<?php endif; ?>
	</div>
</section>
