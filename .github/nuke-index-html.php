<?php
/**
 * MU-Plugin: Permanently kill index.html (2026-05-21-v2)
 *
 * Problem: LiteSpeed serves index.html as a static file to logged-out users,
 * bypassing .htaccess RewriteRules entirely. unlink() fails (permissions).
 *
 * Fix strategy:
 *  1. Overwrite index.html with an instant JS/meta redirect to /home/
 *     (so even if LiteSpeed serves it, the browser redirects immediately)
 *  2. Add .htaccess rules covering both ^$ and ^index\.html$
 *  3. Purge LiteSpeed cache for root URL via LS API
 *  4. Purge WP Engine cache via WP Engine helper
 *
 * New filename = fresh OPcache compilation.
 */

add_action( 'init', function () {

    $htaccess   = ABSPATH . '.htaccess';
    $index_html = ABSPATH . 'index.html';

    // ── 1. Overwrite index.html with instant redirect ─────────────────────────
    $redirect_content = '<!DOCTYPE html>'
        . '<html><head>'
        . '<meta charset="utf-8">'
        . '<meta http-equiv="refresh" content="0;url=/home/">'
        . '<script>window.location.replace("/home/");</script>'
        . '</head><body></body></html>';

    if ( file_exists( $index_html ) ) {
        $overwritten = file_put_contents( $index_html, $redirect_content );
        error_log( 'nuke-index-html: overwrite index.html=' . ( $overwritten !== false ? 'SUCCESS (' . $overwritten . ' bytes)' : 'FAILED' ) );
    }

    // Try unlink as well — works if permissions allow
    if ( file_exists( $index_html ) ) {
        @unlink( $index_html );
    }

    // ── 2. Inject .htaccess rules (both root and index.html paths) ────────────
    if ( file_exists( $htaccess ) && is_writable( $htaccess ) ) {
        $content = file_get_contents( $htaccess );
        if ( strpos( $content, '# BEGIN Wordie Kill Index HTML' ) === false ) {
            $rule = "# BEGIN Wordie Kill Index HTML\n"
                  . "<IfModule mod_rewrite.c>\n"
                  . "RewriteEngine On\n"
                  . "RewriteRule ^index\.html$ /home/ [R=301,L]\n"
                  . "RewriteRule ^$ /home/ [R=301,L]\n"
                  . "</IfModule>\n"
                  . "# END Wordie Kill Index HTML\n\n";
            file_put_contents( $htaccess, $rule . $content );
            error_log( 'nuke-index-html: .htaccess rules injected' );
        }
    }

    // ── 3. Purge LiteSpeed cache for root URL ─────────────────────────────────
    do_action( 'litespeed_purge_url', home_url( '/' ) );
    do_action( 'litespeed_purge_url', home_url( '/index.html' ) );

    // ── 4. Purge WP Engine platform cache ─────────────────────────────────────
    if ( function_exists( 'wpe_param' ) || class_exists( 'WpeCommon' ) ) {
        if ( class_exists( 'WpeCommon' ) ) {
            WpeCommon::purge_memcached();
            WpeCommon::clear_maxcdn_cache();
            WpeCommon::purge_varnish_cache();
        }
    }

} );

add_action( 'admin_notices', function () {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    $index_html = ABSPATH . 'index.html';
    $htaccess   = ABSPATH . '.htaccess';

    $gone       = ! file_exists( $index_html );
    $overwritten = $gone ? false : ( file_get_contents( $index_html ) !== false && strpos( file_get_contents( $index_html ), 'window.location.replace' ) !== false );
    $has_rule   = file_exists( $htaccess ) && strpos( file_get_contents( $htaccess ), '# BEGIN Wordie Kill Index HTML' ) !== false;

    if ( $gone && $has_rule ) {
        $color = 'success';
        $msg   = '✅ <strong>index.html deleted</strong> + .htaccess rules active. Root URL clean.';
    } elseif ( $overwritten && $has_rule ) {
        $color = 'warning';
        $msg   = '⚠️ <strong>index.html overwritten</strong> with redirect (delete failed) + .htaccess rules active. Root redirects via meta-refresh.';
    } elseif ( $has_rule ) {
        $color = 'warning';
        $msg   = '⚠️ .htaccess rule active. index.html exists but may be overwritten.';
    } else {
        $color = 'error';
        $msg   = '❌ <strong>nuke-index-html FAILED</strong> — check file permissions.';
    }

    echo '<div class="notice notice-' . esc_attr( $color ) . ' is-dismissible"><p>' . $msg . '</p></div>';
} );
