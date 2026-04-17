<?php
/**
 * Meadan Homes — inc/theme-setup.php
 *
 * Theme supports, nav menus, image sizes, editor styles.
 */

defined( 'ABSPATH' ) || exit;

add_action( 'after_setup_theme', function () {

    // -------------------------------------------------------------------------
    // Core theme supports
    // -------------------------------------------------------------------------
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'html5', [
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
        'navigation-widgets',
    ] );
    add_theme_support( 'align-wide' );
    add_theme_support( 'responsive-embeds' );

    // Editor styles — loaded in Gutenberg canvas
    add_theme_support( 'editor-styles' );
    add_editor_style( 'assets/css/editor.css' );

    // Disable default block patterns so only our custom blocks appear
    remove_theme_support( 'core-block-patterns' );

    // -------------------------------------------------------------------------
    // Navigation menus
    // -------------------------------------------------------------------------
    register_nav_menus( [
        'primary' => __( 'Primary Navigation', 'meadan' ),
        'footer'  => __( 'Footer Navigation', 'meadan' ),
    ] );

    // -------------------------------------------------------------------------
    // Custom image sizes
    // -------------------------------------------------------------------------
    add_image_size( 'meadan-hero',    1920, 1080, true );
    add_image_size( 'meadan-card',     640, 480,  true );
    add_image_size( 'meadan-3x2',      900, 600,  true );
    add_image_size( 'meadan-square',   600, 600,  true );
    add_image_size( 'meadan-gallery',  800, 600,  false );

    // -------------------------------------------------------------------------
    // Text domain
    // -------------------------------------------------------------------------
    load_theme_textdomain( 'meadan', get_template_directory() . '/languages' );

} );

// ---------------------------------------------------------------------------
// Disable emoji scripts (performance)
// ---------------------------------------------------------------------------
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );
