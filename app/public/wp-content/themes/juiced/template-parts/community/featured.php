<?php
/**
 * Community page Section 2 — this month's spotlight(s).
 *
 * The newest month's one or two spotlights (picked in page-templates/
 * community.php), shown big on the cream ground. One spotlight gets a wide
 * split card — photo left, story right; two sit side by side as tall cards.
 * Each card carries the type badge, name, role, pull quote, intro, and a link
 * through to the full story. A spotlight without a photo gets a soft green
 * panel with its type icon, so the layout holds while the client uploads.
 *
 * Expects, via get_template_part()'s $args:
 *   posts  WP_Post[]  The featured spotlights (0–2).
 *
 * @package Juiced
 */

$juiced_posts   = isset( $args['posts'] ) ? $args['posts'] : array();
$juiced_heading = juiced_page_field( 'community_featured_heading', 'Community Spotlight' );
$juiced_cards   = array_values( array_filter( array_map( 'juiced_spotlight_card_data', $juiced_posts ) ) );
$juiced_single  = 1 === count( $juiced_cards );
?>
<section class="bg-brand-cream text-brand-ink" aria-labelledby="community-featured-h">
	<div class="mx-auto max-w-7xl px-4 py-14 md:py-20">

		<header class="max-w-2xl">
			<?php if ( $juiced_cards ) : ?>
				<p class="text-sm font-bold uppercase tracking-[.14em] text-brand-rust">
					<?php echo esc_html( $juiced_cards[0]['month'] ); ?>
				</p>
			<?php endif; ?>
			<h2 id="community-featured-h" class="mt-2 font-display text-4xl font-bold leading-tight text-brand-forest md:text-5xl">
				<?php echo esc_html( $juiced_heading ); ?>
			</h2>
		</header>

		<?php if ( ! $juiced_cards ) : ?>
			<p class="mt-10 rounded-3xl bg-white px-6 py-10 text-center text-brand-ink/70 ring-1 ring-brand-forest/15">
				<?php esc_html_e( 'Our first spotlight is on the way — check back soon.', 'juiced' ); ?>
			</p>
		<?php else : ?>

			<div class="mt-10 grid gap-8 <?php echo $juiced_single ? '' : 'lg:grid-cols-2'; ?>">
				<?php foreach ( $juiced_cards as $juiced_card ) : ?>
					<article class="group grid overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-brand-forest/10 <?php echo $juiced_single ? 'md:grid-cols-2' : ''; ?>">

						<a href="<?php echo esc_url( $juiced_card['permalink'] ); ?>" class="block overflow-hidden" tabindex="-1" aria-hidden="true">
							<?php if ( $juiced_card['image'] ) : ?>
								<img src="<?php echo esc_url( $juiced_card['image'] ); ?>" alt=""
									 class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-[1.03] <?php echo $juiced_single ? 'aspect-[4/3] md:aspect-auto md:min-h-[460px]' : 'aspect-[4/3]'; ?>" />
							<?php else : ?>
								<div class="flex h-full w-full items-center justify-center bg-brand-green/10 text-brand-green <?php echo $juiced_single ? 'aspect-[4/3] md:aspect-auto md:min-h-[460px]' : 'aspect-[4/3]'; ?>">
									<?php echo juiced_spotlight_icon( $juiced_card['type'], 48 ); // phpcs:ignore WordPress.Security.EscapeOutput -- static inline SVG. ?>
								</div>
							<?php endif; ?>
						</a>

						<div class="flex flex-col p-6 md:p-10">
							<span class="inline-flex items-center gap-1.5 self-start rounded-full bg-brand-gold px-3.5 py-1.5 text-sm font-semibold text-brand-ink">
								<?php echo juiced_spotlight_icon( $juiced_card['type'] ); // phpcs:ignore WordPress.Security.EscapeOutput -- static inline SVG. ?>
								<?php echo esc_html( $juiced_card['badge'] ); ?>
							</span>

							<h3 class="mt-4 font-display text-3xl font-bold leading-tight text-brand-forest md:text-4xl">
								<a href="<?php echo esc_url( $juiced_card['permalink'] ); ?>" class="transition-colors hover:text-brand-green">
									<?php echo esc_html( $juiced_card['title'] ); ?>
								</a>
							</h3>

							<?php if ( $juiced_card['role'] ) : ?>
								<p class="mt-1 text-base font-semibold text-brand-rust"><?php echo esc_html( $juiced_card['role'] ); ?></p>
							<?php endif; ?>

							<?php if ( $juiced_card['quote'] ) : ?>
								<blockquote class="mt-6 border-l-4 border-brand-gold pl-4 font-display text-xl leading-snug text-brand-ink md:text-2xl">
									&ldquo;<?php echo esc_html( $juiced_card['quote'] ); ?>&rdquo;
								</blockquote>
							<?php endif; ?>

							<?php if ( $juiced_card['intro'] ) : ?>
								<p class="mt-5 text-base leading-relaxed text-brand-ink/75">
									<?php echo esc_html( wp_strip_all_tags( $juiced_card['intro'] ) ); ?>
								</p>
							<?php endif; ?>

							<a href="<?php echo esc_url( $juiced_card['permalink'] ); ?>"
							   class="mt-auto inline-flex items-center gap-2 self-start pt-8">
								<span class="inline-flex items-center gap-2 rounded-full bg-brand-forest px-6 py-3 font-bold text-brand-cream transition-colors group-hover:bg-brand-gold group-hover:text-brand-ink">
									<?php esc_html_e( 'Read the Story', 'juiced' ); ?>
									<span aria-hidden="true">&rarr;</span>
								</span>
							</a>
						</div>
					</article>
				<?php endforeach; ?>
			</div>

		<?php endif; ?>
	</div>
</section>
