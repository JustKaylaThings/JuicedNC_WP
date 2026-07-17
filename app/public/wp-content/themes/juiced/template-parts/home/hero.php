<?php
/**
 * Homepage Section 2 — Hero.
 *
 * Two-tone headline, subtext, Order Ahead button, three benefit chips, and a
 * product image. All copy is ACF-editable on the Home page (inc/acf-homepage.php);
 * defaults below match the approved mockup so it renders before anything is set.
 *
 * @package Juiced
 */

$lead     = juiced_field( 'hero_headline_lead', 'Blending Together' );
$accent   = juiced_field( 'hero_headline_accent', 'Health & Community' );
$subtext  = juiced_field( 'hero_subtext', "Juiced! is more than a juice bar — it's where wellness meets community. Fuel your body. Connect your spirit." );
$hero_img = juiced_image_url( juiced_field( 'hero_image', '' ), 'large' );
$order    = juiced_field( 'order_ahead_url', '#' );
$benefits = array(
	juiced_field( 'hero_benefit_1', 'Fresh Ingredients' ),
	juiced_field( 'hero_benefit_2', 'Healthy Options' ),
	juiced_field( 'hero_benefit_3', 'Community Focused' ),
);
?>
<section class="relative bg-brand-ink text-brand-cream overflow-hidden bg-grain">
	<div class="mx-auto max-w-7xl px-4 py-14 md:py-20 grid gap-10 md:grid-cols-2 items-center">

		<div>
			<h1 class="font-display text-5xl md:text-6xl leading-[1.03]">
				<span class="block text-white"><?php echo esc_html( $lead ); ?></span>
				<span class="block text-brand-gold"><?php echo esc_html( $accent ); ?></span>
			</h1>

			<p class="mt-6 max-w-md text-lg text-brand-cream/80"><?php echo esc_html( $subtext ); ?></p>

			<div class="mt-8">
				<a href="<?php echo esc_url( $order ); ?>"
				   class="inline-flex items-center gap-2 rounded-full bg-brand-gold px-7 py-3 font-semibold text-white shadow-sm hover:bg-brand-green-light transition">
					<?php esc_html_e( 'Order Ahead', 'juiced' ); ?>
					<span aria-hidden="true">&rarr;</span>
				</a>
			</div>

			<ul class="mt-10 flex flex-wrap gap-x-10 gap-y-4">
				<?php foreach ( $benefits as $i => $benefit ) : ?>
					<?php if ( ! $benefit ) { continue; } ?>
					<li class="flex flex-col items-start gap-2">
						<?php echo juiced_benefit_icon( $i ); // phpcs:ignore WordPress.Security.EscapeOutput -- static inline SVG. ?>
						<span class="eyebrow text-brand-cream/90"><?php echo esc_html( $benefit ); ?></span>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>

		<div class="relative">
			<?php if ( $hero_img ) : ?>
				<img src="<?php echo esc_url( $hero_img ); ?>" alt=""
					 class="w-full h-auto rounded-2xl object-cover" loading="eager" fetchpriority="high" />
			<?php else : ?>
				<div class="aspect-[4/3] w-full rounded-2xl bg-brand-green/15 border border-brand-green/20 grid place-items-center text-center px-6">
					<span class="text-sm text-brand-cream/60"><?php esc_html_e( 'Add a hero image on the Home page (Hero → Hero image).', 'juiced' ); ?></span>
				</div>
			<?php endif; ?>
		</div>

	</div>
</section>
