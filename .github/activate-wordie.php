<?php
/* Activates the Wordie theme on first WordPress load then self-destructs. */
add_action( 'init', function () {
    if ( get_option( 'stylesheet' ) !== 'wordie' ) {
        switch_theme( 'wordie' );
    }
    @unlink( __FILE__ );
}, 1 );
