<?php
/**
 * Homepage Section 4 — Menu showcase ("Good for You. Made with Love.").
 *
 * Dark section on a grain background. A row of category tabs (from the
 * menu_section taxonomy) filters a scrollable rail of menu-item cards without a
 * page reload, powered by Alpine.js: each tab sets `tab` and each panel shows
 * only when its slug matches. Cards come from the menu_item CPT — featured image,
 * title, ingredients, and price (price/ingredients are ACF fields on the CPT,
 * registered in the juiced-cpt plugin). Section copy is ACF-editable on the Home
 * page (inc/acf-homepage.php).
 *
 * Only sections that actually have published items appear (get_terms hide_empty),
 * ordered to match the mockup via juiced_sort_menu_sections(). Tab icons/colors
 * come from juiced_menu_section_style(). Renders nothing when there are no menu
 * items at all.
 *
 * @package Juiced
 */

// Only sections that contain at least one published menu item.
$juiced_sections = get_terms(
	array(
		'taxonomy'   => 'menu_section',
		'hide_empty' => true,
	)
);

if ( is_wp_error( $juiced_sections ) || empty( $juiced_sections ) ) {
	return;
}

$juiced_sections = juiced_sort_menu_sections( $juiced_sections );
$juiced_first    = $juiced_sections[0]->slug;

$juiced_menu_heading = juiced_field( 'menu_heading', 'Good for You.' );
$juiced_menu_accent  = juiced_field( 'menu_heading_accent', 'Made with Love.' );
$juiced_menu_url     = juiced_field( 'menu_link_url', '' );
?>
<section class="relative bg-brand-ink text-brand-cream overflow-hidden bg-grain">
	<div class="mx-auto max-w-7xl px-4 py-16 md:py-24"
		 x-data="{ tab: '<?php echo esc_js( $juiced_first ); ?>' }">

		<!-- Section heading -->
		<h2 class="text-center font-display text-3xl md:text-4xl">
			<span class="text-white"><?php echo esc_html( $juiced_menu_heading ); ?></span>
			<span class="relative text-brand-gold whitespace-nowrap">
				<?php echo esc_html( $juiced_menu_accent ); ?>
				<span class="absolute -bottom-1 left-0 h-[3px] w-full rounded-full bg-brand-green"></span>
			</span>
		</h2>

		<!-- Category tabs -->
		<div class="mt-10 flex flex-wrap items-center justify-center gap-x-8 gap-y-4">
			<?php foreach ( $juiced_sections as $section ) : ?>
				<?php $style = juiced_menu_section_style( $section->slug ); ?>
				<button type="button"
						@click="tab = '<?php echo esc_js( $section->slug ); ?>'"
						class="group flex items-center gap-2 pb-1 border-b-2 transition-colors <?php echo esc_attr( $style['color'] ); ?>"
						:class="tab === '<?php echo esc_js( $section->slug ); ?>' ? 'border-current opacity-100' : 'border-transparent opacity-60 hover:opacity-100'">
					<span aria-hidden="true"><?php echo $style['icon']; // phpcs:ignore WordPress.Security.EscapeOutput -- static inline SVG. ?></span>
					<span class="text-sm md:text-base font-semibold text-white"><?php echo esc_html( $section->name ); ?></span>
				</button>
			<?php endforeach; ?>
		</div>

		<!-- Panels: one scrollable card rail per section, shown when its tab is active -->
		<?php foreach ( $juiced_sections as $section ) : ?>
			<?php
			$items = new WP_Query(
				array(
					'post_type'      => 'menu_item',
					'post_status'    => 'publish',
					'posts_per_page' => 24,
					'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
					'tax_query'      => array(
						array(
							'taxonomy' => 'menu_section',
							'field'    => 'term_id',
							'terms'    => $section->term_id,
						),
					),
				)
			);
			if ( ! $items->have_posts() ) {
				continue;
			}
			?>
			<div x-show="tab === '<?php echo esc_js( $section->slug ); ?>'" x-cloak x-data
				 class="relative mt-12">

				<!-- Prev / next arrows (hidden on touch; rail also scroll-snaps) -->
				<button type="button" @click="$refs.rail.scrollBy({ left: -320, behavior: 'smooth' })"
						class="hidden md:grid place-items-center absolute -left-4 top-1/2 -translate-y-1/2 z-10 w-10 h-10 rounded-full bg-white/10 text-white backdrop-blur hover:bg-white/20 transition"
						aria-label="<?php esc_attr_e( 'Scroll left', 'juiced' ); ?>">
					<span aria-hidden="true">&larr;</span>
				</button>
				<button type="button" @click="$refs.rail.scrollBy({ left: 320, behavior: 'smooth' })"
						class="hidden md:grid place-items-center absolute -right-4 top-1/2 -translate-y-1/2 z-10 w-10 h-10 rounded-full bg-white/10 text-white backdrop-blur hover:bg-white/20 transition"
						aria-label="<?php esc_attr_e( 'Scroll right', 'juiced' ); ?>">
					<span aria-hidden="true">&rarr;</span>
				</button>

				<!-- Card rail -->
				<div x-ref="rail" class="flex gap-5 overflow-x-auto snap-x snap-mandatory no-scrollbar pb-2">
					<?php
					while ( $items->have_posts() ) :
						$items->the_post();
						$mid    = get_the_ID();
						$img    = get_the_post_thumbnail_url( $mid, 'juiced_card' );
						$ingr   = wp_strip_all_tags( (string) get_field( 'ingredients', $mid ) );
						$price  = juiced_format_menu_price( get_field( 'price', $mid ) );
						?>
						<article class="snap-start shrink-0 w-[68vw] sm:w-[44vw] md:w-[calc((100%-4rem)/5)] rounded-2xl bg-white/[0.04] ring-1 ring-white/10 overflow-hidden hover:ring-white/20 transition">
							<div class="aspect-[4/5] w-full bg-white/5">
								<?php if ( $img ) : ?>
									<img src="<?php echo esc_url( $img ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>"
										 class="w-full h-full object-cover" loading="lazy" />
								<?php else : ?>
									<div class="w-full h-full grid place-items-center text-brand-cream/30 text-xs px-3 text-center">
										<?php esc_html_e( 'Add a photo', 'juiced' ); ?>
									</div>
								<?php endif; ?>
							</div>
							<div class="p-4 text-center">
								<h3 class="font-display font-bold text-white text-lg leading-tight"><?php the_title(); ?></h3>
								<?php if ( $ingr ) : ?>
									<p class="mt-1.5 text-xs text-brand-cream/60 leading-relaxed"><?php echo esc_html( $ingr ); ?></p>
								<?php endif; ?>
								<?php if ( $price ) : ?>
									<p class="mt-2 font-semibold text-white"><?php echo esc_html( $price ); ?></p>
								<?php endif; ?>
							</div>
						</article>
					<?php endwhile; ?>
				</div>
			</div>
			<?php wp_reset_postdata(); ?>
		<?php endforeach; ?>

		<!-- View full menu -->
		<?php if ( $juiced_menu_url ) : ?>
			<div class="mt-14 flex justify-center">
				<a href="<?php echo esc_url( $juiced_menu_url ); ?>"
				   class="inline-flex items-center gap-2 rounded-full bg-brand-gold px-7 py-3 font-semibold text-white shadow-sm hover:bg-brand-green-light transition">
					<?php esc_html_e( 'View Full Menu', 'juiced' ); ?>
					<span aria-hidden="true">&rarr;</span>
				</a>
			</div>
		<?php endif; ?>

	</div>
</section>
