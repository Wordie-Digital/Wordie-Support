<?php
if ( !defined( 'ABSPATH' ) ) exit;

$tab = ( isset( $_GET['tab' ] ) && $_GET['tab' ] == 'searches' ) ? 'searches' : 'dashboard';
?>

<div class="wpsl-stats-date-filter">
    <form method="get" autocomplete="off">
        <input type="hidden" value="wpsl_stores" name="post_type" />
        <input type="hidden" value="wpsl_statistics" name="page" />
        <input type="hidden" value="<?php echo esc_attr( $tab ); ?>" name="tab" />
        
        <?php
        if ( isset( $_GET['address'] ) && $_GET['address'] ) {
            echo '<input type="hidden" value="' . esc_attr( urldecode( $_GET['address'] ) ) . '" name="address" />';
        }
        ?>

        <?php echo $this->date_range_dropdown(); ?>
        <input type="submit" value="<?php _e( 'Go', 'wpsl-statistics' ); ?>" class="button-primary" id="wpsl-load-stats">
        
        <?php
        // Show the link to export the statistics data.
        if ( !isset( $_GET['s'] ) && current_user_can( 'wpsl_statistics_export' ) ) {
            $stats_export = new WPSL_Statistics_Export();

            echo $stats_export->export_link();
        }

        if ( isset( $_GET['range'] ) && $_GET['range'] == 'custom' ) {
            $custom_range_css = '';
        } else {
            $custom_range_css = 'wpsl-hide';
        }

        $start = ( isset( $_GET['start'] ) ) ? urldecode( $_GET['start'] ) : '';
        $end   = ( isset( $_GET['end'] ) ) ? urldecode( $_GET['end'] ) : '';
        ?>

        <div class="wpsl-stats-custom-range <?php echo $custom_range_css; ?>">
            <input id="wpsl-stats-start-date" type="text" value="<?php echo esc_attr( $start ); ?>" name="start" placeholder="yyyy-mm-dd">
            <span><?php _e( 'to', 'wpsl-statistics' ); ?></span>
            <input id="wpsl-stats-end-date" type="text" value="<?php echo esc_attr( $end ); ?>" name="end" placeholder="yyyy-mm-dd">
        </div>
    </form>
</div>