<?php
/**
 * Code-defined ACF field group for the Careers page.
 *
 * Same approach as inc/acf-contact-page.php: registered in PHP so the fields
 * are version-controlled and work with ACF free, attached to any page using
 * the "Careers Page" template.
 *
 * One tab per area — Hero (the green panel's headline and photo), Why Work
 * With Us (its perk tiles), Application Form.
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
				'key'             => 'group_juiced_careers_page',
				'title'           => 'Careers Page',
				'fields'          => array(

					// --- Hero -------------------------------------------------
					array(
						'key'   => 'field_juiced_careers_hero_tab',
						'label' => 'Hero',
						'type'  => 'tab',
					),
					array(
						'key'          => 'field_juiced_careers_hero_lead',
						'label'        => 'Headline — cream part',
						'name'         => 'careers_hero_lead',
						'type'         => 'text',
						'instructions' => 'e.g. "Join the".',
					),
					array(
						'key'          => 'field_juiced_careers_hero_accent',
						'label'        => 'Headline — accent line',
						'name'         => 'careers_hero_accent',
						'type'         => 'text',
						'instructions' => 'Shown in orange. e.g. "Juiced! Team".',
					),
					array(
						'key'   => 'field_juiced_careers_hero_subtext',
						'label' => 'Subtext',
						'name'  => 'careers_hero_subtext',
						'type'  => 'textarea',
						'rows'  => 3,
					),
					array(
						'key'           => 'field_juiced_careers_hero_image',
						'label'         => 'Team photo',
						'name'          => 'careers_hero_image',
						'type'          => 'image',
						'return_format' => 'array',
						'preview_size'  => 'medium',
						'instructions'  => 'A team or behind-the-counter photo works best. Shown as a rounded photo in the green panel, under the subtext.',
					),

					// --- Why Work With Us -------------------------------------
					array(
						'key'   => 'field_juiced_careers_why_tab',
						'label' => 'Why Choose Juiced!',
						'type'  => 'tab',
					),
					array(
						'key'          => 'field_juiced_careers_why_heading',
						'label'        => 'Heading',
						'name'         => 'careers_why_heading',
						'type'         => 'text',
						'instructions' => 'e.g. "Why Work With Us".',
					),
					array(
						'key'          => 'field_juiced_careers_perks',
						'label'        => 'Perks',
						'name'         => 'careers_perks',
						'type'         => 'textarea',
						'rows'         => 5,
						'new_lines'    => '',
						'instructions' => 'One perk per line, shown as tiles in the green panel. Clearing this hides them.',
					),


					// --- Application Form -------------------------------------
					array(
						'key'   => 'field_juiced_careers_form_tab',
						'label' => 'Application Form',
						'type'  => 'tab',
					),
					array(
						'key'          => 'field_juiced_careers_form_heading',
						'label'        => 'Heading',
						'name'         => 'careers_form_heading',
						'type'         => 'text',
						'instructions' => 'e.g. "Apply Now".',
					),
					array(
						'key'   => 'field_juiced_careers_form_intro',
						'label' => 'Intro',
						'name'  => 'careers_form_intro',
						'type'  => 'text',
					),
					array(
						'key'          => 'field_juiced_careers_form_button',
						'label'        => 'Button label',
						'name'         => 'careers_form_button',
						'type'         => 'text',
						'instructions' => 'e.g. "Submit Application".',
					),
					array(
						'key'          => 'field_juiced_careers_form_recipient',
						'label'        => 'Send applications to',
						'name'         => 'careers_form_recipient',
						'type'         => 'email',
						'instructions' => 'Email address applications (and résumés) go to. Defaults to the site admin email (Settings → General).',
					),
				),
				'location'        => array(
					array(
						array(
							'param'    => 'page_template',
							'operator' => '==',
							'value'    => 'page-templates/careers.php',
						),
					),
				),
				'menu_order'      => 0,
				'position'        => 'normal',
				'label_placement' => 'top',
				'hide_on_screen'  => array( 'the_content' ),
				'description'     => 'Copy for the /careers page.',
			)
		);
	}
);
