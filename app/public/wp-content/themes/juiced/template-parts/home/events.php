<?php
/**
 * Homepage Section 3 — This Week at Juiced (upcoming events).
 *
 * PHP port of the Next.js EventsStrip. Pulls the next 3 upcoming events from the
 * Event CPT (juiced_upcoming_events(), inc/event-dates.php), which resolves each
 * record — one-off or weekly recurring — to its next concrete date and sorts by
 * it. Section copy is ACF-editable on the Home page (inc/acf-homepage.php).
 *
 * Each card shows a colored date badge (weekday + day number), the title, the
 * excerpt, a flyer thumbnail, and a colored time row. Badge/title/time colors
 * rotate through a fixed palette (purple / green / pink) by card position,
 * matching the approved mockup. The flyer is the event's featured image, cropped
 * to a square (object-cover); events with no image get a soft colored fallback
 * panel showing the category name or a calendar icon, so no card looks bare.
 * Renders nothing when there are no upcoming events.
 *
 * @package Juiced
 */

$juiced_events = function_exists( 'juiced_upcoming_events' ) ? juiced_upcoming_events( 3 ) : array();

if ( empty( $juiced_events ) ) {
	return;
}

$juiced_heading = juiced_field( 'events_heading', 'This Week' );
$juiced_accent  = juiced_field( 'events_heading_accent', 'at Juiced' );

// "View All Events" points to the ACF field, falling back to the Events page
// (the CPT has no archive — see juiced_events_page_url()).
$juiced_cal_url = juiced_field( 'events_link_url', '' );
if ( ! $juiced_cal_url ) {
	$juiced_cal_url = juiced_events_page_url();
}

// Per-card accent palette: badge background, matching title/time text, and a
// soft tint for the no-image fallback panel. Literal class strings so Tailwind's
// @source scan picks up the arbitrary hex values.
$juiced_palette = array(
	array( 'bg' => 'bg-[#6c4fd6]', 'text' => 'text-[#6c4fd6]', 'soft' => 'bg-[#6c4fd6]/10' ), // purple
	array( 'bg' => 'bg-brand-green', 'text' => 'text-brand-green', 'soft' => 'bg-brand-green/10' ), // green
	array( 'bg' => 'bg-[#e0559a]', 'text' => 'text-[#e0559a]', 'soft' => 'bg-[#e0559a]/10' ), // pink
);
?>
<section class="relative bg-brand-cream bg-grain overflow-hidden">
	<div class="mx-auto max-w-7xl px-4 py-16 md:py-24">

		<!-- Section heading -->
		<h2 class="text-center font-display text-3xl md:text-4xl text-brand-bark mb-12 md:mb-14">
			<?php echo esc_html( $juiced_heading ); ?>
			<span class="text-brand-gold"><?php echo esc_html( $juiced_accent ); ?></span>
		</h2>

		<!-- Mobile: horizontal snap rail / Desktop: 3-up grid -->
		<div class="-mx-4 px-[9vw] md:mx-0 md:px-0 flex md:grid md:grid-cols-3 gap-5 md:gap-7 overflow-x-auto md:overflow-visible snap-x snap-mandatory no-scrollbar pb-2 md:pb-0 items-stretch">
			<?php
			foreach ( $juiced_events as $i => $row ) :
				$post_id  = $row['post']->ID;
				$when     = $row['when'];
				$color    = $juiced_palette[ $i % 3 ];

				$weekday  = strtoupper( $when->format( 'D' ) ); // TUE
				$day      = $when->format( 'd' );                // 15
				$time     = juiced_format_event_time( get_field( 'start_time', $post_id ) );
				$end_time = juiced_format_event_time( get_field( 'end_time', $post_id ) );
				$excerpt  = wp_strip_all_tags( get_the_excerpt( $post_id ) );
				$image    = get_the_post_thumbnail_url( $post_id, 'juiced_card' );
				$terms    = get_the_terms( $post_id, 'event_category' );
				$category = ( $terms && ! is_wp_error( $terms ) ) ? $terms[0]->name : '';
				$permalink = get_permalink( $post_id );
				?>
				<a href="<?php echo esc_url( $permalink ); ?>"
				   class="group relative flex flex-col h-full snap-center shrink-0 w-[82vw] md:w-auto bg-white rounded-2xl shadow-sm ring-1 ring-black/5 hover:shadow-md hover:ring-black/10 transition-all p-6 md:p-7">

					<!-- Badge + title + flyer thumbnail -->
					<div class="flex items-start gap-4">
						<div class="shrink-0 w-14 rounded-xl <?php echo esc_attr( $color['bg'] ); ?> text-white text-center py-2 leading-none">
							<span class="block text-[0.62rem] font-semibold tracking-[0.12em]"><?php echo esc_html( $weekday ); ?></span>
							<span class="block text-2xl font-bold mt-1"><?php echo esc_html( $day ); ?></span>
						</div>

						<h3 class="min-w-0 flex-1 font-display font-bold text-xl md:text-2xl leading-tight <?php echo esc_attr( $color['text'] ); ?>">
							<?php echo esc_html( get_the_title( $post_id ) ); ?>
						</h3>

						<?php if ( $image ) : ?>
							<img src="<?php echo esc_url( $image ); ?>" alt=""
								 class="shrink-0 w-20 h-20 md:w-24 md:h-24 rounded-xl object-cover" loading="lazy" />
						<?php else : ?>
							<div class="shrink-0 w-20 h-20 md:w-24 md:h-24 rounded-xl <?php echo esc_attr( $color['soft'] . ' ' . $color['text'] ); ?> flex items-center justify-center text-center p-2">
								<?php if ( $category ) : ?>
									<span class="text-[0.68rem] font-semibold uppercase tracking-wide leading-tight"><?php echo esc_html( $category ); ?></span>
								<?php else : ?>
									<svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
										<rect x="3" y="4" width="18" height="18" rx="2" />
										<path d="M16 2v4M8 2v4M3 10h18" stroke-linecap="round" />
									</svg>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					</div>

					<!-- Description -->
					<?php if ( $excerpt ) : ?>
						<p class="mt-4 text-sm text-brand-bark/70 leading-relaxed">
							<?php echo esc_html( wp_trim_words( $excerpt, 16 ) ); ?>
						</p>
					<?php endif; ?>

					<!-- Time row -->
					<?php if ( $time ) : ?>
						<div class="mt-auto pt-6 flex items-center gap-2 text-sm font-semibold <?php echo esc_attr( $color['text'] ); ?>">
							<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
								<circle cx="12" cy="12" r="9" />
								<path d="M12 7v5l3 2" stroke-linecap="round" stroke-linejoin="round" />
							</svg>
							<span><?php echo esc_html( $time ); ?><?php if ( $end_time ) : ?> &ndash; <?php echo esc_html( $end_time ); ?><?php endif; ?></span>
						</div>
					<?php endif; ?>
				</a>
			<?php endforeach; ?>
		</div>

		<!-- View all events -->
		<?php if ( $juiced_cal_url ) : ?>
			<div class="mt-12 flex justify-center">
				<a href="<?php echo esc_url( $juiced_cal_url ); ?>"
				   class="inline-flex items-center gap-2 rounded-full border border-brand-bark/20 bg-white px-6 py-2.5 text-sm font-medium text-brand-bark hover:border-brand-green hover:text-brand-green transition-colors">
					<?php esc_html_e( 'View All Events', 'juiced' ); ?>
					<span aria-hidden="true">&rarr;</span>
				</a>
			</div>
		<?php endif; ?>

	</div>
</section>
