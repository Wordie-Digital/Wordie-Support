<?php
/**
 * Wordie — functions.php
 *
 * Bootstraps theme setup, ACF sync, block registration, and asset enqueueing.
 */

defined( 'ABSPATH' ) || exit;

define( 'WORDIE_VERSION', '1.0.0' );
define( 'WORDIE_DIR', get_template_directory() );
define( 'WORDIE_URI', get_template_directory_uri() );

// ---------------------------------------------------------------------------
// Required files
// ---------------------------------------------------------------------------
require_once WORDIE_DIR . '/inc/theme-setup.php';
require_once WORDIE_DIR . '/inc/block-registration.php';
require_once WORDIE_DIR . '/inc/acf-options.php';
require_once WORDIE_DIR . '/inc/ai-endpoints.php';

// ---------------------------------------------------------------------------
// ACF local JSON — save / load paths
// ---------------------------------------------------------------------------
add_filter( 'acf/settings/save_json', function () {
	return WORDIE_DIR . '/acf-fields';
} );

add_filter( 'acf/settings/load_json', function ( $paths ) {
	$paths[] = WORDIE_DIR . '/acf-fields';
	return $paths;
} );

// ---------------------------------------------------------------------------
// Enqueue front-end assets
// ---------------------------------------------------------------------------
add_action( 'wp_enqueue_scripts', function () {

	wp_enqueue_style(
		'wordie-global',
		WORDIE_URI . '/assets/css/global.css',
		[],
		WORDIE_VERSION
	);

	wp_enqueue_style(
		'wordie-utilities',
		WORDIE_URI . '/assets/css/utilities.css',
		[ 'wordie-global' ],
		WORDIE_VERSION
	);

	// Navigation JS
	wp_enqueue_script(
		'wordie-navigation',
		WORDIE_URI . '/assets/js/navigation.js',
		[],
		WORDIE_VERSION,
		true
	);

	// Work Section carousel JS — only when block is present
	if ( is_singular() && has_block( 'acf/work-section' ) ) {
		wp_enqueue_script(
			'wordie-work-section',
			WORDIE_URI . '/assets/js/work-section.js',
			[],
			WORDIE_VERSION,
			true
		);
	}

	// Testimonial carousel JS — only when block is present
	if ( is_singular() && has_block( 'acf/testimonial' ) ) {
		wp_enqueue_script(
			'wordie-testimonial',
			WORDIE_URI . '/assets/js/testimonial.js',
			[],
			WORDIE_VERSION,
			true
		);
	}

} );

// ---------------------------------------------------------------------------
// Fix DirectoryIndex: prevent React app index.html from intercepting root URL.
// Prepends DirectoryIndex index.php to .htaccess so LiteSpeed serves WordPress
// for / requests instead of the static React app. Runs once and is idempotent.
// ---------------------------------------------------------------------------
add_action( 'init', function () {
	$htaccess = ABSPATH . '.htaccess';
	if ( ! file_exists( $htaccess ) || ! is_writable( $htaccess ) ) {
		return;
	}
	$content = file_get_contents( $htaccess );
	if ( false !== strpos( $content, '# BEGIN Wordie DirectoryIndex Fix' ) ) {
		return;
	}
	$prepend = "# BEGIN Wordie DirectoryIndex Fix\nDirectoryIndex index.php\n# END Wordie DirectoryIndex Fix\n\n";
	file_put_contents( $htaccess, $prepend . $content );
} );

// ---------------------------------------------------------------------------
// Enqueue block editor assets
// ---------------------------------------------------------------------------
add_action( 'enqueue_block_editor_assets', function () {
	wp_enqueue_style(
		'wordie-editor',
		WORDIE_URI . '/assets/css/editor.css',
		[ 'wp-edit-blocks' ],
		WORDIE_VERSION
	);
} );
