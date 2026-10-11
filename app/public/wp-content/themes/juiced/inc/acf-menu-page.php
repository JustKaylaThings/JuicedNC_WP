<?php
/**
 * Code-defined ACF fields for the Menu page (page-templates/menu.php).
 *
 * Two groups:
 *  - "Menu Page", on the page using the Menu Page template: hero copy, the
 *    featured heading, the smoothie sizes and promise line, the Create Your
 *    Own card and the three add-on price tiers. Defaults match the in-store menu (Oct 2026).
 *  - "Menu Item Extras", on every Menu Item: the benefit dots and a short
 *    badge ("Fan favorite"). These sit beside the plugin's Menu Item Details
 *    group (ingredients, price, featured) in plugins/juiced-cpt.
 *
 * @package Juiced
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Smoothie benefits, in the in-store menu's legend order: key => [label, dot colour].
 *
 * @return array
 */
function juiced_menu_benefits() {
	return array(
		'vegan'    => array( __( 'Vegan', 'juiced' ), '#5fb84a' ),
		'energy'   => array( __( 'Energy boost', 'juiced' ), '#4a8fe0' ),
		'immunity' => array( __( 'Immunity boost', 'juiced' ), '#a97be0' ),
		'meal'     => array( __( 'Meal replacement', 'juiced' ), '#f2811f' ),
		'workout'  => array( __( 'Post workout', 'juiced' ), '#eebd18' ),
		'nuts'     => array( __( 'Contains nuts', 'juiced' ), '#ffffff' ),
	);
}

/**
 * Default add-on tiers from the in-store menu: price => items.
 *
 * @return array
 */
function juiced_menu_default_addons() {
	return array(
		array(
			'price' => '$0.75',
			'items' => 'Acai, Agave, Almond Butter, Almonds, Apple, Avocado, Banana, Bee Pollen, Beets, Blueberry, Cabbage, Cacao Powder, Camu Camu, Carrot, Cayenne Pepper, Celery, Chia Seeds, Cinnamon, Coconut Palm Sugar, Collard Greens, Cranberry, Cucumber, Dates, Flaxseeds, Ginger, Goji Berry, Granola, Hemp Seed, Honey, Kale, Lemon, Lime, Maca Root, Mango, Mint Leaf, Monkfruit, Moringa, Nutmeg, Oats, Orange, Papaya, Passion Fruit, Peach, Peanut Butter, Pear, Peppermint Extract, Pineapple, Spinach, Spirulina, Strawberry, Sweet Potato, Turmeric, Walnuts, Wheat Grass',
		),
		array(
			'price' => '$1.00',
			'items' => 'Barley Grass, Plant Protein, Whey Protein, Chlorophyll, Matcha',
		),
		array(
			'price' => '$1.50',
			'items' => 'Black Seed Oil, CBD Oil, Collagen, Elderberry, Sea Moss, Marine Greens',
		),
	);
}

