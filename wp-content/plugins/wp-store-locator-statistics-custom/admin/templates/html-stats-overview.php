<?php
if ( !defined( 'ABSPATH' ) ) exit;
?>

<div class="wrap wpsl-statistics">
	<h2><?php _e( 'Statistics', 'wpsl-statistics' ); ?></h2>
    
    <?php 
    global $wpdb, $admin;

    $dates       = wpsl_calculate_date_range();
    $current_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : '';
    
    $tabs = array(
        'dashboard' => __( 'Dashboard', 'wpsl-statistics' ),
        'searches'  => __( 'All Searches', 'wpsl-statistics' )
    );

    // Only include the tools tab if the user has the capability to access it.
    if ( current_user_can( 'wpsl_statistics_tools' ) ) {
        $tabs['tools'] = __( 'Tools', 'wpsl-statistics' );
    }

    echo '<h2 id="wpsl-tabs" class="nav-tab-wrapper">';
        
    foreach ( $tabs as $name => $label ) {
        $args = array( 'tab' => $name );

        if ( isset( $_GET['range'] ) ) {
            $args['range'] = wpsl_get_current_date_range();
        }
        
        $query_args = array_merge( $args, $this->get_custom_date_args() );
        
        if ( !$current_tab && $name == 'dashboard' || $current_tab == $name ) {
            $active_tab = 'nav-tab-active';
        } else {
            $active_tab = '';
        }

        echo '<a class="nav-tab ' . $active_tab . '" title="' . esc_attr( $label ) . '" href="' . esc_url( add_query_arg( array_map( 'urlencode', $query_args ), admin_url( 'edit.php?post_type=wpsl_stores&page=wpsl_statistics' ) ) ) . '">' . esc_html( $label ) . '</a>';
    }

    echo '</h2>';

    if ( $current_tab !== 'tools' ) {
        require_once( WPSL_STATISTICS_PLUGIN_DIR . 'admin/templates/html-date-filters.php' );
    }

    switch ( $current_tab ) {
        case 'tools':
            require_once( WPSL_STATISTICS_PLUGIN_DIR . 'admin/templates/html-stats-tools.php' );
        break;
        case 'searches':
            require_once( WPSL_STATISTICS_PLUGIN_DIR . 'admin/templates/html-stats-searches.php' );
        break;
        default:
            require_once( WPSL_STATISTICS_PLUGIN_DIR . 'admin/templates/html-stats-dashboard.php' );
        break;
    }
    ?>
</div>   