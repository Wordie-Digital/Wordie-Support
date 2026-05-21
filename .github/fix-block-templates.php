<?php
/**
 * MU-Plugin: Fix Block Templates (OPcache bypass)
 *
 * WP Engine runs OPcache with validate_timestamps=0 — PHP file changes are
 * never picked up until the cache is explicitly purged or a new filename is
 * compiled for the first time. This plugin redirects WordPress to load
 * front-page-v2.php instead of front-page.php on the homepage so that the
 * corrected block templates (template-v2.php) are included instead of the
 * stale cached originals.
 *
 * Safe to keep permanently — it's a no-op once all templates are on -v2.
 */

defined( 'ABSPATH' ) || exit;

add_filter( 'template_include', function( $template ) {
	if ( is_front_page() ) {
		$new = get_template_directory() . '/front-page-v2.php';
		if ( file_exists( $new ) ) {
			return $new;
		}
	}
	return $template;
}, 99 );
