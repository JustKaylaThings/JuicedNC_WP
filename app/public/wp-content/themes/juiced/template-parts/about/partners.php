<?php
/**
 * About page Section — Proud to Partner With.
 *
 * A white strip after the owners: centered heading, then up to six partner
 * logos in a row. Like the owners, the partners live as plain fields on the
 * About page's ACF group (inc/acf-about-page.php) — six fixed slots, each a
 * name, a logo, and an optional link.
 *
 * A partner with no logo yet renders its name as text, so the band works
 * while logos are collected. A slot is skipped when both name and logo are
 * empty; the section renders nothing when all six are.
 *
 * @package Juiced
 */

$heading = juiced_page_field( 'about_partners_heading', 'Proud to Partner With' );

$partners = array();
foreach ( range( 1, 6 ) as $i ) {
	$name = juiced_page_field( "about_partner{$i}_name", '' );
	$logo = juiced_image_url( juiced_page_field( "about_partner{$i}_logo", '' ), 'medium' );
	if ( ! $name && ! $logo ) {
		continue;
	}
	$partners[] = array(
		'name' => $name,
		'logo' => $logo,
		'url'  => juiced_page_field( "about_partner{$i}_url", '' ),
	);
}

if ( ! $partners ) {
	return;
}
?>
<section class="bg-white">
	<div class="mx-auto max-w-7xl px-4 py-12 md:py-16">

		<?php if ( $heading ) : ?>
			<h2 class="text-center font-display text-2xl text-brand-bark md:text-3xl">
				<?php echo esc_html( $heading ); ?>
			</h2>
		<?php endif; ?>

		<div class="mt-8 flex flex-wrap items-center justify-center gap-x-12 gap-y-8 md:mt-10 md:gap-x-16">
			<?php foreach ( $partners as $partner ) : ?>

				<?php if ( $partner['url'] ) : ?>
					<a href="<?php echo esc_url( $partner['url'] ); ?>" target="_blank" rel="noopener"
					   class="transition-opacity hover:opacity-70">
				<?php endif; ?>

				<?php if ( $partner['logo'] ) : ?>
					<img src="<?php echo esc_url( $partner['logo'] ); ?>"
						 alt="<?php echo esc_attr( $partner['name'] ); ?>"
						 class="h-12 w-auto object-contain md:h-14" loading="lazy" />
				<?php else : ?>
					<span class="text-lg font-bold text-brand-bark/70"><?php echo esc_html( $partner['name'] ); ?></span>
				<?php endif; ?>

				<?php if ( $partner['url'] ) : ?>
					</a>
				<?php endif; ?>

			<?php endforeach; ?>
		</div>

	</div>
</section>
