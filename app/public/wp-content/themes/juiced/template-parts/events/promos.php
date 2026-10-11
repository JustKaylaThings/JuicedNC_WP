<?php
/**
 * Events page — the promo blocks in the row under the planner.
 *
 * Two blocks, emitted as siblings so the planner's grid lays them out as two
 * equal columns: a photo and the social links. The "Host Your Event" box and
 * the newsletter signup used to sit here too; the host pitch moved to the
 * carousel band at the foot of the page (community.php), and the signup was
 * dropped because the orange band in the footer already does that job.
 *
 * The follow copy is page-local; the three social URLs are the site-wide
 * fields the footer reads (juiced_field(), stored on the Home page), so one
 * change updates both. The socials disappear wholesale when no URLs are set —
 * three icons linking nowhere are worse than no icons.
 *
 * @package Juiced
 */

// Photo.
$juiced_image = juiced_image_url( juiced_page_field( 'events_sidebar_image', '' ), 'juiced_card' );

// Socials — site-wide, same accounts the footer links to.
$juiced_follow_heading = juiced_page_field( 'events_follow_heading', 'Follow Us' );
$juiced_follow_body    = juiced_page_field( 'events_follow_body', "See what's happening at Juiced! Tag us in your photos!" );
$juiced_ig_url         = juiced_field( 'community_instagram_url', '' );
$juiced_fb_url         = juiced_field( 'footer_facebook_url', '' );
$juiced_tt_url         = juiced_field( 'footer_tiktok_url', '' );
$juiced_handle         = $juiced_ig_url ? juiced_instagram_handle( $juiced_ig_url ) : '';

$juiced_socials = array(
	'instagram' => $juiced_ig_url,
	'facebook'  => $juiced_fb_url,
	'tiktok'    => $juiced_tt_url,
);
$juiced_socials = array_filter( $juiced_socials );
?>

<!-- Photo -->
<?php if ( $juiced_image ) : ?>
	<img src="<?php echo esc_url( $juiced_image ); ?>" alt=""
		 class="aspect-[4/3] h-full w-full rounded-2xl object-cover" loading="lazy" />
<?php endif; ?>

<!-- Follow us -->
<?php if ( $juiced_socials ) : ?>
	<div class="flex flex-col justify-center rounded-2xl bg-white p-6 ring-1 ring-brand-forest/15 md:p-8">
		<h2 class="font-display text-2xl font-bold leading-tight text-brand-forest">
			<?php echo esc_html( $juiced_follow_heading ); ?>
		</h2>

		<?php if ( $juiced_follow_body ) : ?>
			<p class="mt-2 text-sm leading-relaxed text-brand-bark/70">
				<?php echo esc_html( $juiced_follow_body ); ?>
			</p>
		<?php endif; ?>

		<div class="mt-4 flex flex-wrap items-center gap-3">
			<?php foreach ( $juiced_socials as $juiced_network => $juiced_url ) : ?>
				<a href="<?php echo esc_url( $juiced_url ); ?>" target="_blank" rel="noopener"
				   class="grid h-10 w-10 place-items-center rounded-full bg-brand-forest text-brand-cream transition-colors hover:bg-brand-gold hover:text-brand-ink"
				   aria-label="<?php echo esc_attr( ucfirst( $juiced_network ) ); ?>">
					<?php echo juiced_social_icon( $juiced_network ); // phpcs:ignore WordPress.Security.EscapeOutput -- static inline SVG. ?>
				</a>
			<?php endforeach; ?>

			<?php if ( $juiced_handle ) : ?>
				<a href="<?php echo esc_url( $juiced_ig_url ); ?>" target="_blank" rel="noopener"
				   class="text-sm font-semibold text-brand-rust hover:underline">
					<?php echo esc_html( $juiced_handle ); ?>
				</a>
			<?php endif; ?>
		</div>
	</div>
<?php endif; ?>
