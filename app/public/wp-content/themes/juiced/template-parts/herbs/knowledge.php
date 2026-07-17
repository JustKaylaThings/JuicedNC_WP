<?php
/**
 * Herb Library page Section 3 — "Knowledge is Wellness" band.
 *
 * A soft green statement band between the herb grid and the footer. Trimmed
 * from the mockup by request: just the heading and its subtext, centered — the
 * "Want More Herb Knowledge?" column and its View Events button were dropped.
 *
 * Copy is ACF-editable on the Herb Library page (inc/acf-herbs-page.php);
 * clearing the heading hides the whole band.
 *
 * @package Juiced
 */

$juiced_heading = juiced_page_field( 'herbs_knowledge_heading', 'Knowledge is Wellness' );
$juiced_body    = juiced_page_field( 'herbs_knowledge_body', 'The more we know, the better choices we can make for our bodies and our community.' );

if ( ! $juiced_heading ) {
	return;
}
?>
<section class="bg-brand-cream bg-grain">
	<div class="mx-auto max-w-7xl px-4 pb-16 md:pb-24">
		<div class="rounded-3xl bg-brand-green/10 px-6 py-12 text-center md:py-16">

			<h2 class="font-display text-3xl text-brand-bark md:text-4xl">
				<?php echo esc_html( $juiced_heading ); ?>
			</h2>

			<?php if ( $juiced_body ) : ?>
				<p class="mx-auto mt-4 max-w-2xl text-base leading-relaxed text-brand-bark/75 md:text-lg">
					<?php echo esc_html( $juiced_body ); ?>
				</p>
			<?php endif; ?>

		</div>
	</div>
</section>
