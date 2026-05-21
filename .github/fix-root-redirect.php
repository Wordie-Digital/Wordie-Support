<?php
/**
 * MU-Plugin: Redirect root / → /home/ via .htaccess RewriteRule (2026-05-21)
 *
 * Injects a RewriteRule at the top of .htaccess so root requests redirect
 * to /home/ at the LiteSpeed rewrite phase — before DirectoryIndex or
 * index.html is served. Also attempts to unlink ABSPATH/index.html.
 *
 * Brand-new filename so OPcache compiles it fresh (bypasses stale bytecode).
 */
add_action( 'init', function () {

    $htaccess   = ABSPATH . '.htaccess';
    $index_html = ABSPATH . 'index.html';

    // ── 1. Inject root redirect RewriteRule ───────────────────────────────────
    if ( file_exists( $htaccess ) && is_writable( $htaccess ) ) {
        $content = file_get_contents( $htaccess );
        if ( strpos( $content, '# BEGIN Wordie Root Redirect' ) === false ) {
            $rule = "# BEGIN Wordie Root Redirect\n"
                  . "<IfModule mod_rewrite.c>\n"
                  . "RewriteEngine On\n"
                  . "RewriteRule ^$ /home/ [R=301,L]\n"
                  . "</IfModule>\n"
                  . "# END Wordie Root Redirect\n\n";
            file_put_contents( $htaccess, $rule . $content );
            error_log( 'fix-root-redirect: RewriteRule injected into .htaccess' );
        }
    }

    // ── 2. Try to delete index.html ───────────────────────────────────────────
    if ( file_exists( $index_html ) ) {
        $deleted = @unlink( $index_html );
        error_log( 'fix-root-redirect: unlink index.html=' . ( $deleted ? 'SUCCESS' : 'FAILED' ) );
    }

} );

add_action( 'admin_notices', function () {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    $index_html = ABSPATH . 'index.html';
    $htaccess   = ABSPATH . '.htaccess';
    $has_rule   = file_exists( $htaccess ) && strpos( file_get_contents( $htaccess ), '# BEGIN Wordie Root Redirect' ) !== false;
    $html_gone  = ! file_exists( $index_html );

    if ( $has_rule && $html_gone ) {
        $color = 'success';
        $msg   = '✅ <strong>Root Redirect active</strong> — RewriteRule injected &amp; index.html deleted.';
    } elseif ( $has_rule ) {
        $color = 'warning';
        $msg   = '⚠️ <strong>Root Redirect active</strong> — RewriteRule injected (index.html still exists but bypassed).';
    } else {
        $color = 'error';
        $msg   = '❌ <strong>Root Redirect FAILED</strong> — .htaccess not writable or injection failed.';
    }

    echo '<div class="notice notice-' . $color . ' is-dismissible"><p>' . $msg . '</p></div>';
} );
