<?php
/**
 * Wordie — inc/acf-options.php
 *
 * Registers ACF Options pages for global/shared content.
 * These fields are NOT tied to individual blocks — they are sitewide.
 *
 * Options pages:
 *  - Theme Settings (parent)
 *    - Navigation Settings  (logo, nav CTA)
 *    - Footer Settings      (contact info, social links, footer nav)
 */

defined( 'ABSPATH' ) || exit;

add_action( 'acf/init', function () {

	if ( ! function_exists( 'acf_add_options_page' ) ) {
		return;
	}

	acf_add_options_page( [
		'page_title'  => __( 'Theme Settings', 'wordie' ),
		'menu_title'  => __( 'Theme Settings', 'wordie' ),
		'menu_slug'   => 'wordie-theme-settings',
		'capability'  => 'manage_options',
		'redirect'    => true,
		'icon_url'    => 'dashicons-admin-customizer',
		'position'    => 60,
		'autoload'    => true,
	] );

	acf_add_options_sub_page( [
		'page_title'  => __( 'Navigation Settings', 'wordie' ),
		'menu_title'  => __( 'Navigation', 'wordie' ),
		'menu_slug'   => 'wordie-navigation-settings',
		'parent_slug' => 'wordie-theme-settings',
		'capability'  => 'manage_options',
		'autoload'    => true,
	] );

	acf_add_options_sub_page( [
		'page_title'  => __( 'Footer Settings', 'wordie' ),
		'menu_title'  => __( 'Footer', 'wordie' ),
		'menu_slug'   => 'wordie-footer-settings',
		'parent_slug' => 'wordie-theme-settings',
		'capability'  => 'manage_options',
		'autoload'    => true,
	] );

} );
