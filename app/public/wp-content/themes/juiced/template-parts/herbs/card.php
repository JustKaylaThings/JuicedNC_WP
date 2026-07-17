<?php
/**
 * Herb Library page — one herb as a card in the "All Herbs" grid.
 *
 * Photo up top, name, a couple of lines of description, benefit tag chips, and
 * a Learn More link to the single herb. Herbs without a photo get a soft green
 * panel with a leaf instead, so the grid holds its shape while the client
 * uploads images.
 *
 * Expects, via get_template_part()'s $args:
 *   card     array  A juiced_herb_card_data() result (inc/helpers.php).
 *   compact  bool   Optional. The single-herb "You May Also Like" variant:
 *                   five-up, so the tag chips drop and the type tightens.
 *
 * @package Juiced
 */

$juiced_card    = isset( $args['card'] ) ? $args['card'] : array();
$juiced_compact = ! empty( $args['compact'] );

if ( empty( $juiced_card ) ) {
	return;
}
?>
<article class="group flex h-full flex-col overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-black/5 transition-all hover:shadow-md hover:ring-black/10">

	<!-- Photo -->
	<a href="<?php echo esc_url( $juiced_card['permalink'] ); ?>" class="block" tabindex="-1" aria-hidden="true">
		<?php if ( $juiced_card['image'] ) : ?>
			<img src="<?php echo esc_url( $juiced_card['image'] ); ?>" alt=""
				 class="aspect-[4/3] w-full object-cover transition-transform duration-300 group-hover:scale-[1.03]" loading="lazy" />
		<?php else : ?>
			<div class="flex aspect-[4/3] w-full items-center justify-center bg-brand-green/10 text-brand-green">
				<svg viewBox="0 0 24 24" width="36" height="36" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
					<path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z" />
					<path d="M2 21c0-3 1.85-5.36 5.08-6" />
				</svg>
			</div>
		<?php endif; ?>
	</a>

	<div class="flex flex-1 flex-col <?php echo $juiced_compact ? 'p-4' : 'p-5'; ?>">

		<h3 class="font-display <?php echo $juiced_compact ? 'text-lg' : 'text-xl'; ?> font-bold text-brand-bark">
			<a href="<?php echo esc_url( $juiced_card['permalink'] ); ?>" class="hover:text-brand-green transition-colors">
				<?php echo esc_html( $juiced_card['title'] ); ?>
			</a>
		</h3>

		<?php if ( $juiced_card['excerpt'] ) : ?>
			<p class="mt-2 <?php echo $juiced_compact ? 'text-sm' : 'text-base'; ?> leading-relaxed text-brand-bark/70">
				<?php echo esc_html( wp_trim_words( $juiced_card['excerpt'], $juiced_compact ? 12 : 18 ) ); ?>
			</p>
		<?php endif; ?>

		<!-- Benefit tags -->
		<?php if ( $juiced_card['tags'] && ! $juiced_compact ) : ?>
			<div class="mt-3 flex flex-wrap gap-x-3 gap-y-1.5">
				<?php foreach ( $juiced_card['tags'] as $juiced_tag ) : ?>
					<span class="inline-flex items-center gap-1 text-sm font-medium text-brand-bark/70">
						<span class="<?php echo esc_attr( $juiced_tag['style']['text'] ); ?>">
							<?php echo $juiced_tag['style']['icon']; // phpcs:ignore WordPress.Security.EscapeOutput -- static inline SVG. ?>
						</span>
						<?php echo esc_html( $juiced_tag['name'] ); ?>
					</span>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<a href="<?php echo esc_url( $juiced_card['permalink'] ); ?>"
		   class="mt-auto inline-flex items-center gap-1.5 pt-4 <?php echo $juiced_compact ? 'text-sm' : 'text-base'; ?> font-semibold text-brand-gold transition-colors hover:text-brand-green">
			<?php esc_html_e( 'Learn More', 'juiced' ); ?>
			<span aria-hidden="true" class="transition-transform group-hover:translate-x-0.5">&rarr;</span>
		</a>
	</div>
</article>
