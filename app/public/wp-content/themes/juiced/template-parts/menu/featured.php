<?php
/**
 * Menu Section 1 — featured items, on cream under the green page hero:
 * heading and intro over a green rule, then the featured items in one row.
 *
 * Featured items are published Menu Items with "Featured" switched on (the
 * plugin's Menu Item Details group), in the items' Order, then title; up to
 * three. Each card is a square photo with the badge in an orange pill and the
 * name and type · size · price over a dark fade, then the ingredients and the
 * delivery buttons (juiced_delivery_apps() / juiced_app_button()). A drink without a photo shows a
 * colour block, as in the homepage top five. Renders nothing when no items
 * are featured.
 *
 * @package Juiced
 */

$juiced_featured = get_posts(
	array(
		'post_type'      => 'menu_item',
		'posts_per_page' => 3,
		'orderby'        => array(
			'menu_order' => 'ASC',
			'title'      => 'ASC',
		),
		'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
			array(
				'key'   => 'featured',
				'value' => '1',
			),
		),
	)
);
if ( ! $juiced_featured ) {
	return;
}

$juiced_f_lead    = juiced_page_field( 'menu_featured_heading', 'Featured' );
$juiced_f_accent  = juiced_page_field( 'menu_featured_accent', 'this week' );
$juiced_f_intro   = juiced_page_field( 'menu_featured_intro', 'Our most-loved blends, made fresh to order. Get them delivered or stop in.' );
$juiced_f_apps    = juiced_delivery_apps();
$juiced_f_blocks  = array(
	array( 'bg-brand-gold', 'bg-brand-forest-deep' ),
	array( 'bg-brand-forest-deep', 'bg-brand-gold' ),
	array( 'bg-brand-ink', 'bg-brand-gold' ),
);
?>
<section class="bg-brand-cream text-brand-ink" id="featured" aria-labelledby="menu-featured-h">
	<div class="mx-auto flex max-w-7xl flex-col gap-7 px-4 py-14 md:py-16">

		<div class="flex flex-wrap items-end justify-between gap-x-6 gap-y-2 border-b-[3px] border-brand-forest pb-3.5">
			<h2 id="menu-featured-h" class="m-0 font-display font-bold leading-none text-brand-forest text-balance text-[clamp(2.5rem,5vw,3rem)]">
				<?php echo esc_html( $juiced_f_lead ); ?>
				<?php if ( $juiced_f_accent ) : ?>
					<span class="text-brand-gold"><?php echo esc_html( $juiced_f_accent ); ?></span>
				<?php endif; ?>
			</h2>
			<?php if ( $juiced_f_intro ) : ?>
				<p class="m-0 max-w-sm text-[15px] leading-relaxed text-[#4a4036]"><?php echo esc_html( $juiced_f_intro ); ?></p>
			<?php endif; ?>
		</div>

		<ul class="m-0 grid list-none gap-8 p-0 sm:grid-cols-2 lg:grid-cols-3 lg:gap-6">
			<?php foreach ( $juiced_featured as $k => $item ) : ?>
				<?php
				$title = get_the_title( $item );
				$size  = '';
				if ( preg_match( '/^(.*?)\s*\(([^)]+)\)\s*$/', $title, $m ) ) {
					$title = $m[1];
					$size  = $m[2];
				}
				$type    = '';
				$section = get_the_terms( $item, 'menu_section' );
				if ( $section && ! is_wp_error( $section ) ) {
					$type = preg_replace( '/s$/', '', $section[0]->name );
				}
				$ingr  = array_filter( array_map( 'trim', explode( ',', wp_strip_all_tags( (string) get_field( 'ingredients', $item->ID ) ) ) ) );
				$meta  = implode( ' · ', array_filter( array( $type, $size, juiced_format_menu_price( get_field( 'price', $item->ID ) ) ) ) );
				$badge = get_field( 'menu_badge', $item->ID );
				$photo = get_the_post_thumbnail_url( $item, 'large' );
				$block = $juiced_f_blocks[ $k % count( $juiced_f_blocks ) ];
				?>
				<li class="flex flex-col gap-3.5">
					<div class="relative aspect-square overflow-hidden rounded-3xl">
						<?php if ( $photo ) : ?>
							<img src="<?php echo esc_url( $photo ); ?>" alt="<?php echo esc_attr( $title ); ?>" class="absolute inset-0 h-full w-full object-cover" loading="lazy" />
						<?php else : ?>
							<div class="absolute inset-0 <?php echo esc_attr( $block[0] ); ?>" aria-hidden="true">
								<span class="absolute -right-12 -top-12 h-48 w-48 rounded-full <?php echo esc_attr( $block[1] ); ?>"></span>
							</div>
						<?php endif; ?>
						<?php if ( $badge ) : ?>
							<span class="absolute left-3.5 top-3.5 rounded-full bg-brand-gold px-3 py-1.5 text-xs font-bold uppercase tracking-[.12em] text-brand-ink"><?php echo esc_html( $badge ); ?></span>
						<?php endif; ?>
						<div class="absolute inset-x-0 bottom-0 flex flex-col gap-1 bg-gradient-to-t from-[rgba(10,30,15,.88)] to-transparent px-5 pb-4 pt-16">
							<h3 class="m-0 font-display text-[32px] font-bold leading-none text-white"><?php echo esc_html( $title ); ?></h3>
							<?php if ( $meta ) : ?>
								<span class="text-sm font-semibold text-brand-paper"><?php echo esc_html( $meta ); ?></span>
							<?php endif; ?>
						</div>
					</div>
					<?php if ( $ingr ) : ?>
						<p class="m-0 text-[15px] leading-relaxed text-[#2c3a2e]"><?php echo esc_html( implode( ' · ', $ingr ) ); ?></p>
					<?php endif; ?>
					<?php if ( $juiced_f_apps ) : ?>
						<div class="mt-auto flex flex-wrap items-center gap-1.5">
							<span class="mr-1 text-xs font-bold uppercase tracking-[.14em] text-brand-forest"><?php esc_html_e( 'Order on', 'juiced' ); ?></span>
							<?php foreach ( $juiced_f_apps as $app ) : ?>
								<?php juiced_app_button( $app, 'px-3 py-2 text-xs', $title ); ?>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>

	</div>
</section>
