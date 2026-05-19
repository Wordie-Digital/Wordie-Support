<?php
/**
 * Removes the React Resource Planner app files from the document root
 * so WordPress can serve all front-end requests, then self-destructs.
 */
add_action( 'init', function () {
	$root = untrailingslashit( ABSPATH );

	$targets = [
		$root . '/index.html',
		$root . '/asset-manifest.json',
		$root . '/manifest.json',
		$root . '/robots.txt',
		$root . '/favicon.ico',
		$root . '/logo192.png',
		$root . '/logo512.png',
	];

	foreach ( $targets as $file ) {
		if ( file_exists( $file ) ) {
			@unlink( $file );
		}
	}

	// Remove static/ directory (React build assets).
	$static_dir = $root . '/static';
	if ( is_dir( $static_dir ) ) {
		$it = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator( $static_dir, RecursiveDirectoryIterator::SKIP_DOTS ),
			RecursiveIteratorIterator::CHILD_FIRST
		);
		foreach ( $it as $item ) {
			$item->isDir() ? @rmdir( $item->getRealPath() ) : @unlink( $item->getRealPath() );
		}
		@rmdir( $static_dir );
	}

	@unlink( __FILE__ );
}, 1 );
