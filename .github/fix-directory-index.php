<?php
/**
 * MU-Plugin: Fix DirectoryIndex to prevent React app from intercepting root URL.
 * Prepends "DirectoryIndex index.php" to .htaccess so LiteSpeed serves WordPress
 * (index.php) instead of the React app (index.html) for requests to /.
 */
add_action( 'init', function () {
    $htaccess = ABSPATH . '.htaccess';

    if ( ! file_exists( $htaccess ) || ! is_writable( $htaccess ) ) {
        error_log( 'fix-directory-index: .htaccess not writable at ' . $htaccess );
        return;
    }

    $content = file_get_contents( $htaccess );

    if ( strpos( $content, '# BEGIN Wordie DirectoryIndex Fix' ) !== false ) {
        return; // Already applied.
    }

    $prepend = "# BEGIN Wordie DirectoryIndex Fix\n" .
               "DirectoryIndex index.php\n" .
               "# END Wordie DirectoryIndex Fix\n\n";

    $result = file_put_contents( $htaccess, $prepend . $content );

    error_log( 'fix-directory-index: file_put_contents result=' . var_export( $result, true ) );
} );
