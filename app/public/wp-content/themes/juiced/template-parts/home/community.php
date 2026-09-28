<?php
/**
 * Homepage Section 4 — More Than a Juice Bar ("Garden" redesign, Sep 2026).
 *
 * Deep-green band: an @handle link, a two-tone heading, body copy and two
 * buttons (About us, Follow on Instagram) beside a cream "Instagram card" —
 * a profile header (avatar, handle, Follow) over a 3×2 photo grid.
 *
 * The grid shows the photos picked on the Home page (community_photo_1..6),
 * each linking to the Instagram profile. With no photos picked, the card holds
 * Instagram's official profile embed instead (a blockquote that IG's embed.js,
 * enqueued on the front page in functions.php, swaps for a hosted card), so the
 * section still shows real posts before anyone curates it.
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
$juiced_c_body    = juiced_field( 'community_body', 'From wellness workshops to creative nights and group workouts, Juiced! is a space where people come together, grow together and thrive together.' );
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

$juiced_ig_icon = '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3.5" y="3.5" width="17" height="17" rx="4.5"/><circle cx="12" cy="12" r="3.8"/><circle cx="17.2" cy="6.8" r="0.8" fill="currentColor"/></svg>';
?>
<section class="bg-brand-forest text-brand-cream">
	<div class="mx-auto max-w-7xl px-4 py-16 md:py-20 grid gap-12 items-center <?php echo $juiced_ig_url ? 'lg:grid-cols-[minmax(0,1fr)_minmax(0,540px)] lg:gap-18' : ''; ?>">

		<!-- Text column -->
		<div class="flex flex-col gap-6">
			<?php if ( $juiced_ig_handle ) : ?>
				<a href="<?php echo esc_url( $juiced_ig_url ); ?>" target="_blank" rel="noopener noreferrer"
				   class="inline-flex items-center gap-2 self-start text-[15px] font-semibold text-brand-mint hover:text-brand-gold transition-colors">
					<?php echo $juiced_ig_icon; // phpcs:ignore WordPress.Security.EscapeOutput -- static SVG. ?>
					@<?php echo esc_html( $juiced_ig_handle ); ?>
				</a>
			<?php endif; ?>

			<h2 class="font-display font-bold leading-[1.02] text-[clamp(2.5rem,5.5vw,4rem)]">
				<span class="block"><?php echo esc_html( $juiced_c_heading ); ?></span>
				<span class="block text-brand-gold"><?php echo esc_html( $juiced_c_accent ); ?></span>
			</h2>

			<?php if ( $juiced_c_body ) : ?>
				<p class="max-w-[520px] text-lg md:text-[19px] leading-[1.55] text-brand-mint"><?php echo esc_html( $juiced_c_body ); ?></p>
			<?php endif; ?>

			<div class="mt-2 flex flex-wrap gap-3.5">
				<?php if ( $juiced_c_url && $juiced_c_label ) : ?>
					<a href="<?php echo esc_url( $juiced_c_url ); ?>"
					   class="inline-flex items-center gap-2 rounded-full bg-brand-gold px-6.5 py-4 font-bold text-brand-ink hover:bg-brand-cream transition-colors">
						<?php echo esc_html( $juiced_c_label ); ?> <span aria-hidden="true">&rarr;</span>
					</a>
				<?php endif; ?>
				<?php if ( $juiced_ig_url ) : ?>
					<a href="<?php echo esc_url( $juiced_ig_url ); ?>" target="_blank" rel="noopener noreferrer"
					   class="inline-flex items-center gap-2 rounded-full border-2 border-brand-cream px-6 py-3.5 font-semibold text-brand-cream hover:bg-brand-cream hover:text-brand-forest transition-colors">
						<?php echo $juiced_ig_icon; // phpcs:ignore WordPress.Security.EscapeOutput -- static SVG. ?>
						<?php esc_html_e( 'Follow on Instagram', 'juiced' ); ?>
					</a>
				<?php endif; ?>
			</div>
		</div>

		<?php if ( $juiced_ig_url ) : ?>
			<!-- Instagram card -->
			<div class="w-full max-w-[540px] justify-self-center lg:justify-self-end rounded-3xl bg-brand-cream p-3 text-brand-ink shadow-[0_18px_40px_rgba(0,0,0,.25)]">
				<?php if ( $juiced_c_photos ) : ?>
					<!-- Profile header. The live embed draws its own, so it's only
						 needed over the curated grid. -->
					<div class="flex items-center gap-3 px-2 pb-2 h-14">
						<span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-brand-gold font-display text-lg font-bold text-brand-forest" aria-hidden="true">J!</span>
						<span class="flex min-w-0 flex-col leading-tight">
							<strong class="text-[15px]"><?php echo esc_html( $juiced_ig_handle ? $juiced_ig_handle : 'Instagram' ); ?></strong>
							<span class="text-[13px] text-[#4a4036]">Juiced! NC · Raleigh</span>
						</span>
						<a href="<?php echo esc_url( $juiced_ig_url ); ?>" target="_blank" rel="noopener noreferrer"
						   class="ml-auto rounded-full bg-brand-forest px-4 py-2 text-[13px] font-bold text-brand-cream hover:bg-brand-rust transition-colors">
							<?php esc_html_e( 'Follow', 'juiced' ); ?>
						</a>
					</div>

					<a href="<?php echo esc_url( $juiced_ig_url ); ?>" target="_blank" rel="noopener noreferrer"
					   class="grid grid-cols-3 gap-1.5 overflow-hidden rounded-[14px]"
					   aria-label="<?php echo esc_attr( sprintf( /* translators: %s: Instagram handle. */ __( 'See more on Instagram @%s', 'juiced' ), $juiced_ig_handle ) ); ?>">
						<?php foreach ( $juiced_c_photos as $photo ) : ?>
							<img src="<?php echo esc_url( $photo['url'] ); ?>" alt="<?php echo esc_attr( $photo['alt'] ); ?>"
								 class="aspect-square md:aspect-auto md:h-[180px] w-full object-cover hover:opacity-90 transition-opacity" loading="lazy" />
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
</section>
