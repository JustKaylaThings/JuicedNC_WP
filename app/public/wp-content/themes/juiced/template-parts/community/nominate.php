<?php
/**
 * Community page Section 4 — "Know someone we should spotlight?"
 *
 * Green closing band asking visitors to nominate the next spotlight. The
 * button goes to the Contact page until the client points it elsewhere;
 * clearing the heading hides the band.
 *
 * @package Juiced
 */

$juiced_heading = juiced_page_field( 'community_nominate_heading', 'Know Someone We Should Spotlight?' );

if ( ! $juiced_heading ) {
	return;
}

$juiced_url     = juiced_page_field( 'community_nominate_url', home_url( '/contact/' ) );
$juiced_body    = juiced_page_field( 'community_nominate_body', 'A neighbor doing good, a small business with heart, a regular with a story — tell us who deserves the spotlight next.' );
$juiced_label   = juiced_page_field( 'community_nominate_label', 'Nominate Someone' );
?>
<section class="bg-brand-forest bg-garden text-brand-cream">
	<div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-8 px-4 py-14 md:py-16">
		<div class="max-w-2xl">
			<h2 class="font-display text-3xl font-bold leading-tight md:text-4xl">
				<?php echo esc_html( $juiced_heading ); ?>
			</h2>
			<?php if ( $juiced_body ) : ?>
				<p class="mt-3 text-lg leading-relaxed text-brand-mint">
					<?php echo esc_html( $juiced_body ); ?>
				</p>
			<?php endif; ?>
		</div>

		<a href="<?php echo esc_url( $juiced_url ); ?>"
		   class="inline-flex shrink-0 items-center gap-2 rounded-full bg-brand-gold px-6 py-4 text-brand-ink btn-sticker">
			<?php echo esc_html( $juiced_label ); ?>
			<span aria-hidden="true">&rarr;</span>
		</a>
	</div>
</section>
