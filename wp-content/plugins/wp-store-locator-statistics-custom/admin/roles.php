<?php
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Add WPSL Statistics caps.
 *
 * @since 1.0.0
 * @return void
 */
function wpsl_stats_add_caps() {
    
    global $wp_roles;

    if ( class_exists( 'WP_Roles' ) ) {
        if ( !isset( $wp_roles ) ) {
            $wp_roles = new WP_Roles();
        }
    }

	if ( is_object( $wp_roles ) ) {
        $capabilities = wpsl_stats_get_post_caps();

        foreach ( $capabilities as $cap ) {
            $wp_roles->add_cap( 'wpsl_store_locator_manager', $cap );
            $wp_roles->add_cap( 'administrator',              $cap );
        }
    }
}

/**
 * Get the WPSL Statistics capabilities.
 *
 * @since 1.0.0
 * @return array $capabilities The statistics capabilities
 */
function wpsl_stats_get_post_caps() {

    $capabilities = array(
        'wpsl_statistics',
        'wpsl_statistics_export',
        'wpsl_statistics_tools'
    );

    return $capabilities;
}

/**
 * Remove the WPSL Statistics caps and roles.
 * 
 * Only called from uninstall.php
 *
 * @since 1.0.0
 * @return void
 */
function wpsl_stats_remove_caps_and_roles() {
      
    global $wp_roles;

    if ( class_exists( 'WP_Roles' ) ) {
        if ( !isset( $wp_roles ) ) {
            $wp_roles = new WP_Roles();
        }
    }
    
    if ( is_object( $wp_roles ) ) {
        $capabilities = wpsl_stats_get_post_caps();
        
        foreach ( $capabilities as $cap ) {
            $wp_roles->remove_cap( 'wpsl_store_locator_manager', $cap );
            $wp_roles->remove_cap( 'administrator',              $cap );
        }
    }
}