<?php
/**
 * Code-defined ACF field group for the homepage.
 *
 * Registered in PHP (not the ACF admin UI) so the fields are version-controlled
 * and work with ACF *free* — no Repeater/Flexible Content/Options Page required.
 * The group attaches to the static front page, so the client edits homepage copy
 * by editing the "Home" page. Fields are read via juiced_field() (inc/helpers.php).
 *
 * Sections are added here as they're built. Currently: global + hero (Section 2, 2026 "Garden" redesign)
 * + coming up (Section 3) + community (Section 4) + top five (Section 5)
 * + two neighborhoods (Section 6) + testimonials (Section 7) + footer & newsletter
 * (Section 8, rendered site-wide in footer.php).
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
				'key'      => 'group_juiced_home',
				'title'    => 'Homepage',
				'fields'   => array(

					// --- Global ------------------------------------------------
					array(
						'key'          => 'field_juiced_order_ahead_url',
						'label'        => 'Order Ahead URL',
						'name'         => 'order_ahead_url',
						'type'         => 'url',
						'instructions' => 'Link for the "Order Ahead" buttons in the header and hero (your online ordering page). Also used by the "Order on DoorDash" button in the Top five section.',
					),

					// --- Hero (Section 2) --------------------------------------
					array(
						'key'   => 'field_juiced_hero_tab',
						'label' => 'Hero',
						'type'  => 'tab',
					),
					array(
						'key'          => 'field_juiced_hero_lead',
						'label'        => 'Headline — line 1',
						'name'         => 'hero_headline_lead',
						'type'         => 'text',
						'instructions' => 'First line, shown in cream. e.g. "Health &".',
					),
					array(
						'key'          => 'field_juiced_hero_accent',
						'label'        => 'Headline — line 2 (accent)',
						'name'         => 'hero_headline_accent',
						'type'         => 'text',
						'instructions' => 'Second line, shown in orange. e.g. "Community.".',
					),
					array(
						'key'          => 'field_juiced_hero_subtext',
						'label'        => 'Subtext',
						'name'         => 'hero_subtext',
						'type'         => 'textarea',
						'instructions' => 'Short intro beside the headline.',
						'rows'         => 3,
					),
					array(
						'key'          => 'field_juiced_hero_primary_label',
						'label'        => 'Main button — label',
						'name'         => 'hero_primary_label',
						'type'         => 'text',
						'instructions' => 'Orange button. Defaults to "Join us this week".',
					),
					array(
						'key'          => 'field_juiced_hero_primary_url',
						'label'        => 'Main button — link',
						'name'         => 'hero_primary_url',
						'type'         => 'url',
						'instructions' => 'Leave blank to link to the Events page.',
					),
					array(
						'key'          => 'field_juiced_hero_secondary_label',
						'label'        => 'Second button — label',
						'name'         => 'hero_secondary_label',
						'type'         => 'text',
						'instructions' => 'Outlined button. Defaults to "Order" and links to the Order Ahead URL.',
					),
					array(
						'key'          => 'field_juiced_hero_photo_1',
						'label'        => 'Photo 1',
						'name'         => 'hero_photo_1',
						'type'         => 'image',
						'instructions' => 'Photos sit in a row along the bottom of the hero, left to right. Portrait or square crops work best.',
						'return_format' => 'array',
						'preview_size'  => 'medium',
					),
					array(
						'key'          => 'field_juiced_hero_caption_1',
						'label'        => 'Photo 1 — caption',
						'name'         => 'hero_caption_1',
						'type'         => 'text',
						'instructions' => 'Small orange label on the photo. e.g. "Regulars".',
					),
					array(
						'key'          => 'field_juiced_hero_photo_2',
						'label'        => 'Photo 2',
						'name'         => 'hero_photo_2',
						'type'         => 'image',
						'return_format' => 'array',
						'preview_size'  => 'medium',
					),
					array(
						'key'          => 'field_juiced_hero_caption_2',
						'label'        => 'Photo 2 — caption',
						'name'         => 'hero_caption_2',
						'type'         => 'text',
						'instructions' => 'Small orange label on the photo. e.g. "Open Mic Thursdays".',
					),
					array(
						'key'          => 'field_juiced_hero_photo_3',
						'label'        => 'Photo 3',
						'name'         => 'hero_photo_3',
						'type'         => 'image',
						'return_format' => 'array',
						'preview_size'  => 'medium',
					),
					array(
						'key'          => 'field_juiced_hero_caption_3',
						'label'        => 'Photo 3 — caption',
						'name'         => 'hero_caption_3',
						'type'         => 'text',
						'instructions' => 'Small orange label on the photo. e.g. "Game Night".',
					),
					array(
						'key'          => 'field_juiced_hero_photo_4',
						'label'        => 'Photo 4',
						'name'         => 'hero_photo_4',
						'type'         => 'image',
						'return_format' => 'array',
						'preview_size'  => 'medium',
					),
					array(
						'key'          => 'field_juiced_hero_caption_4',
						'label'        => 'Photo 4 — caption',
						'name'         => 'hero_caption_4',
						'type'         => 'text',
						'instructions' => 'Small orange label on the photo. e.g. "Meet the owners".',
					),
					array(
						'key'          => 'field_juiced_hero_ticker',
						'label'        => 'Ticker',
						'name'         => 'hero_ticker',
						'type'         => 'textarea',
						'instructions' => 'The scrolling orange strip under the hero. One item per line.',
						'rows'         => 7,
					),

					// --- Events (Section 3) ------------------------------------
					array(
						'key'   => 'field_juiced_events_tab',
						'label' => 'Events',
						'type'  => 'tab',
					),
					array(
						'key'          => 'field_juiced_events_eyebrow',
						'label'        => 'Eyebrow',
						'name'         => 'events_eyebrow',
						'type'         => 'text',
						'instructions' => 'Small green label above the heading. Default "Coming up".',
					),
					array(
						'key'          => 'field_juiced_events_heading',
						'label'        => 'Heading',
						'name'         => 'events_heading',
						'type'         => 'text',
						'instructions' => 'First part of the heading, in green. Default "Pull up a chair.".',
					),
					array(
						'key'          => 'field_juiced_events_heading_accent',
						'label'        => 'Heading — accent',
						'name'         => 'events_heading_accent',
						'type'         => 'text',
						'instructions' => 'Second part, in orange-brown. Default "Every week.".',
					),
					array(
						'key'          => 'field_juiced_events_link_label',
						'label'        => 'Calendar button label',
						'name'         => 'events_link_label',
						'type'         => 'text',
						'instructions' => 'Default "Full events calendar".',
					),
					array(
						'key'          => 'field_juiced_events_link_url',
						'label'        => 'Calendar button URL',
						'name'         => 'events_link_url',
						'type'         => 'url',
						'instructions' => 'Leave blank to use the Events page.',
					),
					array(
						'key'           => 'field_juiced_events_featured',
						'label'         => 'Featured event (countdown banner)',
						'name'          => 'events_featured',
						'type'          => 'post_object',
						'post_type'     => array( 'event' ),
						'return_format' => 'id',
						'allow_null'    => 1,
						'instructions'  => 'The big event shown in the banner with a countdown. Leave blank to feature the next one-off (non-weekly) event automatically. The banner disappears once it has started.',
					),
					array(
						'key'     => 'field_juiced_events_cards_note',
						'label'   => 'Event cards',
						'name'    => '',
						'type'    => 'message',
						'message' => 'The cards below the banner show the next four upcoming events from Events. Edit an event to change its photo, time, place, price or sign-up link.',
					),

					// --- Community (Section 4) ---------------------------------
					array(
						'key'   => 'field_juiced_community_tab',
						'label' => 'Community',
						'type'  => 'tab',
					),
					array(
						'key'          => 'field_juiced_community_heading',
						'label'        => 'Heading',
						'name'         => 'community_heading',
						'type'         => 'text',
						'instructions' => 'First line, in cream. Default "More Than a Juice Bar.".',
					),
					array(
						'key'          => 'field_juiced_community_heading_accent',
						'label'        => 'Heading — accent',
						'name'         => 'community_heading_accent',
						'type'         => 'text',
						'instructions' => 'Second line, in orange. Default "We\'re a Community.".',
					),
					array(
						'key'   => 'field_juiced_community_body',
						'label' => 'Body text',
						'name'  => 'community_body',
						'type'  => 'textarea',
						'rows'  => 4,
					),
					array(
						'key'          => 'field_juiced_community_button_label',
						'label'        => 'Button label',
						'name'         => 'community_button_label',
						'type'         => 'text',
						'instructions' => 'Default "Learn More About Us".',
					),
					array(
						'key'          => 'field_juiced_community_button_url',
						'label'        => 'Button URL',
						'name'         => 'community_button_url',
						'type'         => 'url',
						'instructions' => 'Leave blank to use the About page.',
					),
					array(
						'key'          => 'field_juiced_community_instagram_url',
						'label'        => 'Instagram profile URL',
						'name'         => 'community_instagram_url',
						'type'         => 'url',
						'instructions' => 'Your Instagram profile link, e.g. https://www.instagram.com/juicednc/. The @handle is pulled from the URL automatically. Leave blank to hide the Instagram card and button.',
					),
					array(
						'key'           => 'field_juiced_community_photo_1',
						'label'         => 'Instagram card photo 1',
						'name'          => 'community_photo_1',
						'type'          => 'image',
						'return_format' => 'array',
						'preview_size'  => 'thumbnail',
						'wrapper'       => array( 'width' => '33' ),
						'instructions'  => 'Up to 6 square-ish photos for the Instagram card, left to right, top to bottom. Leave all empty to show the live Instagram embed instead.',
					),
					array(
						'key'           => 'field_juiced_community_photo_2',
						'label'         => 'Instagram card photo 2',
						'name'          => 'community_photo_2',
						'type'          => 'image',
						'return_format' => 'array',
						'preview_size'  => 'thumbnail',
						'wrapper'       => array( 'width' => '33' ),
					),
					array(
						'key'           => 'field_juiced_community_photo_3',
						'label'         => 'Instagram card photo 3',
						'name'          => 'community_photo_3',
						'type'          => 'image',
						'return_format' => 'array',
						'preview_size'  => 'thumbnail',
						'wrapper'       => array( 'width' => '33' ),
					),
					array(
						'key'           => 'field_juiced_community_photo_4',
						'label'         => 'Instagram card photo 4',
						'name'          => 'community_photo_4',
						'type'          => 'image',
						'return_format' => 'array',
						'preview_size'  => 'thumbnail',
						'wrapper'       => array( 'width' => '33' ),
					),
					array(
						'key'           => 'field_juiced_community_photo_5',
						'label'         => 'Instagram card photo 5',
						'name'          => 'community_photo_5',
						'type'          => 'image',
						'return_format' => 'array',
						'preview_size'  => 'thumbnail',
						'wrapper'       => array( 'width' => '33' ),
					),
					array(
						'key'           => 'field_juiced_community_photo_6',
						'label'         => 'Instagram card photo 6',
						'name'          => 'community_photo_6',
						'type'          => 'image',
						'return_format' => 'array',
						'preview_size'  => 'thumbnail',
						'wrapper'       => array( 'width' => '33' ),
					),

					// --- Top five (Section 5) ---------------------------------
					array(
						'key'   => 'field_juiced_menu_tab',
						'label' => 'Top five',
						'type'  => 'tab',
					),
					array(
						'key'     => 'field_juiced_menu_note',
						'label'   => 'How this section works',
						'name'    => '',
						'type'    => 'message',
						'message' => 'Pick five drinks from Menu Items, in ranking order. The photo, ingredients, size and price come from each menu item (the size is the part of its title in brackets, e.g. "(32oz)"). Leave all five blank to show Berry Boost, Ginger Glow, Sunset, Rise N\' Grind and Spark Plug. The "Order on DoorDash" button uses the Order Ahead URL at the top of these Home page fields (the button is hidden while that is blank).',
					),
					array(
						'key'          => 'field_juiced_menu_eyebrow',
						'label'        => 'Eyebrow',
						'name'         => 'menu_eyebrow',
						'type'         => 'text',
						'placeholder'  => 'Fuel up · What the neighborhood orders',
					),
					array(
						'key'          => 'field_juiced_menu_heading',
						'label'        => 'Heading',
						'name'         => 'menu_heading',
						'type'         => 'text',
						'placeholder'  => 'The',
						'wrapper'      => array( 'width' => '50' ),
					),
					array(
						'key'          => 'field_juiced_menu_heading_accent',
						'label'        => 'Heading — accent',
						'name'         => 'menu_heading_accent',
						'type'         => 'text',
						'instructions' => 'Shown in rust orange after the heading.',
						'placeholder'  => 'top five',
						'wrapper'      => array( 'width' => '50' ),
					),
					array(
						'key'          => 'field_juiced_menu_link_label',
						'label'        => 'Button label',
						'name'         => 'menu_link_label',
						'type'         => 'text',
						'placeholder'  => 'See the full menu',
						'wrapper'      => array( 'width' => '50' ),
					),
					array(
						'key'          => 'field_juiced_menu_link_url',
						'label'        => 'Button URL',
						'name'         => 'menu_link_url',
						'type'         => 'url',
						'instructions' => 'Leave blank to use the Menu page.',
						'wrapper'      => array( 'width' => '50' ),
					),
					array(
						'key'           => 'field_juiced_top_pick_1',
						'label'         => 'Drink #1',
						'name'          => 'top_pick_1',
						'type'          => 'post_object',
						'post_type'     => array( 'menu_item' ),
						'return_format' => 'id',
						'allow_null'    => 1,
						'ui'            => 1,
						'wrapper'       => array( 'width' => '60' ),
					),
					array(
						'key'         => 'field_juiced_top_tag_1',
						'label'       => 'Drink #1 tag',
						'name'        => 'top_tag_1',
						'type'        => 'text',
						'placeholder' => 'Most ordered',
						'wrapper'     => array( 'width' => '40' ),
					),
					array(
						'key'           => 'field_juiced_top_pick_2',
						'label'         => 'Drink #2',
						'name'          => 'top_pick_2',
						'type'          => 'post_object',
						'post_type'     => array( 'menu_item' ),
						'return_format' => 'id',
						'allow_null'    => 1,
						'ui'            => 1,
						'wrapper'       => array( 'width' => '60' ),
					),
					array(
						'key'         => 'field_juiced_top_tag_2',
						'label'       => 'Drink #2 tag',
						'name'        => 'top_tag_2',
						'type'        => 'text',
						'placeholder' => 'Work Remote favorite',
						'wrapper'     => array( 'width' => '40' ),
					),
					array(
						'key'           => 'field_juiced_top_pick_3',
						'label'         => 'Drink #3',
						'name'          => 'top_pick_3',
						'type'          => 'post_object',
						'post_type'     => array( 'menu_item' ),
						'return_format' => 'id',
						'allow_null'    => 1,
						'ui'            => 1,
						'wrapper'       => array( 'width' => '60' ),
					),
					array(
						'key'         => 'field_juiced_top_tag_3',
						'label'       => 'Drink #3 tag',
						'name'        => 'top_tag_3',
						'type'        => 'text',
						'placeholder' => 'Open Mic go-to',
						'wrapper'     => array( 'width' => '40' ),
					),
					array(
						'key'           => 'field_juiced_top_pick_4',
						'label'         => 'Drink #4',
						'name'          => 'top_pick_4',
						'type'          => 'post_object',
						'post_type'     => array( 'menu_item' ),
						'return_format' => 'id',
						'allow_null'    => 1,
						'ui'            => 1,
						'wrapper'       => array( 'width' => '60' ),
					),
					array(
						'key'         => 'field_juiced_top_tag_4',
						'label'       => 'Drink #4 tag',
						'name'        => 'top_tag_4',
						'type'        => 'text',
						'placeholder' => 'Meal replacement',
						'wrapper'     => array( 'width' => '40' ),
					),
					array(
						'key'           => 'field_juiced_top_pick_5',
						'label'         => 'Drink #5',
						'name'          => 'top_pick_5',
						'type'          => 'post_object',
						'post_type'     => array( 'menu_item' ),
						'return_format' => 'id',
						'allow_null'    => 1,
						'ui'            => 1,
						'wrapper'       => array( 'width' => '60' ),
					),
					array(
						'key'         => 'field_juiced_top_tag_5',
						'label'       => 'Drink #5 tag',
						'name'        => 'top_tag_5',
						'type'        => 'text',
						'placeholder' => 'Green juice',
						'wrapper'     => array( 'width' => '40' ),
					),

					// --- Locations (Section 6) ---------------------------------
					// Only this section's dressing lives here. The business
					// info itself (location names, addresses, phones, hours,
					// parking, directions links, Place IDs, business email) is
					// site-wide and managed in Appearance → Customize →
					// Business Info (inc/customizer.php).
					array(
						'key'   => 'field_juiced_locations_tab',
						'label' => 'Locations',
						'type'  => 'tab',
					),
					array(
						'key'     => 'field_juiced_locations_note',
						'label'   => 'Where the details live',
						'type'    => 'message',
						'message' => 'The location names, addresses, phones, hours, parking and directions links shown in this section are the site-wide business info — edit them once under <strong>Appearance &rarr; Customize &rarr; Business Info</strong> and every page stays in sync.',
					),
					array(
						'key'         => 'field_juiced_locations_label_1',
						'label'       => 'Location 1 — small label',
						'name'        => 'locations_label_1',
						'type'        => 'text',
						'placeholder' => 'Neighborhood 01',
						'wrapper'     => array( 'width' => '50' ),
					),
					array(
						'key'           => 'field_juiced_locations_photo_1',
						'label'         => 'Location 1 — storefront photo',
						'name'          => 'locations_photo_1',
						'type'          => 'image',
						'return_format' => 'array',
						'preview_size'  => 'medium',
						'instructions'  => 'A wide photo works best. Leave blank for a plain colour panel.',
						'wrapper'       => array( 'width' => '50' ),
					),
					array(
						'key'         => 'field_juiced_locations_label_2',
						'label'       => 'Location 2 — small label',
						'name'        => 'locations_label_2',
						'type'        => 'text',
						'placeholder' => 'Neighborhood 02 · Events home',
						'wrapper'     => array( 'width' => '50' ),
					),
					array(
						'key'           => 'field_juiced_locations_photo_2',
						'label'         => 'Location 2 — storefront photo',
						'name'          => 'locations_photo_2',
						'type'          => 'image',
						'return_format' => 'array',
						'preview_size'  => 'medium',
						'instructions'  => 'A wide photo works best. Leave blank for a plain colour panel.',
						'wrapper'       => array( 'width' => '50' ),
					),

					// --- Testimonials (Section 7) ------------------------------
					array(
						'key'   => 'field_juiced_testimonials_tab',
						'label' => 'Testimonials',
						'type'  => 'tab',
					),
					array(
						'key'          => 'field_juiced_testimonials_heading',
						'label'        => 'Heading — start',
						'name'         => 'testimonials_heading',
						'type'         => 'text',
						'instructions' => 'In white. e.g. "What Our".',
					),
					array(
						'key'          => 'field_juiced_testimonials_accent',
						'label'        => 'Heading — accent',
						'name'         => 'testimonials_accent',
						'type'         => 'text',
						'instructions' => 'The orange middle word. e.g. "Community".',
					),
					array(
						'key'          => 'field_juiced_testimonials_heading_end',
						'label'        => 'Heading — end',
						'name'         => 'testimonials_heading_end',
						'type'         => 'text',
						'instructions' => 'In white, after the accent. e.g. "is Saying".',
					),

					// --- Footer & Newsletter (Section 8) -----------------------
					array(
						'key'   => 'field_juiced_footer_tab',
						'label' => 'Footer & Newsletter',
						'type'  => 'tab',
					),
					array(
						'key'          => 'field_juiced_newsletter_heading',
						'label'        => 'Sign-up heading',
						'name'         => 'newsletter_heading',
						'type'         => 'text',
						'placeholder'  => 'Join the',
						'instructions' => 'The "Join the family" sign-up band above the footer, shown on every page. First line, in green.',
						'wrapper'      => array( 'width' => '50' ),
					),
					array(
						'key'          => 'field_juiced_newsletter_heading_accent',
						'label'        => 'Sign-up heading — accent',
						'name'         => 'newsletter_heading_accent',
						'type'         => 'text',
						'placeholder'  => 'Juiced! family.',
						'instructions' => 'Second line, in rust orange.',
						'wrapper'      => array( 'width' => '50' ),
					),
					array(
						'key'          => 'field_juiced_newsletter_body',
						'label'        => 'Sign-up text',
						'name'         => 'newsletter_body',
						'type'         => 'textarea',
						'rows'         => 2,
						'placeholder'  => 'Hear about events first, plus new menu drops and community shout-outs.',
					),
					array(
						'key'          => 'field_juiced_newsletter_action_url',
						'label'        => 'Newsletter form action URL',
						'name'         => 'newsletter_action_url',
						'type'         => 'url',
						'instructions' => 'Paste the form/POST URL from your email provider (Mailchimp, Klaviyo, etc.). In Mailchimp: Audience → Signup forms → Embedded form → copy the URL in the form\'s "action". Leave blank and the Join button will just reload the page.',
					),
					array(
						'key'           => 'field_juiced_newsletter_field_name',
						'label'         => 'Newsletter email field name',
						'name'          => 'newsletter_field_name',
						'type'          => 'text',
						'placeholder'   => 'EMAIL',
						'instructions'  => 'Advanced — the name of the email input your provider expects. Mailchimp uses "EMAIL" (the default); leave as-is unless your provider differs.',
					),
					array(
						'key'          => 'field_juiced_footer_tagline',
						'label'        => 'Footer tagline',
						'name'         => 'footer_tagline',
						'type'         => 'textarea',
						'rows'         => 2,
						'instructions' => 'Short line under the logo in the footer. e.g. "Blending together health and community.".',
					),
					array(
						'key'          => 'field_juiced_footer_facebook_url',
						'label'        => 'Facebook URL',
						'name'         => 'footer_facebook_url',
						'type'         => 'url',
						'instructions' => 'Your Facebook page link. Leave blank to hide the Facebook icon. (Instagram comes from the Community tab.)',
					),
					array(
						'key'          => 'field_juiced_footer_tiktok_url',
						'label'        => 'TikTok URL',
						'name'         => 'footer_tiktok_url',
						'type'         => 'url',
						'instructions' => 'Your TikTok profile link. Leave blank to hide the TikTok icon.',
					),
					array(
						'key'          => 'field_juiced_footer_copyright',
						'label'        => 'Copyright name',
						'name'         => 'footer_copyright',
						'type'         => 'text',
						'instructions' => 'Business name shown after "© <year>" in the footer bar. e.g. "Juiced! Juice Bar · Raleigh, NC".',
					),
					array(
						'key'          => 'field_juiced_footer_privacy_url',
						'label'        => 'Privacy Policy URL',
						'name'         => 'footer_privacy_url',
						'type'         => 'url',
						'instructions' => 'Adds a "Privacy" link after the copyright line. Leave blank to hide it.',
					),
					array(
						'key'          => 'field_juiced_footer_terms_url',
						'label'        => 'Terms of Service URL',
						'name'         => 'footer_terms_url',
						'type'         => 'url',
						'instructions' => 'Adds a "Terms" link after the copyright line. Leave blank to hide it.',
					),
				),
				'location' => array(
					array(
						array(
							'param'    => 'page_type',
							'operator' => '==',
							'value'    => 'front_page',
						),
					),
				),
				'menu_order'            => 0,
				'position'              => 'normal',
				'style'                 => 'default',
				'hide_on_screen'        => array( 'the_content' ),
				'active'                => true,
				'description'           => 'Editable content for the homepage sections.',
			)
		);
	}
);
