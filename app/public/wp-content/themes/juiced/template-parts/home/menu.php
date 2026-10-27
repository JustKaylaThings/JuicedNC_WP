<?php
/**
 * Homepage Section 5 — The top five ("Garden" redesign, Sep 2026).
 *
 * Cream band with a heading row (eyebrow, two-tone heading, "See the full
 * menu" button) over a sage board: a big photo of the selected drink on the
 * left, with a cream card (name, juice/smoothie · size · price, and order
 * buttons for DoorDash, Uber Eats and Grubhub), and a ranked list of five
 * drinks on the right. Tapping a drink swaps the photo (Alpine.js,
 * `i` = selected index). The board also steps through the five on its own
 * every 5s: the photo crossfades and settles from a slight zoom, the card
 * rises in. It pauses
 * while the pointer or keyboard focus is on the board, and doesn't run for
 * visitors who prefer reduced motion. Tapping a drink restarts the clock.
 * Without JS the first drink shows and the list still reads as a ranking.
 *
 * Phones (below md) get a swipe row instead of the board: one card per drink
 * (photo, rank, tag, ingredients, its own order buttons), the next card
 * peeking in from the right, with dots under the row tracking the card in
 * view and the "See the full menu" button beside them. The board, and its
 * auto-advance, only run from md up.
 *
 * The five drinks are menu_item posts picked on the Home page (top_pick_1..5,
 * with an optional short tag each, inc/acf-homepage.php). With none picked it
 * falls back to the client's Oct 2026 list (two juices, three smoothies),
 * looked up by slug; a slug with no published menu item is skipped.
 *
 * Order buttons: one per app, listing the shops with a link for it in
 * Business Info (juiced_delivery_apps()); with two, the button opens a
 * "pick a shop" menu (juiced_app_button()). A drink's own link (top_dd_N, top_ue_N,
 * top_gh_N), e.g. straight to that item in the app, replaces Location 1's.
 * With no Business Info links for an app it is a single link: the drink's
 * own, else the section-wide store link (top_doordash_url etc.; DoorDash
 * falls back once more to Order Ahead URL). An app with no link at all gets
 * no button. The
 * photo, ingredients and price come from the menu item; the size is the part
 * of its title in brackets, e.g. "Berry Boost (32oz)". A drink without a photo
 * shows a colour block instead. Renders nothing when no drinks are found.
 *
 * @package Juiced
 */

$juiced_default_top = array(
	'ginger-glow'      => 'Most ordered',
	'berry-boost'      => 'Fan favorite',
	'pick-me-up'       => 'Something to start your day',
	'spark-plug'       => 'Anti-inflammatory',
	'bright-early-6oz' => 'Most nutrient dense',
);

// Each app's shops (juiced_delivery_apps()), and the prefix of the slots'
// own drink-link fields for it (top_dd_N etc.).
$juiced_apps      = juiced_delivery_apps();
$juiced_slot_keys = array(
	'doordash' => 'dd',
	'ubereats' => 'ue',
	'grubhub'  => 'gh',
);

// Resolve the picks to post IDs, tags and the ranking slot they came from
// (the slot's own order links are read from it).
$juiced_picks = array();
for ( $n = 1; $n <= 5; $n++ ) {
	$id = (int) juiced_field( "top_pick_{$n}", 0 );
	if ( $id && 'publish' === get_post_status( $id ) ) {
		$juiced_picks[] = array( $id, juiced_field( "top_tag_{$n}", '' ), $n );
	}
}
if ( ! $juiced_picks ) {
	foreach ( $juiced_default_top as $slug => $tag ) {
		$post = get_page_by_path( $slug, OBJECT, 'menu_item' );
		if ( $post && 'publish' === $post->post_status ) {
			$juiced_picks[] = array( $post->ID, $tag, count( $juiced_picks ) + 1 );
		}
	}
}
if ( ! $juiced_picks ) {
	return;
}

// No-photo colour blocks (background, accent circle), from the mockup.
$juiced_blocks = array(
	array( 'bg-brand-forest', 'bg-brand-gold' ),
	array( 'bg-brand-gold', 'bg-brand-forest' ),
	array( 'bg-[#f5ecdc]', 'bg-brand-rust' ),
	array( 'bg-brand-ink', 'bg-brand-gold' ),
	array( 'bg-brand-forest-deep', 'bg-brand-mint' ),
);

