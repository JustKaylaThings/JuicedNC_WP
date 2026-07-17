<?php
/**
 * Events page Section 2 — "Find Your Vibe" filter bar.
 *
 * A pill per event_category term that has published events, plus an "All
 * Events" reset and a grid/list layout toggle. Writes to the shared Alpine
 * `events` store (assets/js/main.js); the listing section reads it.
 *
 * Pills are built from the live taxonomy rather than the mockup's fixed five,
 * so categories the client adds appear here automatically —
 * juiced_event_category_style() supplies each one's color and icon and has a
 * fallback for slugs it doesn't know. Renders nothing when no term is in use,
 * which also hides the layout toggle (nothing to toggle).
 *
 * Copy is deliberately not ACF-editable: "Find Your Vibe" is the section's
 * identity in the approved design, and the labels below it are already editable
 * as the category terms themselves.
 *
 * @package Juiced
 */

$juiced_categories = function_exists( 'juiced_event_categories' ) ? juiced_event_categories() : array();

if ( empty( $juiced_categories ) ) {
	return;
}

// Shared pill geometry; state-dependent colors come from the :class bindings.
$juiced_pill = 'inline-flex items-center gap-2 rounded-full border px-4 py-2 text-sm font-medium transition-colors cursor-pointer';
?>
<section class="bg-brand-cream bg-grain" x-data>
	<div class="mx-auto max-w-7xl px-4 pt-14 md:pt-20">

		<h2 class="font-display text-3xl md:text-4xl text-brand-bark">
			<?php esc_html_e( 'Find Your', 'juiced' ); ?>
			<span class="text-brand-gold"><?php esc_html_e( 'Vibe', 'juiced' ); ?></span>
		</h2>

		<div class="mt-6 flex flex-wrap items-center justify-between gap-x-6 gap-y-4">

			<!-- Category pills -->
			<div class="flex flex-wrap items-center gap-2.5" role="group" aria-label="<?php esc_attr_e( 'Filter events by category', 'juiced' ); ?>">

				<button type="button"
						class="<?php echo esc_attr( $juiced_pill ); ?>"
						@click="$store.events.category = 'all'"
						:aria-pressed="$store.events.category === 'all'"
						:class="$store.events.category === 'all'
							? 'bg-brand-gold border-brand-gold text-white'
							: 'bg-white border-black/10 text-brand-bark hover:border-brand-gold/60'">
					<?php esc_html_e( 'All Events', 'juiced' ); ?>
				</button>

				<?php
				foreach ( $juiced_categories as $juiced_term ) :
					$style = juiced_event_category_style( $juiced_term->slug );
					// Term slugs are sanitized by WP (lowercase, no quotes), so they
					// drop straight into the Alpine expressions as string literals.
					$slug = $juiced_term->slug;
					?>
					<button type="button"
							class="<?php echo esc_attr( $juiced_pill ); ?>"
							@click="$store.events.category = '<?php echo esc_attr( $slug ); ?>'"
							:aria-pressed="$store.events.category === '<?php echo esc_attr( $slug ); ?>'"
							:class="$store.events.category === '<?php echo esc_attr( $slug ); ?>'
								? '<?php echo esc_attr( $style['bg'] ); ?> border-transparent text-white'
								: 'bg-white border-black/10 text-brand-bark hover:border-black/25'">
						<span :class="$store.events.category === '<?php echo esc_attr( $slug ); ?>' ? 'text-white' : '<?php echo esc_attr( $style['text'] ); ?>'">
							<?php echo $style['icon']; // phpcs:ignore WordPress.Security.EscapeOutput -- static inline SVG. ?>
						</span>
						<?php echo esc_html( $juiced_term->name ); ?>
					</button>
				<?php endforeach; ?>
			</div>

			<!-- Layout toggle (the search box lives in the hero, writing the
				 same store). -->
			<div class="flex items-center gap-2.5">
				<span class="text-sm text-brand-bark/60"><?php esc_html_e( 'View:', 'juiced' ); ?></span>

				<button type="button"
						class="grid place-items-center h-9 w-9 rounded-lg border transition-colors cursor-pointer"
						@click="$store.events.layout = 'grid'"
						:aria-pressed="$store.events.layout === 'grid'"
						aria-label="<?php esc_attr_e( 'Grid view', 'juiced' ); ?>"
						:class="$store.events.layout === 'grid'
							? 'bg-white border-brand-gold text-brand-gold'
							: 'bg-transparent border-transparent text-brand-bark/40 hover:text-brand-bark/70'">
					<svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" aria-hidden="true">
						<rect x="3" y="3" width="8" height="8" rx="1.5" /><rect x="13" y="3" width="8" height="8" rx="1.5" />
						<rect x="3" y="13" width="8" height="8" rx="1.5" /><rect x="13" y="13" width="8" height="8" rx="1.5" />
					</svg>
				</button>

				<button type="button"
						class="grid place-items-center h-9 w-9 rounded-lg border transition-colors cursor-pointer"
						@click="$store.events.layout = 'list'"
						:aria-pressed="$store.events.layout === 'list'"
						aria-label="<?php esc_attr_e( 'List view', 'juiced' ); ?>"
						:class="$store.events.layout === 'list'
							? 'bg-white border-brand-gold text-brand-gold'
							: 'bg-transparent border-transparent text-brand-bark/40 hover:text-brand-bark/70'">
					<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
						<path d="M4 6h16M4 12h16M4 18h16" />
					</svg>
				</button>
			</div>

		</div>
	</div>
</section>
