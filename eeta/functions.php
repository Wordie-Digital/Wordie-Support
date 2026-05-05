<?php
defined( 'ABSPATH' ) || exit;

define( 'EETA_VERSION', '1.0.0' );
define( 'EETA_DIR', get_template_directory() );
define( 'EETA_URI', get_template_directory_uri() );

// ---------------------------------------------------------------------------
// Theme setup
// ---------------------------------------------------------------------------
add_action( 'after_setup_theme', function () {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'html5', [ 'search-form', 'comment-form', 'gallery', 'caption' ] );

    register_nav_menus( [
        'primary' => __( 'Primary Navigation', 'eeta' ),
        'footer'  => __( 'Footer Links', 'eeta' ),
    ] );
} );

// ---------------------------------------------------------------------------
// ACF local JSON — save / load
// ---------------------------------------------------------------------------
add_filter( 'acf/settings/save_json', fn() => EETA_DIR . '/acf-fields' );

add_filter( 'acf/settings/load_json', function ( $paths ) {
    $paths[] = EETA_DIR . '/acf-fields';
    return $paths;
} );

// ---------------------------------------------------------------------------
// ACF options page (nav + footer)
// ---------------------------------------------------------------------------
add_action( 'acf/init', function () {
    if ( ! function_exists( 'acf_add_options_page' ) ) {
        return;
    }
    acf_add_options_page( [
        'page_title' => 'EETA Site Settings',
        'menu_title' => 'EETA Settings',
        'menu_slug'  => 'eeta-settings',
        'capability' => 'manage_options',
        'redirect'   => false,
    ] );
    acf_add_options_sub_page( [
        'page_title'  => 'Header Settings',
        'menu_title'  => 'Header',
        'menu_slug'   => 'eeta-settings-header',
        'parent_slug' => 'eeta-settings',
    ] );
    acf_add_options_sub_page( [
        'page_title'  => 'Footer Settings',
        'menu_title'  => 'Footer',
        'menu_slug'   => 'eeta-settings-footer',
        'parent_slug' => 'eeta-settings',
    ] );
} );

// ---------------------------------------------------------------------------
// Enqueue assets
// ---------------------------------------------------------------------------
add_action( 'wp_enqueue_scripts', function () {
    wp_enqueue_style(
        'eeta-main',
        EETA_URI . '/assets/css/main.css',
        [],
        EETA_VERSION
    );

    wp_enqueue_script(
        'eeta-main',
        EETA_URI . '/assets/js/main.js',
        [],
        EETA_VERSION,
        true
    );

    // Gravity Forms — only enqueue if needed
    if ( class_exists( 'GFCommon' ) ) {
        wp_enqueue_style( 'gravity-forms-theme-framework' );
    }
} );
