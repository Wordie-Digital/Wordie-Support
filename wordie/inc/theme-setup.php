<?php
/**
 * Wordie — inc/theme-setup.php
 *
 * Core theme support declarations, image sizes, and nav menu registration.
 */

defined( 'ABSPATH' ) || exit;

add_action( 'after_setup_theme', function () {

	load_theme_textdomain( 'wordie', WORDIE_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', [ 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ] );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'custom-logo', [
		'height'      => 96,
		'width'       => 256,
		'flex-height' => true,
		'flex-width'  => true,
	] );

	// Disable WordPress core block patterns — not needed with ACF block system
	remove_theme_support( 'core-block-patterns' );

	register_nav_menus( [
		'primary'      => __( 'Primary Navigation', 'wordie' ),
		'footer'       => __( 'Footer Navigation', 'wordie' ),
		'footer-legal' => __( 'Footer Legal Links', 'wordie' ),
	] );

	// Image sizes derived from Figma design (border-radius: 12px images)
	add_image_size( 'wordie-hero',        1440, 810,  true );
	add_image_size( 'wordie-card',        600,  400,  true );
	add_image_size( 'wordie-card-wide',   740,  557,  true );
	add_image_size( 'wordie-thumbnail',   360,  282,  true );
	add_image_size( 'wordie-portrait',    662,  441,  true );

} );