$juiced_drinks = array();
foreach ( $juiced_picks as $k => $pick ) {
	list( $id, $tag, $slot ) = $pick;
	$title = get_the_title( $id );
	$size  = '';
	if ( preg_match( '/^(.*?)\s*\(([^)]+)\)\s*$/', $title, $m ) ) {
		$title = $m[1];
		$size  = $m[2];
	}

	// "Juice" / "Smoothie" from the menu section ("Juices", "Smoothies").
	$type    = '';
	$section = get_the_terms( $id, 'menu_section' );
	if ( $section && ! is_wp_error( $section ) ) {
		$type = preg_replace( '/s$/', '', $section[0]->name );
	}

	// A drink's own link for an app stands in for Location 1's shop link.
	$order = array();
	foreach ( juiced_order_apps() as $key => $meta ) {
		$own = juiced_field( "top_{$juiced_slot_keys[ $key ]}_{$slot}", '' );
		$app = isset( $juiced_apps[ $key ] ) ? $juiced_apps[ $key ] : $meta + array( 'shops' => array() );
		if ( $own ) {
			$app['shops'][1] = array( 'url' => $own ) + ( isset( $app['shops'][1] ) ? $app['shops'][1] : juiced_order_location_label( 1 ) );
			ksort( $app['shops'] );
		}
		if ( $app['shops'] ) {
			$order[] = $app;
		}
	}

	$juiced_drinks[] = array(
		'rank'  => sprintf( '%02d', $k + 1 ),
		'name'  => $title,
		'type'  => $type,
		'size'  => $size,
		'price' => juiced_format_menu_price( get_field( 'price', $id ) ),
		'ingr'  => wp_strip_all_tags( (string) get_field( 'ingredients', $id ) ),
		'tag'   => $tag,
		'img'   => get_the_post_thumbnail_url( $id, 'large' ),
		'block' => $juiced_blocks[ $k % count( $juiced_blocks ) ],
		'order' => $order,
	);
}

$juiced_menu_page = get_page_by_path( 'menu' );

