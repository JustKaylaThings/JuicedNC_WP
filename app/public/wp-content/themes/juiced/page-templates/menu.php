<?php
/**
 * Template Name: Menu Page
 *
 * The /menu page, in the "Garden Editorial" layout (menu design option E2,
 * without a hero photo): the shared page hero, as on the other pages; the
 * featured items (Menu Items with Featured on) in one row on cream; a sticky
 * bar with the section links and delivery buttons; then the Super Smoothies
 * as a menu list (sizes and benefit key above it, Create Your Own below), the
 * add-on price tiers, and the rest of the menu (juices, seamoss and
 * so on) by section. Copy comes from ACF fields on this page
 * (inc/acf-menu-page.php); the drinks are Menu Items (plugins/juiced-cpt).
 *
 * @package Juiced
 */

get_header();

get_template_part(
	'template-parts/page-hero',
	null,
	array(
		'lead'     => juiced_page_field( 'menu_hero_lead', 'The Juiced!' ),
		'accent'   => juiced_page_field( 'menu_hero_accent', 'Menu' ),
		'subtext'  => juiced_page_field( 'menu_hero_subtext', 'Whole fruits and vegetables, blended to order. Your health journey starts here.' ),
		'image'    => juiced_page_field( 'menu_hero_image', '' ),
		'one_line' => true,
	)
);

get_template_part( 'template-parts/menu/featured' );

get_template_part( 'template-parts/menu/nav' );
?>
<div class="bg-brand-paper text-brand-ink">
	<div class="mx-auto flex max-w-7xl flex-col gap-16 px-4 py-14 md:gap-20 md:py-20">
		<?php
		get_template_part( 'template-parts/menu/smoothies' );

		get_template_part( 'template-parts/menu/addons' );

		get_template_part( 'template-parts/menu/more' );
		?>
	</div>
</div>
<?php
get_footer();
