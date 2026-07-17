<?php
/**
 * Code-defined ACF field group for the Events page.
 *
 * Same approach as inc/acf-homepage.php: registered in PHP so the fields are
 * version-controlled and work with ACF *free* (no Repeater / Flexible Content /
 * Options Page). The group attaches to any page using the "Events Page"
 * template, so the client edits this copy by editing the Events page itself.
 * Fields are read via juiced_page_field() (inc/helpers.php).
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
				'key'      => 'group_juiced_events_page',
				'title'    => 'Events Page',
				'fields'   => array(

					// --- Hero (Section 1) --------------------------------------
					array(
						'key'   => 'field_juiced_ev_hero_tab',
						'label' => 'Hero',
						'type'  => 'tab',
					),
					array(
						'key'          => 'field_juiced_ev_hero_lead',
						'label'        => 'Headline — line 1',
						'name'         => 'events_hero_lead',
						'type'         => 'text',
						'instructions' => 'First line, shown in white. e.g. "Events that".',
					),
					array(
						'key'          => 'field_juiced_ev_hero_accent',
						'label'        => 'Headline — line 2 (accent)',
						'name'         => 'events_hero_accent',
						'type'         => 'text',
						'instructions' => 'Second line, shown in orange. e.g. "Fuel Community".',
					),
					array(
						'key'   => 'field_juiced_ev_hero_subtext',
						'label' => 'Subtext',
						'name'  => 'events_hero_subtext',
						'type'  => 'textarea',
						'rows'  => 3,
					),
					array(
						'key'           => 'field_juiced_ev_hero_image',
						'label'         => 'Hero background image',
						'name'          => 'events_hero_image',
						'type'          => 'image',
						'return_format' => 'array',
						'preview_size'  => 'medium',
						'instructions'  => 'Fills the right side of the hero and fades into the dark background. A wide, landscape photo of a busy event works best — faces to the right of centre, since the headline covers the left.',
					),
					array(
						'key'          => 'field_juiced_ev_hero_chip_1',
						'label'        => 'Chip 1 label',
						'name'         => 'events_hero_chip_1',
						'type'         => 'text',
						'instructions' => 'Leaf icon. e.g. "Connect".',
					),
					array(
						'key'          => 'field_juiced_ev_hero_chip_2',
						'label'        => 'Chip 2 label',
						'name'         => 'events_hero_chip_2',
						'type'         => 'text',
						'instructions' => 'Heart icon. e.g. "Grow".',
					),
					array(
						'key'          => 'field_juiced_ev_hero_chip_3',
						'label'        => 'Chip 3 label',
						'name'         => 'events_hero_chip_3',
						'type'         => 'text',
						'instructions' => 'People icon. e.g. "Belong".',
					),

					// --- Upcoming events listing (Section 3) -------------------
					array(
						'key'   => 'field_juiced_ev_list_tab',
						'label' => 'Upcoming Events',
						'type'  => 'tab',
					),
					array(
						'key'          => 'field_juiced_ev_list_heading',
						'label'        => 'Heading',
						'name'         => 'events_list_heading',
						'type'         => 'text',
						'instructions' => 'Sits above the event list. e.g. "Upcoming Events". The events themselves come from Events in the sidebar — there is nothing to edit here.',
					),
					array(
						'key'          => 'field_juiced_ev_calendar_heading',
						'label'        => 'Calendar heading',
						'name'         => 'events_calendar_heading',
						'type'         => 'text',
						'instructions' => 'Sits above the month calendar in the sidebar. e.g. "Event Calendar". The dots are filled in automatically from your events.',
					),

					// --- Sidebar promos (Section 3d) ---------------------------
					array(
						'key'   => 'field_juiced_ev_sidebar_tab',
						'label' => 'Sidebar',
						'type'  => 'tab',
					),
					array(
						'key'          => 'field_juiced_ev_host_heading',
						'label'        => 'Host box — heading',
						'name'         => 'events_host_heading',
						'type'         => 'text',
						'instructions' => 'e.g. "Host Your Event at Juiced!". Clearing this hides the whole box.',
					),
					array(
						'key'   => 'field_juiced_ev_host_body',
						'label' => 'Host box — body',
						'name'  => 'events_host_body',
						'type'  => 'textarea',
						'rows'  => 3,
					),
					array(
						'key'          => 'field_juiced_ev_host_cta_label',
						'label'        => 'Host box — button label',
						'name'         => 'events_host_cta_label',
						'type'         => 'text',
						'instructions' => 'e.g. "Inquire Today".',
					),
					array(
						'key'          => 'field_juiced_ev_host_cta_url',
						'label'        => 'Host box — button link',
						'name'         => 'events_host_cta_url',
						'type'         => 'url',
						'instructions' => 'Where the button goes — your contact or private events page. The button stays hidden until this is filled in, so it can never lead nowhere.',
					),
					array(
						'key'           => 'field_juiced_ev_sidebar_image',
						'label'         => 'Sidebar photo',
						'name'          => 'events_sidebar_image',
						'type'          => 'image',
						'return_format' => 'array',
						'preview_size'  => 'medium',
						'instructions'  => 'Sits between the host box and the signup form. Cropped to landscape. Leave empty to skip it.',
					),
					array(
						'key'          => 'field_juiced_ev_signup_heading',
						'label'        => 'Signup — heading',
						'name'         => 'events_signup_heading',
						'type'         => 'text',
						'instructions' => 'e.g. "Stay in the Loop". Clearing this hides the sidebar signup form. This is separate from the orange band at the bottom of every page so the two can say different things — but both sign people up to the same list, which is set under Home → Newsletter.',
					),
					array(
						'key'   => 'field_juiced_ev_signup_body',
						'label' => 'Signup — body',
						'name'  => 'events_signup_body',
						'type'  => 'textarea',
						'rows'  => 3,
					),
					array(
						'key'          => 'field_juiced_ev_follow_heading',
						'label'        => 'Follow — heading',
						'name'         => 'events_follow_heading',
						'type'         => 'text',
						'instructions' => 'e.g. "Follow Us". The icons and @handle come from the social links set under Home → Footer, so they always match the footer. The whole block hides while those are empty.',
					),
					array(
						'key'   => 'field_juiced_ev_follow_body',
						'label' => 'Follow — body',
						'name'  => 'events_follow_body',
						'type'  => 'textarea',
						'rows'  => 2,
					),

					// --- Community band (Section 4) ----------------------------
					array(
						'key'   => 'field_juiced_ev_comm_tab',
						'label' => 'Community',
						'type'  => 'tab',
					),
					array(
						'key'          => 'field_juiced_ev_comm_heading',
						'label'        => 'Heading — line 1',
						'name'         => 'events_community_heading',
						'type'         => 'text',
						'instructions' => 'First line, shown in white. e.g. "More Than a Juice Bar.". Clearing this hides the whole dark band above the footer.',
					),
					array(
						'key'          => 'field_juiced_ev_comm_accent',
						'label'        => 'Heading — line 2 (accent)',
						'name'         => 'events_community_accent',
						'type'         => 'text',
						'instructions' => 'Second line, shown in orange. e.g. "We\'re a Community.".',
					),
					array(
						'key'   => 'field_juiced_ev_comm_body',
						'label' => 'Body',
						'name'  => 'events_community_body',
						'type'  => 'textarea',
						'rows'  => 3,
					),
					array(
						'key'          => 'field_juiced_ev_comm_cta_label',
						'label'        => 'Button label',
						'name'         => 'events_community_cta_label',
						'type'         => 'text',
						'instructions' => 'e.g. "Learn More About Us". The button goes to the same place as the Learn More button on the homepage — set that link under Home → Community. Until it is filled in, the button stays hidden here too, so it can never lead nowhere.',
					),
					array(
						'key'           => 'field_juiced_ev_comm_image_1',
						'label'         => 'Photo 1',
						'name'          => 'events_community_image_1',
						'type'          => 'image',
						'return_format' => 'array',
						'preview_size'  => 'medium',
						'instructions'  => 'The band\'s background is a mosaic of up to five photos, sitting behind and to the right of the heading. Candid shots of people at events work best — they are darkened behind the text, so fine detail is lost. Add as many or as few as you like: the mosaic re-tiles to fill the space, and with none set the band falls back to plain dark.',
					),
					array(
						'key'           => 'field_juiced_ev_comm_image_2',
						'label'         => 'Photo 2',
						'name'          => 'events_community_image_2',
						'type'          => 'image',
						'return_format' => 'array',
						'preview_size'  => 'medium',
					),
					array(
						'key'           => 'field_juiced_ev_comm_image_3',
						'label'         => 'Photo 3',
						'name'          => 'events_community_image_3',
						'type'          => 'image',
						'return_format' => 'array',
						'preview_size'  => 'medium',
					),
					array(
						'key'           => 'field_juiced_ev_comm_image_4',
						'label'         => 'Photo 4',
						'name'          => 'events_community_image_4',
						'type'          => 'image',
						'return_format' => 'array',
						'preview_size'  => 'medium',
					),
					array(
						'key'           => 'field_juiced_ev_comm_image_5',
						'label'         => 'Photo 5',
						'name'          => 'events_community_image_5',
						'type'          => 'image',
						'return_format' => 'array',
						'preview_size'  => 'medium',
					),
				),
				'location' => array(
					array(
						array(
							'param'    => 'page_template',
							'operator' => '==',
							'value'    => 'page-templates/events.php',
						),
					),
				),
				'menu_order'            => 0,
				'position'              => 'normal',
				'label_placement'       => 'top',
				'hide_on_screen'        => array( 'the_content' ),
				'description'           => 'Copy for the /events landing page.',
			)
		);
	}
);
