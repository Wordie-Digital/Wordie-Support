<?php
/**
 * Meadan Homes — inc/acf-options.php
 *
 * Registers ACF Options pages:
 *  - Theme Settings (parent)
 *    - Footer Settings (sub-page)
 *    - Global Design (sub-page)
 */

defined( 'ABSPATH' ) || exit;

add_action( 'acf/init', function () {

    if ( ! function_exists( 'acf_add_options_page' ) ) {
        return;
    }

    // -------------------------------------------------------------------------
    // Parent options page — Theme Settings
    // -------------------------------------------------------------------------
    acf_add_options_page( [
        'page_title'  => __( 'Theme Settings', 'meadan' ),
        'menu_title'  => __( 'Theme Settings', 'meadan' ),
        'menu_slug'   => 'theme-settings',
        'capability'  => 'manage_options',
        'redirect'    => true,
        'icon_url'    => 'dashicons-admin-customizer',
        'position'    => 60,
        'autoload'    => true,
    ] );

    // -------------------------------------------------------------------------
    // Sub-page — Footer Settings
    // -------------------------------------------------------------------------
    acf_add_options_sub_page( [
        'page_title'  => __( 'Footer Settings', 'meadan' ),
        'menu_title'  => __( 'Footer Settings', 'meadan' ),
        'menu_slug'   => 'footer-settings',
        'parent_slug' => 'theme-settings',
        'capability'  => 'manage_options',
        'autoload'    => true,
    ] );

    // -------------------------------------------------------------------------
    // Sub-page — Global Design
    // -------------------------------------------------------------------------
    acf_add_options_sub_page( [
        'page_title'  => __( 'Global Design', 'meadan' ),
        'menu_title'  => __( 'Global Design', 'meadan' ),
        'menu_slug'   => 'global-design',
        'parent_slug' => 'theme-settings',
        'capability'  => 'manage_options',
        'autoload'    => true,
    ] );

    // -------------------------------------------------------------------------
    // EETA — Essential Energy Training Academy options
    // -------------------------------------------------------------------------
    acf_add_options_page( [
        'page_title'  => __( 'EETA Settings', 'meadan' ),
        'menu_title'  => __( 'EETA Settings', 'meadan' ),
        'menu_slug'   => 'eeta-settings',
        'capability'  => 'manage_options',
        'redirect'    => true,
        'icon_url'    => 'dashicons-welcome-learn-more',
        'position'    => 61,
        'autoload'    => true,
    ] );

    acf_add_options_sub_page( [
        'page_title'  => __( 'EETA Header', 'meadan' ),
        'menu_title'  => __( 'Header', 'meadan' ),
        'menu_slug'   => 'eeta-settings-header',
        'parent_slug' => 'eeta-settings',
        'capability'  => 'manage_options',
        'autoload'    => true,
    ] );

    acf_add_options_sub_page( [
        'page_title'  => __( 'EETA Footer', 'meadan' ),
        'menu_title'  => __( 'Footer', 'meadan' ),
        'menu_slug'   => 'eeta-settings-footer',
        'parent_slug' => 'eeta-settings',
        'capability'  => 'manage_options',
        'autoload'    => true,
    ] );

} );
