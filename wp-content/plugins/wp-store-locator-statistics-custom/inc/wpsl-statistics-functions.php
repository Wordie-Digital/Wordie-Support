<?php
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Save the searched location data.
 *
 * @since 1.0.0
 * @return void
 */
function wpsl_save_search() {
    
    global $wpdb;
    
    if ( isset( $_GET['autoload'] ) ) {
        return;
    }
    
    if ( !isset( $_GET['search'] ) || isset( $_GET['search'] ) && ( !$_GET['search'] ) ) {
        return;
    }
    
    // Save the searched location, date and coordinates.
    $wpdb->query(
        $wpdb->prepare(
                "
                INSERT INTO $wpdb->wpsl_stats_searches (search_location, search_date, lat, lng) VALUES (%s, %s, %s, %s)
                ", 
                $_GET['search'],
                date_i18n( 'Y-m-d H:i:s' ),
                $_GET['lat'],
                $_GET['lng']
        )
    );

    // Store Last ID
    $last_id = $wpdb->insert_id;
    
    // If a filter is used, then save the set filter ids.
    if ( isset( $_GET['filter'] ) && $_GET['filter'] ) {
        if ( $last_id ) {
            $values     = array();
            $filter_ids = explode( ',', $_GET['filter'] );

            foreach ( $filter_ids as $filter_id ) {
                $values[] = $wpdb->prepare( "(%d, %d)", $last_id, $filter_id );    
            }
            
            if ( $values ) {
                $wpdb->query( "INSERT INTO $wpdb->wpsl_stats_term_relationships (search_id, term_id) VALUES " . join( ',', $values ) . "" );
            }
        }  
    }

    // If a dealer service is used, then save the set service ids.
    if ( isset( $_GET['dealer-service'] ) && $_GET['dealer-service'] ) {
        if ( $last_id ) {
            $values     = array();
            $filter_ids = explode( ',', $_GET['dealer-service'] );

            foreach ( $filter_ids as $filter_id ) {
                $values[] = $wpdb->prepare( "(%d, %d)", $last_id, $filter_id );
            }

            if ( $values ) {
                $wpdb->query( "INSERT INTO $wpdb->wpsl_stats_term_relationships (search_id, term_id) VALUES " . join( ',', $values ) . "" );
            }
        }
    }
}

add_action( 'wpsl_store_search', 'wpsl_save_search' );

/**
 * Add the required JS settings 
 * for the statistics add-on.
 *
 * @since 1.0.0
 * @param  array $settings The original WPSL settings
 * @return array $settings The WPSL settings inc the WPSL Stats setting.
 */
function wpsl_statistics_js_settings( $settings ) {
    
    $settings['collectStatistics'] = true;
    
    return $settings;
}

add_filter( 'wpsl_js_settings', 'wpsl_statistics_js_settings' );
