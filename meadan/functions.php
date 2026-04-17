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

    // Testimonial slider JS
    wp_enqueue_script(
        'meadan-testimonial-slider',
        MEADAN_URI . '/assets/js/testimonial-slider.js',
        [],
        MEADAN_VERSION,
        true
    );
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
