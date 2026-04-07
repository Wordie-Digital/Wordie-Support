<?php
/**
 * WPSL Statistics Install.
 *
 * @author Tijmen Smit
 * @since  1.0.0
 */

if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Run the install.
 *
 * @since 1.0.0
 * @param  boolean $network_wide True if "Network Activate" action is used in WPMU.
 * @return void
 */
function wpsl_statistics_install( $network_wide ) {
    
    global $wpdb;

    require_once( WPSL_STATISTICS_PLUGIN_DIR . 'admin/roles.php' );

    if ( function_exists( 'is_multisite' ) && is_multisite() ) {

        if ( $network_wide ) {
            $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

            foreach ( $blog_ids as $blog_id ) {
                switch_to_blog( $blog_id );

                wpsl_setup_statistics_data();
            }

            restore_current_blog();
        } else {
            wpsl_setup_statistics_data();
        }
    } else {
        wpsl_setup_statistics_data();
    }
}

/**
 * Setup the required data for
 * the statistics plugin to run.
 *
 * @since 1.0.0
 * @return void
 */
function wpsl_setup_statistics_data() {

    wpsl_create_statistics_tables();
    wpsl_stats_add_caps();

    update_option( 'wpsl_stats_version', WPSL_STATISTICS_VERSION_NUM );
}

/**
 * Create the statistics db tables.
 *
 * @since 1.0.0
 * @return void
 */
function wpsl_create_statistics_tables() {
    
    global $wpdb;
    
    $wpdb->wpsl_stats_searches           = $wpdb->prefix . 'wpsl_stats_searches';
    $wpdb->wpsl_stats_term_relationships = $wpdb->prefix . 'wpsl_stats_term_relationships';
    $wpdb->wpsl_stats_terms              = $wpdb->prefix . 'wpsl_stats_terms';    
    
    $charset_collate = '';
    
    if ( !empty( $wpdb->charset ) )
        $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
    if ( !empty( $wpdb->collate ) )
        $charset_collate .= " COLLATE $wpdb->collate";
    
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    
    // Create the statistics table.
    if ( $wpdb->get_var( "SHOW TABLES LIKE '$wpdb->wpsl_stats_searches'" ) != $wpdb->wpsl_stats_searches ) {
        $sql = "CREATE TABLE $wpdb->wpsl_stats_searches (
                             search_id bigint(20) unsigned NOT NULL auto_increment,
                             search_location varchar(255) NOT NULL default '',
                             search_date datetime NOT NULL default '0000-00-00 00:00:00',
                             lat float(10,6) NOT NULL,
                             lng float(10,6) NOT NULL,
                 PRIMARY KEY (search_id)
                          ) $charset_collate;";
        
        dbDelta( $sql );
    }
    
    // Create the statistics term relationships table.
    if ( $wpdb->get_var( "SHOW TABLES LIKE '$wpdb->wpsl_stats_term_relationships'" ) != $wpdb->wpsl_stats_term_relationships ) {
        $sql = "CREATE TABLE $wpdb->wpsl_stats_term_relationships (
                             search_id bigint(20) unsigned NOT NULL default 0,
                             term_id bigint(20) unsigned NOT NULL default 0,
                 PRIMARY KEY (search_id,term_id),
                         KEY term_id (term_id)
                          ) $charset_collate;";
    
        dbDelta( $sql );
    }
    
    // Create the table that keeps track of the used wpsl term names. 
    if ( $wpdb->get_var( "SHOW TABLES LIKE '$wpdb->wpsl_stats_terms'" ) != $wpdb->wpsl_stats_terms ) {
        $sql = "CREATE TABLE $wpdb->wpsl_stats_terms (
                             term_id bigint(20) unsigned NOT NULL,
                             name varchar(200) NOT NULL default '',
                 PRIMARY KEY (term_id),
                         KEY name (name)
               ) $charset_collate;";
    
        dbDelta( $sql );
    }
    
    wpsl_sync_terms();
}

/**
 * Copy any existing WPSL terms to a separate stats table.
 * 
 * We do this to make sure we can always show the correct term name 
 * in case the user decides to delete the original term.
 * 
 * Otherwise we can end up with a term_id in the stats table, 
 * but no name to match it with.
 *
 * @since 1.0.0
 * @return void
 */
function wpsl_sync_terms() {
    
    global $wpdb;
    
    $wpdb->wpsl_stats_terms = $wpdb->prefix . 'wpsl_stats_terms'; 

    $stats_terms = $wpdb->get_results( "SELECT term_id, name FROM $wpdb->wpsl_stats_terms" );

    // If the stats terms table is empty, then copy the 'wpsl_store_category' terms.
    if ( ! $stats_terms ) {

        // Check if we only need to get the normal terms, or also the translated one.
        if ( !function_exists( 'icl_register_string' ) ) {
            $wpsl_terms = wpsl_stats_get_terms();
        } else {
            $wpsl_terms = wpsl_stats_get_wpml_terms();
        }

        $values = array();

        foreach ( $wpsl_terms as $term_id => $term_name ) {
            $values[] = $wpdb->prepare( "(%d, %s)", $term_id, $term_name );  
        }

        if ( $values ) {
            $wpdb->query( "INSERT INTO $wpdb->wpsl_stats_terms (term_id, name) VALUES " . join( ',', $values ) . "" );
        }
    }
}

/**
 * Get the available WPSL terms
 *
 * @since 1.0.0
 * @return array $terms The available $terms
 */
function wpsl_stats_get_terms() {

    global $wp_version;

    $wp45plus = ( version_compare( $wp_version, '4.5.0', '>=' ) ) ? true : false;

    // Get the terms based on the WP version.
    if ( $wp45plus ) {
        $terms = get_terms( array(
            'taxonomy'   => [ 'wpsl_store_category', 'dealer_service' ],
            'fields'     => 'id=>name',
            'hide_empty' => false
        ) );
    } else {
        $terms = get_terms( 'wpsl_store_category', array(
            'fields' => 'id=>name',
            'hide_empty' => false
        ) );
    }

    return $terms;
}

/**
 * Get the WPSL terms translated with WPML
 *
 * @since 1.0.0
 * @return array $wpsl_terms The available $terms
 */
function wpsl_stats_get_wpml_terms() {

    global $sitepress;

    $wpsl_terms = array();

    // Get the used languages
    if ( version_compare( ICL_SITEPRESS_VERSION, 3.2, '>=' ) ) {
        $langs = apply_filters( 'wpml_active_languages', NULL, 'orderby=code&order=asc' );
    } else {
        $langs = icl_get_languages( 'skip_missing=0&orderby=KEY&order=DIR&link_empty_to=str' );
    }

    // Loop over the available languages
    foreach ( $langs as $lang ) {

        // Activate the current language
        $sitepress->switch_lang( $lang['code'] );

        // Get the terms for the active language
        $lang_terms = wpsl_stats_get_terms();

        // Structure the data so we can use it with a prepared statement
        if ( $lang_terms ) {
            foreach ( $lang_terms as $k => $lang_term ) {
                $wpsl_terms[$k] = $lang_term;
            }
        }
    }

    // Restore the default language
    $sitepress->switch_lang( $sitepress->get_default_language() );

    return $wpsl_terms;
}
