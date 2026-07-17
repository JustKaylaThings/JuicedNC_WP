<?php
/**
 * About page Section — Our Story ("Blended with Purpose. Built on Passion.").
 *
 * Two columns on the cream ground: a rounded photo on the left, and on the
 * right a green "Our Story" eyebrow, two-tone heading (lead dark, accent
 * orange), story paragraphs, and a green "Read Our Full Story" button. The
 * section carries id="our-story" — the mission band's "Our Story" button
 * scrolls here.
 *
 * Copy is ACF-editable on the About page (inc/acf-about-page.php); defaults
 * below match the approved mockup. The body textarea becomes one <p> per
 * blank-line-separated block. Empty fields drop their element: no image → the
 * text takes the full width; the button hides until a link is set (there is
 * no full-story page yet to point it at).
 *
 * @package Juiced
 */

$eyebrow = juiced_page_field( 'about_story_eyebrow', 'Our Story' );
$lead    = juiced_page_field( 'about_story_lead', 'Blended with Purpose.' );
$accent  = juiced_page_field( 'about_story_accent', 'Built on Passion.' );
$body    = juiced_page_field(
	'about_story_body',
	"Juiced! started with a simple idea: create a place where healthy living and community connection go hand in hand. What began as a single juice bar has grown into a movement — fueling better habits, supporting local dreams, and building a stronger Raleigh.\n\n"
	. "We're proud of how far we've come, and even more excited about where we're going — together."
);
$image   = juiced_image_url( juiced_page_field( 'about_story_image', '' ), 'large' );
$label   = juiced_page_field( 'about_story_button_label', 'Read Our Full Story' );
$url     = juiced_page_field( 'about_story_button_url', '' );

// One <p> per blank-line-separated block.
$paragraphs = array_filter( array_map( 'trim', preg_split( '/\r?\n\s*\r?\n/', (string) $body ) ) );

if ( ! $lead && ! $accent && ! $paragraphs ) {
	return;
}
?>
<section id="our-story" class="scroll-mt-24 bg-brand-cream bg-grain">
	<div class="mx-auto max-w-7xl px-4 py-14 md:py-20">
		<div class="grid items-center gap-10 <?php echo $image ? 'lg:grid-cols-2 lg:gap-14' : ''; ?>">

			<?php if ( $image ) : ?>
				<div>
					<img src="<?php echo esc_url( $image ); ?>" alt=""
						 class="aspect-[4/3] w-full rounded-2xl object-cover shadow-md" loading="lazy" />
				</div>
			<?php endif; ?>

			<div class="<?php echo $image ? '' : 'max-w-2xl'; ?>">

				<?php if ( $eyebrow ) : ?>
					<p class="text-sm font-bold uppercase tracking-wide text-brand-green">
						<?php echo esc_html( $eyebrow ); ?>
					</p>
				<?php endif; ?>

				<h2 class="mt-2 font-display text-3xl leading-[1.1] md:text-4xl">
					<span class="block text-brand-bark"><?php echo esc_html( $lead ); ?></span>
					<span class="block text-brand-gold"><?php echo esc_html( $accent ); ?></span>
				</h2>

				<?php foreach ( $paragraphs as $paragraph ) : ?>
					<p class="mt-5 text-base leading-relaxed text-brand-bark/70">
						<?php echo esc_html( $paragraph ); ?>
					</p>
				<?php endforeach; ?>

				<?php if ( $label && $url ) : ?>
					<a href="<?php echo esc_url( $url ); ?>"
					   class="mt-7 inline-flex items-center gap-2 rounded-full bg-brand-green px-6 py-3 text-sm font-semibold text-white transition-colors hover:bg-brand-gold">
						<?php echo esc_html( $label ); ?>
						<span aria-hidden="true">&rarr;</span>
					</a>
				<?php endif; ?>

			</div>

		</div>
	</div>
</section>
