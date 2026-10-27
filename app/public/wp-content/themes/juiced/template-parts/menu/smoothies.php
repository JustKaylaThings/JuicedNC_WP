<?php
/**
 * Menu Section 2 — Super Smoothies, set like a printed menu.
 *
 * Heading and promise line over a rule; a green strip with the two sizes and
 * the benefit key; then every Menu Item in the Smoothies section (ordered by
 * its Order box, then title) as a menu line: name, dotted leader and prices,
 * its benefit dots and badge, and ingredients. The Create Your Own bar closes
 * the section.
 *
 * Prices: the dollar amounts in the two size fields ("$8.25 / $12.64"). A
 * smoothie's own Price field replaces them, shortened the same way when it
 * holds dollar amounts ("Reg $9.25 · Lrg $13.64" → "$9.25 / $13.64"). The
 * size in brackets in a title ("Berry Boost (32oz)", used by the homepage top
 * five) is dropped here, since every smoothie comes in both sizes.
 *
 * Rendered inside the paper wrapper in page-templates/menu.php.
 *
 * @package Juiced
 */

$juiced_smoothies = get_posts(
	array(
		'post_type'      => 'menu_item',
		'posts_per_page' => -1,
		'orderby'        => array(
			'menu_order' => 'ASC',
			'title'      => 'ASC',
		),
		'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
			array(
				'taxonomy' => 'menu_section',
				'field'    => 'slug',
				'terms'    => 'smoothies',
			),
		),
	)
);

/**
 * "Reg 20oz — $8.25" → "$8.25 / …": the dollar amounts in a price string, or
 * the string itself when it has none.
 */
$juiced_prices = function ( $text ) {
	$text = trim( (string) $text );
	return preg_match_all( '/\$\s?\d+(?:\.\d{2})?/', $text, $m ) ? implode( ' / ', $m[0] ) : juiced_format_menu_price( $text );
};

