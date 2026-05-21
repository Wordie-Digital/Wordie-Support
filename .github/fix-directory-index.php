<?php
/**
 * MU-Plugin: Kill React app index.html — v3 (RewriteRule + unlink, 2026-05-21)
 *
 * Two-pronged attack:
 *  1. RewriteRule redirect  — injected into .htaccess so root / → /home/ at the
 *     LiteSpeed rewrite phase, BEFORE DirectoryIndex or index.html is evaluated.
 *     This works even when unlink() is blocked.
 *  2. unlink()              — directly deletes ABSPATH/index.html when PHP can.
 *
 * Both are idempotent — safe to run on every init request.
 */
add_action( 'init', function () {

    $htaccess   = ABSPATH . '.htaccess';
    $index_html = ABSPATH . 'index.html';

    // ── 1. Inject RewriteRule redirect ────────────────────────────────────────
    if ( file_exists( $htaccess ) && is_writable( $htaccess ) ) {
        $content = file_get_contents( $htaccess );

        if ( strpos( $content, '# BEGIN Wordie Root Redirect' ) === false ) {
            // Prepend BEFORE WordPress rules so it fires first.
            $rule = "# BEGIN Wordie Root Redirect\n"
                  . "<IfModule mod_rewrite.c>\n"
                  . "RewriteEngine On\n"
                  . "RewriteRule ^$ /home/ [R=301,L]\n"
                  . "</IfModule>\n"
                  . "# END Wordie Root Redirect\n\n";
            file_put_contents( $htaccess, $rule . $content );
            error_log( 'fix-directory-index: RewriteRule root redirect injected into .htaccess' );
        }

        // Also keep DirectoryIndex fix in case it helps
        if ( strpos( $content, '# BEGIN Wordie DirectoryIndex Fix' ) === false ) {
            $content  = file_get_contents( $htaccess ); // re-read after possible prepend above
            $di_block = "# BEGIN Wordie DirectoryIndex Fix\nDirectoryIndex index.php\n# END Wordie DirectoryIndex Fix\n\n";
            file_put_contents( $htaccess, $di_block . $content );
        }
    }

    // ── 2. Try to delete index.html directly ─────────────────────────────────
    if ( file_exists( $index_html ) ) {
        $deleted = @unlink( $index_html );
        error_log( 'fix-directory-index: unlink index.html=' . ( $deleted ? 'SUCCESS' : 'FAILED' ) . ' path=' . $index_html );
    }

} );

// Admin notice — v3 marker + current state
add_action( 'admin_notices', function () {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $index_html = ABSPATH . 'index.html';
    $htaccess   = ABSPATH . '.htaccess';
    $exists     = file_exists( $index_html );
    $has_rule   = file_exists( $htaccess ) && ( strpos( file_get_contents( $htaccess ), '# BEGIN Wordie Root Redirect' ) !== false );

    if ( ! $exists && $has_rule ) {
        $color = 'success';
        $msg   = '✅ <strong>Root fix v3 active</strong> — index.html deleted &amp; RewriteRule redirect in .htaccess.';
    } elseif ( $has_rule ) {
        $color = 'warning';
        $msg   = '⚠️ <strong>Root fix v3</strong> — RewriteRule redirect active (index.html still present but redirected away). Path: ' . esc_html( $index_html );
    } else {
        $color = 'error';
        $msg   = '❌ <strong>Root fix v3 FAILED</strong> — neither index.html deleted nor RewriteRule injected. Check .htaccess permissions.';
    }

    echo '<div class="notice notice-' . $color . ' is-dismissible"><p>' . $msg . '</p></div>';
} );
