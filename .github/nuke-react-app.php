<?php
/**
 * MU-Plugin: Nuke React Resource Planner
 * Self-destructs after running. One-shot only.
 */
add_action( 'init', function () {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $root    = ABSPATH; // WordPress document root
    $deleted = [];
    $failed  = [];
    $skipped = [];

    // Everything WordPress owns in the document root — keep these
    $wp_files = [
        'index.php', 'wp-activate.php', 'wp-blog-header.php',
        'wp-comments-post.php', 'wp-config.php', 'wp-config-sample.php',
        'wp-cron.php', 'wp-links-opml.php', 'wp-load.php', 'wp-login.php',
        'wp-mail.php', 'wp-settings.php', 'wp-signup.php',
        'wp-trackback.php', 'xmlrpc.php', '.htaccess', 'wp-admin',
        'wp-content', 'wp-includes', 'wordie-index.html',
    ];

    // Recursively delete a directory
    function nuke_dir( $dir, &$deleted, &$failed ) {
        if ( ! is_dir( $dir ) ) {
            return;
        }
        foreach ( scandir( $dir ) as $item ) {
            if ( $item === '.' || $item === '..' ) {
                continue;
            }
            $path = $dir . '/' . $item;
            if ( is_dir( $path ) ) {
                nuke_dir( $path, $deleted, $failed );
                if ( @rmdir( $path ) ) {
                    $deleted[] = $path;
                } else {
                    $failed[] = $path;
                }
            } else {
                if ( @unlink( $path ) ) {
                    $deleted[] = $path;
                } else {
                    $failed[] = $path;
                }
            }
        }
    }

    // Scan root for non-WP files
    foreach ( scandir( $root ) as $item ) {
        if ( $item === '.' || $item === '..' ) {
            continue;
        }
        if ( in_array( $item, $wp_files, true ) ) {
            $skipped[] = $item;
            continue;
        }
        $path = rtrim( $root, '/' ) . '/' . $item;
        if ( is_dir( $path ) ) {
            nuke_dir( $path, $deleted, $failed );
            if ( @rmdir( $path ) ) {
                $deleted[] = $path;
            } else {
                $failed[] = $path;
            }
        } else {
            if ( @unlink( $path ) ) {
                $deleted[] = $item;
            } else {
                $failed[] = $item;
            }
        }
    }

    // Log results then self-destruct
    $log  = "=== Nuke React App — " . date( 'Y-m-d H:i:s' ) . " ===\n";
    $log .= "DELETED (" . count( $deleted ) . "):\n" . implode( "\n", $deleted ) . "\n";
    $log .= "FAILED ("  . count( $failed  ) . "):\n" . implode( "\n", $failed  ) . "\n";
    $log .= "KEPT ("    . count( $skipped ) . "):\n" . implode( "\n", $skipped ) . "\n";
    file_put_contents( WP_CONTENT_DIR . '/nuke-react-log.txt', $log );

    @unlink( __FILE__ );
}, 1 );