$juiced_benefits = juiced_menu_benefits();
$juiced_promise  = juiced_page_field( 'menu_promise', 'No dairy · No refined sugar · Whole foods & vegetables ' );
$juiced_sizes    = array_filter(
	array(
		juiced_page_field( 'menu_size_reg', 'Reg 20oz — $8.25' ),
		juiced_page_field( 'menu_size_lrg', 'Lrg 32oz — $12.64' ),
	)
);
$juiced_std      = $juiced_prices( implode( ' ', $juiced_sizes ) );
$juiced_cyo_head = juiced_page_field( 'menu_cyo_heading', 'Create your own' );
$juiced_cyo_body = array_filter( array_map( 'trim', explode( "\n", (string) juiced_page_field( 'menu_cyo_body', "Choice of any 3 fruits\nChoice of coconut milk, almond milk or water" ) ) ) );
if ( $juiced_std ) {
	$juiced_cyo_body[] = $juiced_std;
}
?>
<section class="flex flex-col gap-6" id="smoothies" aria-labelledby="menu-smoothies-h">

	<div class="flex flex-col gap-2 border-b-[3px] border-brand-forest pb-3.5">
		<h2 id="menu-smoothies-h" class="m-0 font-display text-[clamp(2.5rem,5vw,3rem)] font-bold leading-none text-brand-forest">
			<?php esc_html_e( 'Super Smoothies', 'juiced' ); ?>
		</h2>
		<?php if ( $juiced_promise ) : ?>
			<p class="m-0 text-xs font-bold uppercase tracking-[.16em] text-[#4a4036] sm:text-[13px]"><?php echo esc_html( $juiced_promise ); ?></p>
		<?php endif; ?>
	</div>

	<!-- Sizes + benefit key -->
	<div class="flex flex-wrap items-center gap-x-10 gap-y-4 rounded-[22px] bg-brand-forest px-6 py-5 text-brand-cream sm:px-7">
		<?php if ( $juiced_sizes ) : ?>
			<div class="flex flex-wrap items-baseline gap-x-5 gap-y-1.5">
				<h3 class="m-0 text-xs font-bold uppercase tracking-[.2em] text-brand-gold"><?php esc_html_e( 'Sizes', 'juiced' ); ?></h3>
				<?php foreach ( $juiced_sizes as $size ) : ?>
					<?php $parts = array_map( 'trim', preg_split( '/\s+[—–-]\s+/u', $size, 2 ) ); ?>
					<span class="text-base font-semibold tabular-nums">
						<?php echo esc_html( $parts[0] ); ?>
						<?php if ( isset( $parts[1] ) ) : ?>
							<span class="text-brand-gold"><?php echo esc_html( $parts[1] ); ?></span>
						<?php endif; ?>
					</span>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
		<div class="flex flex-wrap items-center gap-x-5 gap-y-1.5">
			<h3 class="m-0 text-xs font-bold uppercase tracking-[.2em] text-brand-gold"><?php esc_html_e( 'Key', 'juiced' ); ?></h3>
			<ul class="m-0 flex list-none flex-wrap gap-x-5 gap-y-1.5 p-0">
				<?php foreach ( $juiced_benefits as $benefit ) : ?>
					<li class="inline-flex items-center gap-2 whitespace-nowrap text-sm text-brand-mint">
						<span class="h-[11px] w-[11px] shrink-0 rounded-full" style="background:<?php echo esc_attr( $benefit[1] ); ?>" aria-hidden="true"></span>
						<?php echo esc_html( $benefit[0] ); ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>

	<!-- Smoothie list -->
	<ul class="m-0 grid list-none gap-x-10 p-0 md:grid-cols-2 lg:grid-cols-3">
		<?php foreach ( $juiced_smoothies as $smoothie ) : ?>
			<?php
			$name  = preg_replace( '/\s*\([^)]*\)\s*$/', '', get_the_title( $smoothie ) );
			$ingr  = array_filter( array_map( 'trim', explode( ',', wp_strip_all_tags( (string) get_field( 'ingredients', $smoothie->ID ) ) ) ) );
			$own   = trim( (string) get_field( 'price', $smoothie->ID ) );
			$price = $own ? $juiced_prices( $own ) : $juiced_std;
			$badge = get_field( 'menu_badge', $smoothie->ID );
			$picks = array_intersect_key( $juiced_benefits, array_flip( (array) get_field( 'benefits', $smoothie->ID ) ) );
			?>
			<li class="flex flex-col gap-1.5 border-b border-brand-forest/20 py-4">
				<div class="flex items-baseline gap-2.5">
					<h3 class="m-0 min-w-0 font-display text-[22px] font-semibold leading-tight text-brand-forest"><?php echo esc_html( $name ); ?></h3>
					<span class="min-w-4 grow -translate-y-1 border-b-2 border-dotted border-brand-forest/35" aria-hidden="true"></span>
					<?php if ( $price ) : ?>
						<span class="shrink-0 whitespace-nowrap text-[15px] font-bold text-brand-rust tabular-nums"><?php echo esc_html( $price ); ?></span>
					<?php endif; ?>
				</div>
				<?php if ( $picks || $badge ) : ?>
					<div class="flex flex-wrap items-center gap-1.5">
						<?php foreach ( $picks as $benefit ) : ?>
							<span class="h-[11px] w-[11px] rounded-full ring-1 ring-brand-ink/30" style="background:<?php echo esc_attr( $benefit[1] ); ?>" title="<?php echo esc_attr( $benefit[0] ); ?>"></span>
						<?php endforeach; ?>
						<?php if ( $picks ) : ?>
							<span class="sr-only"><?php echo esc_html( implode( ', ', wp_list_pluck( $picks, 0 ) ) ); ?></span>
						<?php endif; ?>
						<?php if ( $badge ) : ?>
							<span class="<?php echo $picks ? 'ml-1.5' : ''; ?> text-xs font-bold uppercase tracking-[.12em] text-brand-rust">★ <?php echo esc_html( $badge ); ?></span>
						<?php endif; ?>
					</div>
				<?php endif; ?>
				<?php if ( $ingr ) : ?>
					<p class="m-0 text-[15px] leading-normal text-[#2c3a2e]"><?php echo esc_html( implode( ' · ', $ingr ) ); ?></p>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
	</ul>

	<?php if ( $juiced_cyo_head ) : ?>
		<div class="flex flex-wrap items-center justify-between gap-x-6 gap-y-2 rounded-[22px] bg-brand-gold px-6 py-5 text-brand-ink sm:px-7">
			<h3 class="m-0 font-display text-[28px] font-bold leading-tight"><?php echo esc_html( $juiced_cyo_head ); ?></h3>
			<?php if ( $juiced_cyo_body ) : ?>
				<p class="m-0 text-base font-medium"><?php echo esc_html( implode( ' · ', $juiced_cyo_body ) ); ?></p>
			<?php endif; ?>
		</div>
	<?php endif; ?>

</section>
