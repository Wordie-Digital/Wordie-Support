<?php
add_action( 'admin_init', 'wpsl_stats_check_upgrade' );

/**
 * If the db doesn't hold the current version, run the upgrade procedure
 *
 * @since 1.1.0
 * @return void
 */
function wpsl_stats_check_upgrade() {

    $current_version = get_option( 'wpsl_stats_version' );

    if ( version_compare( $current_version, WPSL_STATISTICS_VERSION_NUM, '==' ) )
        return;

    if ( version_compare( $current_version, '1.1.0', '<' ) ) {
        require_once( WPSL_STATISTICS_PLUGIN_DIR . 'admin/roles.php' );

        wpsl_stats_add_caps();
    }

    update_option( 'wpsl_stats_version', WPSL_STATISTICS_VERSION_NUM );
}