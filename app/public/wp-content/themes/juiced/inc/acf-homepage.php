<?php
/**
 * Code-defined ACF field group for the homepage.
 *
 * Registered in PHP (not the ACF admin UI) so the fields are version-controlled
 * and work with ACF *free* — no Repeater/Flexible Content/Options Page required.
 * The group attaches to the static front page, so the client edits homepage copy
 * by editing the "Home" page. Fields are read via juiced_field() (inc/helpers.php).
 *
 * Sections are added here as they're built. Currently: global + hero (Section 2)
 * + events (Section 3) + menu showcase (Section 4) + community (Section 5)
 * + locations (Section 6) + testimonials (Section 7) + footer & newsletter
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
						'instructions' => 'Link for the "Order Ahead" buttons in the header and hero (your online ordering page).',
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
						'instructions' => 'First line, shown in white. e.g. "Blending Together".',
					),
					array(
						'key'          => 'field_juiced_hero_accent',
						'label'        => 'Headline — line 2 (accent)',
						'name'         => 'hero_headline_accent',
						'type'         => 'text',
						'instructions' => 'Second line, shown in orange. e.g. "Health & Community".',
					),
					array(
						'key'   => 'field_juiced_hero_subtext',
						'label' => 'Subtext',
						'name'  => 'hero_subtext',
						'type'  => 'textarea',
						'rows'  => 3,
					),
					array(
						'key'           => 'field_juiced_hero_image',
						'label'         => 'Hero image',
						'name'          => 'hero_image',
						'type'          => 'image',
						'return_format' => 'array',
						'preview_size'  => 'medium',
						'instructions'  => 'The product/lifestyle image shown on the right of the hero.',
					),
					array(
						'key'          => 'field_juiced_hero_benefit_1',
						'label'        => 'Benefit chip 1',
						'name'         => 'hero_benefit_1',
						'type'         => 'text',
						'instructions' => 'Small label under the buttons (leaf icon).',
					),
					array(
						'key'          => 'field_juiced_hero_benefit_2',
						'label'        => 'Benefit chip 2',
						'name'         => 'hero_benefit_2',
						'type'         => 'text',
						'instructions' => 'Heart icon.',
					),
					array(
						'key'          => 'field_juiced_hero_benefit_3',
						'label'        => 'Benefit chip 3',
						'name'         => 'hero_benefit_3',
						'type'         => 'text',
						'instructions' => 'Community icon.',
					),

					// --- Events (Section 3) ------------------------------------
					array(
						'key'   => 'field_juiced_events_tab',
						'label' => 'Events',
						'type'  => 'tab',
					),
					array(
						'key'          => 'field_juiced_events_heading',
						'label'        => 'Heading',
						'name'         => 'events_heading',
						'type'         => 'text',
						'instructions' => 'First part of the centered heading, in dark. e.g. "This Week".',
					),
					array(
						'key'          => 'field_juiced_events_heading_accent',
						'label'        => 'Heading — accent',
						'name'         => 'events_heading_accent',
						'type'         => 'text',
						'instructions' => 'Second part, shown in orange. e.g. "at Juiced".',
					),
					array(
						'key'          => 'field_juiced_events_link_url',
						'label'        => 'Calendar link URL',
						'name'         => 'events_link_url',
						'type'         => 'url',
						'instructions' => 'Where the "Full calendar" link points. Leave blank to hide it.',
					),

					// --- Menu showcase (Section 4) -----------------------------
					array(
						'key'   => 'field_juiced_menu_tab',
						'label' => 'Menu',
						'type'  => 'tab',
					),
					array(
						'key'          => 'field_juiced_menu_heading',
						'label'        => 'Heading',
						'name'         => 'menu_heading',
						'type'         => 'text',
						'instructions' => 'First part of the centered heading, in white. e.g. "Good for You.".',
					),
					array(
						'key'          => 'field_juiced_menu_heading_accent',
						'label'        => 'Heading — accent',
						'name'         => 'menu_heading_accent',
						'type'         => 'text',
						'instructions' => 'Second part, shown in orange with a green underline. e.g. "Made with Love.".',
					),
					array(
						'key'          => 'field_juiced_menu_link_url',
						'label'        => 'Full menu URL',
						'name'         => 'menu_link_url',
						'type'         => 'url',
						'instructions' => 'Where the "View Full Menu" button points (your full menu or ordering page). Leave blank to hide it.',
					),

					// --- Community (Section 5) ---------------------------------
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
						'instructions' => 'First line, in dark. e.g. "More Than a Juice Bar.".',
					),
					array(
						'key'          => 'field_juiced_community_heading_accent',
						'label'        => 'Heading — accent',
						'name'         => 'community_heading_accent',
						'type'         => 'text',
						'instructions' => 'Second line, shown in orange. e.g. "We\'re a Community.".',
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
						'instructions' => 'e.g. "Learn More About Us". Leave blank (or clear the URL) to hide the button.',
					),
					array(
						'key'          => 'field_juiced_community_button_url',
						'label'        => 'Button URL',
						'name'         => 'community_button_url',
						'type'         => 'url',
						'instructions' => 'Where the button points, e.g. your About page.',
					),
					array(
						'key'          => 'field_juiced_community_instagram_url',
						'label'        => 'Instagram profile URL',
						'name'         => 'community_instagram_url',
						'type'         => 'url',
						'instructions' => 'Your Instagram profile link, e.g. https://www.instagram.com/juicednc/. The live feed on the right embeds this account; the @handle is pulled from the URL automatically. Leave blank to hide the feed.',
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
						'message' => 'The location names, addresses and directions links shown in this section are the site-wide business info — edit them once under <strong>Appearance &rarr; Customize &rarr; Business Info</strong> and every page stays in sync.',
					),
					array(
						'key'          => 'field_juiced_locations_heading',
						'label'        => 'Heading',
						'name'         => 'locations_heading',
						'type'         => 'text',
						'instructions' => 'In dark. e.g. "Two Convenient Locations in".',
					),
					array(
						'key'          => 'field_juiced_locations_heading_accent',
						'label'        => 'Heading — accent',
						'name'         => 'locations_heading_accent',
						'type'         => 'text',
						'instructions' => 'Shown in orange, right after the heading. e.g. "Raleigh, NC".',
					),
					array(
						'key'           => 'field_juiced_locations_emblem',
						'label'         => 'Center emblem',
						'name'          => 'locations_emblem',
						'type'          => 'image',
						'return_format' => 'array',
						'preview_size'  => 'thumbnail',
						'instructions'  => 'Round badge/logo shown between the two locations. Leave blank to use the site logo.',
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
						'label'        => 'Newsletter heading',
						'name'         => 'newsletter_heading',
						'type'         => 'textarea',
						'rows'         => 2,
						'new_lines'    => '',
						'instructions' => 'The orange band above the footer. e.g. "Good Things. Good People. Good Vibes." Press Enter to break onto a second line. Leave blank to hide the whole newsletter band. Note the Events page sidebar has its own signup headed "Stay in the Loop" — both feed the same list, so give this band its own wording rather than repeating that one.',
					),
					array(
						'key'          => 'field_juiced_newsletter_body',
						'label'        => 'Newsletter subtext',
						'name'         => 'newsletter_body',
						'type'         => 'textarea',
						'rows'         => 2,
						'instructions' => 'The line under the newsletter heading.',
					),
					array(
						'key'          => 'field_juiced_newsletter_action_url',
						'label'        => 'Newsletter form action URL',
						'name'         => 'newsletter_action_url',
						'type'         => 'url',
						'instructions' => 'Paste the form/POST URL from your email provider (Mailchimp, Klaviyo, etc.). In Mailchimp: Audience → Signup forms → Embedded form → copy the URL in the form\'s "action". Leave blank and the Sign Up button will just reload the page.',
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
						'key'           => 'field_juiced_newsletter_bg_image',
						'label'         => 'Newsletter background image',
						'name'          => 'newsletter_bg_image',
						'type'          => 'image',
						'return_format' => 'array',
						'preview_size'  => 'medium',
						'instructions'  => 'Decorative fruit background for the orange band — shown on tablet/desktop only (mobile keeps the flat orange). Export at 1920×240px (or 3840×480 for retina) on the band\'s orange, with the middle ~1280px kept clean so the fruit sits in the outer edges, clear of the heading and signup form. Leave blank for the flat orange band.',
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
						'instructions' => 'Business name shown after "© <year>" in the footer bar. e.g. "Juiced! Juice Bar. All Rights Reserved.".',
					),
					array(
						'key'          => 'field_juiced_footer_privacy_url',
						'label'        => 'Privacy Policy URL',
						'name'         => 'footer_privacy_url',
						'type'         => 'url',
						'instructions' => 'Link for the "Privacy Policy" link in the footer bar. Leave blank to hide it.',
					),
					array(
						'key'          => 'field_juiced_footer_terms_url',
						'label'        => 'Terms of Service URL',
						'name'         => 'footer_terms_url',
						'type'         => 'url',
						'instructions' => 'Link for the "Terms of Service" link in the footer bar. Leave blank to hide it.',
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
