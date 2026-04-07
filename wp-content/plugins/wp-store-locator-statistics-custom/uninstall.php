<?php
if ( !defined( 'ABSPATH' ) && !defined( 'WP_UNINSTALL_PLUGIN ') ) {
	exit;
}

// Check if we need to run the uninstall for a single or mu installation.
if ( !is_multisite() ) {
    wpsl_stats_uninstall();
} else {

    global $wpdb;
    
    $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
    $original_blog_id = get_current_blog_id();
    
    foreach ( $blog_ids as $blog_id ) {
        switch_to_blog( $blog_id );
        wpsl_stats_uninstall();  
    }
    
    switch_to_blog( $original_blog_id );
}

/**
 * Remove the used stats data.
 * 
 * @since  1.0.0
 * @return void
 */
function wpsl_stats_uninstall() {
    
    global $wpdb;

    // Remove the stats caps.
    include_once( 'admin/roles.php' );
    wpsl_stats_remove_caps_and_roles();

    // Remove the db tables
    $wpdb->query( 'DROP TABLE IF EXISTS ' . $wpdb->prefix . 'wpsl_stats_searches' );
    $wpdb->query( 'DROP TABLE IF EXISTS ' . $wpdb->prefix . 'wpsl_stats_term_relationships' );
    $wpdb->query( 'DROP TABLE IF EXISTS ' . $wpdb->prefix . 'wpsl_stats_terms' );

    // Remove the stats option.
    delete_option( 'wpsl_stats_version' );
}