<?php
/**
 * Code-defined ACF field group for the About page.
 *
 * Same approach as inc/acf-events-page.php: registered in PHP so the fields are
 * version-controlled and work with ACF *free*. The group attaches to any page
 * using the "About Page" template, so the client edits this copy by editing the
 * About page itself. Fields are read via juiced_page_field() (inc/helpers.php).
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

		// Partners are six identical slots (name, logo, link) — built in a loop
		// so the group below stays readable.
		$partner_fields = array();
		foreach ( range( 1, 6 ) as $i ) {
			$partner_fields[] = array(
				'key'          => "field_juiced_ab_partner{$i}_name",
				'label'        => "Partner {$i} — name",
				'name'         => "about_partner{$i}_name",
				'type'         => 'text',
				'instructions' => 'Shown as text until a logo is uploaded, and used as the logo\'s alt text. Clearing both name and logo hides this partner.',
			);
			$partner_fields[] = array(
				'key'           => "field_juiced_ab_partner{$i}_logo",
				'label'         => "Partner {$i} — logo",
				'name'          => "about_partner{$i}_logo",
				'type'          => 'image',
				'return_format' => 'array',
				'preview_size'  => 'thumbnail',
				'instructions'  => 'A wide logo on a transparent or white background works best — shown about 50px tall.',
			);
			$partner_fields[] = array(
				'key'          => "field_juiced_ab_partner{$i}_url",
				'label'        => "Partner {$i} — link",
				'name'         => "about_partner{$i}_url",
				'type'         => 'text',
				'instructions' => 'Optional — the logo links here (new tab) when set.',
			);
		}

		acf_add_local_field_group(
			array(
				'key'      => 'group_juiced_about_page',
				'title'    => 'About Page',
				'fields'   => array_merge( array(

					// --- Hero (Section 1) --------------------------------------
					array(
						'key'   => 'field_juiced_ab_hero_tab',
						'label' => 'Hero',
						'type'  => 'tab',
					),
					array(
						'key'          => 'field_juiced_ab_hero_lead',
						'label'        => 'Headline — white part',
						'name'         => 'about_hero_lead',
						'type'         => 'text',
						'instructions' => 'Shown in white, wraps naturally. e.g. "More Than a Juice Bar.".',
					),
					array(
						'key'          => 'field_juiced_ab_hero_accent',
						'label'        => 'Headline — accent line',
						'name'         => 'about_hero_accent',
						'type'         => 'text',
						'instructions' => 'The last line, shown in orange. e.g. "We\'re a Community.".',
					),
					array(
						'key'   => 'field_juiced_ab_hero_subtext',
						'label' => 'Subtext',
						'name'  => 'about_hero_subtext',
						'type'  => 'textarea',
						'rows'  => 4,
					),
					array(
						'key'           => 'field_juiced_ab_hero_image',
						'label'         => 'Hero background image',
						'name'          => 'about_hero_image',
						'type'          => 'image',
						'return_format' => 'array',
						'preview_size'  => 'medium',
						'instructions'  => 'Fills the right side of the hero and fades into the dark background. A wide shot of the shop interior works best — subject to the right of centre, since the headline covers the left.',
					),

					// --- Our Mission (Section 2) -------------------------------
					array(
						'key'   => 'field_juiced_ab_mission_tab',
						'label' => 'Our Mission',
						'type'  => 'tab',
					),
					array(
						'key'          => 'field_juiced_ab_mission_heading',
						'label'        => 'Heading — dark part',
						'name'         => 'about_mission_heading',
						'type'         => 'text',
						'instructions' => 'e.g. "Our".',
					),
					array(
						'key'          => 'field_juiced_ab_mission_accent',
						'label'        => 'Heading — accent part',
						'name'         => 'about_mission_accent',
						'type'         => 'text',
						'instructions' => 'Shown in orange after the dark part. e.g. "Mission".',
					),
					array(
						'key'   => 'field_juiced_ab_mission_body',
						'label' => 'Mission statement',
						'name'  => 'about_mission_body',
						'type'  => 'textarea',
						'rows'  => 4,
					),
					array(
						'key'          => 'field_juiced_ab_mission_button_label',
						'label'        => 'Button label',
						'name'         => 'about_mission_button_label',
						'type'         => 'text',
						'instructions' => 'e.g. "Our Story". Clearing this hides the button.',
					),
					array(
						'key'          => 'field_juiced_ab_mission_button_url',
						'label'        => 'Button link',
						'name'         => 'about_mission_button_url',
						'type'         => 'text',
						'instructions' => 'Where the button goes. Defaults to "#our-story", which scrolls to the Our Story section further down this page.',
					),
					array(
						'key'          => 'field_juiced_ab_believe_heading',
						'label'        => 'We Believe In — heading',
						'name'         => 'about_believe_heading',
						'type'         => 'text',
						'instructions' => 'Sits above the value cards to the right of the mission. e.g. "We Believe In".',
					),
					array(
						'key'          => 'field_juiced_ab_believe_items',
						'label'        => 'We Believe In — values',
						'name'         => 'about_believe_items',
						'type'         => 'textarea',
						'rows'         => 5,
						'instructions' => 'One per line as "Title | Short description". The icon is picked from keywords in the title (health, community, positivity, growth). Clearing this hides the value cards and the mission takes the full band.',
					),

					// --- Our Story (Section 3) ---------------------------------
					array(
						'key'   => 'field_juiced_ab_story_tab',
						'label' => 'Our Story',
						'type'  => 'tab',
					),
					array(
						'key'          => 'field_juiced_ab_story_eyebrow',
						'label'        => 'Eyebrow',
						'name'         => 'about_story_eyebrow',
						'type'         => 'text',
						'instructions' => 'Small green label above the heading. e.g. "Our Story".',
					),
					array(
						'key'          => 'field_juiced_ab_story_lead',
						'label'        => 'Heading — line 1',
						'name'         => 'about_story_lead',
						'type'         => 'text',
						'instructions' => 'First line, shown dark. e.g. "Blended with Purpose.".',
					),
					array(
						'key'          => 'field_juiced_ab_story_accent',
						'label'        => 'Heading — line 2 (accent)',
						'name'         => 'about_story_accent',
						'type'         => 'text',
						'instructions' => 'Second line, shown in orange. e.g. "Built on Passion.".',
					),
					array(
						'key'          => 'field_juiced_ab_story_body',
						'label'        => 'Story',
						'name'         => 'about_story_body',
						'type'         => 'textarea',
						'rows'         => 7,
						'instructions' => 'Separate paragraphs with a blank line.',
					),
					array(
						'key'           => 'field_juiced_ab_story_image',
						'label'         => 'Photo',
						'name'          => 'about_story_image',
						'type'          => 'image',
						'return_format' => 'array',
						'preview_size'  => 'medium',
						'instructions'  => 'Sits to the left of the story, rounded. Without one the text takes the full width.',
					),
					array(
						'key'          => 'field_juiced_ab_story_button_label',
						'label'        => 'Button label',
						'name'         => 'about_story_button_label',
						'type'         => 'text',
						'instructions' => 'e.g. "Read Our Full Story".',
					),
					array(
						'key'          => 'field_juiced_ab_story_button_url',
						'label'        => 'Button link',
						'name'         => 'about_story_button_url',
						'type'         => 'text',
						'instructions' => 'Where the button goes. The button stays hidden until this is set.',
					),

					// --- Owners (Section 4) ------------------------------------
					array(
						'key'   => 'field_juiced_ab_owners_tab',
						'label' => 'Owners',
						'type'  => 'tab',
					),
					array(
						'key'          => 'field_juiced_ab_owners_lead',
						'label'        => 'Heading — before the accent',
						'name'         => 'about_owners_lead',
						'type'         => 'text',
						'instructions' => 'e.g. "Meet the".',
					),
					array(
						'key'          => 'field_juiced_ab_owners_accent',
						'label'        => 'Heading — accent word',
						'name'         => 'about_owners_accent',
						'type'         => 'text',
						'instructions' => 'Shown in orange. e.g. "Juiced!".',
					),
					array(
						'key'          => 'field_juiced_ab_owners_tail',
						'label'        => 'Heading — after the accent',
						'name'         => 'about_owners_tail',
						'type'         => 'text',
						'instructions' => 'e.g. "Owners".',
					),
					array(
						'key'          => 'field_juiced_ab_owner1_name',
						'label'        => 'Owner 1 — name',
						'name'         => 'about_owner1_name',
						'type'         => 'text',
						'instructions' => 'Clearing this hides owner 1; the section hides when both names are cleared.',
					),
					array(
						'key'   => 'field_juiced_ab_owner1_role',
						'label' => 'Owner 1 — role',
						'name'  => 'about_owner1_role',
						'type'  => 'text',
					),
					array(
						'key'           => 'field_juiced_ab_owner1_photo',
						'label'         => 'Owner 1 — photo',
						'name'          => 'about_owner1_photo',
						'type'          => 'image',
						'return_format' => 'array',
						'preview_size'  => 'thumbnail',
						'instructions'  => 'Shown as a circle — a square crop works best.',
					),
					array(
						'key'          => 'field_juiced_ab_owner2_name',
						'label'        => 'Owner 2 — name',
						'name'         => 'about_owner2_name',
						'type'         => 'text',
						'instructions' => 'Clearing this hides owner 2.',
					),
					array(
						'key'   => 'field_juiced_ab_owner2_role',
						'label' => 'Owner 2 — role',
						'name'  => 'about_owner2_role',
						'type'  => 'text',
					),
					array(
						'key'           => 'field_juiced_ab_owner2_photo',
						'label'         => 'Owner 2 — photo',
						'name'          => 'about_owner2_photo',
						'type'          => 'image',
						'return_format' => 'array',
						'preview_size'  => 'thumbnail',
						'instructions'  => 'Shown as a circle — a square crop works best.',
					),

					// --- Partners (Section 5) ----------------------------------
					array(
						'key'   => 'field_juiced_ab_partners_tab',
						'label' => 'Partners',
						'type'  => 'tab',
					),
					array(
						'key'          => 'field_juiced_ab_partners_heading',
						'label'        => 'Heading',
						'name'         => 'about_partners_heading',
						'type'         => 'text',
						'instructions' => 'e.g. "Proud to Partner With".',
					),
				), $partner_fields, array(

					// --- Community quote (Section 6) ---------------------------
					array(
						'key'   => 'field_juiced_ab_quote_tab',
						'label' => 'Community Quote',
						'type'  => 'tab',
					),
					array(
						'key'          => 'field_juiced_ab_quote_text',
						'label'        => 'Quote',
						'name'         => 'about_quote_text',
						'type'         => 'textarea',
						'rows'         => 3,
						'instructions' => 'The testimonial, without quote marks — the orange mark is added by the design.',
					),
					array(
						'key'          => 'field_juiced_ab_quote_attribution',
						'label'        => 'Attribution',
						'name'         => 'about_quote_attribution',
						'type'         => 'text',
						'instructions' => 'Who said it, shown in green with a dash. e.g. "Community Member".',
					),
					array(
						'key'           => 'field_juiced_ab_quote_image',
						'label'         => 'Photo',
						'name'          => 'about_quote_image',
						'type'          => 'image',
						'return_format' => 'array',
						'preview_size'  => 'medium',
						'instructions'  => 'Fills the right side of the band and fades into the dark background — subject to the right of centre, since the quote covers the left.',
					),
				) ),
				'location' => array(
					array(
						array(
							'param'    => 'page_template',
							'operator' => '==',
							'value'    => 'page-templates/about.php',
						),
					),
				),
				'menu_order'      => 0,
				'position'        => 'normal',
				'label_placement' => 'top',
				'hide_on_screen'  => array( 'the_content' ),
				'description'     => 'Copy for the /about page.',
			)
		);
	}
);
