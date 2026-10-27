<?php
/**
 * Code-defined ACF field group for the Host an Event page.
 *
 * Same approach as inc/acf-contact-page.php: registered in PHP so the fields
 * are version-controlled and work with ACF free, attached to any page using
 * the "Host an Event Page" template. Every field has a default in its
 * template-part, so the page renders complete before anything is filled in.
 *
 * One tab per section — Hero, The Space, Good to Know & Form.
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

		// Four quick facts for The Space — fixed slots, since ACF free has no Repeater.
		$space_slots = array();
		foreach ( range( 1, 4 ) as $i ) {
			$space_slots[] = array(
				'key'     => "field_juiced_host_space{$i}_label",
				'label'   => "Fact {$i} — label",
				'name'    => "host_space{$i}_label",
				'type'    => 'text',
				'wrapper' => array( 'width' => '25' ),
			);
			$space_slots[] = array(
				'key'          => "field_juiced_host_space{$i}_value",
				'label'        => "Fact {$i} — value",
				'name'         => "host_space{$i}_value",
				'type'         => 'text',
				'wrapper'      => array( 'width' => '35' ),
				'instructions' => 'Clear to hide this fact.',
			);
			$space_slots[] = array(
				'key'     => "field_juiced_host_space{$i}_detail",
				'label'   => "Fact {$i} — detail line",
				'name'    => "host_space{$i}_detail",
				'type'    => 'text',
				'wrapper' => array( 'width' => '40' ),
			);
		}

		acf_add_local_field_group(
			array(
				'key'             => 'group_juiced_host_event_page',
				'title'           => 'Host an Event Page',
				'fields'          => array_merge(
					array(

						// --- Hero -------------------------------------------------
						array(
							'key'   => 'field_juiced_host_hero_tab',
							'label' => 'Hero',
							'type'  => 'tab',
						),
						array(
							'key'          => 'field_juiced_host_hero_lead',
							'label'        => 'Headline — cream part',
							'name'         => 'host_hero_lead',
							'type'         => 'text',
							'instructions' => 'e.g. "Host Your Event".',
						),
						array(
							'key'          => 'field_juiced_host_hero_accent',
							'label'        => 'Headline — accent line',
							'name'         => 'host_hero_accent',
							'type'         => 'text',
							'instructions' => 'Shown in orange. e.g. "at Juiced!".',
						),
						array(
							'key'   => 'field_juiced_host_hero_subtext',
							'label' => 'Subtext',
							'name'  => 'host_hero_subtext',
							'type'  => 'textarea',
							'rows'  => 3,
						),
						array(
							'key'          => 'field_juiced_host_hero_primary',
							'label'        => 'Button',
							'name'         => 'host_hero_primary',
							'type'         => 'text',
							'instructions' => 'Scrolls to the form. e.g. "Pitch Your Event".',
						),
						// --- The Space (right side of the hero) ---------------------
						array(
							'key'   => 'field_juiced_host_space_tab',
							'label' => 'The Space',
							'type'  => 'tab',
						),
						array(
							'key'     => 'field_juiced_host_space_note',
							'label'   => 'Where this shows',
							'type'    => 'message',
							'message' => 'The card on the right side of the hero banner.',
						),
						array(
							'key'          => 'field_juiced_host_space_heading',
							'label'        => 'Heading',
							'name'         => 'host_space_heading',
							'type'         => 'text',
							'instructions' => 'e.g. "The Space".',
						),
						array(
							'key'   => 'field_juiced_host_space_intro',
							'label' => 'Intro',
							'name'  => 'host_space_intro',
							'type'  => 'text',
						),
						array(
							'key'          => 'field_juiced_host_peerspace_url',
							'label'        => 'Peerspace listing',
							'name'         => 'host_peerspace_url',
							'type'         => 'url',
							'instructions' => 'The "Book on Peerspace" button in the hero card links here. Clear to hide it.',
						),
						array(
							'key'          => 'field_juiced_host_peerspace_rating',
							'label'        => 'Peerspace rating',
							'name'         => 'host_peerspace_rating',
							'type'         => 'text',
							'wrapper'      => array( 'width' => '50' ),
							'instructions' => 'Shown on the button with a star, e.g. "4.9". Update it by hand when it changes on Peerspace. Clear to hide.',
						),
						array(
							'key'          => 'field_juiced_host_peerspace_reviews',
							'label'        => 'Peerspace review count',
							'name'         => 'host_peerspace_reviews',
							'type'         => 'text',
							'wrapper'      => array( 'width' => '50' ),
							'instructions' => 'Shown in brackets after the rating, e.g. "26". Clear to hide.',
						),
					),
					$space_slots,
					array(

						// --- Photos of the Space (carousel above Good to Know) -----
						array(
							'key'   => 'field_juiced_host_gallery_tab',
							'label' => 'Photos',
							'type'  => 'tab',
						),
						array(
							'key'     => 'field_juiced_host_gallery_note',
							'label'   => 'Photos of the space',
							'type'    => 'message',
							'message' => 'A swipeable carousel above Good to Know, in this order. Use any number from 1 to 8; with none, it is hidden. Alt text and captions come from the Media Library.',
						),
						array(
							'key'           => 'field_juiced_host_space_photo1',
							'label'         => 'Photo 1',
							'name'          => 'host_space_photo1',
							'type'          => 'image',
							'return_format' => 'array',
							'preview_size'  => 'medium',
							'wrapper'       => array( 'width' => '25' ),
							'instructions'  => 'The first slide — your best wide shot of the room.',
						),
						array(
							'key'           => 'field_juiced_host_space_photo2',
							'label'         => 'Photo 2',
							'name'          => 'host_space_photo2',
							'type'          => 'image',
							'return_format' => 'array',
							'preview_size'  => 'medium',
							'wrapper'       => array( 'width' => '25' ),
							'instructions'  => '',
						),
						array(
							'key'           => 'field_juiced_host_space_photo3',
							'label'         => 'Photo 3',
							'name'          => 'host_space_photo3',
							'type'          => 'image',
							'return_format' => 'array',
							'preview_size'  => 'medium',
							'wrapper'       => array( 'width' => '25' ),
							'instructions'  => '',
						),
						array(
							'key'           => 'field_juiced_host_space_photo4',
							'label'         => 'Photo 4',
							'name'          => 'host_space_photo4',
							'type'          => 'image',
							'return_format' => 'array',
							'preview_size'  => 'medium',
							'wrapper'       => array( 'width' => '25' ),
							'instructions'  => '',
						),
						array(
							'key'           => 'field_juiced_host_space_photo5',
							'label'         => 'Photo 5',
							'name'          => 'host_space_photo5',
							'type'          => 'image',
							'return_format' => 'array',
							'preview_size'  => 'medium',
							'wrapper'       => array( 'width' => '25' ),
							'instructions'  => '',
						),
						array(
							'key'           => 'field_juiced_host_space_photo6',
							'label'         => 'Photo 6',
							'name'          => 'host_space_photo6',
							'type'          => 'image',
							'return_format' => 'array',
							'preview_size'  => 'medium',
							'wrapper'       => array( 'width' => '25' ),
							'instructions'  => '',
						),
						array(
							'key'           => 'field_juiced_host_space_photo7',
							'label'         => 'Photo 7',
							'name'          => 'host_space_photo7',
							'type'          => 'image',
							'return_format' => 'array',
							'preview_size'  => 'medium',
							'wrapper'       => array( 'width' => '25' ),
							'instructions'  => '',
						),
						array(
							'key'           => 'field_juiced_host_space_photo8',
							'label'         => 'Photo 8',
							'name'          => 'host_space_photo8',
							'type'          => 'image',
							'return_format' => 'array',
							'preview_size'  => 'medium',
							'wrapper'       => array( 'width' => '25' ),
							'instructions'  => '',
						),
					),
					array(
						// --- Good to Know & Form -----------------------------------
						array(
							'key'   => 'field_juiced_host_form_tab',
							'label' => 'Good to Know & Form',
							'type'  => 'tab',
						),
						array(
							'key'          => 'field_juiced_host_faq_heading',
							'label'        => 'FAQ heading',
							'name'         => 'host_faq_heading',
							'type'         => 'text',
							'instructions' => 'e.g. "Good to Know".',
						),
						array(
							'key'          => 'field_juiced_host_faq_items',
							'label'        => 'FAQ',
							'name'         => 'host_faq_items',
							'type'         => 'textarea',
							'rows'         => 6,
							'new_lines'    => '',
							'instructions' => 'One per line as "Question | Answer". The first one shows open. Clearing this hides the FAQ and centers the form.',
						),
						array(
							'key'          => 'field_juiced_host_form_heading',
							'label'        => 'Heading',
							'name'         => 'host_form_heading',
							'type'         => 'text',
							'instructions' => 'Form heading, e.g. "Pitch Your Event".',
						),
						array(
							'key'  => 'field_juiced_host_form_intro',
							'label' => 'Intro',
							'name' => 'host_form_intro',
							'type' => 'text',
						),
						array(
							'key'          => 'field_juiced_host_form_types',
							'label'        => '"Type of event" options',
							'name'         => 'host_form_types',
							'type'         => 'textarea',
							'rows'         => 6,
							'new_lines'    => '',
							'instructions' => 'One option per line. Clearing this hides the dropdown.',
						),
						array(
							'key'          => 'field_juiced_host_form_button',
							'label'        => 'Button label',
							'name'         => 'host_form_button',
							'type'         => 'text',
							'instructions' => 'e.g. "Send Event Idea".',
						),
						array(
							'key'          => 'field_juiced_host_form_recipient',
							'label'        => 'Send event ideas to',
							'name'         => 'host_form_recipient',
							'type'         => 'email',
							'instructions' => 'Email address the form delivers to. Defaults to the site admin email (Settings → General).',
						),
					)
				),
				'location'        => array(
					array(
						array(
							'param'    => 'page_template',
							'operator' => '==',
							'value'    => 'page-templates/host-event.php',
						),
					),
				),
				'menu_order'      => 0,
				'position'        => 'normal',
				'label_placement' => 'top',
				'hide_on_screen'  => array( 'the_content' ),
				'description'     => 'Copy for the /host-an-event page.',
			)
		);
	}
);
