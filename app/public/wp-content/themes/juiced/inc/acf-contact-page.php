<?php
/**
 * Code-defined ACF field group for the Contact page.
 *
 * Same approach as inc/acf-about-page.php: registered in PHP so the fields are
 * version-controlled and work with ACF *free*. The group attaches to any page
 * using the "Contact Page" template, so the client edits this copy by editing
 * the Contact page itself. Fields are read via juiced_page_field()
 * (inc/helpers.php); every field has a mockup default in its template-part, so
 * the page renders complete before anything is filled in.
 *
 * The business info itself (phone / email / address / hours, location names
 * and addresses) is NOT here — it lives in Appearance → Customize → Business
 * Info (inc/customizer.php) so the client enters it once for the whole site.
 * Message fields in the tabs below point the client there.
 *
 * One tab per section — Hero, Let's Connect, Message Form, Visit Us.
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

		// The Visit Us cards pull their names, addresses and directions links
		// from the site-wide business info in the Customizer — only the photos
		// are contact-page dressing. Two identical slots, built in a loop.
		$visit_slots = array();
		foreach ( range( 1, 2 ) as $i ) {
			$visit_slots[] = array(
				'key'           => "field_juiced_ct_visit_loc{$i}_photo",
				'label'         => "Location {$i} — photo",
				'name'          => "contact_visit_loc{$i}_photo",
				'type'          => 'image',
				'return_format' => 'array',
				'preview_size'  => 'thumbnail',
				'instructions'  => 'Small storefront shot on the right of the card. Hidden on small screens.',
			);
		}

		acf_add_local_field_group(
			array(
				'key'      => 'group_juiced_contact_page',
				'title'    => 'Contact Page',
				'fields'   => array_merge(
					array(

						// --- Hero (Section 1) --------------------------------------
						array(
							'key'   => 'field_juiced_ct_hero_tab',
							'label' => 'Hero',
							'type'  => 'tab',
						),
						array(
							'key'          => 'field_juiced_ct_hero_lead',
							'label'        => 'Headline — white part',
							'name'         => 'contact_hero_lead',
							'type'         => 'text',
							'instructions' => 'Shown in white. e.g. "We\'d Love to".',
						),
						array(
							'key'          => 'field_juiced_ct_hero_accent',
							'label'        => 'Headline — accent line',
							'name'         => 'contact_hero_accent',
							'type'         => 'text',
							'instructions' => 'The last line, shown in orange. e.g. "Hear From You!".',
						),
						array(
							'key'   => 'field_juiced_ct_hero_subtext',
							'label' => 'Subtext',
							'name'  => 'contact_hero_subtext',
							'type'  => 'textarea',
							'rows'  => 4,
						),
						array(
							'key'           => 'field_juiced_ct_hero_image',
							'label'         => 'Hero background image',
							'name'          => 'contact_hero_image',
							'type'          => 'image',
							'return_format' => 'array',
							'preview_size'  => 'medium',
							'instructions'  => 'Fills the right side of the hero and fades into the dark background. Subject to the right of centre, since the headline covers the left.',
						),

						// --- Let's Connect (Section 2, left column) ----------------
						array(
							'key'   => 'field_juiced_ct_connect_tab',
							'label' => "Let's Connect",
							'type'  => 'tab',
						),
						array(
							'key'          => 'field_juiced_ct_connect_heading',
							'label'        => 'Heading',
							'name'         => 'contact_connect_heading',
							'type'         => 'text',
							'instructions' => 'e.g. "Let\'s Connect".',
						),
						array(
							'key'     => 'field_juiced_ct_connect_note',
							'label'   => 'Where the details live',
							'type'    => 'message',
							'message' => 'The phone numbers, email, address and hours shown here are the site-wide business info — edit them once under <strong>Appearance &rarr; Customize &rarr; Business Info</strong> and every page stays in sync.',
						),
						// --- Message form (Section 2, right column) ----------------
						array(
							'key'   => 'field_juiced_ct_form_tab',
							'label' => 'Message Form',
							'type'  => 'tab',
						),
						array(
							'key'          => 'field_juiced_ct_form_heading',
							'label'        => 'Heading',
							'name'         => 'contact_form_heading',
							'type'         => 'text',
							'instructions' => 'e.g. "Send Us a Message".',
						),
						array(
							'key'          => 'field_juiced_ct_form_topics',
							'label'        => '"How can we help you?" options',
							'name'         => 'contact_form_topics',
							'type'         => 'textarea',
							'rows'         => 5,
							'new_lines'    => '',
							'instructions' => 'One option per line. Clearing this hides the dropdown.',
						),
						array(
							'key'          => 'field_juiced_ct_form_button',
							'label'        => 'Button label',
							'name'         => 'contact_form_button',
							'type'         => 'text',
							'instructions' => 'e.g. "Send Message".',
						),
						array(
							'key'          => 'field_juiced_ct_form_recipient',
							'label'        => 'Send messages to',
							'name'         => 'contact_form_recipient',
							'type'         => 'email',
							'instructions' => 'Email address the form delivers to. Defaults to the site admin email (Settings → General).',
						),

						// --- Visit Us (Section 3) ----------------------------------
						array(
							'key'   => 'field_juiced_ct_visit_tab',
							'label' => 'Visit Us',
							'type'  => 'tab',
						),
						array(
							'key'          => 'field_juiced_ct_visit_heading',
							'label'        => 'Heading',
							'name'         => 'contact_visit_heading',
							'type'         => 'text',
							'instructions' => 'e.g. "Visit Us".',
						),
						array(
							'key'          => 'field_juiced_ct_visit_subtext',
							'label'        => 'Tagline',
							'name'         => 'contact_visit_subtext',
							'type'         => 'text',
							'instructions' => 'e.g. "Two locations. One community.".',
						),
						array(
							'key'     => 'field_juiced_ct_visit_note',
							'label'   => 'Where the details live',
							'type'    => 'message',
							'message' => 'The location names, addresses and directions links come from the site-wide business info under <strong>Appearance &rarr; Customize &rarr; Business Info</strong>. Only the photos below are set here.',
						),
					),
					$visit_slots
				),
				'location' => array(
					array(
						array(
							'param'    => 'page_template',
							'operator' => '==',
							'value'    => 'page-templates/contact.php',
						),
					),
				),
				'menu_order'      => 0,
				'position'        => 'normal',
				'label_placement' => 'top',
				'hide_on_screen'  => array( 'the_content' ),
				'description'     => 'Copy for the /contact page.',
			)
		);
	}
);
