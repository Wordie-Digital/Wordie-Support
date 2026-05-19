<?php
/**
 * MU-Plugin: Fix DirectoryIndex to prevent React app from intercepting root URL.
 * Prepends "DirectoryIndex index.php" to .htaccess so LiteSpeed serves WordPress
 * (index.php) instead of the React app (index.html) for requests to /.
 */
add_action( 'init', function () {
    $htaccess = ABSPATH . '.htaccess';

    $exists   = file_exists( $htaccess );
    $writable = is_writable( $htaccess );

    if ( ! $exists || ! $writable ) {
        error_log( "fix-directory-index: .htaccess not writable. exists={$exists} writable={$writable} path={$htaccess}" );
        return;
    }

    $content = file_get_contents( $htaccess );

    if ( strpos( $content, '# BEGIN Wordie DirectoryIndex Fix' ) !== false ) {
        error_log( 'fix-directory-index: already applied.' );
        return;
    }

    $prepend = "# BEGIN Wordie DirectoryIndex Fix\n" .
               "DirectoryIndex index.php\n" .
               "# END Wordie DirectoryIndex Fix\n\n";

    $result = file_put_contents( $htaccess, $prepend . $content );
    error_log( 'fix-directory-index: write result=' . var_export( $result, true ) );
} );

// Admin notice to show .htaccess state for diagnosis.
add_action( 'admin_notices', function () {
    if ( ! current_user_can( 'manage_options' ) ) return;
    $htaccess = ABSPATH . '.htaccess';
    $exists   = file_exists( $htaccess );
    $writable = is_writable( $htaccess );
    $applied  = $exists && strpos( file_get_contents( $htaccess ) ?: '', '# BEGIN Wordie DirectoryIndex Fix' ) !== false;
    $first_line = $exists ? strtok( file_get_contents( $htaccess ), "\n" ) : 'n/a';
    echo '<div class="notice notice-' . ( $applied ? 'success' : 'error' ) . '"><p>';
    echo '<strong>DirectoryIndex Fix:</strong> ';
    echo "htaccess exists={$exists} writable={$writable} fix_applied=" . ( $applied ? 'YES' : 'NO' );
    echo " | first_line=" . esc_html( $first_line );
    echo '</p></div>';
} );
