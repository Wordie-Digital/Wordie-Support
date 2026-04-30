<?php
/**
 * Meadan — inc/posts/group-single-post.php
 * ACF field group for single blog posts (post_type == post).
 *
 * Fields:
 *   post_featured_designs  — relationship to design CPT (0–many)
 *
 * @package MeadanHomes
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'acf_add_local_field_group' ) ) {
    return;
}

acf_add_local_field_group( [
    'key'      => 'group_post_single',
    'title'    => 'Blog Post — Featured Designs',
    'fields'   => [
        [
            'key'               => 'field_post_featured_designs',
            'label'             => 'Featured Designs',
            'name'              => 'post_featured_designs',
            'type'              => 'relationship',
            'instructions'      => 'Select one or more Designs to feature in the article. If multiple designs are chosen they display as a Swiper carousel.',
            'required'          => 0,
            'post_type'         => [ 'design' ],
            'taxonomy'          => [],
            'filters'           => [ 'search' ],
            'elements'          => [ 'featured_image' ],
            'min'               => 0,
            'max'               => 10,
            'return_format'     => 'object',
        ],
    ],
    'location' => [
        [
            [
                'param'    => 'post_type',
                'operator' => '==',
                'value'    => 'post',
            ],
        ],
    ],
    'menu_order'            => 10,
    'position'              => 'normal',
    'style'                 => 'default',
    'label_placement'       => 'top',
    'instruction_placement' => 'label',
    'hide_on_screen'        => [],
    'active'                => true,
] );
