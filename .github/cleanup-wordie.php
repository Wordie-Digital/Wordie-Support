<?php
/* Removes incorrectly deployed Wordie files from wordiev2 then self-destructs. */
add_action( 'init', function () {
    $base = WP_CONTENT_DIR;
    @unlink( $base . '/mu-plugins/activate-wordie.php' );
    $theme_dir = $base . '/themes/wordie';
    if ( is_dir( $theme_dir ) ) {
        foreach ( new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator( $theme_dir, FilesystemIterator::SKIP_DOTS ),
            RecursiveIteratorIterator::CHILD_FIRST
        ) as $f ) {
            $f->isDir() ? @rmdir( $f->getRealPath() ) : @unlink( $f->getRealPath() );
        }
        @rmdir( $theme_dir );
    }
    @unlink( __FILE__ );
}, 1 );
