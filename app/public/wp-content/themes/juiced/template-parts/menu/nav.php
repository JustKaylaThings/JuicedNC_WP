<?php
/**
 * Menu page — bar under the featured band that sticks below the site header
 * (.menu-section-bar in src/tailwind.css): links to the page's sections on
 * the left, the delivery buttons (juiced_delivery_apps(), each with a shop menu when
 * both locations are on that app) on the right. On a
 * phone the bar keeps one line and scrolls sideways.
 *
 * @package Juiced
 */

$juiced_nav_links = array(
	'#featured'  => __( 'Featured', 'juiced' ),
	'#smoothies' => __( 'Super smoothies', 'juiced' ),
	'#add-ons'   => __( 'Add ons', 'juiced' ),
	'#more'      => __( 'Juices & More', 'juiced' ),
);
$juiced_nav_apps  = juiced_delivery_apps();
?>
<div class="menu-section-bar border-y-2 border-brand-forest bg-brand-cream">
	<div class="mx-auto flex max-w-7xl items-center gap-x-6 gap-y-3 overflow-x-auto px-4 py-3 md:flex-wrap md:justify-between md:overflow-visible">
		<nav aria-label="<?php esc_attr_e( 'Menu sections', 'juiced' ); ?>" class="flex shrink-0 gap-x-6 md:gap-x-7">
			<?php foreach ( $juiced_nav_links as $href => $label ) : ?>
				<a href="<?php echo esc_attr( $href ); ?>" class="whitespace-nowrap font-display text-base font-semibold text-brand-forest transition-colors hover:text-brand-rust md:text-lg"><?php echo esc_html( $label ); ?></a>
			<?php endforeach; ?>
		</nav>
		<?php if ( $juiced_nav_apps ) : ?>
			<div class="flex shrink-0 items-center gap-1.5">
				<span class="mr-1 whitespace-nowrap text-xs font-bold uppercase tracking-[.14em] text-[#4a4036]"><?php esc_html_e( 'Delivery', 'juiced' ); ?></span>
				<?php foreach ( $juiced_nav_apps as $app ) : ?>
					<?php juiced_app_button( $app, 'px-3.5 py-2 text-[13px]' ); ?>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</div>
