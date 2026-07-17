<?php
/**
 * Herb Library page Section 2 — "Explore Herbs" sidebar + herb grid.
 *
 * The main band of the page: a category sidebar down the left (every
 * herb_category term in use, plus an "All Herbs" reset and a "Did You Know?"
 * note beneath), and the herb card grid filling the rest, headed by a live
 * count and an A–Z / Z–A sort.
 *
 * Every published herb renders server-side, alphabetically; the herbsLibrary
 * Alpine component (assets/js/main.js) decides which are on screen. Category
 * clicks write the shared `herbs` store — the same store the hero's search box
 * writes as the visitor types — so the two filters compose. Sorting flips CSS
 * `order` over the alphabetical render instead of moving the DOM. With JS off
 * the full grid simply stays visible, A–Z, and every herb remains reachable at
 * /herbs/{slug}.
 *
 * Category names/icons come from the terms themselves via
 * juiced_herb_topic_icon(), so client-added categories appear automatically.
 *
 * @package Juiced
 */

$juiced_herbs = get_posts(
	array(
		'post_type'      => 'herb',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => 'title',
		'order'          => 'ASC',
		'no_found_rows'  => true,
	)
);

if ( empty( $juiced_herbs ) ) {
	return;
}

// Resolve each herb once; the same pass builds the positional array the Alpine
// component filters on (s: category slugs, t: search haystack).
$juiced_cards    = array();
$juiced_cards_js = array();

foreach ( $juiced_herbs as $juiced_post ) {
	$juiced_card = juiced_herb_card_data( $juiced_post );
	if ( empty( $juiced_card ) ) {
		continue;
	}
	$juiced_cards[]    = $juiced_card;
	$juiced_cards_js[] = array(
		's' => $juiced_card['slugs'],
		't' => $juiced_card['haystack'],
	);
}

if ( empty( $juiced_cards ) ) {
	return;
}

$juiced_categories = get_terms(
	array(
		'taxonomy'   => 'herb_category',
		'hide_empty' => true,
	)
);
$juiced_categories = is_wp_error( $juiced_categories ) ? array() : $juiced_categories;

// Heading + count labels for the Alpine component, so "All Herbs" and the
// category names it swaps in stay translatable and server-owned.
$juiced_labels = array(
	'all'   => __( 'All Herbs', 'juiced' ),
	'one'   => __( 'herb found', 'juiced' ),
	'many'  => __( 'herbs found', 'juiced' ),
	'terms' => wp_list_pluck( $juiced_categories, 'name', 'slug' ),
);

$juiced_sidebar_heading = juiced_page_field( 'herbs_sidebar_heading', 'Explore Herbs' );
$juiced_dyk_heading     = juiced_page_field( 'herbs_didyouknow_heading', 'Did You Know?' );
$juiced_dyk_body        = juiced_page_field( 'herbs_didyouknow_body', 'Herbs have been used for thousands of years to support health and healing.' );

