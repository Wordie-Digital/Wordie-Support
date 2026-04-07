<?php 
if ( !defined( 'ABSPATH' ) ) exit;

global $wpdb, $wpsl_settings;

require_once( WPSL_STATISTICS_PLUGIN_DIR . 'admin/class-popular-searches-table.php' );

$stats_data  = $this->statistics_report->collect_data();
$css_classes = $this->statistics_report->get_css_classes();
?>

<div id="wpsl-stats-wrap" class="wpsl-stats-dashboard <?php echo esc_attr( $css_classes ); ?>">
    
    <div class="postbox-container wpsl-searches">
        <div class="metabox-holder">
            <div class="postbox">
                <h3><?php _e( 'Searches', 'wpsl-statistics' ); ?></h3>
                <div class="inside">
                    <div id="wpsl-stats-graph" style="height:300px;"></div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="postbox-container wpsl-popular-searches">
        <div class="metabox-holder"> 
            <div class="postbox">
                <h3><?php _e( 'Popular Locations', 'wpsl-statistics' ); ?></h3>
                <div class="inside">
                    <div id="wpsl-popular-searches">
                        <?php 
                        $popular_location_searches = new WPSL_Statistics_Popular_Searches_Table();
                        $popular_location_searches->prepare_items();
                        $popular_location_searches->display(); 
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php 
    // Make sure the terms table isn't empty before showing the div.
    if ( $this->statistics_report->terms_exist ) {
    ?>
    <div class="postbox-container wpsl-stats-used-categories">
        <div class="metabox-holder">
            <div class="postbox">
                <h3><?php _e( 'Dealer Types', 'wpsl-statistics' ); ?></h3>
                <div class="inside">
                    <div id="wpsl-stats-pie-chart" style="width:100%; height:355px;"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="postbox-container wpsl-stats-used-categories" style="margin-right:0;">
        <div class="metabox-holder">
            <div class="postbox">
                <h3><?php _e( 'Product Types', 'wpsl-statistics' ); ?></h3>
                <div class="inside">
                    <div id="wpsl-stats-pie-chart-services" style="width:100%; height:355px;"></div>
                </div>
            </div>
        </div>
    </div>
    <?php 
    }
    ?>
    
    <div class="postbox-container wpsl-heatmap" style="margin-left: 0;">
        <div class="metabox-holder">
            <div class="postbox">
                <h3><?php _e( 'Heatmap', 'wpsl-statistics' ); ?></h3>
                <div class="inside">
                    <div id="wpsl-stats-map"></div>
                </div>
            </div>
        </div>
    </div>    
</div>

<script type="text/javascript">
/* <![CDATA[ */
var wpslStats = <?php echo json_encode( $stats_data ); ?>;
/* ]]> */
</script>
