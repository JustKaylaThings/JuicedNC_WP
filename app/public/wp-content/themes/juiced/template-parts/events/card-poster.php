<?php
/**
 * Events page — one event as a poster (the filter bar's "grid" view).
 *
 * The flyer leads: a tall portrait crop with the date badge sitting on it, and
 * the details stacked underneath. The companion is card-row.php, which draws
 * the same event as a wide row for "list" view; both take their values from
 * juiced_event_card_data() so they can't disagree.
 *
 * Expects, via get_template_part()'s $args:
 *   card  array  A juiced_event_card_data() result.
 *
 * The crop is portrait because these images are event flyers, which are
 * designed portrait — a landscape crop cuts the top and bottom off the artwork.
 * Events with no flyer get a solid accent panel carrying the category name, so
 * a missing image reads as a design choice rather than a hole in the grid.
 *
 * @package Juiced
 */

$juiced_card = isset( $args['card'] ) ? $args['card'] : array();

if ( empty( $juiced_card ) ) {
	return;
}

$juiced_color = $juiced_card['color'];
$juiced_term  = $juiced_card['term'];
?>
<article class="group flex h-full flex-col overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-black/5 transition-all hover:shadow-md hover:ring-black/10">

	<!-- Flyer + date badge -->
	<a href="<?php echo esc_url( $juiced_card['permalink'] ); ?>" class="relative block aspect-[4/5] overflow-hidden" tabindex="-1" aria-hidden="true">
		<?php if ( $juiced_card['image'] ) : ?>
			<img src="<?php echo esc_url( $juiced_card['image'] ); ?>" alt=""
				 class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-[1.04]" loading="lazy" />
		<?php else : ?>
			<div class="flex h-full w-full items-center justify-center p-5 text-center <?php echo esc_attr( $juiced_color['soft'] . ' ' . $juiced_color['text'] ); ?>">
				<?php if ( $juiced_term ) : ?>
					<span class="font-display text-lg font-bold uppercase leading-tight tracking-wide"><?php echo esc_html( $juiced_term->name ); ?></span>
				<?php else : ?>
					<svg viewBox="0 0 24 24" width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
						<rect x="3" y="4" width="18" height="18" rx="2" />
						<path d="M16 2v4M8 2v4M3 10h18" stroke-linecap="round" />
					</svg>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<div class="absolute left-3 top-3 w-12 rounded-xl <?php echo esc_attr( $juiced_color['bg'] ); ?> py-1.5 text-center leading-none text-white shadow-md">
			<span class="block text-[0.58rem] font-semibold tracking-[0.1em]"><?php echo esc_html( $juiced_card['weekday'] ); ?></span>
			<span class="block text-xl font-bold mt-0.5"><?php echo esc_html( $juiced_card['day'] ); ?></span>
			<span class="block text-[0.58rem] font-semibold tracking-[0.1em] mt-0.5"><?php echo esc_html( $juiced_card['month'] ); ?></span>
		</div>
	</a>

	<div class="flex flex-1 flex-col p-5">

		<h3 class="font-display text-lg font-bold leading-tight <?php echo esc_attr( $juiced_color['text'] ); ?>">
			<a href="<?php echo esc_url( $juiced_card['permalink'] ); ?>" class="hover:underline">
				<?php echo esc_html( $juiced_card['title'] ); ?>
			</a>
		</h3>

		<!-- Time -->
		<?php if ( $juiced_card['start'] ) : ?>
			<div class="mt-1.5 flex items-center gap-1.5 text-sm font-medium text-brand-bark/70">
				<svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
					<circle cx="12" cy="12" r="9" />
					<path d="M12 7v5l3 2" stroke-linecap="round" stroke-linejoin="round" />
				</svg>
				<span><?php echo esc_html( $juiced_card['start'] ); ?><?php if ( $juiced_card['end'] ) : ?> &ndash; <?php echo esc_html( $juiced_card['end'] ); ?><?php endif; ?></span>
			</div>
		<?php endif; ?>

		<!-- Description. Clamped rather than trimmed: posters sit shoulder to
			 shoulder in a grid, so ragged card heights show. -->
		<?php if ( $juiced_card['excerpt'] ) : ?>
			<p class="mt-2.5 line-clamp-2 text-sm leading-relaxed text-brand-bark/70">
				<?php echo esc_html( $juiced_card['excerpt'] ); ?>
			</p>
		<?php endif; ?>

		<!-- Tags -->
		<?php if ( $juiced_term || $juiced_card['audience'] ) : ?>
			<div class="mt-3 flex flex-wrap items-center gap-2">
				<?php
				if ( $juiced_term ) :
					$juiced_style = juiced_event_category_style( $juiced_term->slug );
					?>
					<span class="inline-flex items-center gap-1.5 rounded-full bg-black/[0.04] px-3 py-1 text-sm font-medium text-brand-bark/75">
						<span class="<?php echo esc_attr( $juiced_style['text'] ); ?>">
							<?php echo $juiced_style['icon']; // phpcs:ignore WordPress.Security.EscapeOutput -- static inline SVG. ?>
						</span>
						<?php echo esc_html( $juiced_term->name ); ?>
					</span>
				<?php endif; ?>

				<?php if ( $juiced_card['audience'] ) : ?>
					<span class="inline-flex items-center rounded-full bg-black/[0.04] px-3 py-1 text-sm font-medium text-brand-bark/75">
						<?php echo esc_html( $juiced_card['audience'] ); ?>
					</span>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<!-- Price + action. mt-auto pins this to the bottom so the buttons line
			 up across a row of cards whatever the copy above does. -->
		<div class="mt-auto pt-4">
			<span class="block text-base font-bold <?php echo esc_attr( $juiced_color['text'] ); ?>">
				<?php echo esc_html( $juiced_card['cost'] ); ?>
			</span>
			<a href="<?php echo esc_url( $juiced_card['permalink'] ); ?>"
			   class="mt-2.5 inline-flex items-center justify-center rounded-lg border px-3.5 py-2 text-xs font-semibold transition-colors <?php echo esc_attr( $juiced_color['border'] . ' ' . $juiced_color['text'] . ' ' . $juiced_color['hover'] ); ?> hover:text-white">
				<?php esc_html_e( 'Learn More', 'juiced' ); ?>
			</a>
		</div>
	</div>
</article>
