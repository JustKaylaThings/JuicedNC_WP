<?php
/**
 * Herb CPT — post type, `herb_category` taxonomy, and `herbFields` ACF group.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// -------------------------------------------------------------------------
// 1. Custom Post Type: Herb
// -------------------------------------------------------------------------
function juiced_register_herb_cpt() {
    $labels = [
        'name'               => 'Herbs',
        'singular_name'      => 'Herb',
        'add_new'            => 'Add New',
        'add_new_item'       => 'Add New Herb',
        'edit_item'          => 'Edit Herb',
        'new_item'           => 'New Herb',
        'view_item'          => 'View Herb',
        'search_items'       => 'Search Herbs',
        'not_found'          => 'No herbs found',
        'not_found_in_trash' => 'No herbs found in Trash',
    ];

    register_post_type( 'herb', [
        'labels'              => $labels,
        'public'              => true,
        // No archive: the Herb Library page (page-templates/herbs.php) is the
        // canonical index at /herbs/, same pattern as the Events page.
        'has_archive'         => false,
        'supports'            => [ 'title', 'editor', 'excerpt', 'thumbnail' ],
        'rewrite'             => [ 'slug' => 'herbs' ],
        'menu_icon'           => 'dashicons-palmtree',
        // WPGraphQL settings
        'show_in_graphql'     => true,
        'graphql_single_name' => 'herb',
        'graphql_plural_name' => 'herbs',
    ] );
}
add_action( 'init', 'juiced_register_herb_cpt' );

// -------------------------------------------------------------------------
// 2. Taxonomy: Herb Category
// -------------------------------------------------------------------------
function juiced_register_herb_category_taxonomy() {
    $labels = [
        'name'          => 'Herb Categories',
        'singular_name' => 'Herb Category',
        'search_items'  => 'Search Herb Categories',
        'all_items'     => 'All Herb Categories',
        'edit_item'     => 'Edit Herb Category',
        'update_item'   => 'Update Herb Category',
        'add_new_item'  => 'Add New Herb Category',
        'new_item_name' => 'New Herb Category Name',
        'menu_name'     => 'Herb Categories',
    ];

    register_taxonomy( 'herb_category', [ 'herb' ], [
        'labels'            => $labels,
        'hierarchical'      => true,
        'public'            => true,
        'rewrite'           => [ 'slug' => 'herb-category' ],
        // WPGraphQL settings
        'show_in_graphql'     => true,
        'graphql_single_name' => 'herbCategory',
        'graphql_plural_name' => 'herbCategories',
    ] );
}
add_action( 'init', 'juiced_register_herb_category_taxonomy' );

// -------------------------------------------------------------------------
// 2b. Taxonomy: Herb Tag
// -------------------------------------------------------------------------
function juiced_register_herb_tag_taxonomy() {
    $labels = [
        'name'                       => 'Herb Tags',
        'singular_name'              => 'Herb Tag',
        'search_items'               => 'Search Herb Tags',
        'popular_items'              => 'Popular Herb Tags',
        'all_items'                  => 'All Herb Tags',
        'edit_item'                  => 'Edit Herb Tag',
        'update_item'                => 'Update Herb Tag',
        'add_new_item'               => 'Add New Herb Tag',
        'new_item_name'              => 'New Herb Tag Name',
        'separate_items_with_commas' => 'Separate herb tags with commas',
        'add_or_remove_items'        => 'Add or remove herb tags',
        'choose_from_most_used'      => 'Choose from the most used herb tags',
        'menu_name'                  => 'Herb Tags',
    ];

    register_taxonomy( 'herb_tag', [ 'herb' ], [
        'labels'              => $labels,
        'hierarchical'        => false,
        'public'              => true,
        'show_ui'             => true,
        'show_admin_column'   => true,
        'rewrite'             => [ 'slug' => 'herb-tag' ],
        'show_in_graphql'     => true,
        'graphql_single_name' => 'herbTag',
        'graphql_plural_name' => 'herbTags',
    ] );
}
add_action( 'init', 'juiced_register_herb_tag_taxonomy' );

// -------------------------------------------------------------------------
// 3. ACF Field Group: Herb Fields
// -------------------------------------------------------------------------
function juiced_register_herb_acf_fields() {
    if ( ! function_exists( 'acf_add_local_field_group' ) ) {
        return;
    }

    acf_add_local_field_group( [
        'key'    => 'group_herb_fields',
        'title'  => 'Herb Fields',
        'fields' => [
            [
                'key'               => 'field_herb_type',
                'label'             => 'Type',
                'name'              => 'herb_type',
                'type'              => 'text',
                'instructions'      => 'The part of the plant used — e.g. Root, Leaf, Flower. Shown as the badge above the herb\'s name.',
                'show_in_graphql'   => 1,
                'graphql_field_name'=> 'herbType',
            ],
            [
                'key'               => 'field_herb_tagline',
                'label'             => 'Tagline',
                'name'              => 'tagline',
                'type'              => 'text',
                'instructions'      => 'Short epithet under the name, e.g. "The Golden Healer".',
                'show_in_graphql'   => 1,
                'graphql_field_name'=> 'tagline',
            ],
            [
                'key'               => 'field_herb_latin_name',
                'label'             => 'Latin Name',
                'name'              => 'latin_name',
                'type'              => 'text',
                'show_in_graphql'   => 1,
                'graphql_field_name'=> 'latinName',
            ],
            [
                'key'               => 'field_herb_benefits',
                'label'             => 'Benefits',
                'name'              => 'benefits',
                'type'              => 'textarea',
                'instructions'      => 'One per line — shown as the green checklist under "Overview" on the herb page.',
                'show_in_graphql'   => 1,
                'graphql_field_name'=> 'benefits',
            ],
            [
                'key'               => 'field_herb_key_benefits',
                'label'             => 'Key Benefits',
                'name'              => 'key_benefits',
                'type'              => 'textarea',
                'instructions'      => 'One per line as "Title | Short description" — fills the "Key Benefits" panel on the herb page. The icon is picked from keywords in the title (immunity, digestion, energy, sleep, skin, brain…).',
                'show_in_graphql'   => 1,
                'graphql_field_name'=> 'keyBenefits',
            ],
            [
                'key'               => 'field_herb_usage_notes',
                'label'             => 'Usage Notes',
                'name'              => 'usage_notes',
                'type'              => 'textarea',
                'show_in_graphql'   => 1,
                'graphql_field_name'=> 'usageNotes',
            ],
            [
                'key'               => 'field_herb_cautions',
                'label'             => 'Cautions',
                'name'              => 'cautions',
                'type'              => 'textarea',
                'show_in_graphql'   => 1,
                'graphql_field_name'=> 'cautions',
            ],
            [
                'key'               => 'field_herb_preparation_methods',
                'label'             => 'Preparation Methods',
                'name'              => 'preparation_methods',
                'type'              => 'checkbox',
                'choices'           => [
                    'tea'       => 'Tea / Infusion',
                    'tincture'  => 'Tincture',
                    'capsule'   => 'Capsule',
                    'topical'   => 'Topical',
                    'raw'       => 'Raw / Fresh',
                    'decoction' => 'Decoction',
                    'syrup'     => 'Syrup',
                    'powder'    => 'Powder',
                ],
                'show_in_graphql'   => 1,
                'graphql_field_name'=> 'preparationMethods',
            ],
            [
                'key'               => 'field_herb_origin',
                'label'             => 'Origin',
                'name'              => 'origin',
                'type'              => 'text',
                'show_in_graphql'   => 1,
                'graphql_field_name'=> 'origin',
            ],
            [
                'key'               => 'field_herb_related_products',
                'label'             => 'Related Products',
                'name'              => 'related_products',
                'type'              => 'relationship',
                'post_type'         => [ 'product' ],
                'filters'           => [ 'search' ],
                'return_format'     => 'object',
                'show_in_graphql'   => 1,
                'graphql_field_name'=> 'relatedProducts',
            ],
        ],
        'location' => [
            [
                [
                    'param'    => 'post_type',
                    'operator' => '==',
                    'value'    => 'herb',
                ],
            ],
        ],
        'show_in_graphql' => 1,
        'graphql_field_name' => 'herbFields',
    ] );
}
add_action( 'acf/init', 'juiced_register_herb_acf_fields' );

// -------------------------------------------------------------------------
// 4. Admin list: Featured image thumbnail column
// -------------------------------------------------------------------------
function juiced_herb_admin_columns( $columns ) {
    $new = [];
    foreach ( $columns as $key => $label ) {
        if ( 'title' === $key ) {
            $new['herb_thumbnail'] = 'Image';
        }
        $new[ $key ] = $label;
    }
    return $new;
}
add_filter( 'manage_herb_posts_columns', 'juiced_herb_admin_columns' );

function juiced_herb_admin_column_content( $column, $post_id ) {
    if ( 'herb_thumbnail' !== $column ) {
        return;
    }

    $thumb_id = get_post_thumbnail_id( $post_id );
    $url      = '';

    if ( $thumb_id ) {
        $img = wp_get_attachment_image_src( $thumb_id, 'thumbnail' );
        if ( $img && ! empty( $img[0] ) ) {
            $url = $img[0];
        } else {
            $url = wp_get_attachment_url( $thumb_id );
        }
    }

    if ( ! $url ) {
        preg_match( '/<img[^>]+src=["\']([^"\']+)["\']/i', get_post_field( 'post_content', $post_id ), $m );
        if ( ! empty( $m[1] ) ) {
            $url = $m[1];
        }
    }

    if ( $url ) {
        printf(
            '<img src="%s" width="60" height="60" style="border-radius:4px;object-fit:cover;" alt="" />',
            esc_url( $url )
        );
    } else {
        echo '<span style="color:#bbb;">—</span>';
    }
}
add_action( 'manage_herb_posts_custom_columns', 'juiced_herb_admin_column_content', 10, 2 );
