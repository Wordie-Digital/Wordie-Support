<?php
/**
 * Meadan Homes — functions.php
 *
 * Bootstraps theme setup, ACF sync, block registration, and asset enqueueing.
 */

defined( 'ABSPATH' ) || exit;

define( 'MEADAN_VERSION', '2.0.0' );
define( 'MEADAN_DIR', get_template_directory() );
define( 'MEADAN_URI', get_template_directory_uri() );

// ---------------------------------------------------------------------------
// Required files
// ---------------------------------------------------------------------------
require_once MEADAN_DIR . '/inc/theme-setup.php';
require_once MEADAN_DIR . '/inc/block-registration.php';
require_once MEADAN_DIR . '/inc/acf-options.php';
require_once MEADAN_DIR . '/inc/posts/group-single-post.php';
require_once MEADAN_DIR . '/inc/seed-blog-posts.php';
require_once MEADAN_DIR . '/inc/cpt-offer.php';

// ---------------------------------------------------------------------------
// ACF local JSON — save / load paths
// ---------------------------------------------------------------------------
add_filter( 'acf/settings/save_json', function () {
    return MEADAN_DIR . '/acf-fields';
} );

add_filter( 'acf/settings/load_json', function ( $paths ) {
    $paths[] = MEADAN_DIR . '/acf-fields';
    return $paths;
} );

// ---------------------------------------------------------------------------
// Enqueue front-end assets
// ---------------------------------------------------------------------------
add_action( 'wp_enqueue_scripts', function () {
    // Global stylesheet
    wp_enqueue_style(
        'meadan-main',
        MEADAN_URI . '/assets/css/main.css',
        [],
        MEADAN_VERSION
    );

    // Navigation JS — dropdowns, mobile accordion, scroll state
    wp_enqueue_script(
        'meadan-nav',
        MEADAN_URI . '/assets/js/nav.js',
        [],
        filemtime( MEADAN_DIR . '/assets/js/nav.js' ),
        true
    );

    // Testimonial slider JS
    wp_enqueue_script(
        'meadan-testimonial-slider',
        MEADAN_URI . '/assets/js/testimonial-slider.js',
        [],
        MEADAN_VERSION,
        true
    );

    // Single Blog Post — page styles
    if ( is_singular( 'post' ) ) {
        wp_enqueue_style(
            'meadan-single-post',
            MEADAN_URI . '/assets/css/blocks/single-post.css',
            [ 'meadan-main' ],
            filemtime( MEADAN_DIR . '/assets/css/blocks/single-post.css' )
        );
    }

    // Offers & Partnerships — archive styles
    if ( is_post_type_archive( 'offer' ) ) {
        wp_enqueue_style(
            'meadan-archive-offer',
            MEADAN_URI . '/assets/css/blocks/archive-offer.css',
            [ 'meadan-main' ],
            filemtime( MEADAN_DIR . '/assets/css/blocks/archive-offer.css' )
        );
    }

    // Offers & Partnerships — single styles (also loads archive for shared .offer-card)
    if ( is_singular( 'offer' ) ) {
        wp_enqueue_style(
            'meadan-archive-offer',
            MEADAN_URI . '/assets/css/blocks/archive-offer.css',
            [ 'meadan-main' ],
            filemtime( MEADAN_DIR . '/assets/css/blocks/archive-offer.css' )
        );
        wp_enqueue_style(
            'meadan-single-offer',
            MEADAN_URI . '/assets/css/blocks/single-offer.css',
            [ 'meadan-main', 'meadan-archive-offer' ],
            filemtime( MEADAN_DIR . '/assets/css/blocks/single-offer.css' )
        );
    }

    // Our Process — page template styles + shared contact section CSS + slider JS
    if ( is_page_template( 'templates/page-our-process.php' ) ) {
        wp_enqueue_style(
            'meadan-contact-section',
            MEADAN_URI . '/assets/css/blocks/contact-section.css',
            [ 'meadan-main' ],
            filemtime( MEADAN_DIR . '/assets/css/blocks/contact-section.css' )
        );
        wp_enqueue_style(
            'meadan-page-our-process',
            MEADAN_URI . '/assets/css/blocks/page-our-process.css',
            [ 'meadan-main', 'meadan-contact-section' ],
            filemtime( MEADAN_DIR . '/assets/css/blocks/page-our-process.css' )
        );
        wp_enqueue_script(
            'meadan-process-overview',
            MEADAN_URI . '/assets/js/process-overview.js',
            [],
            filemtime( MEADAN_DIR . '/assets/js/process-overview.js' ),
            true
        );
    }
} );

// ---------------------------------------------------------------------------
// Template routing — single post
// ---------------------------------------------------------------------------
add_filter( 'single_template', function ( $template ) {
    if ( is_singular( 'post' ) ) {
        $candidate = MEADAN_DIR . '/templates/single-post.php';
        if ( file_exists( $candidate ) ) {
            return $candidate;
        }
    }
    return $template;
} );

// ---------------------------------------------------------------------------
// Template routing — offer CPT single
// ---------------------------------------------------------------------------
add_filter( 'single_template', function ( $template ) {
    if ( is_singular( 'offer' ) ) {
        $candidate = MEADAN_DIR . '/templates/single-offer.php';
        if ( file_exists( $candidate ) ) {
            return $candidate;
        }
    }
    return $template;
} );

// ---------------------------------------------------------------------------
// Template routing — offer CPT archive
// ---------------------------------------------------------------------------
add_filter( 'archive_template', function ( $template ) {
    if ( is_post_type_archive( 'offer' ) ) {
        $candidate = MEADAN_DIR . '/templates/archive-offer.php';
        if ( file_exists( $candidate ) ) {
            return $candidate;
        }
    }
    return $template;
} );

// ---------------------------------------------------------------------------
// Register Our Process page template so it appears in WP Admin > Page Attributes
// ---------------------------------------------------------------------------
add_filter( 'theme_page_templates', function ( $templates ) {
    $templates['templates/page-our-process.php'] = __( 'Our Process', 'meadan' );
    return $templates;
} );

// ---------------------------------------------------------------------------
// Enqueue block editor assets
// ---------------------------------------------------------------------------
add_action( 'enqueue_block_editor_assets', function () {
    wp_enqueue_style(
        'meadan-editor',
        MEADAN_URI . '/assets/css/editor.css',
        [ 'wp-edit-blocks' ],
        MEADAN_VERSION
    );
} );
