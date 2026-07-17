<?php
/**
 * Events page Section 1 — Hero.
 *
 * Two-tone headline, subtext, and three icon chips over a full-bleed event
 * photo that fades into the dark background from the left, so the copy stays
 * legible over whatever image the client uploads. Copy is ACF-editable on the
 * Events page (inc/acf-events-page.php); defaults below match the approved
 * mockup so the section renders before anything is set. With no image the
 * gradient collapses to solid ink, which still reads correctly.
 *
 * The chips reuse juiced_benefit_icon() — the same leaf / heart / people set as
 * the homepage hero, in the same order — so the two heroes stay visually of a
 * piece; only the labels differ.
 *
 * The search box under the subtext writes to the same Alpine `events` store as
 * the filter bar's pills (assets/js/main.js), so the two compose — the listing
 * below narrows as you type. JS-only, like the pills.
 *
 * @package Juiced
 */

$lead    = juiced_page_field( 'events_hero_lead', 'Events that' );
$accent  = juiced_page_field( 'events_hero_accent', 'Fuel Community' );
$subtext = juiced_page_field( 'events_hero_subtext', "From wellness workshops to creative nights and just-for-fun gatherings, Juiced! is where Raleigh comes together. There's always something good happening here." );
$image   = juiced_image_url( juiced_page_field( 'events_hero_image', '' ), 'juiced_hero' );
$chips   = array(
	juiced_page_field( 'events_hero_chip_1', 'Connect' ),
	juiced_page_field( 'events_hero_chip_2', 'Grow' ),
	juiced_page_field( 'events_hero_chip_3', 'Belong' ),
);
?>
<section class="relative isolate bg-brand-ink text-brand-cream overflow-hidden bg-grain" x-data>

	<?php if ( $image ) : ?>
		<div class="absolute inset-0 -z-10" aria-hidden="true">
			<img src="<?php echo esc_url( $image ); ?>" alt=""
				 class="h-full w-full object-cover object-center" loading="eager" fetchpriority="high" />
			<!-- Left-to-right scrim: solid behind the copy, clearing by the right
				 edge. Heavier on mobile, where the text sits over the image. -->
			<div class="absolute inset-0 bg-gradient-to-r from-brand-ink via-brand-ink/85 to-brand-ink/65 md:from-28% md:via-brand-ink/55 md:via-60% md:to-brand-ink/5"></div>
		</div>
	<?php endif; ?>

	<div class="mx-auto max-w-7xl px-4 py-16 md:py-24 min-h-[380px] md:min-h-[460px] flex items-center">
		<div class="max-w-xl">

			<h1 class="font-display text-5xl md:text-6xl leading-[1.03]">
				<span class="block text-white"><?php echo esc_html( $lead ); ?></span>
				<span class="block text-brand-gold"><?php echo esc_html( $accent ); ?></span>
			</h1>

			<?php if ( $subtext ) : ?>
				<p class="mt-6 max-w-md text-base md:text-lg text-brand-cream/80 leading-relaxed">
					<?php echo esc_html( $subtext ); ?>
				</p>
			<?php endif; ?>

			<!-- Search: writes the same store the filter pills do, so the two
				 compose. JS-only, like the pills — with JS off it has nothing
				 to narrow. -->
			<div class="mt-8 flex max-w-md items-center gap-1.5 rounded-full bg-white py-2 pl-5 pr-2.5 shadow-sm transition-shadow focus-within:ring-2 focus-within:ring-brand-gold/60">
				<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"
					 class="shrink-0 text-brand-bark/50" aria-hidden="true">
					<circle cx="11" cy="11" r="7" />
					<path d="m20 20-3.5-3.5" stroke-linecap="round" />
				</svg>
				<label for="event-search" class="sr-only"><?php esc_html_e( 'Search events', 'juiced' ); ?></label>
				<input type="text" id="event-search"
					   placeholder="<?php esc_attr_e( 'Search events…', 'juiced' ); ?>"
					   @input="$store.events.query = $event.target.value"
					   :value="$store.events.query"
					   class="w-full border-0 bg-transparent text-sm text-brand-bark placeholder:text-brand-bark/45 focus:outline-none focus:ring-0" />
				<button type="button" x-cloak x-show="$store.events.query"
						@click="$store.events.query = ''"
						aria-label="<?php esc_attr_e( 'Clear search', 'juiced' ); ?>"
						class="grid h-7 w-7 shrink-0 cursor-pointer place-items-center rounded-full text-brand-bark/40 transition-colors hover:bg-black/[0.05] hover:text-brand-bark/70">
					<svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
						<path d="M6 6l12 12M18 6 6 18" />
					</svg>
				</button>
			</div>

			<ul class="mt-10 flex flex-wrap gap-x-10 gap-y-4">
				<?php foreach ( $chips as $i => $chip ) : ?>
					<?php if ( ! $chip ) { continue; } ?>
					<li class="flex flex-col items-start gap-2">
						<?php echo juiced_benefit_icon( $i ); // phpcs:ignore WordPress.Security.EscapeOutput -- static inline SVG. ?>
						<span class="eyebrow text-brand-cream/90"><?php echo esc_html( $chip ); ?></span>
					</li>
				<?php endforeach; ?>
			</ul>

		</div>
	</div>
</section>
