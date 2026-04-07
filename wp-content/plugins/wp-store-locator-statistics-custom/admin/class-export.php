<?php
/**
 * WPSL Statistics Export class
 *
 * @since  1.0.0
 * @author Tijmen Smit
 */

if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'WPSL_Statistics_Export' ) ) {

    class WPSL_Statistics_Export {

        /**
         * The range of the stats data to export.
         * Set to either days or hours.
         *
         * @var string
         * @since 1.0.0
         */
        public $range_type;

        /**
         * Class constructor
         */
        function __construct() {

            add_action( 'init',       array( $this, 'init' ) );
            add_action( 'admin_init', array( $this, 'export_actions' ) );
        }

        /**
         * Init the required class.
         *
         * @since 1.0.0
         * @return void
         */
        public function init() {
            $this->statistics_report = new WPSL_Statistics_Report();
        }

        /**
         * Handle the different stats export actions.
         *
         * @since 1.0.0
         * @return void
         */
        public function export_actions() {

            if ( isset( $_GET['wpsl_stats_export'] ) ) {

                check_admin_referer( 'wpsl_stats_export' );

                if ( !current_user_can( 'wpsl_statistics_export' ) ) {
                    wp_die( __( 'You do not have permission to export the statistics.', 'wpsl-statistics' ), '', array( 'response' => 403 ) );
                }

                if ( isset( $_GET['tab'] ) && $_GET['tab'] ) {
                    $tab = sanitize_text_field( $_GET['tab'] );
                } else {
                    $tab = '';
                }

                if ( wpsl_is_single_date( $this->statistics_report->date_range ) ) {
                    $this->range_type = 'hours';
                } else {
                    $this->range_type = 'days';
                }

                switch ( $tab ) {
                    case 'searches':
                        if ( isset( $_GET['address'] ) ) {
                            $this->detailed_search_stats();
                        } else {
                            $this->all_searches_stats();
                        }
                        break;
                    case 'dashboard':
                    default :
                        $this->dashboard_stats();
                        break;
                }
            }
        }

        /**
         * Export the dashboard statistics
         *
         * @since 1.0.0
         * @return void
         */
        public function dashboard_stats() {

            // Get the search graph data
            $graph_data = $this->search_graph_data_export();

            // Get the terms data
            $terms_data = $this->populair_terms_data_export();

            // Get the popular locations data
            $popular_locations_data = $this->popular_location_data_export();

            // Combine the stats data from the different arrays
            $export_data = array_merge_recursive( $graph_data, $terms_data, $popular_locations_data );

            $this->output_csv( $export_data, 'dashboard' );
        }

        /**
         * Export the statistics from
         * the "All Searches" page.
         *
         * @since 1.0.0
         * @return void
         */
        public function all_searches_stats() {

            require_once( WPSL_STATISTICS_PLUGIN_DIR . 'admin/class-all-searches-table.php' );

            $all_searches  = new WPSL_Statistics_All_Searches_Table();
            $all_searches->prepare_items();

            // 'true' makes the format of the returned data usable for the CSV export.
            $location_list = $all_searches->get_location_list( true );

            $export_data['headers'] = array(
                array(
                    'location',
                    'searches'
                )
            );

            $export_data['data'][] = $location_list;

            $this->output_csv( $export_data, 'all_searches' );
        }

        /**
         * Export the detailed search stats for a specific location
         *
         * @since 1.0.0
         * @return void
         */
        public function detailed_search_stats() {

            // Get the search graph data
            $graph_data = $this->search_graph_data_export();

            // Get the terms data
            $terms_data = $this->populair_terms_data_export();

            // Combine both the arrays holding the stats data
            $export_data = array_merge_recursive( $graph_data, $terms_data );

            $this->output_csv( $export_data, 'detailed_search' );
        }

        /**
         * Prepare the popular location data for export.
         *
         * @since 1.0.0
         * @return array
         */
        public function popular_location_data_export() {

            require_once( WPSL_STATISTICS_PLUGIN_DIR . 'admin/class-popular-searches-table.php' );

            $popular_searches = new WPSL_Statistics_Popular_Searches_Table();
            $popular_searches->prepare_items();

            $popular_location_data = $popular_searches->get_location_list();

            // Set the correct headers for the terms data
            $export_data['headers'][] = array(
                'location',
                'searches'
            );

            if ( $popular_location_data ) {
                $export_data['data'][] = $popular_location_data;
            } else {
                $export_data['data'][] = array(
                    array( __( 'No data found', 'wpsl-statistics' ), '' )
                );
            }

            return $export_data;
        }

        /**
         * Prepare the search graph data for export
         *
         * @since 1.0.0
         * @return array $export_data The search graph data
         */
        public function search_graph_data_export() {

            $graph_data      = $this->statistics_report->get_search_graph_data();
            $full_date_range = wpsl_create_full_date_range( $this->statistics_report->date_range, $this->range_type );

            // Loop over the full date range, and collect the search count for each date.
            foreach ( $full_date_range as $date ) {
                $count = $this->statistics_report->get_search_count( $date, $graph_data );

                if ( empty( $count ) ) {
                    $count = 0;
                }

                // Create the search date in the correct format.
                if ( $this->range_type == 'hours' ) {
                    $hour_format    = apply_filters( 'wpsl_stats_export_hour_format', 'F j,Y H:i' );
                    $formatted_date = esc_html( date( $hour_format, strtotime( $date ) ) );
                } else {
                    $date_format    = apply_filters( 'wpsl_stats_export_date_format', 'F j, Y' );
                    $formatted_date = esc_html( date_i18n( $date_format, strtotime( $date ) ) );
                }

                $searches[] = array(
                    $formatted_date,
                    $count
                );
            }

            $export_data['headers'] = array(
                array(
                    'date',
                    'searches'
                )
            );

            $export_data['data'][] = $searches;

            return $export_data;
        }

        /**
         * Prepare the terms data for export
         *
         * @since 1.0.0
         * @return array $export_data The terms data
         */
        public function populair_terms_data_export() {

            $terms_data = $this->statistics_report->get_popular_terms_data();

            // Set the correct headers for the terms data
            $export_data['headers'][] = array(
                'category name',
                'usage'
            );

            if ( $terms_data ) {

                // Remove the term_id field, not needed in the CSV export.
                foreach ( $terms_data as $k => $v ) {
                    unset( $terms_data[$k]['term_id'] );
                }

                $export_data['data'][] = $terms_data;
            } else {
                $export_data['data'][] = array(
                    array( __( 'No data found', 'wpsl-statistics' ), '' )
                );
            }

            return $export_data;
        }

        /**
         * Output the stats data in a CSV file
         *
         * @since 1.0.0
         * @param array  $stats_data The collected stats data that we need to export
         * @param string $type       The type of stats ( dashboard, all searches or detailed location stats ).
         * @return void
         */
        public function output_csv( $stats_data, $type ) {

            // Create the name of the CSV file.
            $file_name = 'wpsl-stats-' . $this->create_export_name( $type ) . '.csv';

            // output headers so that the file is downloaded rather than displayed
            header( 'Content-Type: text/csv; charset=utf-8' );
            header( 'Content-Disposition: attachment; filename=' . $file_name . '' );

            // create a file pointer connected to the output stream
            $output = fopen( 'php://output', 'w' );

            // Create the start rows included in the beginning of the CSV file
            $start_rows = array(
                array(
                    '# ----------------------------------------'
                ),
                array(
                    '# '. $this->create_export_desc( $type )
                ),
                array(
                    '# '. $this->export_date_range()
                ),
                array(
                    '# ----------------------------------------'
                ),
                array(
                    ''
                )
            );

            // Output the start rows.
            foreach ( $start_rows as $k => $start_row ) {
                fputcsv( $output, $start_row );
            }

            // Output the headers used in the CSV file
            foreach ( $stats_data['headers'] as $k => $row ) {
                fputcsv( $output, $row );

                /*
                 * Output the stats data itself.
                 *
                 * If it's not an array, that means we show a
                 * custom msg in the CSV file.
                 */
                if ( is_array( $stats_data['data'][$k] ) ) {
                    foreach ( $stats_data['data'][$k] as $v => $data ) {
                        fputcsv( $output, $data );
                    }
                } else {
                    fputcsv( $output, $stats_data['data'][$k] );
                }

                // Include whitespace after the first data set.
                fputcsv( $output, array() );
            }

            fclose( $output );

            exit();
        }

        /**
         * Create the description of the exported CSV data
         *
         * @since 1.0.0
         * @param  string $type        The type of description we need to create
         * @return string $export_desc The description of the content in the CSV file
         */
        public function create_export_desc( $type ) {

            $export_desc = '';

            if ( $type == 'detailed_search' ) {
                $export_desc = __( 'Search details for', 'wpsl-statistics' ) . ' ' . sanitize_text_field( $_GET['address'] );
            } else if ( $type == 'all_searches' ) {
                $export_desc = __( 'All searches overview', 'wpsl-statistics' );
            } else if ( $type == 'dashboard' ) {
                $export_desc = __( 'Dashboard statistics', 'wpsl-statistics' );
            }

            return $export_desc;
        }

        /**
         * Create the part of the export name
         * that contains the start / end date.
         *
         * @since 1.0.0
         * @return string $dates The start-end date combined.
         */
        public function export_date_range() {

            $date_parts = array( 'start' );

            /*
             * Only include the end date is we show the
             * daily stats instead of the hourly stats.
             */
            if ( $this->range_type == 'days' ) {
                array_push( $date_parts, 'end' );

                foreach ( $date_parts as $date_part ) {
                    $section = explode( ' ', $this->statistics_report->date_range->{$date_part} );
                    $dates[] = str_replace( '-', '',  $section[0] );
                }

                $dates = implode( '-', $dates );
            } else {
                $section = explode( ' ', $this->statistics_report->date_range->start );
                $dates   = str_replace( '-', '',  $section[0] );
            }

            return $dates;
        }

        /**
         * Create the file name for the stats CSV export.
         *
         * This is based on the section the user wants to export,
         * and the selected date range.
         *
         * @since 1.0.0
         * @param  string $type        The type of stats the CSV file contains
         * @return string $export_name The middle part of the file name
         */
        public function create_export_name( $type ) {

            if ( $type == 'detailed_search' ) {

                // Remove any non alphanumeric characters except spaces from the address
                $address = preg_replace( '/[^A-Za-z0-9 ]/', '', urldecode( $_GET['address'] ) );

                // Replace the spaces with '-'.
                $name = str_replace( ' ', '-', strtolower( $address ) );

                // Include the start and end date in the file name
                $start_end_date = $this->export_date_range();

                $export_name = $name . '-' . $start_end_date;
            } else if ( $type == 'all_searches' ) {
                $export_name = 'all-searches-' . $this->export_date_range();
            } else if ( $type == 'dashboard' ) {
                $export_name = 'dashboard-' . $this->export_date_range();
            }

            return $export_name;
        }

        /**
         * Create the stats export link.
         *
         * @since 1.0.0
         * @return string $export_url The link that enables users to export the statistics data
         */
        public function export_link() {

            $query_args = array();

            if ( isset( $_GET['tab'] ) && $_GET['tab'] ) {
                $query_args['tab'] = $_GET['tab'];
            }

            if ( isset( $_GET['address'] ) && $_GET['address'] ) {
                $query_args['address'] = $_GET['address'];
            }

            if ( isset( $_GET['range'] ) && $_GET['range'] ) {
                $query_args['range'] = $_GET['range'];
            }

            if ( isset( $_GET['start'] ) && $_GET['start'] ) {
                $query_args['start'] = $_GET['start'];
            }

            if ( isset( $_GET['end'] ) && $_GET['end'] ) {
                $query_args['end'] = $_GET['end'];
            }

            if ( isset( $_GET['orderby'] ) && $_GET['orderby'] ) {
                $query_args['orderby'] = $_GET['orderby'];
            }

            if ( isset( $_GET['order'] ) && $_GET['order'] ) {
                $query_args['order'] = $_GET['order'];
            }

            if ( isset( $_GET['paged'] ) && $_GET['paged'] ) {
                $query_args['paged'] = $_GET['paged'];
            }

            $export_url = '<a title="'. __( 'Export to CSV file', 'wpsl-statistics' ) .'" class="wpsl-stats-export button-primary icon-download" href="' . esc_url( wp_nonce_url( add_query_arg( $query_args, admin_url( 'edit.php?post_type=wpsl_stores&page=wpsl_statistics' ) ), 'wpsl_stats_export' ) . '&wpsl_stats_export' ) . '">' . __( 'Export', 'wpsl-statistics' ) . '</a>';

            return $export_url;
        }
    }

    new WPSL_Statistics_Export();
}