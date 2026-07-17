<?php
/**
 * Code-defined ACF field group for the Locations page.
 *
 * Same approach as inc/acf-about-page.php: registered in PHP so the fields are
 * version-controlled and work with ACF *free*. The group attaches to any page
 * using the "Locations Page" template, so the client edits this copy by
 * editing the Locations page itself. Fields are read via juiced_page_field()
 * (inc/helpers.php).
 *
 * Sections are added here as they're built, one tab each.
 *
 * @package Juiced
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'acf/init',
	function () {
		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			return;
		}

		// The two location slots share the same field set (Find Your Juiced!
		// section) — built in a loop so the group below stays readable. Each
		// slot's name, address, phone, hours, parking and directions link are
		// the site-wide business info in Appearance → Customize → Business
		// Info, so only this page's dressing lives here.
		$location_fields = array();
		foreach ( range( 1, 2 ) as $i ) {
			$location_fields[] = array(
				'key'     => "field_juiced_loc_loc{$i}_note",
				'label'   => "Location {$i}",
				'type'    => 'message',
				'message' => "Location {$i}'s name, address, phone, hours, parking and directions link come from the site-wide business info under <strong>Appearance &rarr; Customize &rarr; Business Info</strong>. Only its photos and ordering link are set here.",
			);
			$location_fields[] = array(
				'key'           => "field_juiced_loc_loc{$i}_photo",
				'label'         => "Location {$i} — photo",
				'name'          => "locations_loc{$i}_photo",
				'type'          => 'image',
				'return_format' => 'array',
				'preview_size'  => 'thumbnail',
				'instructions'  => 'Interior shot beside the address and details.',
			);
			$location_fields[] = array(
				'key'           => "field_juiced_loc_loc{$i}_map",
				'label'         => "Location {$i} — map image",
				'name'          => "locations_loc{$i}_map",
				'type'          => 'image',
				'return_format' => 'array',
				'preview_size'  => 'thumbnail',
				'instructions'  => 'A wide map screenshot with the shop at the centre — shown below the details, about 2:1.',
			);
			$location_fields[] = array(
				'key'          => "field_juiced_loc_order_label{$i}",
				'label'        => "Location {$i} — order button label",
				'name'         => "locations_order_label{$i}",
				'type'         => 'text',
				'instructions' => 1 === $i ? 'e.g. "Order Rock Quarry".' : 'e.g. "Order Hill Street".',
			);
			$location_fields[] = array(
				'key'          => "field_juiced_loc_order_url{$i}",
				'label'        => "Location {$i} — ordering link",
				'name'         => "locations_order_url{$i}",
				'type'         => 'text',
				'instructions' => "Location {$i}'s DoorDash ordering link — the red DoorDash button in the tinted card, next to Get Directions. The button stays hidden until this is set.",
			);
		}

		acf_add_local_field_group(
			array(
				'key'      => 'group_juiced_locations_page',
				'title'    => 'Locations Page',
				'fields'   => array_merge( array(

					// --- Hero (Section 1) --------------------------------------
					array(
						'key'   => 'field_juiced_loc_hero_tab',
						'label' => 'Hero',
						'type'  => 'tab',
					),
					array(
						'key'          => 'field_juiced_loc_hero_lead',
						'label'        => 'Headline — white part',
						'name'         => 'locations_hero_lead',
						'type'         => 'text',
						'instructions' => 'The first line, shown in white. e.g. "Two Locations.".',
					),
					array(
						'key'          => 'field_juiced_loc_hero_accent',
						'label'        => 'Headline — accent line',
						'name'         => 'locations_hero_accent',
						'type'         => 'text',
						'instructions' => 'The last line, shown in orange. e.g. "One Community.".',
					),
					array(
						'key'   => 'field_juiced_loc_hero_subtext',
						'label' => 'Subtext',
						'name'  => 'locations_hero_subtext',
						'type'  => 'textarea',
						'rows'  => 3,
					),
					array(
						'key'          => 'field_juiced_loc_hero_badges',
						'label'        => 'Badges',
						'name'         => 'locations_hero_badges',
						'type'         => 'textarea',
						'rows'         => 4,
						'instructions' => 'Small icon badges under the subtext, one per line. The icon is picked from keywords in the text (fresh/ingredients, healthy, community). Clearing this hides the row.',
					),
					array(
						'key'           => 'field_juiced_loc_hero_image1',
						'label'         => 'Storefront photo 1',
						'name'          => 'locations_hero_image1',
						'type'          => 'image',
						'return_format' => 'array',
						'preview_size'  => 'medium',
						'instructions'  => 'Fills the right half of the hero, side by side with photo 2. A tall shot of the shop front works best. With neither photo the copy stands alone on the dark background.',
					),
					array(
						'key'          => 'field_juiced_loc_hero_label1',
						'label'        => 'Photo 1 — location pill',
						'name'         => 'locations_hero_label1',
						'type'         => 'text',
						'instructions' => 'The green pill over photo 1. e.g. "North Raleigh". Clearing this hides the pill.',
					),
					array(
						'key'           => 'field_juiced_loc_hero_image2',
						'label'         => 'Storefront photo 2',
						'name'          => 'locations_hero_image2',
						'type'          => 'image',
						'return_format' => 'array',
						'preview_size'  => 'medium',
						'instructions'  => 'The second storefront, to the right of photo 1.',
					),
					array(
						'key'          => 'field_juiced_loc_hero_label2',
						'label'        => 'Photo 2 — location pill',
						'name'         => 'locations_hero_label2',
						'type'         => 'text',
						'instructions' => 'The pink pill over photo 2. e.g. "Downtown Raleigh". Clearing this hides the pill.',
					),

					// --- Find Your Juiced! (Section 2) -------------------------
					array(
						'key'   => 'field_juiced_loc_find_tab',
						'label' => 'Find Your Juiced!',
						'type'  => 'tab',
					),
					array(
						'key'          => 'field_juiced_loc_find_lead',
						'label'        => 'Heading — dark part',
						'name'         => 'locations_find_lead',
						'type'         => 'text',
						'instructions' => 'e.g. "Find Your".',
					),
					array(
						'key'          => 'field_juiced_loc_find_accent',
						'label'        => 'Heading — accent part',
						'name'         => 'locations_find_accent',
						'type'         => 'text',
						'instructions' => 'Shown in orange after the dark part. e.g. "Juiced!".',
					),
					array(
						'key'          => 'field_juiced_loc_directions_label',
						'label'        => 'Directions button — label',
						'name'         => 'locations_directions_label',
						'type'         => 'text',
						'instructions' => 'Shared by both buttons. e.g. "Get Directions". Clearing this hides them.',
					),
				), $location_fields ),
				'location' => array(
					array(
						array(
							'param'    => 'page_template',
							'operator' => '==',
							'value'    => 'page-templates/locations.php',
						),
					),
				),
				'menu_order'      => 0,
				'position'        => 'normal',
				'label_placement' => 'top',
				'hide_on_screen'  => array( 'the_content' ),
				'description'     => 'Copy for the /locations page.',
			)
		);
	}
);
