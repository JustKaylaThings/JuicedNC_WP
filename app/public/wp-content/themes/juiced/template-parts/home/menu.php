<?php
/**
 * Homepage Section 5 — The top five ("Garden" redesign, Sep 2026).
 *
 * Cream band with a heading row (eyebrow, two-tone heading, "See the full
 * menu" button) over a sage board: a big photo of the selected drink on the
 * left, with a cream card (name, size · price, Order on DoorDash), and a ranked
 * list of five drinks on the right. Tapping a drink swaps the photo (Alpine.js,
 * `i` = selected index). Without JS the first drink shows and the list still
 * reads as a ranking.
 *
 * The five drinks are menu_item posts picked on the Home page (top_pick_1..5,
 * with an optional short tag each, inc/acf-homepage.php). With none picked it
 * falls back to the five from the approved mockup, looked up by slug. The
 * photo, ingredients and price come from the menu item; the size is the part
 * of its title in brackets, e.g. "Berry Boost (32oz)". A drink without a photo
 * shows a colour block instead. Renders nothing when no drinks are found.
 *
 * @package Juiced
 */

$juiced_default_top = array(
	'berry-boost'  => 'Most ordered',
	'ginger-glow'  => 'Work Remote favorite',
	'sunset'       => 'Open Mic go-to',
	'rise-n-grind' => 'Meal replacement',
	'spark-plug'   => 'Green juice',
);

