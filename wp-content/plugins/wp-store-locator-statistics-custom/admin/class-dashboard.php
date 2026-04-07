<?php
/**
 * WPSL Statistics Dashboard class
 *
 * @since  1.1.0
 * @author Tijmen Smit
 */

if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'WPSL_Statistics_Dashboard' ) ) {

    class WPSL_Statistics_Dashboard {

        /**
         * Class constructor
         */
        public function __construct() {

            add_action( 'admin_enqueue_scripts', array( $this, 'dashboard_styles' ) );

            if ( current_user_can( 'wpsl_statistics' ) ) {
                add_action( 'wp_dashboard_setup', array( $this, 'init' ) );
            }
        }

        /**
         * Load the CSS styles for the dashboard widget
         *
         * @since 1.1.0
         * @return void
         */
        public function dashboard_styles() {

            global $pagenow;

            $min = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

            if ( $pagenow == 'index.php' ) {
                wp_enqueue_style( 'wpsl-statistics-dashboard', plugins_url( '/css/dashboard-widget'. $min .'.css', __FILE__ ), false );
            }
        }

        /**
         * Init the dashboard widget.
         *
         * @since 1.1.0
         * @return void
         */
        public function init() {
            wp_add_dashboard_widget( 'wpsl_statistics', __( 'WP Store Locator Statistics' , 'wpsl-statistics' ), array( $this, 'widget' ) );
        }

        /**
         * Create the widget output.
         *
         * @since 1.1.0
         * @return void
         */
        public function widget() {

            require_once( WPSL_STATISTICS_PLUGIN_DIR . 'admin/class-popular-searches-table.php' );

            $stats_report     = new WPSL_Statistics_Report();
            $popular_searches = new WPSL_Statistics_Popular_Searches_Table();

            $recent_searches  = $popular_searches->get_location_list( 'today' );
            $dashboard_range  = array( 'today', 'yesterday', 'week', 'month' );
            $results          = array();

            foreach ( $dashboard_range as $range ) {
                $dates = wpsl_calculate_date_range( $range );

                $stats_report->date_range = $dates;

                $graph_data = $stats_report->get_search_graph_data();
                $total      = 0;

                foreach ( $graph_data as $data ) {
                    $total += $data->count;
                }

                $results[$range] = $total;
            }

            $all_url = admin_url( 'edit.php?post_type=wpsl_stores&page=wpsl_statistics&tab=searches' );
            ?>
            <div class="wpsl-dashboard-statistics">
                <div class="wpsl-dashboard-statistics-overview">
                    <h4><?php _e( 'Searches', 'wpsl-statistics' ); ?></h4>
                    <table>
                        <tr>
                            <td><?php _e( 'Today', 'wpsl-statistics' ); ?></td>
                            <td><?php echo $results['today']; ?></td>
                        </tr>
                    </table>
                    <table>
                        <tr>
                            <td><?php _e( 'Yesterday', 'wpsl-statistics' ); ?></td>
                            <td><?php echo $results['yesterday']; ?></td>
                        </tr>
                    </table>
                    <table>
                        <tr>
                            <td><?php _e( 'Week', 'wpsl-statistics' ); ?></td>
                            <td><?php echo $results['week']; ?></td>
                        </tr>
                    </table>
                    <table>
                        <tr>
                            <td><?php _e( 'Current Month', 'wpsl-statistics' ); ?></td>
                            <td><?php echo $results['month']; ?></td>
                        </tr>
                    </table>
                </div>

                <table class="wpsl-dashboard-recent-statistics">
                    <thead>
                        <tr>
                            <td colspan="2">
                                <?php
                                _e('Today\'s Popular Searches', 'wpsl-statistics' );
                                 echo '&nbsp;–&nbsp;' . '<a href="' . esc_url( $all_url ) . '">' . __( 'View All', 'wpsl-statistics' ) . '</a>';
                                ?>
                            </td>
                        </tr>
                        <?php if ( $recent_searches ) { ?>
                        <tr>
                            <th><?php _e('Location', 'wpsl-statistics' ); ?></th>
                            <th><?php _e('Searches', 'wpsl-statistics' ); ?></th>
                        </tr>
                        <?php } ?>
                    </thead>
                    <tbody>
                    <?php
                    if ( $recent_searches ) {
                        foreach ( $recent_searches as $recent_search ) {
                            echo '<tr>';
                                echo '<td>';
                                echo '<a href="' . esc_url( admin_url( 'edit.php?post_type=wpsl_stores&page=wpsl_statistics&tab=searches&address=' . $recent_search['address'] . '&range=today' ) ) .'">' . esc_html( $recent_search['address'] ) . '</a>';
                                echo '</td>';
                                echo '<td>';
                                echo absint( $recent_search['count'] );
                                echo '</td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td>' . __( 'No recent searches found', 'wpsl-statistics' ) . '</td></tr>';
                    }
                    ?>
                    </tbody>
                </table>
            </div>
            <?php
        }
    }

    new WPSL_Statistics_Dashboard();
}