$juiced_m_eyebrow = juiced_field( 'menu_eyebrow', 'Fuel up · What the neighborhood orders' );
$juiced_m_heading = juiced_field( 'menu_heading', 'The' );
$juiced_m_accent  = juiced_field( 'menu_heading_accent', 'top five' );
$juiced_m_label   = juiced_field( 'menu_link_label', 'See the full menu' );
$juiced_m_url     = juiced_field( 'menu_link_url', $juiced_menu_page ? get_permalink( $juiced_menu_page ) : home_url( '/menu/' ) );
?>
<section class="bg-brand-cream text-brand-ink">
	<div class="mx-auto max-w-7xl px-4 py-16 md:py-22 flex flex-col gap-8 md:gap-10"
		 x-data="{
			i: 0, n: <?php echo (int) count( $juiced_drinks ); ?>, paused: false, timer: null,
			start() {
				if ( this.n < 2 || window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches || ! window.matchMedia( '(min-width: 768px)' ).matches ) return;
				clearInterval( this.timer );
				this.timer = setInterval( () => { if ( ! this.paused ) this.i = ( this.i + 1 ) % this.n; }, 5000 );
			},
			pick( k ) { this.i = k; this.start(); }
		 }"
		 x-init="start()">

		<!-- Heading row -->
		<div class="flex flex-wrap items-end justify-between gap-6">
			<div class="flex flex-col gap-3.5">
				<?php if ( $juiced_m_eyebrow ) : ?>
					<span class="text-xs font-bold uppercase tracking-[.22em] text-brand-forest"><?php echo esc_html( $juiced_m_eyebrow ); ?></span>
				<?php endif; ?>
				<h2 class="text-drop font-display font-bold leading-none text-brand-forest text-[clamp(2.5rem,5vw,3.75rem)]">
					<?php echo esc_html( $juiced_m_heading ); ?>
					<span class="text-brand-gold"><?php echo esc_html( $juiced_m_accent ); ?></span>
				</h2>
			</div>
			<?php if ( $juiced_m_url && $juiced_m_label ) : ?>
				<a href="<?php echo esc_url( $juiced_m_url ); ?>"
				   class="hidden md:inline-flex items-center rounded-full bg-brand-forest px-6.5 py-4 text-brand-cream btn-sticker">
					<?php echo esc_html( $juiced_m_label ); ?>
				</a>
			<?php endif; ?>
		</div>

		<!-- Phones: swipe row of cards -->
		<div class="md:hidden flex flex-col gap-6" x-data="{ a: 0 }">
			<ol class="my-0! -mx-4! flex list-none snap-x snap-mandatory scroll-px-4 gap-3 overflow-x-auto px-4 py-0 no-scrollbar"
				aria-label="<?php esc_attr_e( 'The top five', 'juiced' ); ?>"
				@scroll.passive="const el = $el, step = el.firstElementChild.offsetWidth + 12; a = el.scrollLeft >= el.scrollWidth - el.clientWidth - 4 ? <?php echo (int) count( $juiced_drinks ) - 1; ?> : Math.round( el.scrollLeft / step )">
				<?php foreach ( $juiced_drinks as $k => $drink ) : ?>
					<li class="flex w-[min(320px,84vw)] shrink-0 snap-start flex-col overflow-hidden rounded-3xl bg-[#cfd9cb]">
						<div class="relative h-[230px] <?php echo $drink['img'] ? '' : esc_attr( $drink['block'][0] ); ?>">
							<?php if ( $drink['img'] ) : ?>
								<img src="<?php echo esc_url( $drink['img'] ); ?>" alt="<?php echo esc_attr( $drink['name'] ); ?>"
									 class="absolute inset-0 h-full w-full object-cover" loading="lazy" />
							<?php else : ?>
								<span class="absolute -right-10 -top-10 h-40 w-40 rounded-full <?php echo esc_attr( $drink['block'][1] ); ?>" aria-hidden="true"></span>
							<?php endif; ?>
							<span class="absolute left-3 top-3 flex h-13 min-w-13 items-center justify-center rounded-full bg-brand-forest px-2.5 font-display text-[26px] font-bold text-brand-gold"><?php echo esc_html( $drink['rank'] ); ?></span>
							<?php if ( $drink['tag'] ) : ?>
								<span class="absolute right-3 top-3 max-w-[60%] rounded-2xl bg-brand-cream px-3 py-1.5 text-right text-xs font-bold leading-tight text-brand-forest"><?php echo esc_html( $drink['tag'] ); ?></span>
							<?php endif; ?>
						</div>
						<div class="flex flex-1 flex-col gap-3.5 p-4">
							<div class="flex flex-col gap-1">
								<span class="font-display text-[26px] font-bold leading-[1.05] text-brand-forest"><?php echo esc_html( $drink['name'] ); ?></span>
								<?php $meta = implode( ' · ', array_filter( array( $drink['type'], $drink['size'], $drink['price'] ) ) ); ?>
								<?php if ( $meta ) : ?>
									<span class="text-[13px] font-semibold text-[#4a4036]"><?php echo esc_html( $meta ); ?></span>
								<?php endif; ?>
								<?php if ( $drink['ingr'] ) : ?>
									<span class="line-clamp-2 text-sm leading-snug text-[#2c3a2e]"><?php echo esc_html( $drink['ingr'] ); ?></span>
								<?php endif; ?>
							</div>
							<?php if ( $drink['order'] ) : ?>
								<div class="mt-auto flex flex-wrap gap-1">
									<?php foreach ( $drink['order'] as $app ) : ?>
										<?php juiced_app_button( $app, 'min-h-11 px-3 text-[13px]', $drink['name'] ); ?>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>
						</div>
					</li>
				<?php endforeach; ?>
			</ol>

			<div class="flex items-center justify-between gap-4">
				<div class="flex items-center gap-1.5" aria-hidden="true">
					<?php foreach ( $juiced_drinks as $k => $drink ) : ?>
						<span class="h-2 rounded-full transition-all duration-200"
							  :class="a === <?php echo (int) $k; ?> ? 'w-6 bg-brand-forest' : 'w-2 bg-brand-forest/25'"
							  class="<?php echo $k ? 'w-2 bg-brand-forest/25' : 'w-6 bg-brand-forest'; ?>"></span>
					<?php endforeach; ?>
				</div>
				<?php if ( $juiced_m_url && $juiced_m_label ) : ?>
					<a href="<?php echo esc_url( $juiced_m_url ); ?>"
					   class="inline-flex min-h-12 items-center rounded-full bg-brand-forest px-5.5 text-brand-cream btn-sticker">
						<?php echo esc_html( $juiced_m_label ); ?>
					</a>
				<?php endif; ?>
			</div>
		</div>

		<!-- Sage board -->
		<div @mouseenter="paused = true" @mouseleave="paused = false" @focusin="paused = true" @focusout="paused = false"
			 class="hidden md:grid gap-6 rounded-[32px] bg-[#cfd9cb] p-3 sm:p-5 lg:p-7 lg:grid-cols-[minmax(0,500px)_minmax(0,1fr)] lg:gap-10">

			<!-- Photo of the selected drink -->
			<div class="relative min-h-[420px] sm:min-h-[480px] lg:min-h-[600px] overflow-hidden rounded-[22px]" aria-live="polite">
				<?php foreach ( $juiced_drinks as $k => $drink ) : ?>
					<?php // Stacked layers with the first drink on top, so without JS it shows; Alpine fades the rest out. Kept in the DOM (not display:none) so lazy photos preload. ?>
					<div class="absolute inset-0 transition-opacity duration-700 ease-in-out motion-reduce:duration-300" style="z-index:<?php echo (int) ( count( $juiced_drinks ) - $k ); ?>"
						 :class="i === <?php echo (int) $k; ?> ? 'opacity-100 z-10!' : 'opacity-0 pointer-events-none'"
						 :aria-hidden="(i !== <?php echo (int) $k; ?>).toString()">
						<?php if ( $drink['img'] ) : ?>
							<img src="<?php echo esc_url( $drink['img'] ); ?>" alt="<?php echo esc_attr( $drink['name'] ); ?>"
								 class="absolute inset-0 h-full w-full object-cover transition-transform duration-[1200ms] ease-out motion-reduce:transition-none"
								 :class="i === <?php echo (int) $k; ?> ? 'scale-100' : 'scale-105'" loading="lazy" />
						<?php else : ?>
							<div class="absolute inset-0 overflow-hidden <?php echo esc_attr( $drink['block'][0] ); ?>" aria-hidden="true">
								<span class="absolute -right-15 -top-15 h-75 w-75 rounded-full <?php echo esc_attr( $drink['block'][1] ); ?>"></span>
								<span class="absolute left-[12%] bottom-[30%] h-30 w-30 rounded-full opacity-35 <?php echo esc_attr( $drink['block'][1] ); ?>"></span>
							</div>
						<?php endif; ?>

						<div class="absolute inset-x-3 bottom-3 sm:inset-x-5 sm:bottom-5 flex flex-col gap-3.5 rounded-[18px] bg-brand-cream px-4.5 py-4 transition duration-500 ease-out motion-reduce:transition-none"
							 :class="i === <?php echo (int) $k; ?> ? 'translate-y-0 opacity-100 delay-200' : 'translate-y-4 opacity-0'">
							<div class="flex flex-col gap-0.5">
								<span class="font-display text-[26px] font-bold leading-none text-brand-forest"><?php echo esc_html( $drink['name'] ); ?></span>
								<?php $meta = implode( ' · ', array_filter( array( $drink['type'], $drink['size'], $drink['price'] ) ) ); ?>
								<?php if ( $meta ) : ?>
									<span class="text-[13px] text-[#4a4036]"><?php echo esc_html( $meta ); ?></span>
								<?php endif; ?>
							</div>
							<?php if ( $drink['order'] ) : ?>
								<div class="flex flex-wrap items-center gap-2">
									<span class="mr-1 flex flex-col text-xs font-bold uppercase leading-tight tracking-[.16em] text-brand-forest">
										<span><?php esc_html_e( 'Order', 'juiced' ); ?></span>
										<span><?php esc_html_e( 'Delivery', 'juiced' ); ?></span>
									</span>
									<?php foreach ( $drink['order'] as $app ) : ?>
										<?php juiced_app_button( $app, 'px-4 py-2.5 text-sm', $drink['name'], ':tabindex="i === ' . (int) $k . ' ? 0 : -1"' ); ?>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>

			<!-- Ranked list -->
			<ol class="m-0! flex list-none flex-col justify-center gap-1 p-0 lg:pr-3">
				<?php foreach ( $juiced_drinks as $k => $drink ) : ?>
					<li>
						<button type="button" @click="pick( <?php echo (int) $k; ?> )"
								:aria-pressed="(i === <?php echo (int) $k; ?>).toString()"
								aria-pressed="<?php echo $k ? 'false' : 'true'; ?>"
								class="group grid w-full cursor-pointer grid-cols-[48px_minmax(0,1fr)] sm:grid-cols-[72px_minmax(0,1fr)_auto] items-center gap-3 sm:gap-4.5 rounded-[18px] border-b px-4 py-3.5 sm:px-5 sm:py-4 text-left transition-colors"
								:class="i === <?php echo (int) $k; ?> ? 'bg-brand-forest text-brand-cream border-transparent' : 'text-brand-forest border-brand-forest/20 hover:bg-brand-forest/10'">
							<span class="font-display text-[32px] sm:text-[42px] font-bold leading-none"
								  :class="i === <?php echo (int) $k; ?> ? 'text-brand-gold' : 'text-brand-rust'"><?php echo esc_html( $drink['rank'] ); ?></span>
							<span class="flex min-w-0 flex-col gap-0.5">
								<span class="font-display text-[22px] sm:text-[28px] font-semibold leading-[1.1]"><?php echo esc_html( $drink['name'] ); ?></span>
								<?php if ( $drink['ingr'] ) : ?>
									<span class="truncate text-sm"
										  :class="i === <?php echo (int) $k; ?> ? 'text-brand-mint' : 'text-[#2c3a2e]'"><?php echo esc_html( $drink['ingr'] ); ?></span>
								<?php endif; ?>
								<?php if ( $drink['tag'] ) : ?>
									<span class="text-[13px] font-bold sm:hidden"><?php echo esc_html( $drink['tag'] ); ?></span>
								<?php endif; ?>
							</span>
							<?php if ( $drink['tag'] ) : ?>
								<span class="hidden sm:block whitespace-nowrap text-[13px] font-bold"><?php echo esc_html( $drink['tag'] ); ?></span>
							<?php endif; ?>
						</button>
					</li>
				<?php endforeach; ?>
			</ol>

		</div>
	</div>
</section>
