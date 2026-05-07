<?php
/**
 * Meadan — inc/cpt-offer.php
 *
 * Registers the 'offer' Custom Post Type.
 *
 * Archive URL:  /offers-and-partnerships/
 * Single URL:   /offers-and-partnerships/{slug}/
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', function () {

    register_post_type( 'offer', [
        'labels' => [
            'name'                  => __( 'Offers & Partnerships', 'meadan' ),
            'singular_name'         => __( 'Offer', 'meadan' ),
            'add_new'               => __( 'Add New', 'meadan' ),
            'add_new_item'          => __( 'Add New Offer', 'meadan' ),
            'edit_item'             => __( 'Edit Offer', 'meadan' ),
            'new_item'              => __( 'New Offer', 'meadan' ),
            'view_item'             => __( 'View Offer', 'meadan' ),
            'view_items'            => __( 'View Offers', 'meadan' ),
            'search_items'          => __( 'Search Offers', 'meadan' ),
            'not_found'             => __( 'No offers found', 'meadan' ),
            'not_found_in_trash'    => __( 'No offers found in Trash', 'meadan' ),
            'all_items'             => __( 'All Offers', 'meadan' ),
            'archives'              => __( 'Offer Archives', 'meadan' ),
            'attributes'            => __( 'Offer Attributes', 'meadan' ),
            'menu_name'             => __( 'Offers & Partners', 'meadan' ),
        ],
        'description'        => __( 'Exclusive offers and partner benefits for Meadan Homes clients.', 'meadan' ),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'show_in_rest'       => true,   // enables Gutenberg editor
        'query_var'          => true,
        'capability_type'    => 'post',
        'has_archive'        => 'offers-and-partnerships',
        'rewrite'            => [
            'slug'       => 'offers-and-partnerships',
            'with_front' => false,
        ],
        'supports' => [
            'title',
            'editor',
            'thumbnail',
            'excerpt',
            'custom-fields',
            'revisions',
        ],
        'menu_icon'     => 'dashicons-tag',
        'menu_position' => 25,
    ] );

} );
