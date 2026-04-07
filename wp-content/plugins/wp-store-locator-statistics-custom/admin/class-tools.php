<?php
/**
 * WPSL Statistics Tools class
 *
 * @since  1.1.0
 * @author Tijmen Smit
 */

if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'WPSL_Statistics_Tools' ) ) {

    class WPSL_Statistics_Tools {

        /**
         * Class constructor
         */
        function __construct() {
            add_action( 'wpsl_stats_tools', array( $this, 'statistics_tools_actions' ) );
        }

        /**
         * Handle the stats tools actions.
         *
         * @since 1.1.0
         * @return void
         */
        public function statistics_tools_actions() {
            if ( isset( $_POST['wpsl_stats_tools']['bulk_reset'] ) ) {
                $this->bulk_reset();
            }
        }

        /**
         * Bulk reset all statistics.
         *
         * @since 1.1.0
         * @return void
         */
        private function bulk_reset() {

            global $wpdb, $wpsl_admin;

            check_admin_referer( 'wpsl_stats_tools', 'wpsl_stats_tools_nonce' );

            if ( !current_user_can( 'wpsl_statistics_tools' ) ) {
                wp_die( __( 'You do not have permission to reset the statistics.', 'wpsl-statistics' ), '', array( 'response' => 403 ) );
            }

            if (
                false !== $wpdb->query( "DELETE FROM {$wpdb->wpsl_stats_term_relationships}" )
                && false !== $wpdb->query( "DELETE FROM {$wpdb->wpsl_stats_searches}" )
                && false !== $wpdb->query( "DELETE FROM {$wpdb->wpsl_stats_terms};" )
            ) {
                $wpsl_admin->notices->save( 'update', __( 'Statistics successfully reset.', 'wpsl-statistics' ) );
            } else {
                $wpsl_admin->notices->save( 'error', __( 'Something went wrong. Please try again.', 'wpsl-statistics' ) );
            }

            // If we don't force a redirect, then the notices don't show up...
            wp_redirect( admin_url( 'edit.php?post_type=wpsl_stores&page=wpsl_statistics&tab=tools' ) );
            exit();
        }

    }

    new WPSL_Statistics_Tools();
}
