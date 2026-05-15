<?php
/**
 * Meadan — inc/cpt-registration.php
 * Registers custom post types: Project, Design.
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', function () {

    // -----------------------------------------------------------------------
    // CPT: Project
    // -----------------------------------------------------------------------
    register_post_type( 'project', [
        'labels' => [
            'name'               => __( 'Projects', 'meadan' ),
            'singular_name'      => __( 'Project', 'meadan' ),
            'add_new_item'       => __( 'Add New Project', 'meadan' ),
            'edit_item'          => __( 'Edit Project', 'meadan' ),
            'new_item'           => __( 'New Project', 'meadan' ),
            'view_item'          => __( 'View Project', 'meadan' ),
            'search_items'       => __( 'Search Projects', 'meadan' ),
            'not_found'          => __( 'No projects found', 'meadan' ),
            'not_found_in_trash' => __( 'No projects in trash', 'meadan' ),
        ],
        'public'            => true,
        'has_archive'       => true,
        'rewrite'           => [ 'slug' => 'projects' ],
        'menu_icon'         => 'dashicons-portfolio',
        'menu_position'     => 5,
        'supports'          => [ 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes' ],
        'show_in_rest'      => true,
        'template'          => [],
        'template_lock'     => false,
    ] );

    // -----------------------------------------------------------------------
    // CPT: Design
    // -----------------------------------------------------------------------
    register_post_type( 'design', [
        'labels' => [
            'name'               => __( 'Designs', 'meadan' ),
            'singular_name'      => __( 'Design', 'meadan' ),
            'add_new_item'       => __( 'Add New Design', 'meadan' ),
            'edit_item'          => __( 'Edit Design', 'meadan' ),
            'new_item'           => __( 'New Design', 'meadan' ),
            'view_item'          => __( 'View Design', 'meadan' ),
            'search_items'       => __( 'Search Designs', 'meadan' ),
            'not_found'          => __( 'No designs found', 'meadan' ),
            'not_found_in_trash' => __( 'No designs in trash', 'meadan' ),
        ],
        'public'            => true,
        'has_archive'       => true,
        'rewrite'           => [ 'slug' => 'designs' ],
        'menu_icon'         => 'dashicons-layout',
        'menu_position'     => 6,
        'supports'          => [ 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes' ],
        'show_in_rest'      => true,
        'template'          => [],
        'template_lock'     => false,
    ] );

    // -----------------------------------------------------------------------
    // CPT: Display Home
    // -----------------------------------------------------------------------
    register_post_type( 'display-home', [
        'labels' => [
            'name'               => __( 'Display Homes', 'meadan' ),
            'singular_name'      => __( 'Display Home', 'meadan' ),
            'add_new_item'       => __( 'Add New Display Home', 'meadan' ),
            'edit_item'          => __( 'Edit Display Home', 'meadan' ),
            'new_item'           => __( 'New Display Home', 'meadan' ),
            'view_item'          => __( 'View Display Home', 'meadan' ),
            'search_items'       => __( 'Search Display Homes', 'meadan' ),
            'not_found'          => __( 'No display homes found', 'meadan' ),
            'not_found_in_trash' => __( 'No display homes in trash', 'meadan' ),
        ],
        'public'            => true,
        'has_archive'       => true,
        'rewrite'           => [ 'slug' => 'display-homes' ],
        'menu_icon'         => 'dashicons-admin-home',
        'menu_position'     => 7,
        'supports'          => [ 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes' ],
        'show_in_rest'      => true,
        'template'          => [],
        'template_lock'     => false,
    ] );

    // -----------------------------------------------------------------------
    // Meta boxes: Design specs (bedrooms, bathrooms, sqm)
    // -----------------------------------------------------------------------
    register_meta( 'post', '_design_bedrooms',  [ 'object_subtype' => 'design', 'show_in_rest' => true, 'single' => true, 'type' => 'integer' ] );
    register_meta( 'post', '_design_bathrooms', [ 'object_subtype' => 'design', 'show_in_rest' => true, 'single' => true, 'type' => 'integer' ] );
    register_meta( 'post', '_design_sqm',       [ 'object_subtype' => 'design', 'show_in_rest' => true, 'single' => true, 'type' => 'number'  ] );

} );

// ---------------------------------------------------------------------------
// Flush rewrite rules on activation (run once via theme switch hook)
// ---------------------------------------------------------------------------
add_action( 'after_switch_theme', function () {
    flush_rewrite_rules();
} );
