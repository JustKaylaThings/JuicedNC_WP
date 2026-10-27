<?php
/**
 * Homepage Section 4 — More Than a Juice Bar ("Garden" redesign, Sep 2026).
 *
 * Deep-green band (faint leaf pattern, .bg-garden in src/tailwind.css): an @handle link, a two-tone heading, body copy and two
 * buttons (About us, Follow on Instagram) beside a cream "Instagram card" —
 * a profile header (avatar, handle, Follow) over a 3×2 photo grid.
 *
 * The grid shows the photos picked on the Home page (community_photo_1..6),
 * each linking to the Instagram profile. With no photos picked, the card holds
 * Instagram's official profile embed instead (a blockquote that IG's embed.js,
 * enqueued on the front page in functions.php, swaps for a hosted card), so the
 * section still shows real posts before anyone curates it.
 *
 * Along the bottom of the section, a full-width dark strip runs live Google
 * reviews from both shops as a marquee (option H, Oct 2026; moved out of the
 * Instagram card to span the section), with the combined rating and review count.
 * On desktop the whole section, reviews strip included, is exactly one viewport
 * tall (never under 640px), with a band of green under the strip; the card is
 * capped to the space above the strip
 * and clips the bottom of a tall Instagram embed. On phones it's at least one
 * viewport tall and grows with the stacked content.
 * Reviews come from inc/google-reviews.php; with no API key or no qualifying
 * reviews the strip is left out. This replaced
 * the separate testimonials carousel (Section 7).
 *
 * All copy, the photos and the Instagram URL are ACF-editable on the Home page
 * (inc/acf-homepage.php). The @handle is derived from the URL via
 * juiced_instagram_handle().
 *
 * @package Juiced
 */

$juiced_about = get_page_by_path( 'about' );

$juiced_c_heading = juiced_field( 'community_heading', 'More Than a Juice Bar.' );
$juiced_c_accent  = juiced_field( 'community_heading_accent', "We're a Community." );
$juiced_c_body    = juiced_field( 'community_body', 'Juiced! is where Raleigh comes together for healthy fun.' );
$juiced_c_label   = juiced_field( 'community_button_label', 'Learn More About Us' );
$juiced_c_url     = juiced_field( 'community_button_url', $juiced_about ? get_permalink( $juiced_about ) : home_url( '/about/' ) );

$juiced_ig_url    = juiced_field( 'community_instagram_url', 'https://www.instagram.com/juicednc/' );
$juiced_ig_handle = juiced_instagram_handle( $juiced_ig_url );

$juiced_c_photos = array();
for ( $n = 1; $n <= 6; $n++ ) {
	$image = juiced_field( "community_photo_{$n}", '' );
	$url   = juiced_image_url( $image, 'juiced_card' );
	if ( $url ) {
		$juiced_c_photos[] = array(
			'url' => $url,
			'alt' => is_array( $image ) ? ( $image['alt'] ?? '' ) : '',
		);
	}
}

$juiced_reviews    = juiced_community_reviews( juiced_review_place_ids() );
$juiced_rv_summary = $juiced_reviews ? juiced_reviews_summary( juiced_review_place_ids() ) : array();

