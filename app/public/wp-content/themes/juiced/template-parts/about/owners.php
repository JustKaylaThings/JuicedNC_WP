<?php
/**
 * About page Section — Meet the Owners.
 *
 * Centered heading with the brand name in orange ("Meet the *Juiced!*
 * Owners"), then the two owners side by side — circular photo, name, role.
 * There are exactly two, so they live as plain fields on the About page's own
 * ACF group (inc/acf-about-page.php) rather than a CPT — no admin list to
 * manage for a fixed pair.
 *
 * An owner with no photo gets a soft green circle with a person icon, so the
 * pair holds its shape while photos come in. An owner is skipped when their
 * name is cleared; the section renders nothing when both are.
 *
 * @package Juiced
 */

$lead   = juiced_page_field( 'about_owners_lead', 'Meet the' );
$accent = juiced_page_field( 'about_owners_accent', 'Juiced!' );
$tail   = juiced_page_field( 'about_owners_tail', 'Owners' );

$owners = array();
foreach ( array( 1, 2 ) as $i ) {
	$name = juiced_page_field( "about_owner{$i}_name", '' );
	if ( ! $name ) {
		continue;
	}
	$owners[] = array(
		'name'  => $name,
		'role'  => juiced_page_field( "about_owner{$i}_role", '' ),
		'photo' => juiced_image_url( juiced_page_field( "about_owner{$i}_photo", '' ), 'medium' ),
	);
}

if ( ! $owners ) {
	return;
}
?>
<section class="bg-brand-cream bg-grain">
	<div class="mx-auto max-w-7xl px-4 py-14 md:py-20">

		<?php if ( $lead || $accent || $tail ) : ?>
			<h2 class="text-center font-display text-3xl text-brand-bark md:text-4xl">
				<?php echo esc_html( $lead ); ?>
				<span class="text-brand-gold"><?php echo esc_html( $accent ); ?></span>
				<?php echo esc_html( $tail ); ?>
			</h2>
		<?php endif; ?>

		<div class="mt-10 flex flex-wrap items-start justify-center gap-12 md:gap-24">
			<?php foreach ( $owners as $owner ) : ?>
				<div class="text-center">

					<?php if ( $owner['photo'] ) : ?>
						<img src="<?php echo esc_url( $owner['photo'] ); ?>"
							 alt="<?php echo esc_attr( $owner['name'] ); ?>"
							 class="mx-auto aspect-square w-40 rounded-full object-cover md:w-48" loading="lazy" />
					<?php else : ?>
						<div class="mx-auto grid aspect-square w-40 place-items-center rounded-full bg-brand-green/10 text-brand-green md:w-48">
							<svg viewBox="0 0 24 24" width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" aria-hidden="true">
								<circle cx="12" cy="8.5" r="4" />
								<path d="M4.5 20c1-3.8 4-6 7.5-6s6.5 2.2 7.5 6" />
							</svg>
						</div>
					<?php endif; ?>

					<h3 class="mt-5 font-display text-xl font-bold text-brand-bark"><?php echo esc_html( $owner['name'] ); ?></h3>
					<?php if ( $owner['role'] ) : ?>
						<p class="mt-1 text-sm text-brand-bark/70"><?php echo esc_html( $owner['role'] ); ?></p>
					<?php endif; ?>

				</div>
			<?php endforeach; ?>
		</div>

	</div>
</section>
