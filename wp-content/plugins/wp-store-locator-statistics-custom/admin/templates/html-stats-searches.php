<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( isset( $_GET['address'] ) && $_GET['address'] ) {
    require_once( WPSL_STATISTICS_PLUGIN_DIR . 'admin/templates/html-stats-address-details.php' );
} else {
    require_once( WPSL_STATISTICS_PLUGIN_DIR . 'admin/class-all-searches-table.php' );
    
    $all_location_searches = new WPSL_Statistics_All_Searches_Table();
    $all_location_searches->prepare_items();
    ?>
    
    <form id="wpsl-stats-search" method="get">
        <?php 
        $date_range = wpsl_get_current_date_range();
        $all_location_searches->search_box( 'Search', 'wpsl-statistics' );
        $all_location_searches->display();
        ?>
        
        <input type="hidden" value="searches" name="tab" />
        <input type="hidden" value="wpsl_stores" name="post_type" />
        <input type="hidden" value="wpsl_statistics" name="page" />
        <input type="hidden" value="<?php echo $date_range; ?>" name="range" />
    </form>
<?php
}