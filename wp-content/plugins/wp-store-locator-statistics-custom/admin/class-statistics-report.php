<?php
/**
 * WPSL Statistics Report class
 * 
 * @since  1.0.0
 * @author Tijmen Smit
 */

if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'WPSL_Statistics_Report' ) ) {

    class WPSL_Statistics_Report {

        /**
         * Are terms used within the WPSL plugin?
         *
         * @var bool
         * @since 1.0.0
         */
        public $terms_exist;

        /**
         * Holds the start and end date range.
         *
         * @var object
         * @since 1.0.0
         */
        public $date_range;

        /**
         * Are the address details
         * included in the URL?
         *
         * @var bool
         * @since 1.0.0
         */
        public $address_details;

        /**
         * Show the used terms in the
         * nearby locations section?
         *
         * @var bool
         * @since 1.0.0
         */
        public $show_nearby_terms;
                
        /**
         * Class constructor
         */
		function __construct() {

            $this->terms_exist       = $this->check_terms_exist();
            $this->date_range        = wpsl_calculate_date_range();
            $this->address_details   = ( isset( $_GET['address'] ) && $_GET['address'] ) ? true : false;
            $this->show_nearby_terms = apply_filters( 'wpsl_stats_show_nearby_terms', true );
        }
        
        /**
         * Get the statistics data.
         * 
         * @since 1.0.0
         * @return void | array $statistics_data The collected stats data.
         */  
        public function collect_data() {
                        
            $statistics_data = array(
                'search_graph' => $this->get_search_graph()
            );

            // Only include the heatmap data if we are not on an address details page.
            if ( !$this->address_details ) {
                $statistics_data['heatmap'] = $this->get_search_latlng();
            }

            if ( $this->terms_exist ) {
                $statistics_data['terms'] = $this->get_popular_terms();
                $statistics_data['services'] = $this->get_popular_services();
            }
            
            // Only get the list of nearby locations if we are on the address details page.
            if ( $this->address_details ) {
                $statistics_data['nearby'] = $this->get_nearby_locations();
            }

            return $statistics_data;
        }
        
        /**
         * Get the data required to create the search graph.
         * 
         * @since  1.0.0
         * @return array $search_graph
         */
        public function get_search_graph() {

            $graph_data   = array();
            $hticks_range = array();
            $range_type   = ( wpsl_is_single_date( $this->date_range ) ) ? 'hours' : 'days';
            $results      = $this->get_search_graph_data();

            $full_date_range = wpsl_create_full_date_range( $this->date_range, $range_type );

            $graph_data['cols'][] = array(
                'label' => 'Data',
                'type'  => 'date'
            );
            
            $graph_data['cols'][] = array(
                'label' => 'Number',
                'type'  => 'number'
            );
            
            // Include the tooltip
            $graph_data['cols'][] = array(
                'type' => 'string',
                'role' => 'tooltip',
                'p' => array(
                    'html' => true
                )
            );
            
            $i           = 0;
            $total_count = 0;
            $max_count   = 0;

            /*
             * Loop over the date range and assign the 
             * search count to each date row.
             */
            foreach ( $full_date_range as $date ) {
                $count = $this->get_search_count( $date, $results );
                
                if ( empty( $count ) ) {
                    $count = 0;
                }

                $tooltip = $this->create_tooltip( $range_type, $count, $date );

                // : only exists if the date is set to today, yesterday or a custom date range covering 1 day.
                if ( strpos( $date, ':' ) !== false ) {
                    $date       = preg_split( "/(-|:| )/", date( "Y-n-d H", strtotime( $date ) ) );
                    $graph_date = 'Date(' . $date[0] . ',' . ( $date[1] - 1 ) . ',' . $date[2] . ',' . $date[3] . ',' . '00' . ')';
                } else {
                    $date       = explode( '-', date( "Y-n-d", strtotime( $date ) ) );
                    $graph_date = 'Date(' . $date[0] . ',' . ( $date[1] - 1 ) . ',' . $date[2] . ')';
                }
                
                // Include the search data
                $graph_data['rows'][$i]['c'] = array(
                    array( 'v' => $graph_date ),
                    array( 'v' => $count )
                );

                $hticks_range[] = $graph_date;
                
                // Include the tooltip data
                $graph_data['rows'][$i]['c'][2] = array( 'v' => $tooltip );

                if ( $count > $max_count ) {
                    $max_count = $count;
                }

                $total_count = $total_count + $count;
                $i++;
            }

            $max_count = $this->check_max_graph_count( $max_count );

            $search_graph = array(
                'graph'        => $graph_data,
                'total_search' => $total_count,
                'vticks'       => $this->create_tick_range( $max_count ),
                'hticks'       => $hticks_range,
                'range'        => $range_type
            );
            
            return $search_graph;
        }

        /**
         * Run the SQL query that collects
         * data for the search graph.
         *
         * @since 1.0.0
         * @param void
         * @return array $results
         */
        public function get_search_graph_data() {

            global $wpdb;

            $query_parts = $this->get_query_parts();

            $sql = "SELECT COUNT(*) as count, DATE_FORMAT(search_date, '{$query_parts['sql']['date_format']}') AS searched_date
                      FROM $wpdb->wpsl_stats_searches 
                     WHERE search_date BETWEEN %s AND %s
                  {$query_parts['sql']['location']}
                  GROUP BY searched_date";

            $results = $wpdb->get_results( $wpdb->prepare( $sql, $query_parts['placeholders'] ) );

            return $results;
        }
        
        /**
         * Make sure the max graph count is set to at least 20.
         * 
         * @since 1.0.0
         * @param  int $max_count
         * @return int $max_count
         */
        public function check_max_graph_count( $max_count ) {
            
            $max_count = round( $max_count, -1 ) * 2;
            
            if ( !$max_count || $max_count < 20 ) {
                $max_count = 20;
            }
            
            return $max_count;
        }
        
        /**
         * Create the graph tick range.
         *
         * @since  1.0.0
         * @param  int   $max_count The max search count
         * @return array $range The range of ticks used in the graph
         */
        public function create_tick_range( $max_count ) {

            $divide = 4;
            $arr[]  = 0;
            $base   = $max_count / $divide;

            for ( $i = 1; $i <= $divide; $i++ ) {
                $arr[] = round( $i * $base );
            }

            $range = array_reverse( $arr );

            return $range;
        }
        
        /**
         * Create the tooltip.
         * 
         * @since  1.0.0
         * @param  string  $range_type  Set to hours or days
         * @param  integer $count       The search count
         * @param  string  $search_date The search data
         * @return string  $tooltip     The HTML for the tooltip with the correct data
         */
        public function create_tooltip( $range_type, $count, $search_date ) {
                        
            $hrs     = ( $range_type == 'hours' ) ? ' H:00' : '';
            $date    = date_i18n( get_option( 'date_format' ) . $hrs, strtotime( $search_date ) );
            $tooltip = '<p class="wpsl-stats-tooltip"><strong>' . $date . '</strong>'. __( 'Searches: ', 'wpsl-statistics' ) . $count . '</p>';
            
            return $tooltip;
        }
        
        /**
         * Get the search count for the search date.
         *
         * @since 1.0.0
         * @param string $date    The start date
         * @param array  $results The search results
         * @return void | the search count
         */
        public function get_search_count( $date, $results ) {

            foreach ( $results as $result ) {
                if ( isset( $result->searched_date ) && ( $date == $result->searched_date ) ) {
                    return $result->count;
                }
            }
        }
        
        /**
         * Get the latlng values for a date range
         * 
         * This is used to create the heatmap.
         *
         * @since 1.0.0
         * @return array $latlng The latlng values for the searched locations
         */
        public function get_search_latlng() {
            
            global $wpdb, $wpsl_settings;
            
            $latlng = array();
         
            if ( isset( $wpsl_settings['active_map_service'] ) && $wpsl_settings['active_map_service'] == 'osm' ) {
                $map_service = 'osm';
            } else {
                $map_service = '';
            }

            if ( $map_service == 'osm' ) {
                $count_sql = ', COUNT(*) as searches';
                $group_sql = 'GROUP BY lat, lng HAVING COUNT(*) > 0 ORDER BY searches DESC';
            } else {
                $count_sql = '';
                $group_sql = '';
            }
         
            $results = $wpdb->get_results(
                $wpdb->prepare( "SELECT lat, lng $count_sql FROM $wpdb->wpsl_stats_searches WHERE search_date BETWEEN %s AND %s $group_sql",
                    $this->date_range->start, 
                    $this->date_range->end
                )
            );
            
            if ( $results ) {
                foreach ( $results as $result ) {
                    if ( $map_service == 'osm' ) {
                        $latlng[] = array(
                            'lat'   => $result->lat,
                            'lng'   => $result->lng,
                            'count' => $result->searches
                        );
                    } else {
                    	$latlng[] = $result->lat . ',' . $result->lng; 
                	} 
            	}
            }
            
            return $latlng;
        }
        
        /**
         * Get the placeholders and SQL parts 
         * used in the stats queries.
         * 
         * @since 1.0.0
         * @return array $query_parts
         */
        public function get_query_parts() {
            
            $single_date = wpsl_is_single_date( $this->date_range );

            if ( $single_date ) {
                $query_parts['sql']['date_format'] = '%%Y-%%m-%%d %%H:00:00';
            } else {
                $query_parts['sql']['date_format'] = '%%Y-%%m-%%d';
            }

            $query_parts['placeholders'] = array(
                $this->date_range->start, 
                $this->date_range->end
            );
                        
            // Check if we need to restrict the search by address.
            if ( $this->address_details ) {
                $query_parts['placeholders'][] = urldecode( $_GET['address'] );
                $query_parts['sql']['location'] = ' AND search_location = %s';
            } else {
                $query_parts['sql']['location'] = '';
            }  
            
            return $query_parts;
        }
        
        /**
         * Check if there is term data available.
         * 
         * If this isn't the case, then there is no
         * point in showing the term pie chart section.
         * 
         * @since 1.0.0
         * @return boolean $results Whether or not term data exists
         */  
        public function check_terms_exist() {
           
            global $wpdb;

            $results = $wpdb->get_results( "SELECT term_id FROM $wpdb->wpsl_stats_terms LIMIT 1" );

            return $results;   
        }
        
        /**
         * Get the used search terms.
         * 
         * The returned data is shown in the pie chart.
         * 
         * @since 1.0.0
         * @return json | void $chart Data used to render the used terms piechart.
         */  
        public function get_popular_terms() {

            $popular_terms = $this->get_popular_terms_data();

            if ( $popular_terms ) {
                $rows = array();
                $chart['cols'] = array(
                    array( 'label' => 'Terms',      'type' => 'string' ),
                    array( 'label' => 'Percentage', 'type' => 'number' )
                );

                foreach ( $popular_terms as $term ) {
                    $temp = array();
                    
                    // the following line will be used to slice the Pie chart
                    $temp[] = array( 'v' => (string) $term['name'] );

                    // Values of each slice
                    $temp[] = array( 'v' => (int) $term['count'] );
                    $rows[] = array( 'c' => $temp );
                }

                $chart['rows'] = $rows;
                
                return json_encode( $chart );
            }
        }

        public function get_popular_services() {

            $popular_terms = $this->get_popular_services_data();

            if ( $popular_terms ) {
                $rows = array();
                $chart['cols'] = array(
                    array( 'label' => 'Terms',      'type' => 'string' ),
                    array( 'label' => 'Percentage', 'type' => 'number' )
                );

                foreach ( $popular_terms as $term ) {
                    $temp = array();

                    // the following line will be used to slice the Pie chart
                    $temp[] = array( 'v' => (string) $term['name'] );

                    // Values of each slice
                    $temp[] = array( 'v' => (int) $term['count'] );
                    $rows[] = array( 'c' => $temp );
                }

                $chart['rows'] = $rows;

                return json_encode( $chart );
            }
        }

        /**
         * Run the SQL query that collects
         * data for the pie chart showing the used terms
         *
         * @since  1.0.0
         * @return array|null $popular_terms Returned SQL results.
         */
        public function get_popular_terms_data() {

            global $wpdb;

            $excludes = implode( ',', get_terms( array(
              'taxonomy'   => [ 'dealer_service' ],
              'fields'     => 'ids',
              'hide_empty' => false,
            ) ) ?: [] );

            $query_parts   = $this->get_query_parts();
            $popular_terms = $wpdb->get_results(
                $wpdb->prepare( "SELECT stats_terms.name, COUNT(*) AS count, stats_rel.term_id
                                   FROM $wpdb->wpsl_stats_searches AS stats
                             INNER JOIN $wpdb->wpsl_stats_term_relationships AS stats_rel ON stats_rel.search_id = stats.search_id
                             INNER JOIN $wpdb->wpsl_stats_terms AS stats_terms ON stats_terms.term_id = stats_rel.term_id
                                  WHERE stats_rel.term_id NOT IN ($excludes) && search_date BETWEEN %s AND %s   
                                  {$query_parts['sql']['location']}
                               GROUP BY term_id
                               ORDER BY count DESC",
                    $query_parts['placeholders']
                ), ARRAY_A
            );

            return $popular_terms;
        }

        public function get_popular_services_data() {

            global $wpdb;

            $excludes = implode( ',', get_terms( array(
              'taxonomy'   => [ 'wpsl_store_category' ],
              'fields'     => 'ids',
              'hide_empty' => false,
            ) ) ?: [] );

            $query_parts   = $this->get_query_parts();
            $popular_terms = $wpdb->get_results(
                $wpdb->prepare( "SELECT stats_terms.name, COUNT(*) AS count, stats_rel.term_id
                                   FROM $wpdb->wpsl_stats_searches AS stats
                             INNER JOIN $wpdb->wpsl_stats_term_relationships AS stats_rel ON stats_rel.search_id = stats.search_id
                             INNER JOIN $wpdb->wpsl_stats_terms AS stats_terms ON stats_terms.term_id = stats_rel.term_id
                                  WHERE stats_rel.term_id NOT IN ($excludes) && search_date BETWEEN %s AND %s   
                                  {$query_parts['sql']['location']}
                               GROUP BY term_id
                               ORDER BY count DESC",
                    $query_parts['placeholders']
                ), ARRAY_A
            );

            return $popular_terms;
        }

        /**
         * Get the data for 10 nearest locations.
         * 
         * @since  1.0.0
         * @return array $nearby_locations The address details for nearby locations
         */    
        public function get_nearby_locations() {

            global $wpdb, $wpsl_stats, $wpsl_settings;

            $nearby_locations = array();
            $location_limit   = apply_filters( 'wpsl_stats_nearby_limit', 10 );
            
            // Get the coordinates for the address we are showing the stats for.
            $latlng = $wpdb->get_results(
                $wpdb->prepare( "SELECT lat, lng FROM $wpdb->wpsl_stats_searches WHERE search_location = %s LIMIT 1", $_GET['address'] )
            );

            if ( $latlng ) {
                
                // The placeholder values for the prepared SQL statement.
                $placeholders = apply_filters( 'wpsl_stats_nearby_sql_placeholders', array(
                    $radius = ( $wpsl_settings['distance_unit'] == 'km' ) ? 6371 : 3959,
                    $latlng[0]->lat,
                    $latlng[0]->lng,
                    $latlng[0]->lat,
                    $wpsl_stats->nearby_locations_radius // default to 50.
                ) );

                $sort = apply_filters( 'wpsl_stats_nearby_sql_sort', "HAVING distance < %d ORDER BY distance LIMIT $location_limit" );

                $sql = apply_filters( 'wpsl_stats_nearby_sql', "
                        SELECT post_lat.meta_value AS lat,
                               post_lng.meta_value AS lng,
                               posts.ID,
                               ( %d * acos( cos( radians( %s ) ) * cos( radians( post_lat.meta_value ) ) * cos( radians( post_lng.meta_value ) - radians( %s ) ) + sin( radians( %s ) ) * sin( radians( post_lat.meta_value ) ) ) ) 
                            AS distance
                          FROM $wpdb->posts AS posts
                    INNER JOIN $wpdb->postmeta AS post_lat ON post_lat.post_id = posts.ID AND post_lat.meta_key = 'wpsl_lat'
                    INNER JOIN $wpdb->postmeta AS post_lng ON post_lng.post_id = posts.ID AND post_lng.meta_key = 'wpsl_lng'
                         WHERE posts.post_type = 'wpsl_stores'
                           AND posts.post_status = 'publish' $sort"
                );

                $stores = $wpdb->get_results( $wpdb->prepare( $sql, $placeholders ) );

                $nearby_locations = array(
                    'start' => array(
                        'id'    => 0,
                        'lat'   => $latlng[0]->lat,
                        'lng'   => $latlng[0]->lng,
                        'store' => __( 'Searched Location', 'wpsl-statistics' )
                    )
                );

                $i = 0;
                
                // Loop over the returned SQL results, and collect the meta data.
                foreach ( $stores as $store ) {
                    $nearby_locations['locations'][$i] = apply_filters( 'wpsl_stats_nearby_locations_meta_fields', array(
                        'id'       => $store->ID,
                        'lat'      => $store->lat,
                        'lng'      => $store->lng,
                        'store'    => get_the_title( $store->ID ),
                        'address'  => get_post_meta( $store->ID, 'wpsl_address', true ),
                        'city'     => get_post_meta( $store->ID, 'wpsl_city',    true ),
                        'state'    => get_post_meta( $store->ID, 'wpsl_state',   true ),
                        'zip'      => get_post_meta( $store->ID, 'wpsl_zip',     true ),
                        'distance' => round( $store->distance, 1 )
                    ), $store->ID );
                    
                    if ( $wpsl_settings['permalinks'] ) {
                        $nearby_locations['locations'][$i]['permalink'] = get_permalink( $store->ID );
                    } else {
                        $nearby_locations['locations'][$i]['url'] = get_post_meta( $store->ID, 'wpsl_url', true );
                    }

                    /*
                     * Check if the terms exist, and if we should include the nearby terms in the data.
                     *
                     * The 'show_nearby_terms' default to true, but can be changed with the
                     * 'wpsl_stats_show_nearby_terms' filter to false.
                     */
                    if ( $this->terms_exist && $this->show_nearby_terms ) {
                        $terms = wp_get_post_terms( $store->ID, 'wpsl_store_category' );

                        $nearby_locations['locations'][$i]['terms'] = '';

                        if ( $terms ) {
                            if ( !is_wp_error( $terms ) ) {
                                if ( count( $terms ) > 1 ) {
                                    $location_terms = array();

                                    foreach ( $terms as $term ) {
                                        $location_terms[] = $term->name;
                                    }

                                    $nearby_locations['locations'][$i]['terms'] = implode( ', ', $location_terms );
                                } else {
                                    $nearby_locations['locations'][$i]['terms'] = $terms[0]->name;
                                }
                            }
                        }
                    }
                    
                    $i++;
                }
            }

            return $nearby_locations;
        }

        /**
         * Get the necessary CSS classes.
         *
         * @since 1.0.0
         * @return array|void $classes The collected CSS classes.
         */
        public function get_css_classes() {

            global $wpsl_settings;

            $classes = array();

            // Check if there are WPSL terms.
            if ( $this->terms_exist ) {
                $classes[] = 'wpsl-terms-exist';
            } else {
                $classes[] = 'wpsl-no-terms';
            }

            // OpenStreetMaps is not supported until WPSL 3.0
            if ( isset( $wpsl_settings['active_map_service'] ) && $wpsl_settings['active_map_service'] == 'osm' ) {
                $classes[] = 'wpsl-osm';
            } else {
                $classes[] = 'wpsl-gmap';
            }

            /*
             * If the date range if not for today / yesterday, then we need to hide
             * the first item on the x-graph to prevent it from being to close to 0 on the y axis.
             */
            if ( !wpsl_is_single_date( $this->date_range ) ) {
                $classes[] = 'wpsl-no-single-day';
            }

            if ( !empty( $classes ) ) {
                return join( ' ', $classes );
            }
        }
    }
}
