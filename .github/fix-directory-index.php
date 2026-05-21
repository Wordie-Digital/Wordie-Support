<?php
/**
 * MU-Plugin: Delete React app index.html from server root.
 *
 * The Resource Planner index.html lives at ABSPATH/index.html and takes
 * priority over index.php (WordPress) when LiteSpeed serves the root URL.
 * DirectoryIndex via .htaccess is ignored by WP Engine's server-level config.
 *
 * This plugin directly deletes the file so WordPress index.php handles /.
 * Runs on every init until the file is gone, then becomes a no-op.
 */
add_action( 'init', function () {

    $index_html = ABSPATH . 'index.html';

    if ( ! file_exists( $index_html ) ) {
        return; // already gone — nothing to do
    }

    $deleted = @unlink( $index_html );
    error_log( 'fix-directory-index: delete index.html result=' . ( $deleted ? 'SUCCESS' : 'FAILED' ) . ' path=' . $index_html );

    // Also ensure .htaccess has DirectoryIndex index.php as a belt-and-braces backup
    $htaccess = ABSPATH . '.htaccess';
    if ( file_exists( $htaccess ) && is_writable( $htaccess ) ) {
        $content = file_get_contents( $htaccess );
        if ( strpos( $content, '# BEGIN Wordie DirectoryIndex Fix' ) === false ) {
            $prepend = "# BEGIN Wordie DirectoryIndex Fix\nDirectoryIndex index.php\n# END Wordie DirectoryIndex Fix\n\n";
            file_put_contents( $htaccess, $prepend . $content );
        }
    }

} );

// Admin notice — shows delete status and file existence
add_action( 'admin_notices', function () {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    $index_html = ABSPATH . 'index.html';
    $exists     = file_exists( $index_html );
    $color      = $exists ? 'error' : 'success';
    $msg        = $exists
        ? '⚠️ <strong>index.html still exists</strong> at ' . esc_html( $index_html ) . ' — delete failed (check PHP file permissions).'
        : '✅ <strong>index.html deleted</strong> — WordPress is now served at the root URL.';
    echo '<div class="notice notice-' . $color . ' is-dismissible"><p>' . $msg . '</p></div>';
} );
