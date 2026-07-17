<?php
/**
 * Herb Library page Section 1 — Hero.
 *
 * Two-tone headline, subtext, and a search bar over a full-bleed herb photo
 * that fades into the dark background from the left — the same construction as
 * the events hero, so the two pages stay visually of a piece. Copy is
 * ACF-editable on the Herb Library page (inc/acf-herbs-page.php); defaults
 * below match the approved mockup so the section renders before anything is
 * set. With no image the gradient collapses to solid ink, which still reads
 * correctly.
 *
 * The search box writes the shared Alpine `herbs` store (assets/js/main.js) as
 * the visitor types, and the listing section below narrows live. With JS off it
 * degrades to a plain GET form: ?herb_search= reloads the page and seeds the
 * input (and, via x-init, the store) so both paths land in the same place. The
 * "Popular Searches" pills are pre-filled links to that same query.
 *
 * @package Juiced
 */

$lead        = juiced_page_field( 'herbs_hero_lead', 'The Power of' );
$accent      = juiced_page_field( 'herbs_hero_accent', 'Plants. Naturally.' );
$subtext     = juiced_page_field( 'herbs_hero_subtext', 'Explore the healing power of herbs. Learn their benefits, how they support your wellness, and how we use them in our juices and smoothies.' );
$image       = juiced_image_url( juiced_page_field( 'herbs_hero_image', '' ), 'juiced_hero' );
$placeholder = juiced_page_field( 'herbs_hero_search_placeholder', 'Search herbs by name or benefit…' );
$popular     = juiced_page_field( 'herbs_hero_popular', 'detox, immune, energy, anti-inflammatory, focus' );
$popular     = array_filter( array_map( 'trim', explode( ',', $popular ) ) );
$query       = isset( $_GET['herb_search'] ) ? sanitize_text_field( wp_unslash( $_GET['herb_search'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- public, read-only filter.
?>
<section class="relative isolate bg-brand-ink text-brand-cream overflow-hidden bg-grain">

	<?php if ( $image ) : ?>
		<div class="absolute inset-0 -z-10" aria-hidden="true">
			<img src="<?php echo esc_url( $image ); ?>" alt=""
				 class="h-full w-full object-cover object-center" loading="eager" fetchpriority="high" />
			<!-- Left-to-right scrim: solid behind the copy, clearing by the right
				 edge. Heavier on mobile, where the text sits over the image. -->
			<div class="absolute inset-0 bg-gradient-to-r from-brand-ink via-brand-ink/85 to-brand-ink/65 md:from-28% md:via-brand-ink/55 md:via-60% md:to-brand-ink/5"></div>
		</div>
	<?php endif; ?>

	<div class="mx-auto max-w-7xl px-4 py-16 md:py-20 min-h-[380px] md:min-h-[440px] flex items-center">
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

			<!-- Search -->
			<form role="search" method="get" action="<?php echo esc_url( get_permalink() ); ?>"
				  x-data @submit.prevent="document.getElementById('herb-listing')?.scrollIntoView({ behavior: 'smooth' })"
				  class="mt-8 flex items-center gap-2 rounded-xl bg-white p-1.5 pl-4 shadow-lg">
				<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"
					 class="shrink-0 text-brand-bark/50" aria-hidden="true">
					<circle cx="11" cy="11" r="7" />
					<path d="m20 20-3.5-3.5" stroke-linecap="round" />
				</svg>
				<label for="herb-search" class="sr-only"><?php esc_html_e( 'Search herbs', 'juiced' ); ?></label>
				<input type="text" id="herb-search" name="herb_search" x-ref="search"
					   value="<?php echo esc_attr( $query ); ?>"
					   placeholder="<?php echo esc_attr( $placeholder ); ?>"
					   x-init="$store.herbs.query = $el.value"
					   @input="$store.herbs.query = $event.target.value"
					   :value="$store.herbs.query"
					   class="min-w-0 flex-1 border-0 bg-transparent py-2 text-base text-brand-bark placeholder:text-brand-bark/45 focus:outline-none focus:ring-0" />
				<!-- Clear: JS-only, like the live filtering it undoes. -->
				<button type="button" x-cloak x-show="$store.herbs.query"
						@click="$store.herbs.query = ''; $refs.search.focus()"
						aria-label="<?php esc_attr_e( 'Clear search', 'juiced' ); ?>"
						class="grid h-8 w-8 shrink-0 cursor-pointer place-items-center rounded-full text-brand-bark/40 transition-colors hover:bg-black/[0.05] hover:text-brand-bark/70">
					<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
						<path d="M6 6l12 12M18 6 6 18" />
					</svg>
				</button>
				<button type="submit"
						class="shrink-0 rounded-lg bg-brand-green px-5 py-2.5 text-base font-semibold text-white transition-colors hover:bg-brand-gold">
					<?php esc_html_e( 'Search', 'juiced' ); ?>
				</button>
			</form>

			<!-- Popular searches -->
			<?php if ( $popular ) : ?>
				<div class="mt-4 flex flex-wrap items-center gap-2">
					<span class="text-sm font-semibold text-brand-cream/70"><?php esc_html_e( 'Popular Searches:', 'juiced' ); ?></span>
					<?php foreach ( $popular as $term ) : ?>
						<a href="<?php echo esc_url( add_query_arg( 'herb_search', rawurlencode( $term ), get_permalink() ) ); ?>"
						   class="rounded-full border border-white/25 px-3.5 py-1 text-sm font-medium text-brand-cream/85 transition-colors hover:border-brand-gold hover:text-brand-gold">
							<?php echo esc_html( $term ); ?>
						</a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

		</div>
	</div>
</section>
