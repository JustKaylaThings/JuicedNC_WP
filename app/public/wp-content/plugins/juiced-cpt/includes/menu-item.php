<?php
/**
 * Menu Item CPT — post type, `menu_section` taxonomy, and `menuItemFields` ACF group.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// -------------------------------------------------------------------------
// 4. Custom Post Type: Menu Item
// -------------------------------------------------------------------------
function juiced_register_menu_item_cpt() {
    $labels = [
        'name'               => 'Menu Items',
        'singular_name'      => 'Menu Item',
        'add_new'            => 'Add New',
        'add_new_item'       => 'Add New Menu Item',
        'edit_item'          => 'Edit Menu Item',
        'new_item'           => 'New Menu Item',
        'view_item'          => 'View Menu Item',
        'search_items'       => 'Search Menu Items',
        'not_found'          => 'No menu items found',
        'not_found_in_trash' => 'No menu items found in Trash',
    ];

    register_post_type( 'menu_item', [
        'labels'              => $labels,
        'public'              => true,
        'has_archive'         => false,
        'supports'            => [ 'title', 'editor', 'thumbnail', 'page-attributes' ],
        'rewrite'             => [ 'slug' => 'menu-items' ],
        'menu_icon'           => 'dashicons-food',
        'show_in_graphql'     => true,
        'graphql_single_name' => 'foodItem',
        'graphql_plural_name' => 'foodItems',
    ] );
}
add_action( 'init', 'juiced_register_menu_item_cpt' );

// -------------------------------------------------------------------------
// 5. Taxonomy: Menu Section
// -------------------------------------------------------------------------
function juiced_register_menu_section_taxonomy() {
    $labels = [
        'name'          => 'Menu Sections',
        'singular_name' => 'Menu Section',
        'search_items'  => 'Search Menu Sections',
        'all_items'     => 'All Menu Sections',
        'edit_item'     => 'Edit Menu Section',
        'update_item'   => 'Update Menu Section',
        'add_new_item'  => 'Add New Menu Section',
        'new_item_name' => 'New Menu Section Name',
        'menu_name'     => 'Menu Sections',
    ];

    register_taxonomy( 'menu_section', [ 'menu_item' ], [
        'labels'              => $labels,
        'hierarchical'        => true,
        'public'              => true,
        'rewrite'             => [ 'slug' => 'menu-section' ],
        'show_in_graphql'     => true,
        'graphql_single_name' => 'menuSection',
        'graphql_plural_name' => 'menuSections',
    ] );
}
add_action( 'init', 'juiced_register_menu_section_taxonomy' );

// -------------------------------------------------------------------------
// 6. ACF Field Group: Menu Item Details
// -------------------------------------------------------------------------
function juiced_register_menu_item_acf_fields() {
    if ( ! function_exists( 'acf_add_local_field_group' ) ) {
        return;
    }

    acf_add_local_field_group( [
        'key'    => 'group_menu_item_details',
        'title'  => 'Menu Item Details',
        'fields' => [
            [
                'key'                => 'field_menu_item_ingredients',
                'label'              => 'Ingredients',
                'name'               => 'ingredients',
                'type'               => 'textarea',
                'show_in_graphql'    => 1,
                'graphql_field_name' => 'ingredients',
            ],
            [
                'key'                => 'field_menu_item_featured',
                'label'              => 'Featured',
                'name'               => 'featured',
                'type'               => 'true_false',
                'ui'                 => 1,
                'show_in_graphql'    => 1,
                'graphql_field_name' => 'featured',
            ],
        ],
        'location' => [
            [
                [
                    'param'    => 'post_type',
                    'operator' => '==',
                    'value'    => 'menu_item',
                ],
            ],
        ],
        'show_in_graphql'     => 1,
        'graphql_field_name'  => 'menuItemFields',
        'graphql_types'       => [ 'FoodItem' ],
    ] );
}
add_action( 'acf/init', 'juiced_register_menu_item_acf_fields' );
