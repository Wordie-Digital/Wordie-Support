<?php
/* Sets permalink structure to /%postname%/ and flushes rewrite rules, then self-destructs. */
add_action( 'init', function () {
    update_option( 'permalink_structure', '/%postname%/' );
    flush_rewrite_rules( true );
    @unlink( __FILE__ );
}, 1 );
