<?php
/**
 * Events page Section 3 — "Upcoming Events" listing + sidebar.
 *
 * The main band of the page: every upcoming event in a wide left column, with
 * the calendar / promo sidebar alongside it on desktop and stacked beneath on
 * mobile. Events come from juiced_upcoming_events() (inc/event-dates.php),
 * which resolves one-off and recurring records to their next concrete date and
 * sorts by it, so "upcoming" is real rather than publish order.
 *
 * Two views, driven by the filter bar's "View:" toggle through the shared
 * `events` store: grid draws posters (card-poster.php) two up, list draws wide
 * rows (card-row.php) stacked. Both are rendered server-side and swapped with
 * x-show, because their structure differs too much to reshape one into the
 * other with class bindings. That does put each event in the DOM twice; the
 * hidden view's flyers stay unfetched thanks to loading="lazy", and both views
 * read their values from one juiced_event_card_data() call per event, so the
 * duplication is markup only.
 *
 * Filtering and paging are client-side over that server-rendered markup: the
 * eventsListing component (assets/js/main.js) decides which cards are on screen.
 * Cards past the first page ship with an inline display:none rather than waiting
 * for Alpine to hide them — Alpine is deferred, so x-show alone would flash the
 * whole list before collapsing it. Same reason the list view ships hidden: grid
 * is the default. "View More Events" carries x-cloak for the mirror-image
 * reason: with JS off the button can't work, so it should never appear.
 *
 * That does mean a no-JS visitor sees the first page of posters only. The
 * tradeoff is deliberate — the alternative flashes a long list on every load for
 * everyone — and each event is still individually reachable at /event/{slug}
 * and indexable from the sitemap.
 *
 * @package Juiced
 */

// Well past the 6 the design shows, so "View More" has something to reveal,
// but bounded — this renders every card up front.
$juiced_events = function_exists( 'juiced_upcoming_events' ) ? juiced_upcoming_events( 24 ) : array();

if ( empty( $juiced_events ) ) {
	return;
}

$juiced_per_page = 6;

// Resolve each event once, then hand the same data to both views. Also builds
// the positional array the Alpine component filters on — s: term slugs for the
// category pills, t: a lowercased haystack (title, blurb, category, audience,
// cost) for the filter bar's search box — so filtering never reads the DOM.
$juiced_cards    = array();
$juiced_cards_js = array();

foreach ( $juiced_events as $juiced_i => $juiced_row ) {
	$juiced_card = juiced_event_card_data( $juiced_row, $juiced_i );
	if ( empty( $juiced_card ) ) {
		continue;
	}

	$juiced_terms = get_the_terms( $juiced_card['id'], 'event_category' );
	$juiced_terms = ( $juiced_terms && ! is_wp_error( $juiced_terms ) ) ? $juiced_terms : array();

	$juiced_haystack = implode(
		' ',
		array_filter(
			array(
				$juiced_card['title'],
				$juiced_card['excerpt'],
				implode( ' ', wp_list_pluck( $juiced_terms, 'name' ) ),
				$juiced_card['audience'],
				$juiced_card['cost'],
			)
		)
	);

	// Entity-decode so texturized quotes and &nbsp; can't hide words from the
	// search box; NBSPs become plain spaces for the same reason.
	$juiced_haystack = html_entity_decode( wp_strip_all_tags( $juiced_haystack ), ENT_QUOTES, get_bloginfo( 'charset' ) );
	$juiced_haystack = str_replace( "\xC2\xA0", ' ', $juiced_haystack );

	$juiced_cards[]    = $juiced_card;
	$juiced_cards_js[] = array(
		's' => array_values( wp_list_pluck( $juiced_terms, 'slug' ) ),
		't' => mb_strtolower( $juiced_haystack ),
	);
}

if ( empty( $juiced_cards ) ) {
	return;
}

$juiced_heading = juiced_page_field( 'events_list_heading', 'Upcoming Events' );
?>
<section class="bg-brand-cream bg-grain"
		 x-data="eventsListing(<?php echo esc_attr( wp_json_encode( $juiced_cards_js ) ); ?>, <?php echo esc_attr( $juiced_per_page ); ?>)">
	<div class="mx-auto max-w-7xl px-4 pt-10 pb-16 md:pt-12 md:pb-24">
		<div class="grid gap-8 lg:grid-cols-3 lg:gap-10">

			<!-- Events -->
			<div class="lg:col-span-2">

				<h2 class="font-display text-2xl md:text-3xl text-brand-bark">
					<?php echo esc_html( $juiced_heading ); ?>
				</h2>

				<!-- Grid view: posters -->
				<div x-show="$store.events.layout === 'grid'"
					 class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2">
					<?php foreach ( $juiced_cards as $juiced_i => $juiced_card ) : ?>
						<div x-show="isVisible(<?php echo esc_attr( $juiced_i ); ?>)"
							<?php echo $juiced_i >= $juiced_per_page ? ' style="display:none"' : ''; ?>>
							<?php get_template_part( 'template-parts/events/card-poster', null, array( 'card' => $juiced_card ) ); ?>
						</div>
					<?php endforeach; ?>
				</div>

				<!-- List view: rows -->
				<div x-show="$store.events.layout === 'list'" style="display:none"
					 class="mt-6 flex flex-col gap-4">
					<?php foreach ( $juiced_cards as $juiced_i => $juiced_card ) : ?>
						<div x-show="isVisible(<?php echo esc_attr( $juiced_i ); ?>)"
							<?php echo $juiced_i >= $juiced_per_page ? ' style="display:none"' : ''; ?>>
							<?php get_template_part( 'template-parts/events/card-row', null, array( 'card' => $juiced_card ) ); ?>
						</div>
					<?php endforeach; ?>
				</div>

				<!-- No event matches the active filter. Only reachable with JS on,
					 which is also the only way to change the filter. -->
				<p x-cloak x-show="isEmpty" class="mt-6 rounded-2xl bg-white px-6 py-10 text-center text-sm text-brand-bark/60 ring-1 ring-black/5">
					<?php esc_html_e( 'Nothing on the calendar matches that just yet — try another search or category.', 'juiced' ); ?>
				</p>

				<!-- View more -->
				<div x-cloak x-show="hasMore && !expanded" class="mt-8 flex justify-center">
					<button type="button" @click="expanded = true"
							class="inline-flex cursor-pointer items-center gap-2 rounded-full border border-brand-gold/40 bg-white px-6 py-2.5 text-sm font-medium text-brand-gold transition-colors hover:border-brand-gold hover:bg-brand-gold hover:text-white">
						<?php esc_html_e( 'View More Events', 'juiced' ); ?>
						<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
							<path d="M6 9l6 6 6-6" />
						</svg>
					</button>
				</div>
			</div>

			<!-- Sidebar: calendar + promos -->
			<div class="lg:col-span-1">
				<div class="flex flex-col gap-6">
					<?php get_template_part( 'template-parts/events/calendar' ); ?>
					<?php get_template_part( 'template-parts/events/promos' ); ?>
				</div>
			</div>

		</div>
	</div>
</section>
