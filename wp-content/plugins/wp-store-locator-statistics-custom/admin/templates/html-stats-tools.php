<?php
/* Statistics tools template */
if ( !defined( 'ABSPATH' ) ) exit;

if ( !current_user_can( 'wpsl_statistics_tools' ) ) {
    wp_die( __( 'You do not have permission to access the tools page.', 'wpsl-statistics' ), '', array( 'response' => 403 ) );
}

$max_upload_size = wp_max_upload_size();
?>

<div id="wpsl-tools">
    <form method="post" id="wpsl-stats-tools-form" action="<?php echo admin_url( 'edit.php?post_type=wpsl_stores&page=wpsl_statistics&tab=tools' ); ?>">
        <?php
        wp_nonce_field( 'wpsl_stats_tools', 'wpsl_stats_tools_nonce' );
        ?>
        <table class="form-table">
            <tbody>
            <tr>
                <th scope="row">
                    <label for="wpsl-bulk-reset"><?php _e( 'Reset Statistics', 'wpsl-statistics' ); ?></label>
                </th>
                <td>
                    <input type="checkbox" id="wpsl-bulk-reset" name="wpsl_stats_tools[bulk_reset]" value="">
                </td>
            </tr>
            </tbody>
        </table>
        <input type="hidden" name="wpsl_action" value="stats_tools" />
        <p><input type="submit" value="<?php _e( 'Submit', 'wpsl-statistics' ); ?>" class="button-primary"></p>
    </form>
</div>
<div id="dialog" title="Please confirm" style="display: none;">
    <p><?php _e( 'Are you sure you want to reset the statistics? This cannot be undone, unless you restore a backup.', 'wpsl-statistics' ); ?></p>
</div>