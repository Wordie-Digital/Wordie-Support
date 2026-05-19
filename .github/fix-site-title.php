<?php
/* Updates site title and tagline then self-destructs. */
add_action( 'init', function () {
    update_option( 'blogname', 'Wordie' );
    update_option( 'blogdescription', 'Design-led WordPress development, powered by AI.' );
    @unlink( __FILE__ );
}, 1 );
