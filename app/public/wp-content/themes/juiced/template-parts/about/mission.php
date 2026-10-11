<?php
/**
 * About page Section 2 — Our Mission.
 *
 * One centered statement on the cream ground: two-tone heading ("Our" green,
 * "Mission" orange), the mission statement, and a green "Our Story" button
 * that jumps to the Our Story timeline right below (its partial carries
 * id="our-story"). The We Believe In values used to share this band; they
 * now have their own section (template-parts/about/believe.php).
 *
 * Copy is ACF-editable on the About page (inc/acf-about-page.php); defaults
 * below match the approved mockup. Clearing the heading and statement hides
 * the band; clearing the button label hides the button.
 *
 * @package Juiced
 */

$heading = juiced_page_field( 'about_mission_heading', 'Our' );
$accent  = juiced_page_field( 'about_mission_accent', 'Mission' );
$body    = juiced_page_field( 'about_mission_body', 'To blend health and community by serving delicious, nutrient-packed offerings and creating spaces where people can connect, grow, and thrive together.' );
$label   = juiced_page_field( 'about_mission_button_label', 'Our Story' );
$url     = juiced_page_field( 'about_mission_button_url', '#our-story' );

if ( ! $heading && ! $accent && ! $body ) {
	return;
}
?>
<section class="bg-brand-cream text-brand-ink">
	<div class="mx-auto max-w-3xl px-4 py-16 text-center md:py-26">

		<h2 class="font-display font-bold leading-none text-brand-forest text-[clamp(2.5rem,6vw,3.75rem)]">
			<?php echo esc_html( $heading ); ?>
			<span class="text-brand-gold"><?php echo esc_html( $accent ); ?></span>
		</h2>

		<?php if ( $body ) : ?>
			<p class="mt-5 text-lg leading-relaxed text-brand-bark/70 md:text-xl">
				<?php echo esc_html( $body ); ?>
			</p>
		<?php endif; ?>

		<?php if ( $label && $url ) : ?>
			<a href="<?php echo esc_url( $url ); ?>"
			   class="mt-7 inline-flex items-center gap-2 rounded-full bg-brand-forest px-6 py-3 text-sm font-bold text-brand-cream transition-colors hover:bg-brand-gold hover:text-brand-ink">
				<?php echo esc_html( $label ); ?>
				<span aria-hidden="true">&darr;</span>
			</a>
		<?php endif; ?>

	</div>
</section>