add_action(
	'acf/init',
	function () {
		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			return;
		}

		$benefit_choices = array();
		foreach ( juiced_menu_benefits() as $key => $benefit ) {
			$benefit_choices[ $key ] = $benefit[0];
		}

		acf_add_local_field_group(
			array(
				'key'      => 'group_juiced_menu_item_extras',
				'title'    => 'Menu Item Extras',
				'fields'   => array(
					array(
						'key'          => 'field_juiced_menu_item_benefits',
						'label'        => 'Benefits',
						'name'         => 'benefits',
						'type'         => 'checkbox',
						'instructions' => 'The coloured dots on the Menu page, as on the in-store menu.',
						'choices'      => $benefit_choices,
						'layout'       => 'horizontal',
						'return_format' => 'value',
					),
					array(
						'key'          => 'field_juiced_menu_item_badge',
						'label'        => 'Badge',
						'name'         => 'menu_badge',
						'type'         => 'text',
						'instructions' => 'Optional short label on the Menu page card, e.g. "Fan favorite".',
					),
				),
				'location' => array(
					array(
						array(
							'param'    => 'post_type',
							'operator' => '==',
							'value'    => 'menu_item',
						),
					),
				),
				'menu_order' => 1,
			)
		);

		$addon_fields = array();
		foreach ( juiced_menu_default_addons() as $i => $tier ) {
			$n              = $i + 1;
			$addon_fields[] = array(
				'key'         => "field_juiced_menu_addon_price_{$n}",
				'label'       => "Add-ons tier {$n} — price",
				'name'        => "menu_addon_price_{$n}",
				'type'        => 'text',
				'placeholder' => $tier['price'],
				'wrapper'     => array( 'width' => '25' ),
			);
			$addon_fields[] = array(
				'key'          => "field_juiced_menu_addon_items_{$n}",
				'label'        => "Add-ons tier {$n} — items",
				'name'         => "menu_addon_items_{$n}",
				'type'         => 'textarea',
				'rows'         => 3,
				'instructions' => 'Separate with commas. Leave blank to use the in-store menu list.',
				'placeholder'  => $tier['items'],
				'wrapper'      => array( 'width' => '75' ),
			);
		}

		acf_add_local_field_group(
			array(
				'key'             => 'group_juiced_menu_page',
				'title'           => 'Menu Page',
				'fields'          => array_merge(
					array(
						// --- Hero ---------------------------------------------
						array(
							'key'   => 'field_juiced_menu_hero_tab',
							'label' => 'Hero',
							'type'  => 'tab',
						),
						array(
							'key'         => 'field_juiced_menu_hero_lead',
							'label'       => 'Headline — cream part',
							'name'        => 'menu_hero_lead',
							'type'        => 'text',
							'placeholder' => 'The Juiced!',
						),
						array(
							'key'         => 'field_juiced_menu_hero_accent',
							'label'       => 'Headline — accent line',
							'name'        => 'menu_hero_accent',
							'type'        => 'text',
							'placeholder' => 'Menu',
						),
						array(
							'key'         => 'field_juiced_menu_hero_subtext',
							'label'       => 'Subtext',
							'name'        => 'menu_hero_subtext',
							'type'        => 'textarea',
							'rows'        => 3,
							'placeholder' => 'Whole fruits and vegetables, blended to order. Your health journey starts here.',
						),
						array(
							'key'           => 'field_juiced_menu_hero_image',
							'label'         => 'Hero background image',
							'name'          => 'menu_hero_image',
							'type'          => 'image',
							'return_format' => 'array',
							'preview_size'  => 'medium',
						),

						// --- Featured ----------------------------------------
						array(
							'key'   => 'field_juiced_menu_featured_tab',
							'label' => 'Featured',
							'type'  => 'tab',
						),
						array(
							'key'     => 'field_juiced_menu_featured_note',
							'label'   => 'How this section works',
							'name'    => '',
							'type'    => 'message',
							'message' => 'The cream section under the hero. Under its heading, up to three Menu Items with "Featured" switched on sit in one row, in the order set by each item\'s "Order" box. Each card shows the item\'s photo, badge, ingredients and the DoorDash / Uber Eats / Grubhub buttons (store links from the Home page\'s Top five tab).',
						),
						array(
							'key'         => 'field_juiced_menu_featured_heading',
							'label'       => 'Heading',
							'name'        => 'menu_featured_heading',
							'type'        => 'text',
							'placeholder' => 'Featured',
							'wrapper'     => array( 'width' => '50' ),
						),
						array(
							'key'          => 'field_juiced_menu_featured_accent',
							'label'        => 'Heading — accent',
							'name'         => 'menu_featured_accent',
							'type'         => 'text',
							'instructions' => 'Shown in orange after the heading.',
							'placeholder'  => 'this week',
							'wrapper'      => array( 'width' => '50' ),
						),
						array(
							'key'   => 'field_juiced_menu_featured_intro',
							'label'       => 'Intro',
							'name'        => 'menu_featured_intro',
							'type'        => 'textarea',
							'rows'        => 2,
							'placeholder' => 'Our most-loved blends, made fresh to order. Get them delivered or stop in.',
						),

						// --- Smoothies ----------------------------------------
						array(
							'key'   => 'field_juiced_menu_smoothies_tab',
							'label' => 'Smoothies',
							'type'  => 'tab',
						),
						array(
							'key'     => 'field_juiced_menu_smoothies_note',
							'label'   => 'How this section works',
							'name'    => '',
							'type'    => 'message',
							'message' => 'The smoothies are the Menu Items in the Smoothies section, in the order set by each item\'s "Order" box (Page Attributes). Each shows its ingredients, benefit dots and badge. A smoothie\'s own Price replaces the standard sizes below (e.g. "Reg $9.25 · Lrg $13.64").',
						),
						array(
							'key'         => 'field_juiced_menu_promise',
							'label'       => 'Promise line',
							'name'        => 'menu_promise',
							'type'        => 'text',
							'placeholder' => 'No dairy · No refined sugar · Whole foods & vegetables ',
						),
						array(
							'key'         => 'field_juiced_menu_size_reg',
							'label'       => 'Regular size',
							'name'        => 'menu_size_reg',
							'type'        => 'text',
							'placeholder' => 'Reg 20oz — $8.25',
							'wrapper'     => array( 'width' => '50' ),
						),
						array(
							'key'         => 'field_juiced_menu_size_lrg',
							'label'       => 'Large size',
							'name'        => 'menu_size_lrg',
							'type'        => 'text',
							'placeholder' => 'Lrg 32oz — $12.64',
							'wrapper'     => array( 'width' => '50' ),
						),
						array(
							'key'         => 'field_juiced_menu_cyo_heading',
							'label'       => 'Create Your Own — heading',
							'name'        => 'menu_cyo_heading',
							'type'        => 'text',
							'placeholder' => 'Create your own',
						),
						array(
							'key'         => 'field_juiced_menu_cyo_body',
							'label'       => 'Create Your Own — text',
							'name'        => 'menu_cyo_body',
							'type'        => 'textarea',
							'rows'        => 2,
							'placeholder' => "Choice of any 3 fruits\nChoice of coconut milk, almond milk or water",
						),

						// --- Add-ons ------------------------------------------
						array(
							'key'   => 'field_juiced_menu_addons_tab',
							'label' => 'Add-ons',
							'type'  => 'tab',
						),
					),
					$addon_fields
				),
				'location'        => array(
					array(
						array(
							'param'    => 'page_template',
							'operator' => '==',
							'value'    => 'page-templates/menu.php',
						),
					),
				),
				'hide_on_screen'  => array( 'the_content' ),
				'menu_order'      => 0,
			)
		);
	}
);
