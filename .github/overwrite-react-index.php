<?php
/**
 * MU-Plugin: Overwrite React app index.html with a redirect
 * Self-destructs after running.
 */
add_action( 'init', function () {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $index = ABSPATH . 'index.html';

    $redirect_html = '<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="refresh" content="0;url=/home/">
<title>Wordie</title>
</head>
<body>
<script>window.location.replace("/home/");</script>
</body>
</html>';

    $log = '';

    if ( file_exists( $index ) ) {
        $result = file_put_contents( $index, $redirect_html );
        if ( $result !== false ) {
            $log = 'SUCCESS: Overwrote ' . $index . ' (' . $result . ' bytes written)';
        } else {
            $log = 'FAILED: Could not write to ' . $index;
            // Try chmod first then retry
            @chmod( $index, 0644 );
            $result2 = file_put_contents( $index, $redirect_html );
            $log .= $result2 !== false ? ' | RETRY SUCCESS' : ' | RETRY ALSO FAILED';
        }
    } else {
        $log = 'NOT FOUND: ' . $index . ' — nothing to overwrite';
    }

    file_put_contents( WP_CONTENT_DIR . '/overwrite-react-log.txt', $log );
    @unlink( __FILE__ );
}, 1 );
