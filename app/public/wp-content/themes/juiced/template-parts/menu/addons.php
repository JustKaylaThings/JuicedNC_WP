<?php
/**
 * Menu Section 3 — smoothie add-ons, in the in-store menu's three price tiers.
 *
 * Heading over a rule, then one line per tier: the price in large rust type
 * beside its items as a paragraph. Prices and items are ACF fields on this
 * page (comma-separated); a blank tier uses the in-store list from
 * juiced_menu_default_addons().
 *
 * Rendered inside the paper wrapper in page-templates/menu.php.
 *
 * @package Juiced
 */

$juiced_tiers = array();
foreach ( juiced_menu_default_addons() as $i => $tier ) {
	$n     = $i + 1;
	$price = juiced_page_field( "menu_addon_price_{$n}", $tier['price'] );
	$items = array_filter( array_map( 'trim', explode( ',', (string) juiced_page_field( "menu_addon_items_{$n}", $tier['items'] ) ) ) );
	if ( $items ) {
		$juiced_tiers[] = array( $price, $items );
	}
}
if ( ! $juiced_tiers ) {
	return;
}
?>
<section class="flex flex-col gap-5" id="add-ons" aria-labelledby="menu-addons-h">
	<h2 id="menu-addons-h" class="m-0 border-b-[3px] border-brand-forest pb-3.5 font-display text-[clamp(2.5rem,5vw,3rem)] font-bold leading-none text-brand-forest">
		<?php esc_html_e( 'Add ons', 'juiced' ); ?>
	</h2>
	<div class="flex flex-col">
		<?php foreach ( $juiced_tiers as $tier ) : ?>
			<div class="flex flex-col gap-1 border-b border-brand-forest/20 py-3 sm:flex-row sm:gap-6">
				<span class="shrink-0 font-display text-[26px] font-bold text-brand-rust tabular-nums sm:w-20"><?php echo esc_html( $tier[0] ); ?></span>
				<p class="m-0 min-w-0 text-base leading-[1.75] text-[#2c3a2e]"><?php echo esc_html( implode( ' · ', $tier[1] ) ); ?></p>
			</div>
		<?php endforeach; ?>
	</div>
</section>