// Resolve the picks to post IDs + tags.
$juiced_picks = array();
for ( $n = 1; $n <= 5; $n++ ) {
	$id = (int) juiced_field( "top_pick_{$n}", 0 );
	if ( $id && 'publish' === get_post_status( $id ) ) {
		$juiced_picks[] = array( $id, juiced_field( "top_tag_{$n}", '' ) );
	}
}
if ( ! $juiced_picks ) {
	foreach ( $juiced_default_top as $slug => $tag ) {
		$post = get_page_by_path( $slug, OBJECT, 'menu_item' );
		if ( $post && 'publish' === $post->post_status ) {
			$juiced_picks[] = array( $post->ID, $tag );
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
	list( $id, $tag ) = $pick;
	$title = get_the_title( $id );
	$size  = '';
	if ( preg_match( '/^(.*?)\s*\(([^)]+)\)\s*$/', $title, $m ) ) {
		$title = $m[1];
		$size  = $m[2];
	}
	$juiced_drinks[] = array(
		'rank'  => sprintf( '%02d', $k + 1 ),
		'name'  => $title,
		'size'  => $size,
		'price' => juiced_format_menu_price( get_field( 'price', $id ) ),
		'ingr'  => wp_strip_all_tags( (string) get_field( 'ingredients', $id ) ),
		'tag'   => $tag,
		'img'   => get_the_post_thumbnail_url( $id, 'large' ),
		'block' => $juiced_blocks[ $k % count( $juiced_blocks ) ],
	);
}

$juiced_menu_page = get_page_by_path( 'menu' );

$juiced_m_eyebrow = juiced_field( 'menu_eyebrow', 'Fuel up · What the neighborhood orders' );
$juiced_m_heading = juiced_field( 'menu_heading', 'The' );
$juiced_m_accent  = juiced_field( 'menu_heading_accent', 'top five' );
$juiced_m_label   = juiced_field( 'menu_link_label', 'See the full menu' );
$juiced_m_url     = juiced_field( 'menu_link_url', $juiced_menu_page ? get_permalink( $juiced_menu_page ) : home_url( '/menu/' ) );
$juiced_order_url = juiced_field( 'order_ahead_url', '' );
?>
<section class="bg-brand-cream text-brand-ink">
	<div class="mx-auto max-w-7xl px-4 py-16 md:py-22 flex flex-col gap-8 md:gap-10" x-data="{ i: 0 }">

		<!-- Heading row -->
		<div class="flex flex-wrap items-end justify-between gap-6">
			<div class="flex flex-col gap-3.5">
				<?php if ( $juiced_m_eyebrow ) : ?>
					<span class="text-xs font-bold uppercase tracking-[.22em] text-brand-forest"><?php echo esc_html( $juiced_m_eyebrow ); ?></span>
				<?php endif; ?>
				<h2 class="font-display font-bold leading-none text-brand-forest text-[clamp(2.5rem,5vw,3.75rem)]">
					<?php echo esc_html( $juiced_m_heading ); ?>
					<span class="text-brand-rust"><?php echo esc_html( $juiced_m_accent ); ?></span>
				</h2>
			</div>
			<?php if ( $juiced_m_url && $juiced_m_label ) : ?>
				<a href="<?php echo esc_url( $juiced_m_url ); ?>"
				   class="inline-flex items-center rounded-full bg-brand-forest px-6.5 py-4 font-bold text-brand-cream hover:bg-brand-rust transition-colors">
					<?php echo esc_html( $juiced_m_label ); ?>
				</a>
			<?php endif; ?>
		</div>

		<!-- Sage board -->
		<div class="grid gap-6 rounded-[32px] bg-[#cfd9cb] p-3 sm:p-5 lg:p-7 lg:grid-cols-[minmax(0,500px)_minmax(0,1fr)] lg:gap-10">

			<!-- Photo of the selected drink -->
			<div class="relative min-h-[420px] sm:min-h-[480px] lg:min-h-[600px] overflow-hidden rounded-[22px]" aria-live="polite">
				<?php foreach ( $juiced_drinks as $k => $drink ) : ?>
					<?php // Stacked layers with the first drink on top, so without JS it shows; Alpine fades the rest out. Kept in the DOM (not display:none) so lazy photos preload. ?>
					<div class="absolute inset-0 transition-opacity duration-300" style="z-index:<?php echo (int) ( count( $juiced_drinks ) - $k ); ?>"
						 :class="i === <?php echo (int) $k; ?> ? 'opacity-100 z-10!' : 'opacity-0 pointer-events-none'"
						 :aria-hidden="(i !== <?php echo (int) $k; ?>).toString()">
						<?php if ( $drink['img'] ) : ?>
							<img src="<?php echo esc_url( $drink['img'] ); ?>" alt="<?php echo esc_attr( $drink['name'] ); ?>"
								 class="absolute inset-0 h-full w-full object-cover" loading="lazy" />
						<?php else : ?>
							<div class="absolute inset-0 overflow-hidden <?php echo esc_attr( $drink['block'][0] ); ?>" aria-hidden="true">
								<span class="absolute -right-15 -top-15 h-75 w-75 rounded-full <?php echo esc_attr( $drink['block'][1] ); ?>"></span>
								<span class="absolute left-[12%] bottom-[30%] h-30 w-30 rounded-full opacity-35 <?php echo esc_attr( $drink['block'][1] ); ?>"></span>
							</div>
						<?php endif; ?>

						<div class="absolute inset-x-3 bottom-3 sm:inset-x-5 sm:bottom-5 flex flex-wrap items-center justify-between gap-3 rounded-[18px] bg-brand-cream px-4.5 py-4">
							<div class="flex flex-col gap-0.5">
								<span class="font-display text-[26px] font-bold leading-none text-brand-forest"><?php echo esc_html( $drink['name'] ); ?></span>
								<?php $meta = implode( ' · ', array_filter( array( $drink['size'], $drink['price'] ) ) ); ?>
								<?php if ( $meta ) : ?>
									<span class="text-[13px] text-[#4a4036]"><?php echo esc_html( $meta ); ?></span>
								<?php endif; ?>
							</div>
							<?php if ( $juiced_order_url ) : ?>
								<a href="<?php echo esc_url( $juiced_order_url ); ?>" target="_blank" rel="noopener noreferrer"
								   class="whitespace-nowrap rounded-full bg-brand-gold px-4.5 py-3 text-sm font-bold text-brand-ink hover:bg-brand-forest hover:text-brand-cream transition-colors">
									<?php esc_html_e( 'Order on DoorDash', 'juiced' ); ?>
								</a>
							<?php endif; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>

			<!-- Ranked list -->
			<ol class="m-0! flex list-none flex-col justify-center gap-1 p-0 lg:pr-3">
				<?php foreach ( $juiced_drinks as $k => $drink ) : ?>
					<li>
						<button type="button" @click="i = <?php echo (int) $k; ?>"
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
