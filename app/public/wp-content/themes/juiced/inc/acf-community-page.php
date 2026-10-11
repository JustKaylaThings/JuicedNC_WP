<?php
/**
 * Code-defined ACF field group for the Community page.
 *
 * Same approach as inc/acf-events-page.php: registered in PHP so the fields are
 * version-controlled and work with ACF *free*. The group attaches to any page
 * using the "Community Page" template. The spotlights themselves are their own
 * post type (Community in the admin menu); these fields are only the page's
 * surrounding copy. Fields are read via juiced_page_field() (inc/helpers.php).
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
				'key'      => 'group_juiced_community_page',
				'title'    => 'Community Page',
				'fields'   => array(

					// --- Hero (Section 1) --------------------------------------
					array(
						'key'   => 'field_juiced_cm_hero_tab',
						'label' => 'Hero',
						'type'  => 'tab',
					),
					array(
						'key'          => 'field_juiced_cm_hero_lead',
						'label'        => 'Headline — line 1',
						'name'         => 'community_hero_lead',
						'type'         => 'text',
						'instructions' => 'Shown in white. e.g. "Raleigh Is".',
					),
					array(
						'key'          => 'field_juiced_cm_hero_accent',
						'label'        => 'Headline — line 2 (accent)',
						'name'         => 'community_hero_accent',
						'type'         => 'text',
						'instructions' => 'Shown in orange. e.g. "Our Community.".',
					),
					array(
						'key'   => 'field_juiced_cm_hero_subtext',
						'label' => 'Subtext',
						'name'  => 'community_hero_subtext',
						'type'  => 'textarea',
						'rows'  => 3,
					),
					array(
						'key'           => 'field_juiced_cm_hero_image',
						'label'         => 'Background photo',
						'name'          => 'community_hero_image',
						'type'          => 'image',
						'return_format' => 'array',
						'preview_size'  => 'medium',
						'instructions'  => 'Optional. Fades into the green from the left, so keep the subject to the right. With none, the band is plain green.',
					),

					// --- Spotlights (Sections 2 & 3) ---------------------------
					array(
						'key'   => 'field_juiced_cm_spot_tab',
						'label' => 'Spotlights',
						'type'  => 'tab',
					),
					array(
						'key'     => 'field_juiced_cm_spot_msg',
						'label'   => 'Where spotlights come from',
						'type'    => 'message',
						'message' => 'Each spotlight is its own post under <strong>Community</strong> in the left-hand menu. The newest month\'s spotlights (up to two) are featured at the top of this page; every earlier month drops into the archive below automatically.',
					),
					array(
						'key'          => 'field_juiced_cm_featured_heading',
						'label'        => 'Featured heading',
						'name'         => 'community_featured_heading',
						'type'         => 'text',
						'instructions' => 'e.g. "Community Spotlight". The month is added above it automatically.',
					),
					array(
						'key'          => 'field_juiced_cm_archive_heading',
						'label'        => 'Archive heading',
						'name'         => 'community_archive_heading',
						'type'         => 'text',
						'instructions' => 'e.g. "Past Spotlights".',
					),

					// --- Nominate (Section 4) ----------------------------------
					array(
						'key'   => 'field_juiced_cm_nom_tab',
						'label' => 'Nominate',
						'type'  => 'tab',
					),
					array(
						'key'          => 'field_juiced_cm_nom_heading',
						'label'        => 'Heading',
						'name'         => 'community_nominate_heading',
						'type'         => 'text',
						'instructions' => 'e.g. "Know Someone We Should Spotlight?". Clearing this hides the band.',
					),
					array(
						'key'   => 'field_juiced_cm_nom_body',
						'label' => 'Body',
						'name'  => 'community_nominate_body',
						'type'  => 'textarea',
						'rows'  => 3,
					),
					array(
						'key'   => 'field_juiced_cm_nom_label',
						'label' => 'Button label',
						'name'  => 'community_nominate_label',
						'type'  => 'text',
					),
					array(
						'key'          => 'field_juiced_cm_nom_url',
						'label'        => 'Button link',
						'name'         => 'community_nominate_url',
						'type'         => 'text',
						'instructions' => 'Leave empty to use the Contact page. Or a form link, or an email as mailto:hello@juicednc.com.',
					),
				),
				'location' => array(
					array(
						array(
							'param'    => 'page_template',
							'operator' => '==',
							'value'    => 'page-templates/community.php',
						),
					),
				),
				'menu_order'            => 0,
				'position'              => 'normal',
				'label_placement'       => 'top',
				'hide_on_screen'        => array( 'the_content' ),
				'description'           => 'Copy for the /community page.',
			)
		);
	}
);
