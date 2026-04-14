<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Class to create the table layout for the term searches.
 *
 * @package WordPress
 * @subpackage List_Table
 * @since 1.0.0
 */
class WPSL_Statistics_All_Searches_Table extends WP_List_Table {
    
    /**
     * Holds the amount of stores that are shown on each page
     *
     * @since 1.0.0
     * @var string
     */
    public $per_page = '';

    /**
     * The total ammount of searches
     *
     * @since 1.0.0
     * @var int
     */
    public $total = 0;
    
    /**
     * Class constructor
     */
    function __construct() {
                
        parent::__construct( array(
            'singular' => __( 'Store Search', 'wpsl-statistics' ),
            'plural'   => __( 'Store Searches', 'wpsl-statistics' ),
            'ajax'     => false
        ) );
        
        $this->init();
    }
    
    /**
     * Set the paging var
     *
     * @since 1.0.0
     * @return void
     */
    public function init() {

        $per_page = get_user_meta( get_current_user_id(), 'wpsl_stats_per_page', true );

        if ( $per_page < 1 ) {
            $per_page = 20;
        }

        $this->per_page = apply_filters( 'wpsl_stats_per_page', $per_page );
    }

    /**
     * Get the list of searched locations.
     * 
     * @since  1.0.0
     * @param boolean $export Adjust the structure of the returned data based on this val
     * @return array  $result The required store data
     */
    function get_location_list( $export = false ) {
        
        global $wpdb;

        $search_sql = '';
        $count      = 0;
        $dates      = wpsl_calculate_date_range();
        
        // Order params.
        $orderby   = isset( $_GET['orderby'] ) ? $_GET['orderby'] : 'count';
        $order     = isset( $_GET['order'] ) ? $_GET['order'] : 'DESC';
        $order_sql = 'ORDER BY ' . $orderby . ' ' . $order; 
        
        // Limit the results by search?
		if ( isset( $_GET['s'] ) && !empty( $_GET['s'] ) ) {
			$search_sql = " AND search_location LIKE '%%" . $wpdb->esc_like( urldecode( trim( $_GET['s'] ) ) ) . "%%' ";
		}
        
        // Pagination parameters.
        if ( !$export ) {
            $paged     = isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1;
            $limit_sql = 'LIMIT ' . (int)( $paged - 1 ) * $this->per_page . ',' . (int)$this->per_page;
        } else {
            $limit_sql = '';
        }

        $sql = apply_filters( 'wpsl_stats_location_list_sql',
            "SELECT search_location AS location, COUNT( search_location ) AS count
               FROM $wpdb->wpsl_stats_searches 
              WHERE search_date BETWEEN %s AND %s
              $search_sql GROUP BY location $order_sql $limit_sql"
        );

        $placeholders = apply_filters( 'wpsl_stats_location_list_placeholders', array(
            $dates->start,
            $dates->end
        ) );
        
        // Get the data shown in the overview table, but limit it to 20 ( default ) results each time.
        $searches = $wpdb->get_results( $wpdb->prepare( $sql, $placeholders ), ARRAY_A );

        if ( $searches ) {
            
            // Get the total amount of locations that fall within the date range for the paging.
            $count = $wpdb->get_var(
                $wpdb->prepare( "SELECT count(*) FROM ( SELECT search_location 
                                   FROM $wpdb->wpsl_stats_searches
                                  WHERE search_date BETWEEN %s AND %s
                                  $search_sql
                               GROUP BY search_location ) AS count", 
                    $dates->start,
                    $dates->end
            ) );
        }

        if ( !$export ) {
            $response = array(
                'data'  => $searches,
                'count' => $count
            );
        } else {
            $response = $searches;
        }
        
        return $response;
    }

    /**
     * Get the list of columns
     * 
     * @return array $columns The list of columns
     */
    function get_columns() {
        
        $columns = array(
            'location' => __( 'Location', 'wpsl-statistics' ),
            'count'    => __( 'Searches', 'wpsl-statistics' )
        );
        
        return apply_filters( 'wpsl_stats_all_searches_columns', $columns );
    }
    
    /**
     * Define which columns should be sortable
     * 
     * @return array $sortable_columns The list of sortable scolumns
     */
    function get_sortable_columns() {
        
        $sortable_columns = array(
            'location' => array( 'location', false ),
            'count'    => array( 'count', true )
        );

        return apply_filters( 'wpsl_stats_all_searches_sortable_columns', $sortable_columns );
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
            case 'location':
                $value = $wpsl_stats->create_stats_details_url( $item[ $column_name ] );
                break;
            case 'count':
                $value = $item[ $column_name ];
                break;
            default:
                $value = isset( $item[ $column_name ] ) ? $item[ $column_name ] : '';
                break;
        }

        return apply_filters( 'wpsl_stats_all_searches_column_' . $column_name, $value );
    }
        
    /**
	 * No items found text.
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
        $sortable = $this->get_sortable_columns();
        
        $response = $this->get_location_list();
        
        $this->items = $response['data'];
        $this->total = $response['count'];
        $this->_column_headers = array( $columns, $hidden, $sortable );        

        $this->set_pagination_args( array(
			'total_items' => $this->total,
			'per_page'    => $this->per_page,
			'total_pages' => ceil( $this->total / $this->per_page )
		) );
    }
}