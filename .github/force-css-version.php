<?php
/**
 * MU-Plugin: Force CSS version via filemtime (2026-05-21)
 *
 * OPcache serves stale functions.php bytecode so WORDIE_VERSION never updates.
 * filemtime() reads from the filesystem at runtime — bypasses OPcache entirely.
 * Deregisters all wordie-* styles and re-registers with fresh file timestamps.
 *
 * New filename = OPcache compiles this fresh on first load.
 */
add_action( 'wp_enqueue_scripts', function () {

    $theme_dir = get_template_directory();
    $theme_uri = get_template_directory_uri();
    $css_dir   = $theme_dir . '/assets/css';

    // Global stylesheets
    $globals = [
        'wordie-global'     => '/global.css',
        'wordie-utilities'  => '/utilities.css',
        'wordie-site-footer' => '/site-footer.css',
    ];

    foreach ( $globals as $handle => $file ) {
        $path = $css_dir . $file;
        if ( wp_style_is( $handle, 'enqueued' ) && file_exists( $path ) ) {
            wp_deregister_style( $handle );
            wp_enqueue_style( $handle, $theme_uri . '/assets/css' . $file, [], filemtime( $path ) );
        }
    }

    // Block stylesheets (all files in assets/css/blocks/)
    $blocks_dir = $css_dir . '/blocks';
    foreach ( glob( $blocks_dir . '/*.css' ) as $css_file ) {
        $handle = 'wordie-' . basename( $css_file, '.css' );
        if ( wp_style_is( $handle, 'enqueued' ) ) {
            wp_deregister_style( $handle );
            wp_enqueue_style(
                $handle,
                $theme_uri . '/assets/css/blocks/' . basename( $css_file ),
                [ 'wordie-global' ],
                filemtime( $css_file )
            );
        }
    }

}, 99 );