// Shared geometry for the sidebar category buttons; state colors are bound.
$juiced_cat_btn = 'flex w-full cursor-pointer items-center gap-2.5 rounded-lg px-3.5 py-2.5 text-left text-base font-medium transition-colors';
?>
<section id="herb-listing" class="bg-brand-cream bg-grain"
		 x-data="herbsLibrary(<?php echo esc_attr( wp_json_encode( $juiced_cards_js ) ); ?>, <?php echo esc_attr( wp_json_encode( $juiced_labels ) ); ?>)">
	<div class="mx-auto max-w-7xl px-4 py-14 md:py-20">
		<div class="grid gap-8 lg:grid-cols-[16rem_1fr] lg:gap-10">

			<!-- Sidebar -->
			<aside>
				<h2 class="font-display text-2xl text-brand-bark">
					<?php echo esc_html( $juiced_sidebar_heading ); ?>
				</h2>

				<div class="mt-5 rounded-2xl bg-white p-2 shadow-sm ring-1 ring-black/5"
					 role="group" aria-label="<?php esc_attr_e( 'Filter herbs by category', 'juiced' ); ?>">

					<button type="button"
							class="<?php echo esc_attr( $juiced_cat_btn ); ?>"
							@click="$store.herbs.category = 'all'"
							:aria-pressed="$store.herbs.category === 'all'"
							:class="$store.herbs.category === 'all'
								? 'bg-brand-green/10 text-brand-green'
								: 'text-brand-bark/75 hover:bg-black/[0.04]'">
						<svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
							<circle cx="12" cy="12" r="9" />
							<path d="m8.5 12 2.5 2.5 4.5-5" />
						</svg>
						<?php esc_html_e( 'All Herbs', 'juiced' ); ?>
					</button>

					<?php foreach ( $juiced_categories as $juiced_term ) : ?>
						<?php $juiced_topic = juiced_herb_topic_icon( $juiced_term->slug ); ?>
						<button type="button"
								class="<?php echo esc_attr( $juiced_cat_btn ); ?>"
								@click="$store.herbs.category = '<?php echo esc_attr( $juiced_term->slug ); ?>'"
								:aria-pressed="$store.herbs.category === '<?php echo esc_attr( $juiced_term->slug ); ?>'"
								:class="$store.herbs.category === '<?php echo esc_attr( $juiced_term->slug ); ?>'
									? 'bg-brand-green/10 text-brand-green'
									: 'text-brand-bark/75 hover:bg-black/[0.04]'">
							<span class="<?php echo esc_attr( $juiced_topic['text'] ); ?>">
								<?php echo $juiced_topic['icon']; // phpcs:ignore WordPress.Security.EscapeOutput -- static inline SVG. ?>
							</span>
							<?php echo esc_html( $juiced_term->name ); ?>
						</button>
					<?php endforeach; ?>
				</div>

				<!-- Did You Know? -->
				<?php if ( $juiced_dyk_heading ) : ?>
					<div class="mt-6 rounded-2xl bg-brand-paper p-5">
						<h3 class="font-display text-xl text-brand-gold"><?php echo esc_html( $juiced_dyk_heading ); ?></h3>
						<?php if ( $juiced_dyk_body ) : ?>
							<p class="mt-2 text-base leading-relaxed text-brand-bark/75"><?php echo esc_html( $juiced_dyk_body ); ?></p>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</aside>

			<!-- Grid -->
			<div>
				<div class="flex flex-wrap items-baseline justify-between gap-x-6 gap-y-3">
					<div class="flex items-baseline gap-3">
						<h2 class="font-display text-2xl text-brand-bark"
							x-text="heading"><?php echo esc_html( $juiced_labels['all'] ); ?></h2>
						<span class="text-sm text-brand-bark/60"
							  x-text="countLabel"><?php echo esc_html( count( $juiced_cards ) . ' ' . ( 1 === count( $juiced_cards ) ? $juiced_labels['one'] : $juiced_labels['many'] ) ); ?></span>
					</div>

					<!-- Sort: JS-only control over a JS-only reorder, so it hides without JS. -->
					<label x-cloak class="flex items-center gap-2 text-sm text-brand-bark/60">
						<?php esc_html_e( 'Sort by:', 'juiced' ); ?>
						<select x-model="sort"
								class="cursor-pointer rounded-lg border border-black/10 bg-white px-3 py-1.5 text-sm font-medium text-brand-bark focus:border-brand-gold focus:outline-none">
							<option value="az"><?php esc_html_e( 'A–Z', 'juiced' ); ?></option>
							<option value="za"><?php esc_html_e( 'Z–A', 'juiced' ); ?></option>
						</select>
					</label>
				</div>

				<div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
					<?php foreach ( $juiced_cards as $juiced_i => $juiced_card ) : ?>
						<div x-show="isVisible(<?php echo esc_attr( $juiced_i ); ?>)"
							 :style="'order:' + orderOf(<?php echo esc_attr( $juiced_i ); ?>)">
							<?php get_template_part( 'template-parts/herbs/card', null, array( 'card' => $juiced_card ) ); ?>
						</div>
					<?php endforeach; ?>
				</div>

				<!-- No herb matches. Only reachable with JS on, which is also the
					 only way to narrow the grid. -->
				<div x-cloak x-show="isEmpty"
					 class="mt-6 rounded-2xl bg-white px-6 py-10 text-center ring-1 ring-black/5">
					<p class="text-sm text-brand-bark/60">
						<?php esc_html_e( 'No herbs match that just yet — try another search or category.', 'juiced' ); ?>
					</p>
					<button type="button" @click="reset()"
							class="mt-4 inline-flex cursor-pointer items-center rounded-full border border-brand-gold/40 bg-white px-5 py-2 text-sm font-medium text-brand-gold transition-colors hover:border-brand-gold hover:bg-brand-gold hover:text-white">
						<?php esc_html_e( 'Show All Herbs', 'juiced' ); ?>
					</button>
				</div>
			</div>

		</div>
	</div>
</section>
