<?php
/**
 * One spotlight as a card — the Community archive grid and the single
 * spotlight's "More Spotlights" row.
 *
 * Photo with the type badge pinned over it, then the month, name, role, a
 * couple of lines of intro, and a Read the Story link. No photo → a soft green
 * panel with the type icon, same fallback as the featured cards.
 *
 * Expects, via get_template_part()'s $args:
 *   card  array  A juiced_spotlight_card_data() result (inc/helpers.php).
 *
 * @package Juiced
 */

$juiced_card = isset( $args['card'] ) ? $args['card'] : array();

if ( empty( $juiced_card ) ) {
	return;
}
?>
<article class="group flex h-full flex-col overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-brand-forest/10 transition-all hover:shadow-md hover:ring-brand-forest/20">

	<a href="<?php echo esc_url( $juiced_card['permalink'] ); ?>" class="relative block overflow-hidden" tabindex="-1" aria-hidden="true">
		<?php if ( $juiced_card['thumb'] ) : ?>
			<img src="<?php echo esc_url( $juiced_card['thumb'] ); ?>" alt=""
				 class="aspect-[4/3] w-full object-cover transition-transform duration-300 group-hover:scale-[1.03]" loading="lazy" />
		<?php else : ?>
			<div class="flex aspect-[4/3] w-full items-center justify-center bg-brand-green/10 text-brand-green">
				<?php echo juiced_spotlight_icon( $juiced_card['type'], 36 ); // phpcs:ignore WordPress.Security.EscapeOutput -- static inline SVG. ?>
			</div>
		<?php endif; ?>
		<span class="absolute left-3 top-3 inline-flex items-center gap-1.5 rounded-full bg-white/95 px-3 py-1 text-xs font-bold text-brand-forest shadow-sm">
			<?php echo juiced_spotlight_icon( $juiced_card['type'], 13 ); // phpcs:ignore WordPress.Security.EscapeOutput -- static inline SVG. ?>
			<?php echo esc_html( $juiced_card['badge'] ); ?>
		</span>
	</a>

	<div class="flex flex-1 flex-col p-5">
		<p class="text-xs font-bold uppercase tracking-[.12em] text-brand-rust"><?php echo esc_html( $juiced_card['month'] ); ?></p>

		<h3 class="mt-1.5 font-display text-xl font-bold leading-tight text-brand-forest">
			<a href="<?php echo esc_url( $juiced_card['permalink'] ); ?>" class="transition-colors hover:text-brand-green">
				<?php echo esc_html( $juiced_card['title'] ); ?>
			</a>
		</h3>

		<?php if ( $juiced_card['role'] ) : ?>
			<p class="mt-0.5 text-sm font-semibold text-brand-ink/70"><?php echo esc_html( $juiced_card['role'] ); ?></p>
		<?php endif; ?>

		<?php if ( $juiced_card['intro'] ) : ?>
			<p class="mt-3 text-sm leading-relaxed text-brand-ink/70">
				<?php echo esc_html( wp_trim_words( wp_strip_all_tags( $juiced_card['intro'] ), 20 ) ); ?>
			</p>
		<?php endif; ?>

		<a href="<?php echo esc_url( $juiced_card['permalink'] ); ?>"
		   class="mt-auto inline-flex items-center gap-1.5 pt-4 text-sm font-bold text-brand-rust transition-colors hover:text-brand-green">
			<?php esc_html_e( 'Read the Story', 'juiced' ); ?>
			<span aria-hidden="true" class="transition-transform group-hover:translate-x-0.5">&rarr;</span>
		</a>
	</div>
</article>
