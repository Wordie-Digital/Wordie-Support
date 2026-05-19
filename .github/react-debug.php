<?php
/**
 * MU-Plugin: Debug React removal — check file existence + permissions
 */
add_action( 'admin_notices', function () {
    if ( ! current_user_can( 'manage_options' ) ) return;

    $index = ABSPATH . 'index.html';
    $exists   = file_exists( $index );
    $readable = is_readable( $index );
    $writable = is_writable( $index );
    $perms    = $exists ? substr( sprintf( '%o', fileperms( $index ) ), -4 ) : 'n/a';
    $owner    = $exists && function_exists('posix_getpwuid') ? @posix_getpwuid( fileowner( $index ) )['name'] : 'unknown';
    $phpuser  = function_exists('get_current_user') ? get_current_user() : ( function_exists('posix_getpwuid') ? @posix_getpwuid( posix_geteuid() )['name'] : 'unknown' );

    // Try overwrite
    $write_test = @file_put_contents( $index, file_get_contents( $index ) ?: '' );

    echo '<div class="notice notice-warning"><p>';
    echo '<strong>React Debug:</strong> ';
    echo "ABSPATH={$index} | exists=" . ( $exists ? 'YES' : 'NO' );
    echo " | readable=" . ( $readable ? 'YES' : 'NO' );
    echo " | writable=" . ( $writable ? 'YES' : 'NO' );
    echo " | perms={$perms} | owner={$owner} | phpuser={$phpuser}";
    echo " | write_test=" . ( $write_test !== false ? 'SUCCESS('.$write_test.')' : 'FAILED' );
    echo '</p></div>';
} );
