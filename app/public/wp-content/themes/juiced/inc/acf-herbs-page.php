<?php
/**
 * Code-defined ACF field group for the Herb Library page.
 *
 * Same approach as inc/acf-events-page.php: registered in PHP so the fields are
 * version-controlled and work with ACF *free*. The group attaches to any page
 * using the "Herb Library Page" template, so the client edits this copy by
 * editing the Herb Library page itself. Fields are read via juiced_page_field()
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

		acf_add_local_field_group(
			array(
				'key'      => 'group_juiced_herbs_page',
				'title'    => 'Herb Library Page',
				'fields'   => array(

					// --- Hero (Section 1) --------------------------------------
					array(
						'key'   => 'field_juiced_hb_hero_tab',
						'label' => 'Hero',
						'type'  => 'tab',
					),
					array(
						'key'          => 'field_juiced_hb_hero_lead',
						'label'        => 'Headline — line 1',
						'name'         => 'herbs_hero_lead',
						'type'         => 'text',
						'instructions' => 'First line, shown in white. e.g. "The Power of".',
					),
					array(
						'key'          => 'field_juiced_hb_hero_accent',
						'label'        => 'Headline — line 2 (accent)',
						'name'         => 'herbs_hero_accent',
						'type'         => 'text',
						'instructions' => 'Second line, shown in orange. e.g. "Plants. Naturally.".',
					),
					array(
						'key'   => 'field_juiced_hb_hero_subtext',
						'label' => 'Subtext',
						'name'  => 'herbs_hero_subtext',
						'type'  => 'textarea',
						'rows'  => 3,
					),
					array(
						'key'           => 'field_juiced_hb_hero_image',
						'label'         => 'Hero background image',
						'name'          => 'herbs_hero_image',
						'type'          => 'image',
						'return_format' => 'array',
						'preview_size'  => 'medium',
						'instructions'  => 'Fills the right side of the hero and fades into the dark background. A wide, landscape photo of fresh herbs works best — subject to the right of centre, since the headline covers the left.',
					),
					array(
						'key'          => 'field_juiced_hb_hero_search_placeholder',
						'label'        => 'Search placeholder',
						'name'         => 'herbs_hero_search_placeholder',
						'type'         => 'text',
						'instructions' => 'Grey hint text inside the search box. e.g. "Search herbs by name or benefit…".',
					),
					array(
						'key'          => 'field_juiced_hb_hero_popular',
						'label'        => 'Popular searches',
						'name'         => 'herbs_hero_popular',
						'type'         => 'text',
						'instructions' => 'Comma-separated list of quick search terms shown as pills under the search box, e.g. "detox, immune, energy". Each one runs that search when clicked. Clearing this hides the row.',
					),

					// --- Explore Herbs (Section 2) -----------------------------
					array(
						'key'   => 'field_juiced_hb_explore_tab',
						'label' => 'Explore Herbs',
						'type'  => 'tab',
					),
					array(
						'key'          => 'field_juiced_hb_sidebar_heading',
						'label'        => 'Sidebar heading',
						'name'         => 'herbs_sidebar_heading',
						'type'         => 'text',
						'instructions' => 'Sits above the category list. e.g. "Explore Herbs". The categories themselves come from Herbs → Herb Categories in the sidebar, and the herbs from Herbs — there is nothing else to edit here.',
					),
					array(
						'key'          => 'field_juiced_hb_dyk_heading',
						'label'        => 'Did You Know? — heading',
						'name'         => 'herbs_didyouknow_heading',
						'type'         => 'text',
						'instructions' => 'The note box under the category list. Clearing this hides the whole box.',
					),
					array(
						'key'   => 'field_juiced_hb_dyk_body',
						'label' => 'Did You Know? — body',
						'name'  => 'herbs_didyouknow_body',
						'type'  => 'textarea',
						'rows'  => 4,
					),

					// --- Knowledge band (Section 3) ----------------------------
					array(
						'key'   => 'field_juiced_hb_knowledge_tab',
						'label' => 'Knowledge',
						'type'  => 'tab',
					),
					array(
						'key'          => 'field_juiced_hb_knowledge_heading',
						'label'        => 'Heading',
						'name'         => 'herbs_knowledge_heading',
						'type'         => 'text',
						'instructions' => 'The green band above the footer. e.g. "Knowledge is Wellness". Clearing this hides the whole band.',
					),
					array(
						'key'   => 'field_juiced_hb_knowledge_body',
						'label' => 'Subtext',
						'name'  => 'herbs_knowledge_body',
						'type'  => 'textarea',
						'rows'  => 3,
					),
				),
				'location' => array(
					array(
						array(
							'param'    => 'page_template',
							'operator' => '==',
							'value'    => 'page-templates/herbs.php',
						),
					),
				),
				'menu_order'      => 0,
				'position'        => 'normal',
				'label_placement' => 'top',
				'hide_on_screen'  => array( 'the_content' ),
				'description'     => 'Copy for the /herbs landing page.',
			)
		);
	}
);
