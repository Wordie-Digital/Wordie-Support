<?php
if ( !defined( 'ABSPATH' ) ) exit;

global $wpdb, $wpsl_settings;

$address     = urldecode( $_GET['address'] );
$stats_data  = $this->statistics_report->collect_data();
$css_classes = $this->statistics_report->get_css_classes();
?>

<div id="wpsl-stats-wrap" class="wpsl-search-details <?php echo esc_attr( $css_classes ); ?>">
    <p><?php echo sprintf( __( 'Search Details for %s', 'wpsl-statistics' ), esc_html( $address ) ); ?></p>

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
    
    <div class="postbox-container wpsl-nearby-locations">
        <div class="metabox-holder">
            <div class="postbox">
                <h3><?php _e( 'Nearby Locations', 'wpsl-statistics' ); ?></h3>
                <div class="inside">
                    <div id="wpsl-stats-map" class="wpsl-address-details"></div>
                    <ul></ul>
                </div>
            </div>
        </div>
    </div>
    
    <?php
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

    <div class="postbox-container wpsl-stats-used-categories" style="margin-left: 0;">
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
</div>

<script type="text/javascript">
/* <![CDATA[ */
var wpslStats = <?php echo json_encode( $stats_data ); ?>;
/* ]]> */
</script>
