<?php
namespace CustomOptionPageACF;

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists(COPACF_CustomOptionsPageLocation::class)) :

class COPACF_CustomOptionsPageLocation extends \ACF_Location
{
    public function initialize()
    {
        $this->name = 'custom_options_page';
        $this->label = __('Custom Options Page', 'custom-option-page-for-acf');
        $this->category = 'forms';
        $this->object_type = 'option';
    }

    public function get_values($rule)
    {
        $choices = [];

        $pages = copacf_get_custom_options_pages();

        if (!empty($pages)) {
            foreach ($pages as $page) {
                $choices[$page->ID] = $page->post_title;
            }
        } else {
            $choices[] = __('No pages exist', 'custom-option-page-for-acf');
        }

        return $choices;
    }

    public function match($rule, $screen, $field_group)
	{
		// Current admin screen page slug
		$page_slug = !empty($_GET['page']) ? sanitize_text_field(wp_unslash($_GET['page'])) : '';
		if (!$page_slug) {
			return false;
		}

		// Load the ACF-COP post selected in the location rule
		$target_post = get_post((int) $rule['value']);

		if (!$target_post || $target_post->post_type !== 'acf-cop') {
			return false;
		}

		// Match by comparing slug to current page
		return $target_post->post_name === $page_slug;
	}
	
}

acf_register_location_type(__NAMESPACE__ . '\\COPACF_CustomOptionsPageLocation');

endif;