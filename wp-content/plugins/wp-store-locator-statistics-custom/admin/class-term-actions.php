<?php
/**
 * WPSL Statistics term actions.
 * 
 * @since  1.0.0
 * @author Tijmen Smit
 */

if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'WPSL_Statistics_Term_Actions' ) ) {

    class WPSL_Statistics_Term_Actions {

        /**
         * Class constructor
         */
		function __construct() {
            
            add_action( 'created_wpsl_store_category', array( $this, 'created_term' ), 10, 2 );
            add_action( 'created_dealer_service', array( $this, 'created_term_dealer_service' ), 10, 2 );
            add_action( 'edited_wpsl_store_category',  array( $this, 'edited_term' ), 10, 2 );
            add_action( 'edited_dealer_service',  array( $this, 'edited_term_dealer_service' ), 10, 2 );
		}
        
        /**
         * When a new WPSL term is created make sure it's 
         * also added to the stats terms table.
         * 
         * Doing this makes sure that after a WPSL term is deleted, 
         * we can still use the term name in the already collected statistics 
         * if the term was used. 
         * 
         * Otherwise the stats table will contain the used term id, 
         * but the term name to match it with won't exist anymore.
         * 
         * @since 1.0.0
         * @param int $term_id Term ID.
         * @param int $tt_id   Term taxonomy ID.
         * @return void
         */
        public function created_term( $term_id, $tt_id ) {
            
            global $wpdb;

            $term = get_term( $term_id, 'wpsl_store_category' );
            
            $wpdb->query( $wpdb->prepare( "INSERT INTO $wpdb->wpsl_stats_terms (term_id, name) VALUES (%d, %s)", $term_id, $term->name ) );
        }

        public function created_term_dealer_service( $term_id, $tt_id ) {

            global $wpdb;

            $term = get_term( $term_id, 'dealer_service' );

            $wpdb->query( $wpdb->prepare( "INSERT INTO $wpdb->wpsl_stats_terms (term_id, name) VALUES (%d, %s)", $term_id, $term->name ) );
        }
        
        /**
         * When a WPSL term is edited we also update
         * the term name in the stats term table to make sure
         * the correct name is used in the statistics.
         * 
         * @since 1.0.0
         * @param int $term_id Term ID.
         * @param int $tt_id   Term taxonomy ID.
         * @return void
         */
        public function edited_term( $term_id, $tt_id ) {
            
            global $wpdb;

            $term = get_term( $term_id, 'wpsl_store_category' );
            
            $wpdb->query( $wpdb->prepare( "UPDATE $wpdb->wpsl_stats_terms SET name = %s WHERE term_id = %d", $term->name, $term_id ) );
        }

        public function edited_term_dealer_service( $term_id, $tt_id ) {

            global $wpdb;

            $term = get_term( $term_id, 'dealer_service' );

            $wpdb->query( $wpdb->prepare( "UPDATE $wpdb->wpsl_stats_terms SET name = %s WHERE term_id = %d", $term->name, $term_id ) );
        }
    }
 
    new WPSL_Statistics_Term_Actions();
}
