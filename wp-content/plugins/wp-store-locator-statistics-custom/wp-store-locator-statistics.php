<?php
/*
Plugin URI: https://wpstorelocator.co/add-ons/statistics/
Plugin Name: WP Store Locator - Statistics
Description: Keep track where users are searching for, and see where there is demand for a possible store.
Author: Tijmen Smit
Author URI: https://wpstorelocator.co/
Version: 1.1.21
Text Domain: wpsl-statistics
Domain Path: /languages/
License: GPL v3

Copyright (C) 2016 Tijmen Smit - tijmen@wpstorelocator.co

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

if ( !defined( 'WPSL_STATISTICS_BASENAME' ) )
    define( 'WPSL_STATISTICS_BASENAME', plugin_basename( __FILE__ ) );

if ( !defined( 'WPSL_STATISTICS_PLUGIN_DIR' ) )
    define( 'WPSL_STATISTICS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

if ( !defined( 'WPSL_STATISTICS_VERSION_NUM' ) )
    define( 'WPSL_STATISTICS_VERSION_NUM', '1.1.21' );

class WPSL_Statistics {
    
    public $min_version = '2.2.4';

    /**
     * Class constructor
     */          
    function __construct() {

        $this->maybe_update_wpsl();
        
        add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
    }

    /**
     * Make sure WPSL meets the min required version
     * before including the required files and handling the license.
     *
     * @since 1.0.0
     * @return void
     */
    public function maybe_update_wpsl() {

        if ( defined( 'WPSL_VERSION_NUM' ) ) {
            if ( version_compare( WPSL_VERSION_NUM, $this->min_version, '<' ) ) {
                add_action( 'all_admin_notices', array( $this, 'update_wpsl_notice' ) );
            } else {
                $this->set_table_names();
                $this->includes();
                $this->setup_license();
            }
        }
    }

    /**
     * Show a notice telling the user to update 
     * WPSL before they can use the statistics add-on.
     *
     * @since 1.0.0
     * @return void
     */
    public function update_wpsl_notice() {
        echo '<div class="error"><p>' . sprintf( __( 'The Statistics add-on requires at least version %s of WP Store Locator. Please upgrade to the %slatest version%s.', 'wpsl-statistics' ), $this->min_version, '<a href="https://wordpress.org/plugins/wp-store-locator/">', '</a>' ) . '</p></div>';
    }
    
    /**
     * Set the table names.
     *
     * @since 1.0.0
     * @return void
     */
    public function set_table_names() {
                            
        global $wpdb;

        $wpdb->wpsl_stats_searches           = $wpdb->prefix . 'wpsl_stats_searches';
        $wpdb->wpsl_stats_terms              = $wpdb->prefix . 'wpsl_stats_terms';
        $wpdb->wpsl_stats_term_relationships = $wpdb->prefix . 'wpsl_stats_term_relationships';
    }

    /**
     * Include the required files.
     *
     * @since 1.0.0
     * @return void
     */
    public function includes() {
        
        require_once( WPSL_STATISTICS_PLUGIN_DIR . 'inc/wpsl-statistics-functions.php' );
        
        if ( is_admin() ) {
            require_once( WPSL_STATISTICS_PLUGIN_DIR . 'admin/class-admin.php' );
        }
    }

    /**
     * Handle the addon license.
     *
     * @since 1.0.0
     * @return void
     */
    public function setup_license() {
        
        if ( class_exists( 'WPSL_License_Manager' ) ) {
            $license = new WPSL_License_Manager( 'Statistics', WPSL_STATISTICS_VERSION_NUM, 'Tijmen Smit', __FILE__ );
        }
    }

    /**
     * Load the translations from the language folder.
     *
     * @since 1.0.0
     * @return void
     */
    public function load_plugin_textdomain() {

        $domain = 'wpsl-statistics';
        $locale = apply_filters( 'plugin_locale', get_locale(), $domain );

        // Load the language file from the /wp-content/languages/wp-store-locator-statistics folder, custom + update proof translations.
        load_textdomain( $domain, WP_LANG_DIR . '/wp-store-locator-statistics/' . $domain . '-' . $locale . '.mo' );

        // Load the language file from the /wp-content/plugins/wp-store-locator-statistics/languages/ folder.
        load_plugin_textdomain( $domain, false, dirname( WPSL_STATISTICS_BASENAME ) . '/languages/' );
    }
}

/**
 * Get started.
 *
 * @since 1.0.0
 * @return void
 */
function wpsl_statistics_init() {
    
    // Make sure WP Store Locator itself is active.
    if ( !class_exists( 'WP_Store_locator' ) ) {
        return;
    }
    
    new WPSL_Statistics();
}

add_action( 'plugins_loaded', 'wpsl_statistics_init' );

/**
 * Handle the plugin activation.
 *
 * @since 1.0.0
 * @param  boolean $network_wide True if "Network Activate" action is used in WPMU.
 * @return void
 */
function wpsl_statistics_activate( $network_wide ) {
    require_once( WPSL_STATISTICS_PLUGIN_DIR . 'inc/install.php' );
    
    wpsl_statistics_install( $network_wide );
}

register_activation_hook( __FILE__, 'wpsl_statistics_activate' );