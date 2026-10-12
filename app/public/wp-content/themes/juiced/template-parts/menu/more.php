<?php
/**
 * Menu Section 4 — the rest of the menu: every Menu Item outside Smoothies,
 * Baked Goods and Snacks,
 * grouped by menu section (Juices, Seamoss, …) in the sections'
 * alphabetical order, with Juices first. Empty sections are skipped; the whole
 * band renders nothing when there are no other items. Syrups
 * stacks under Shots in one column ($juiced_stack below).
 *
 * Each row shows the name, the size from its title's brackets, ingredients
 * and price, whichever are filled in. Rendered inside the paper wrapper in
 * page-templates/menu.php.
 *
 * @package Juiced
 */

$juiced_sections = get_terms(
	array(
		'taxonomy'   => 'menu_section',
		'hide_empty' => true,
	)
);
if ( is_wp_error( $juiced_sections ) ) {
	return;
}
$juiced_sections = array_filter(
	$juiced_sections,
	function ( $term ) {
		// Smoothies have their own band; Baked Goods and Snacks are off the
		// menu for now (client note, Oct 2026).
		return ! in_array( $term->slug, array( 'smoothies', 'baked-goods', 'snacks' ), true );
	}
);
if ( ! $juiced_sections ) {
	return;
}
usort(
	$juiced_sections,
	function ( $a, $b ) {
		return ( 'juices' === $b->slug ) - ( 'juices' === $a->slug ) ?: strcmp( $a->name, $b->name );
	}
);

// Small sections that share a column with another, under their own header
// (section slug => slug of the section above it).
$juiced_stack   = array( 'syrups' => 'shots' );
$juiced_columns = array();
foreach ( $juiced_sections as $section ) {
	$items = get_posts(
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
					'terms'    => $section->term_id,
				),
			),
		)
	);
	if ( ! $items ) {
		continue;
	}
	$juiced_host = $juiced_stack[ $section->slug ] ?? '';
	if ( $juiced_host && isset( $juiced_columns[ $juiced_host ] ) ) {
		$juiced_columns[ $juiced_host ][] = array( $section, $items );
	} else {
		$juiced_columns[ $section->slug ] = array( array( $section, $items ) );
	}
}
if ( ! $juiced_columns ) {
	return;
}
?>
<section class="flex flex-col gap-5" id="more" aria-labelledby="menu-more-h">
	<h2 id="menu-more-h" class="m-0 border-b-[3px] border-brand-forest pb-3.5 font-display text-[clamp(2.5rem,5vw,3rem)] font-bold leading-none text-brand-forest">
		<?php esc_html_e( 'Juices & More', 'juiced' ); ?>
	</h2>

	<div class="grid gap-x-10 gap-y-8 md:grid-cols-2 lg:grid-cols-3">
		<?php foreach ( $juiced_columns as $column ) : ?>
			<div class="flex flex-col gap-8">
				<?php foreach ( $column as list( $section, $items ) ) : ?>
					<div class="flex flex-col gap-2">
						<h3 class="m-0 text-[13px] font-bold uppercase tracking-[.2em] text-brand-rust"><?php echo esc_html( $section->name ); ?></h3>
						<ul class="m-0 list-none p-0">
							<?php foreach ( $items as $item ) : ?>
								<?php
								$title = get_the_title( $item );
								$size  = '';
								if ( preg_match( '/^(.*?)\s*\(([^)]+)\)\s*$/', $title, $m ) ) {
									$title = $m[1];
									$size  = $m[2];
								}
								$ingr  = array_filter( array_map( 'trim', explode( ',', wp_strip_all_tags( (string) get_field( 'ingredients', $item->ID ) ) ) ) );
								$price = juiced_format_menu_price( get_field( 'price', $item->ID ) );
								?>
								<li class="flex items-baseline justify-between gap-4 border-b border-brand-forest/15 py-2.5">
									<div class="flex min-w-0 flex-col gap-0.5">
										<span class="font-display text-xl font-semibold text-brand-forest">
											<?php echo esc_html( $title ); ?>
											<?php if ( $size ) : ?>
												<span class="ml-1 font-sans text-sm font-medium text-[#4a4036]"><?php echo esc_html( $size ); ?></span>
											<?php endif; ?>
										</span>
										<?php if ( $ingr ) : ?>
											<span class="text-sm text-[#2c3a2e]"><?php echo esc_html( implode( ' · ', $ingr ) ); ?></span>
										<?php endif; ?>
									</div>
									<?php if ( $price ) : ?>
										<span class="shrink-0 font-bold text-brand-rust tabular-nums"><?php echo esc_html( $price ); ?></span>
									<?php endif; ?>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endforeach; ?>
	</div>
</section>
