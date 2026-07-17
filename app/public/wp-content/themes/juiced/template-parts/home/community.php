<?php
/**
 * Homepage Section 5 — Community ("More Than a Juice Bar. We're a Community.").
 *
 * Light two-column band: an editorial text column (an @handle eyebrow, two-tone
 * heading, body, and an outlined "Learn More" button) beside a live Instagram
 * feed. The feed is Instagram's official profile embed — a <blockquote> that IG's
 * embed.js (enqueued on the front page, see functions.php) swaps for a hosted
 * card of the account's recent posts. No API key or server calls involved; the
 * account is whatever profile URL the client sets.
 *
 * All copy and the Instagram URL are ACF-editable on the Home page
 * (inc/acf-homepage.php). The @handle shown in the eyebrow is derived from the
 * URL via juiced_instagram_handle(). The feed column is omitted if no URL is set.
 *
 * @package Juiced
 */

$juiced_c_heading = juiced_field( 'community_heading', 'More Than a Juice Bar.' );
$juiced_c_accent  = juiced_field( 'community_heading_accent', "We're a Community." );
$juiced_c_body    = juiced_field( 'community_body', 'From wellness workshops to creative nights and group workouts, Juiced! is a space where people come together, grow together and thrive together.' );
$juiced_c_label   = juiced_field( 'community_button_label', 'Learn More About Us' );
$juiced_c_url     = juiced_field( 'community_button_url', '' );

$juiced_ig_url    = juiced_field( 'community_instagram_url', 'https://www.instagram.com/juicednc/' );
$juiced_ig_handle = juiced_instagram_handle( $juiced_ig_url );
$juiced_has_feed  = ! empty( $juiced_ig_url );
?>
<section class="relative bg-brand-cream bg-grain overflow-hidden">
	<div class="mx-auto max-w-7xl px-4 py-16 md:py-24 grid gap-10 lg:gap-14 <?php echo $juiced_has_feed ? 'lg:grid-cols-5' : ''; ?> items-center">

		<!-- Text column -->
		<div class="<?php echo $juiced_has_feed ? 'lg:col-span-2' : 'max-w-2xl'; ?>">
			<?php if ( $juiced_ig_handle ) : ?>
				<a href="<?php echo esc_url( $juiced_ig_url ); ?>" target="_blank" rel="noopener noreferrer"
				   class="inline-flex items-center gap-2 mb-4 text-sm font-semibold tracking-wide text-brand-bark/70 hover:text-brand-green transition-colors">
					<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><rect x="3.5" y="3.5" width="17" height="17" rx="4.5"/><circle cx="12" cy="12" r="3.8"/><circle cx="17.2" cy="6.8" r="0.8" fill="currentColor"/></svg>
					@<?php echo esc_html( $juiced_ig_handle ); ?>
				</a>
			<?php endif; ?>

			<h2 class="font-display text-4xl md:text-5xl leading-[1.05]">
				<span class="block text-brand-bark"><?php echo esc_html( $juiced_c_heading ); ?></span>
				<span class="block text-brand-gold"><?php echo esc_html( $juiced_c_accent ); ?></span>
			</h2>

			<?php if ( $juiced_c_body ) : ?>
				<p class="mt-6 max-w-md text-lg text-brand-bark/70 leading-relaxed"><?php echo esc_html( $juiced_c_body ); ?></p>
			<?php endif; ?>

			<div class="mt-8 flex flex-wrap items-center gap-4">
				<?php if ( $juiced_c_url && $juiced_c_label ) : ?>
					<a href="<?php echo esc_url( $juiced_c_url ); ?>"
					   class="inline-flex items-center gap-2 rounded-full border border-brand-bark/25 bg-white px-6 py-3 text-sm font-semibold text-brand-bark hover:border-brand-green hover:text-brand-green transition-colors">
						<?php echo esc_html( $juiced_c_label ); ?>
						<span aria-hidden="true">&rarr;</span>
					</a>
				<?php endif; ?>

				<?php if ( $juiced_ig_url ) : ?>
					<a href="<?php echo esc_url( $juiced_ig_url ); ?>" target="_blank" rel="noopener noreferrer"
					   class="inline-flex items-center gap-2 rounded-full bg-brand-bark px-6 py-3 text-sm font-semibold text-brand-cream hover:bg-brand-green transition-colors">
						<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><rect x="3.5" y="3.5" width="17" height="17" rx="4.5"/><circle cx="12" cy="12" r="3.8"/><circle cx="17.2" cy="6.8" r="0.8" fill="currentColor"/></svg>
						<?php esc_html_e( 'Follow on Instagram', 'juiced' ); ?>
					</a>
				<?php endif; ?>
			</div>
		</div>

		<!-- Live Instagram feed (official profile embed) -->
		<?php if ( $juiced_has_feed ) : ?>
			<div class="lg:col-span-3 flex justify-center lg:justify-end">
				<div class="w-full max-w-[540px]">
					<blockquote class="instagram-media"
						data-instgrm-permalink="<?php echo esc_url( $juiced_ig_url ); ?>"
						data-instgrm-version="14"
						style="background:#FFF; border:0; border-radius:12px; box-shadow:0 0 1px 0 rgba(0,0,0,0.5),0 1px 10px 0 rgba(0,0,0,0.15); margin:0; max-width:100%; width:100%; padding:0;">
						<a href="<?php echo esc_url( $juiced_ig_url ); ?>" target="_blank" rel="noopener noreferrer" style="text-decoration:none;">
							<?php
							/* translators: %s: Instagram @handle. */
							printf( esc_html__( 'View %s on Instagram', 'juiced' ), $juiced_ig_handle ? '@' . esc_html( $juiced_ig_handle ) : esc_html__( 'our feed', 'juiced' ) );
							?>
						</a>
					</blockquote>
				</div>
			</div>
		<?php endif; ?>

	</div>
</section>
