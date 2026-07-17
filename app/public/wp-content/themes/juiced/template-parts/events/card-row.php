<?php
/**
 * Events page — one event as a wide row (the filter bar's "list" view).
 *
 * Flyer down the left, details in the middle, action and price on the right.
 * The companion is card-poster.php, which draws the same event as a poster for
 * "grid" view; both take their values from juiced_event_card_data() so they
 * can't disagree.
 *
 * Expects, via get_template_part()'s $args:
 *   card   array  A juiced_event_card_data() result.
 *   flyer  bool   Optional, default true. False drops the flyer column (and
 *                 its no-image fallback panel) entirely — the single-event
 *                 page's "More Upcoming Events" list wants text-only rows.
 *
 * The accent (date badge, title, Learn More, price) comes from the card's
 * palette slot. The category tag is colored by its term instead —
 * juiced_event_category_style() — keeping it matched to its pill in the filter
 * bar above.
 *
 * @package Juiced
 */

$juiced_card  = isset( $args['card'] ) ? $args['card'] : array();
$juiced_flyer = ! isset( $args['flyer'] ) || $args['flyer'];

if ( empty( $juiced_card ) ) {
	return;
}

$juiced_color = $juiced_card['color'];
$juiced_term  = $juiced_card['term'];
?>
<article class="group flex overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-black/5 transition-all hover:shadow-md hover:ring-black/10">

	<!-- Flyer -->
	<?php if ( ! $juiced_flyer ) : ?>
		<?php // No flyer column at all — the details start at the card edge. ?>
	<?php elseif ( $juiced_card['image'] ) : ?>
		<img src="<?php echo esc_url( $juiced_card['image'] ); ?>" alt=""
			 class="hidden sm:block w-40 md:w-48 shrink-0 self-stretch object-cover" loading="lazy" />
	<?php else : ?>
		<div class="hidden sm:flex w-40 md:w-48 shrink-0 self-stretch items-center justify-center p-4 text-center <?php echo esc_attr( $juiced_color['soft'] . ' ' . $juiced_color['text'] ); ?>">
			<?php if ( $juiced_term ) : ?>
				<span class="text-xs font-semibold uppercase tracking-wide leading-tight"><?php echo esc_html( $juiced_term->name ); ?></span>
			<?php else : ?>
				<svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
					<rect x="3" y="4" width="18" height="18" rx="2" />
					<path d="M16 2v4M8 2v4M3 10h18" stroke-linecap="round" />
				</svg>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<div class="flex min-w-0 flex-1 flex-col gap-4 p-5 sm:flex-row sm:items-center md:p-6">

		<div class="min-w-0 flex-1">

			<!-- Date badge + title -->
			<div class="flex items-start gap-3">
				<div class="w-12 shrink-0 rounded-xl <?php echo esc_attr( $juiced_color['bg'] ); ?> py-1.5 text-center leading-none text-white">
					<span class="block text-[0.58rem] font-semibold tracking-[0.1em]"><?php echo esc_html( $juiced_card['weekday'] ); ?></span>
					<span class="block text-xl font-bold mt-0.5"><?php echo esc_html( $juiced_card['day'] ); ?></span>
					<span class="block text-[0.58rem] font-semibold tracking-[0.1em] mt-0.5"><?php echo esc_html( $juiced_card['month'] ); ?></span>
				</div>

				<div class="min-w-0 flex-1">
					<h3 class="font-display text-xl font-bold leading-tight md:text-2xl <?php echo esc_attr( $juiced_color['text'] ); ?>">
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
				</div>
			</div>

			<!-- Description -->
			<?php if ( $juiced_card['excerpt'] ) : ?>
				<p class="mt-3 text-sm leading-relaxed text-brand-bark/70">
					<?php echo esc_html( wp_trim_words( $juiced_card['excerpt'], 20 ) ); ?>
				</p>
			<?php endif; ?>

			<!-- Tags -->
			<?php if ( $juiced_term || $juiced_card['audience'] ) : ?>
				<div class="mt-3.5 flex flex-wrap items-center gap-2">
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
		</div>

		<!-- Price + action -->
		<div class="flex shrink-0 items-center gap-3 sm:w-28 sm:flex-col sm:justify-center sm:gap-2">
			<span class="text-base font-bold <?php echo esc_attr( $juiced_color['text'] ); ?>">
				<?php echo esc_html( $juiced_card['cost'] ); ?>
			</span>
			<a href="<?php echo esc_url( $juiced_card['permalink'] ); ?>"
			   class="inline-flex items-center justify-center rounded-lg border px-3.5 py-2 text-xs font-semibold transition-colors <?php echo esc_attr( $juiced_color['border'] . ' ' . $juiced_color['text'] . ' ' . $juiced_color['hover'] ); ?> hover:text-white">
				<?php esc_html_e( 'Learn More', 'juiced' ); ?>
			</a>
		</div>
	</div>
</article>