$juiced_ig_icon = '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3.5" y="3.5" width="17" height="17" rx="4.5"/><circle cx="12" cy="12" r="3.8"/><circle cx="17.2" cy="6.8" r="0.8" fill="currentColor"/></svg>';
?>
<section class="flex min-h-svh flex-col pb-16 md:pb-24 lg:h-svh lg:min-h-[640px] bg-brand-forest bg-garden text-brand-cream">
	<div class="mx-auto w-full max-w-7xl flex-1 content-center px-4 py-8 grid gap-9 items-center lg:min-h-0 lg:grid-rows-[minmax(0,1fr)] <?php echo $juiced_ig_url ? 'lg:grid-cols-[minmax(0,1fr)_minmax(0,440px)] lg:gap-18' : ''; ?>">

		<!-- Text column -->
		<div class="flex flex-col gap-6">
			<?php if ( $juiced_ig_handle ) : ?>
				<a href="<?php echo esc_url( $juiced_ig_url ); ?>" target="_blank" rel="noopener noreferrer"
				   class="inline-flex items-center gap-2 self-start text-[15px] font-semibold text-brand-mint hover:text-brand-gold transition-colors">
					<?php echo $juiced_ig_icon; // phpcs:ignore WordPress.Security.EscapeOutput -- static SVG. ?>
					@<?php echo esc_html( $juiced_ig_handle ); ?>
				</a>
			<?php endif; ?>

			<h2 class="text-drop font-display font-bold leading-[1.02] text-[clamp(2.5rem,5.5vw,4rem)]">
				<span class="block"><?php echo esc_html( $juiced_c_heading ); ?></span>
				<span class="block text-brand-gold"><?php echo esc_html( $juiced_c_accent ); ?></span>
			</h2>

			<?php if ( $juiced_c_body ) : ?>
				<p class="max-w-[520px] text-lg md:text-xl leading-relaxed text-brand-mint"><?php echo juiced_logo_text( $juiced_c_body, 'inline-block h-[1.9em] w-auto -my-[.5em] align-middle' ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in the helper. ?></p>
			<?php endif; ?>

			<div class="mt-2 flex flex-wrap gap-3.5">
				<?php if ( $juiced_c_url && $juiced_c_label ) : ?>
					<a href="<?php echo esc_url( $juiced_c_url ); ?>"
					   class="inline-flex items-center gap-2 rounded-full bg-brand-gold px-6.5 py-4 text-brand-ink btn-sticker">
						<?php echo esc_html( $juiced_c_label ); ?> <span aria-hidden="true">&rarr;</span>
					</a>
				<?php endif; ?>
				<?php if ( $juiced_ig_url ) : ?>
					<a href="<?php echo esc_url( $juiced_ig_url ); ?>" target="_blank" rel="noopener noreferrer"
					   class="inline-flex items-center gap-2 rounded-full px-6 py-3.5 bg-brand-cream text-brand-ink btn-sticker">
						<?php echo $juiced_ig_icon; // phpcs:ignore WordPress.Security.EscapeOutput -- static SVG. ?>
						<?php esc_html_e( 'Follow on Instagram', 'juiced' ); ?>
					</a>
				<?php endif; ?>
			</div>
		</div>

		<?php if ( $juiced_ig_url ) : ?>
			<!-- Instagram card -->
			<div class="w-full max-w-[440px] justify-self-center lg:justify-self-end lg:max-h-full overflow-hidden rounded-3xl bg-brand-cream p-3 text-brand-ink shadow-[0_18px_40px_rgba(0,0,0,.25)]">
				<?php if ( $juiced_c_photos ) : ?>
					<!-- Profile header. The live embed draws its own, so it's only
						 needed over the curated grid. -->
					<div class="flex items-center gap-3 px-1.5 pb-1.5 h-[50px]">
						<span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-brand-gold font-display text-lg font-bold text-brand-forest" aria-hidden="true">J!</span>
						<span class="flex min-w-0 flex-col leading-tight">
							<strong class="text-[15px]"><?php echo esc_html( $juiced_ig_handle ? $juiced_ig_handle : 'Instagram' ); ?></strong>
							<span class="text-[13px] text-[#4a4036]">Juiced! NC · Raleigh</span>
						</span>
						<a href="<?php echo esc_url( $juiced_ig_url ); ?>" target="_blank" rel="noopener noreferrer"
						   class="ml-auto rounded-full bg-brand-forest px-4 py-2 text-[13px] text-brand-cream btn-sticker">
							<?php esc_html_e( 'Follow', 'juiced' ); ?>
						</a>
					</div>

					<a href="<?php echo esc_url( $juiced_ig_url ); ?>" target="_blank" rel="noopener noreferrer"
					   class="grid grid-cols-3 gap-1.5 overflow-hidden rounded-[14px]"
					   aria-label="<?php echo esc_attr( sprintf( /* translators: %s: Instagram handle. */ __( 'See more on Instagram @%s', 'juiced' ), $juiced_ig_handle ) ); ?>">
						<?php foreach ( $juiced_c_photos as $photo ) : ?>
							<img src="<?php echo esc_url( $photo['url'] ); ?>" alt="<?php echo esc_attr( $photo['alt'] ); ?>"
								 class="aspect-square md:aspect-auto md:h-[136px] w-full object-cover hover:opacity-90 transition-opacity" loading="lazy" />
						<?php endforeach; ?>
					</a>
				<?php else : ?>
					<!-- No curated photos yet: Instagram's live profile embed. -->
					<div class="overflow-hidden rounded-[14px] bg-white">
						<blockquote class="instagram-media"
							data-instgrm-permalink="<?php echo esc_url( $juiced_ig_url ); ?>"
							data-instgrm-version="14"
							style="background:#FFF; border:0; margin:0; max-width:100%; min-width:0; width:100%; padding:0;">
							<a href="<?php echo esc_url( $juiced_ig_url ); ?>" target="_blank" rel="noopener noreferrer" class="block p-6 text-center font-semibold text-brand-forest">
								<?php
								/* translators: %s: Instagram @handle. */
								printf( esc_html__( 'View %s on Instagram', 'juiced' ), $juiced_ig_handle ? '@' . esc_html( $juiced_ig_handle ) : esc_html__( 'our feed', 'juiced' ) );
								?>
							</a>
						</blockquote>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>

	</div>

	<?php if ( $juiced_reviews ) : ?>
		<!-- Google reviews strip, full-bleed along the bottom of the section.
		 The list is printed twice so the CSS loop is seamless; the copy is
		 hidden from screen readers. -->
		<div class="bg-brand-forest-deep pt-5 pb-8 md:pb-10 text-brand-cream">
			<div class="mx-auto flex max-w-7xl items-center justify-between gap-3 px-4 pb-4 text-[15px] md:text-base font-semibold text-brand-mint">
				<span class="flex items-center gap-2.5">
					<?php if ( $juiced_rv_summary ) : ?>
						<strong class="font-display text-3xl leading-none text-brand-cream"><?php echo esc_html( number_format_i18n( $juiced_rv_summary['rating'], 1 ) ); ?></strong>
						<?php echo juiced_stars( $juiced_rv_summary['rating'], 17 ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in helper. ?>
						<?php esc_html_e( 'on Google', 'juiced' ); ?>
					<?php else : ?>
						<?php esc_html_e( 'Reviews from Google', 'juiced' ); ?>
					<?php endif; ?>
				</span>
				<?php if ( $juiced_rv_summary ) : ?>
					<span>
						<?php
						/* translators: %s: number of Google reviews. */
						echo esc_html( sprintf( _n( '%s review', '%s reviews', $juiced_rv_summary['count'], 'juiced' ), number_format_i18n( $juiced_rv_summary['count'] ) ) );
						?>
					</span>
				<?php endif; ?>
			</div>

			<div class="juiced-reviews overflow-hidden">
				<div class="juiced-marquee juiced-marquee--reviews flex w-max">
					<?php for ( $copy = 0; $copy < 2; $copy++ ) : ?>
						<ul class="flex shrink-0 gap-4 pr-4" <?php echo $copy ? 'aria-hidden="true"' : 'aria-label="' . esc_attr__( 'Google reviews', 'juiced' ) . '"'; ?>>
							<?php
							foreach ( $juiced_reviews as $k => $review ) :
								$juiced_rv_tag  = empty( $review['link'] ) ? 'span' : 'a';
								$juiced_rv_attr = 'a' === $juiced_rv_tag ? ' href="' . esc_url( $review['link'] ) . '" target="_blank" rel="noopener noreferrer"' . ( $copy ? ' tabindex="-1"' : '' ) : '';
								$juiced_rv_tone = 1 === $k % 3 ? 'bg-brand-cream text-brand-ink' : 'bg-brand-forest text-brand-cream';
								?>
								<li>
									<<?php echo $juiced_rv_tag . $juiced_rv_attr; // phpcs:ignore WordPress.Security.EscapeOutput -- tag is fixed, attributes escaped above. ?> class="flex items-center gap-3.5 rounded-full py-3 pr-7 pl-3 <?php echo esc_attr( $juiced_rv_tone ); ?>">
										<?php if ( $review['photo'] ) : ?>
											<img src="<?php echo esc_url( $review['photo'] ); ?>" alt="" referrerpolicy="no-referrer" loading="lazy" class="h-12 w-12 shrink-0 rounded-full bg-brand-gold object-cover" />
										<?php else : ?>
											<span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-brand-gold font-display text-lg font-semibold text-brand-ink" aria-hidden="true"><?php echo esc_html( strtoupper( mb_substr( $review['author'], 0, 1 ) ) ); ?></span>
										<?php endif; ?>
										<span class="min-w-0">
											<span class="block max-w-[260px] truncate text-base leading-snug sm:max-w-[380px]"><?php echo esc_html( wp_trim_words( $review['text'], 30 ) ); ?></span>
											<span class="mt-1 flex items-center gap-2 text-sm opacity-80">
												<?php echo juiced_stars( $review['rating'], 14 ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in helper. ?>
												<?php echo esc_html( $review['author'] ); ?>
											</span>
										</span>
									</<?php echo $juiced_rv_tag; // phpcs:ignore WordPress.Security.EscapeOutput -- fixed tag name. ?>>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endfor; ?>
				</div>
			</div>
		</div>
	<?php endif; ?>
</section>
