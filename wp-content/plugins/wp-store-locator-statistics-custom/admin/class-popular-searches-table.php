<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Class to create the table layout for the term searches
 *
 * @package WordPress
 * @subpackage List_Table
 * @since 1.0
 */
class WPSL_Statistics_Popular_Searches_Table extends WP_List_Table {

    /**
     * Class constructor
     */
    function __construct() {
            
        parent::__construct( array(
            'singular' => __( 'Store Search', 'wpsl-statistics' ),
            'plural'   => __( 'Store Searches', 'wpsl-statistics' ),
            'ajax'     => false
        ) );
    }

    /**
     * Get the list of the 10 most searched locations.
     * 
     * @since 1.0.0
     * @param string $range The date range to use in the SQL query
     * @return array $results The top 10 searched locations.
     */
    function get_location_list( $range = '' ) {
        
        global $wpdb;
        
        $dates = wpsl_calculate_date_range( $range );

        // The placeholder values for the prepared SQL statement.
        $placeholders = apply_filters( 'wpsl_stats_popular_sql_placeholders', array(
            $dates->start,
            $dates->end
        ) );

        $sql = apply_filters( 'wpsl_stats_popular_sql',
            "SELECT search_location AS address, COUNT( search_location ) AS count
               FROM $wpdb->wpsl_stats_searches 
              WHERE search_date BETWEEN %s AND %s
            GROUP BY address 
            ORDER BY count DESC LIMIT 10"
        );

        $results = $wpdb->get_results( $wpdb->prepare( $sql, $placeholders ), ARRAY_A );

        return $results;
    }
 
    /**
     * Get the list of columns
     * 
     * @return array $columns The list of columns
     */
    function get_columns() {
        
        $columns = array(
            'address' => __( 'Location', 'wpsl-statistics' ),
            'count'   => __( 'Searches', 'wpsl-statistics' )
        );

        return $columns;
    }

    /**
     * Define what data to show on each column of the table
     *
     * @param  Array  $item        Data
     * @param  String $column_name - Current column name
     *
     * @return Mixed
     */
    public function column_default( $item, $column_name ) {
        
        global $wpsl_stats;
        
        switch( $column_name ) {
            case 'address':
                $address = $item[ $column_name ];
                
                if ( strlen( $address ) > 30 ) {
                    $address = substr( $address, 0, 27 ) . '...';
                }

                $value = $wpsl_stats->create_stats_details_url( $address );
                break;
            case 'count':
                $value = $item[ $column_name ];
                break;
        }
        
        return $value;
    }
    
    /**
	 * No items found message.
	 */
    public function no_items() {
        _e( 'No data found.', 'wpsl-statistics' );
    }

    /**
     * Prepares the list of items for displaying.
     * 
     * @uses WP_List_Table::set_pagination_args()
     * @return void
     */
    function prepare_items() {
        
        $columns  = $this->get_columns();
        $hidden   = array();
        $sortable = array();
        
        $response = $this->get_location_list();
        
        $this->items = $response;
        $this->_column_headers = array( $columns, $hidden, $sortable );
    }    
}