<?php
/**
 * WPSL Statistics Admin class
 * 
 * @since  1.0.0
 * @author Tijmen Smit
 */

if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'WPSL_Statistics_Admin' ) ) {

    class WPSL_Statistics_Admin {
        
        /**
         * Holds the used search radius for the 'nearby locations' map.
         *
         * @since 1.0.0
         * @var int
         */
        public $nearby_locations_radius;

        /**
         * Class constructor
         */
		function __construct() {

            $this->includes();

            add_action( 'admin_init',                            array( $this, 'init' ) );
            add_action( 'admin_menu',                            array( $this, 'admin_menu' ) );
            add_action( 'admin_enqueue_scripts',                 array( $this, 'admin_scripts' ) );
            add_action( 'admin_footer',                          array( $this, 'admin_footer' ) );
            add_action( 'wp_loaded',                             array( $this, 'remove_unused_query_args' ) );
            add_action( 'load-wpsl_stores_page_wpsl_statistics', array( $this, 'screen_option' ) );

            add_filter( 'set-screen-option',                     array( $this, 'set_paging_options' ), 20, 3 );
            add_filter( 'admin_body_class',                      array( $this, 'paging_body_class' ) );
        }

        /**
         * Include the required files.
         * 
         * @since 1.0.0
         * @return void
         */
        public function includes() {
            require_once( WPSL_STATISTICS_PLUGIN_DIR . 'admin/dates.php' );
            require_once( WPSL_STATISTICS_PLUGIN_DIR . 'admin/class-statistics-report.php' );
            require_once( WPSL_STATISTICS_PLUGIN_DIR . 'admin/class-term-actions.php' );
            require_once( WPSL_STATISTICS_PLUGIN_DIR . 'admin/class-export.php' );
            require_once( WPSL_STATISTICS_PLUGIN_DIR . 'admin/class-dashboard.php' );
            require_once( WPSL_STATISTICS_PLUGIN_DIR . 'admin/class-tools.php' );
            require_once( WPSL_STATISTICS_PLUGIN_DIR . 'admin/upgrade.php' );
        }
        
        /**
         * Init
         *
         * @since 1.0.0
         * @return void
         */
        public function init() {

            $this->nearby_locations_radius = apply_filters( 'wpsl_stats_nearby_locations_radius', 50 );
            $this->statistics_report       = new WPSL_Statistics_Report();
        }

        /**
         * Add the 'Statistics' sub menu to the 
         * existing WPSL menu.
         * 
         * @since  1.0.0
         * @return void
         */        
        public function admin_menu() {
            add_submenu_page( 'edit.php?post_type=wpsl_stores', __( 'Statistics', 'wpsl-statistics' ), __( 'Statistics', 'wpsl-statistics' ), 'wpsl_statistics', 'wpsl_statistics', array( $this, 'load_template' ) );
        }

        /**
         * Load the correct page template.
         *
         * @since 1.0.0
         * @return void
         */
        public function load_template() {
            require_once( WPSL_STATISTICS_PLUGIN_DIR . 'admin/templates/html-stats-overview.php' );
        }
        
        /**
         * Create the date range dropdown
         *
         * @since 1.0.0
         * @return string $dropdown The HTML used in the date range dropdown.
         */
        public function date_range_dropdown() {
            
            $dropdown_values = array(
                'today'        => __( 'Today', 'wpsl-statistics' ),
                'yesterday'    => __( 'Yesterday', 'wpsl-statistics' ),
                'week'         => __( 'This week', 'wpsl-statistics' ),
                'last_week'    => __( 'Last week', 'wpsl-statistics' ),
                'month'        => __( 'This month', 'wpsl-statistics' ),
                'last_month'   => __( 'Last month', 'wpsl-statistics' ),
                'quarter'      => __( 'This quarter', 'wpsl-statistics' ),
                'last_quarter' => __( 'Last quarter', 'wpsl-statistics' ),
                'custom'       => __( 'Custom', 'wpsl-statistics' )
            );
                        
            $current_range = wpsl_get_current_date_range();
            
            $dropdown = '<select id="wpsl-change-stats-range" name="range" autocomplete="off">';
            
            foreach ( $dropdown_values as $key => $dropdown_value ) {
                $selected = ( $key == $current_range ) ? ' selected="selected"' : '';
                $dropdown .= "<option value='" . esc_attr( $key ) . "' $selected>" . esc_html( $dropdown_value ) . "</option>";
            }
			
			$dropdown .= "</select>";
			
			return $dropdown;   
        }
        
        /**
         * Create the stats details url.
         * 
         * @since 1.0.0
         * @param  string $address Current column name
         * @return string $address_url The url including the address details and the date range
         */
        public function create_stats_details_url( $address ) {

            $args = array(
                'address' => $address,
                'range'   => wpsl_get_current_date_range()
            );
            
            $query_args = array_merge( $args, $this->get_custom_date_args() );

            $address_url = '<a href="' . esc_url( add_query_arg( array_map( 'urlencode', $query_args ), admin_url( 'edit.php?post_type=wpsl_stores&page=wpsl_statistics&tab=searches' ) ) ) . '">' . esc_html( $address ) . '</a>'; 

            return $address_url;
        }
        
        /**
         * Get the set custom date query args.
         * 
         * @since 1.0.0
         * @return array $date_args The query arguments for the custom date range.
         */
        public function get_custom_date_args() {
            
            $date_args  = array();
            $query_args = array( 'start', 'end' );
            
            foreach ( $query_args as $arg ) {
                if ( isset( $_GET[$arg] ) && $_GET[$arg] ) {
                    $date_args[$arg] = $_GET[$arg];
                }
            }

            return $date_args;
        }

        /**
         * WPSL settings used in the wpsl-statistics.js.
         *
         * @since 1.0.0
         * @return array $settings_js The settings used in the wpsl-statistics.js
         */
        public function js_settings() {
            
            global $wpsl, $wpsl_settings;

            $js_settings = apply_filters( 'wpsl_stats_js_settings', array(
                'startLatlng'       => $wpsl_settings['start_latlng'],
                'startLabel'        => $wpsl_settings['start_label'],
                'zoomLevel'         => $wpsl_settings['zoom_level'],
                'markerPath'        => WPSL_URL . 'img/markers/',
                'markerIconProps'   => $this->get_marker_props(),
                'searchedMarker'    => $wpsl->frontend->create_retina_filename( $wpsl_settings['start_marker'] ),
                'nearbyMarker'      => $wpsl->frontend->create_retina_filename( $wpsl_settings['store_marker'] ),
                'noDataFound'       => __( 'No data found.', 'wpsl-statistics' ),
                'noExportData'      => __( 'No export data available', 'wpsl-statistics' ),
                'to'                => __( 'to', 'wpsl-statistics' ),
                'openStreetMap'     => __( 'OpenStreetMap', 'wpsl-statistics' ),
                'noNearbyLocations' => sprintf( __( 'No locations found within a %d %s radius.', 'wpsl-statistics' ), $this->nearby_locations_radius, $wpsl_settings['distance_unit'] )
            ) );
            
            return $js_settings;
        }
        
        /**
         * Get the used marker properties.
         *
         * @since 1.0.0
         * @link https://developers.google.com/maps/documentation/javascript/3.exp/reference#Icon
         * @return array $marker_props The marker properties.
         */        
        public function get_marker_props() {
            
            $marker_props = apply_filters( 'wpsl_stats_marker_props', array(
                'scaledSize' => '24,35', // 50% of the normal image to make it work on retina screens.
                'origin'     => '0,0',
                'anchor'     => '12,35'
            ) );
            
            return $marker_props;
        }
        
        /**
         * Add the required admin CSS / JS file.
         *
         * @since  1.0.0
         * @return void
         */        
        public function admin_scripts() {
            
            global $wpsl_settings;
            
            $min = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

            if ( isset( $_GET['page'] ) && ( $_GET['page'] == 'wpsl_statistics' ) ) {
                
                /*
                 * Make sure no other Google Map scripts can 
                 * interfere with the one from the store locator.
                 * 
                 * From the WPSL base plugin.
                 */
                wpsl_deregister_other_gmaps();

                wp_enqueue_style( 'jquery-style', '//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/themes/smoothness/jquery-ui.css' );
                wp_enqueue_style( 'wpsl-statistics-admin', plugins_url( '/css/style'. $min .'.css', __FILE__ ), false );
                
                wp_enqueue_script( 'underscore' );
                wp_enqueue_script( 'jquery-ui-datepicker' );
                wp_enqueue_script( 'wpsl-statistics', plugins_url( '/js/wpsl-statistics.js', __FILE__ ), array( 'jquery' ), false );

                if ( isset( $_GET['tab'] ) && $_GET['tab'] == 'tools' ) {
                    wp_enqueue_script( 'wpsl-statistics-tools', plugins_url( '/js/wpsl-statistics-tools' . $min . '.js', __FILE__ ), array( 'jquery' ), WPSL_STATISTICS_VERSION_NUM, true );
                }

                // Only load the Google Maps and Charts library when it's needed.
                if ( isset( $_GET['tab'] ) && $_GET['tab'] == 'dashboard' || isset( $_GET['address'] ) || !isset( $_GET['tab'] ) ) {

                    // Load the required files for the active map provider ( OSM only supported after WPSL 3.0+ ).
                    if ( isset( $wpsl_settings['active_map_service'] ) && $wpsl_settings['active_map_service'] == 'osm' ) {
                        wp_enqueue_script( 'wpsl-leaflet', 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.4.0/leaflet.js', '', '', true );
                        wp_enqueue_style( 'wpsl-leaflet', 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.4.0/leaflet.css', '', '' );

                        wp_enqueue_script( 'wpsl-stats-heatmap',  plugins_url( '/js/heatmap'. $min .'.js', __FILE__ ), '', '', true );
                        wp_enqueue_script( 'wpsl-stats-leaflet-heatmap',  plugins_url( '/js/leaflet-heatmap'. $min .'.js', __FILE__ ), '', '', true );
                        wp_enqueue_script( 'wpsl-stats-leaflet-bounce',  plugins_url( '/js/leaflet-bouncemarker'. $min .'.js', __FILE__ ), '', '', true );
                    } else {
                    	wp_enqueue_script( 'wpsl-gmap', ( '//maps.google.com/maps/api/js?libraries=places,visualization&language=' . $wpsl_settings['api_language'] . '&key=' . $wpsl_settings['api_browser_key']  ), false, '', true );
                    }

                    wp_enqueue_script( 'wpsl-chart-loader', ( '//www.gstatic.com/charts/loader.js' ), false, '', true );
                }

                wp_localize_script( 'wpsl-statistics', 'wpslStatsSettings', $this->js_settings() );
            }
        }
        
        /**
         * Add the underscore template code in the footer.
         * 
         * @since 1.0.0
         * @return void
         */
        public function admin_footer() {
            require_once( WPSL_STATISTICS_PLUGIN_DIR . 'admin/underscore-templates.php' );
        }

        /**
         * Remove any unused query args.
         *
         * @since 1.0.0
         * @return void
         */
        public function remove_unused_query_args() {

            if ( isset( $_GET['range'] ) && $_GET['range'] !== 'custom' && isset( $_GET['start'] ) && isset( $_GET['end'] ) ) {
                $referer = remove_query_arg( array( 'start', 'end' ) );

                wp_redirect( $referer );
                exit;
            }
        }

        /**
         * Add the screen options tab
         *
         * @since 1.1.0
         * @return void
         */
        public function screen_option() {

            if ( isset( $_GET['tab'] ) && $_GET['tab'] !== 'searches' || !isset( $_GET['tab'] ) ) {
                return;
            }

            $option = 'per_page';

            $args = array(
                'label'   => __( 'Items Per Page', 'wpsl-statistics' ),
                'default' => 20,
                'option'  => 'wpsl_stats_per_page'
            );

            add_screen_option( $option, $args );
        }

        /**
         * Sets the screen options option
         *
         * @since 1.1.0
         * @param bool|int $status Screen option value. Default false to skip.
         * @param string   $option The option name.
         * @param int      $value  The number of rows to use.
         * @return int
         */
        public function set_paging_options( $status, $option, $value ) {

            if ( 'wpsl_stats_per_page' === $option && ( $value > 0 && $value < 1000 ) ) {
                return $value;
            }

            return $status;
        }

        /**
         * Set a body class on the stats page that
         * shows the screen option tab.
         *
         * @since 1.1.0
         * @param array  $classes The classes that are placed on the body tag
         * @return array $classes
         */
        public function paging_body_class( $classes ) {

            $screen = get_current_screen();

            if ( ( $screen->id == 'wpsl_stores_page_wpsl_statistics' ) && ( isset( $_GET['tab'] ) && $_GET['tab'] == 'searches' ) ) {
                $classes .= 'wpsl-stats-all-searches';
            }

            return $classes;
        }
    }

    $GLOBALS['wpsl_stats'] = new WPSL_Statistics_Admin();
}
