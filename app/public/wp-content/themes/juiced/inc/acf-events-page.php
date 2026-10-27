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
						'instructions' => 'Shown in cream, before the orange part. e.g. "Events that".',
					),
					array(
						'key'          => 'field_juiced_ev_hero_accent',
						'label'        => 'Headline — accent',
						'name'         => 'events_hero_accent',
						'type'         => 'text',
						'instructions' => 'Shown in orange, after the first part. e.g. "Fuel Community".',
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
						'instructions'  => 'Fills the right side of the hero and fades into the green background. A wide, landscape photo of a busy event works best — faces to the right of centre, since the headline covers the left. The hero is a short band, so the photo is cropped top and bottom.',
					),
					// --- Featured event (above the calendar) ---------------------
					array(
						'key'   => 'field_juiced_ev_featured_tab',
						'label' => 'Featured event',
						'type'  => 'tab',
					),
					array(
						'key'           => 'field_juiced_ev_featured_event',
						'label'         => 'Featured event',
						'name'          => 'events_featured_event',
						'type'          => 'post_object',
						'post_type'     => array( 'event' ),
						'return_format' => 'id',
						'allow_null'    => 1,
						'instructions'  => 'The green card above the calendar, with a thumbnail of the event\'s flyer (its featured image), a countdown and a tickets button. Leave blank to use the event featured on the Home page, or else the next one-off (non-weekly) event. The card disappears once the event has passed.',
					),
					array(
						'key'          => 'field_juiced_ev_featured_eyebrow',
						'label'        => 'Label',
						'name'         => 'events_featured_eyebrow',
						'type'         => 'text',
						'placeholder'  => 'Featured event',
						'instructions' => 'The small orange tag at the top of the card. Default "Featured event".',
					),
					// --- Calendar (Section 2) -----------------------------------
					array(
						'key'   => 'field_juiced_ev_list_tab',
						'label' => 'Calendar',
						'type'  => 'tab',
					),
					array(
						'key'          => 'field_juiced_ev_list_heading',
						'label'        => 'List heading',
						'name'         => 'events_list_heading',
						'type'         => 'text',
						'instructions' => 'Sits above the short list beside the calendar. e.g. "Upcoming Events". The events themselves come from Events in the admin menu — there is nothing to edit here.',
					),
					array(
						'key'          => 'field_juiced_ev_calendar_heading',
						'label'        => 'Calendar label',
						'name'         => 'events_calendar_heading',
						'type'         => 'text',
						'instructions' => 'Small orange label above the month name. e.g. "Event Calendar". The dots are filled in automatically from your events: green for weekly or monthly ones, orange for one-off specials.',
					),

					// --- Photo / follow row (under the calendar) ---------------
					array(
						'key'   => 'field_juiced_ev_sidebar_tab',
						'label' => 'Photo & Follow',
						'type'  => 'tab',
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

					// --- Host Your Event carousel (Section 4) -----------------
					array(
						'key'   => 'field_juiced_ev_comm_tab',
						'label' => 'Past Events Carousel',
						'type'  => 'tab',
					),
					array(
						'key'          => 'field_juiced_ev_gallery_heading',
						'label'        => 'Heading',
						'name'         => 'events_gallery_heading',
						'type'         => 'text',
						'instructions' => 'e.g. "Host Your Event at Juiced!". Clearing this hides the whole green band under the calendar.',
					),
					array(
						'key'   => 'field_juiced_ev_gallery_body',
						'label' => 'Body',
						'name'  => 'events_gallery_body',
						'type'  => 'textarea',
						'rows'  => 3,
					),
					array(
						'key'          => 'field_juiced_ev_gallery_cta_label',
						'label'        => 'Button label',
						'name'         => 'events_gallery_cta_label',
						'type'         => 'text',
						'instructions' => 'e.g. "Inquire Today".',
					),
					array(
						'key'          => 'field_juiced_ev_host_cta_url',
						'label'        => 'Button link',
						'name'         => 'events_host_cta_url',
						'type'         => 'url',
						'instructions' => 'Leave blank to use the Host an Event page.',
					),
					array(
						'key'           => 'field_juiced_ev_comm_image_1',
						'label'         => 'Photo 1',
						'name'          => 'events_community_image_1',
						'type'          => 'image',
						'return_format' => 'array',
						'preview_size'  => 'medium',
						'instructions'  => 'Up to eight photos from past events, shown as a swipeable carousel beside the heading. They are cropped to landscape (4:3). The photo\'s Alt Text and Caption from the Media Library are used — the caption shows over the bottom of the photo, so it\'s a good spot for the event name. With no photos the band shows just the text.',
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
					array(
						'key'           => 'field_juiced_ev_comm_image_6',
						'label'         => 'Photo 6',
						'name'          => 'events_community_image_6',
						'type'          => 'image',
						'return_format' => 'array',
						'preview_size'  => 'medium',
					),
					array(
						'key'           => 'field_juiced_ev_comm_image_7',
						'label'         => 'Photo 7',
						'name'          => 'events_community_image_7',
						'type'          => 'image',
						'return_format' => 'array',
						'preview_size'  => 'medium',
					),
					array(
						'key'           => 'field_juiced_ev_comm_image_8',
						'label'         => 'Photo 8',
						'name'          => 'events_community_image_8',
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
