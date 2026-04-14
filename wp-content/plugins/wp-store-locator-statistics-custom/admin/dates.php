<?php
/**
 * WPSL Statistics date functions.
 * 
 * @since  1.0.0
 * @author Tijmen Smit
 */

if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Get the current date range.
 * 
 * If no range is set then it will default to 'week'.
 * 
 * @since  1.0.0
 * @return string $current_range The used date range.
 */
function wpsl_get_current_date_range() {
    
    $current_range = isset( $_GET['range'] ) ? urldecode( $_GET['range'] ) : 'week';
    $allowed_range = array( 'today', 'yesterday', 'week', 'last_week', 'month', 'last_month', 'quarter', 'last_quarter', 'custom' );
    
    if ( !in_array( $current_range, $allowed_range ) ) {
        $current_range = 'week';
    }
    
    return $current_range;
}

/**
 * Calculate the start and end date range.
 * 
 * @since  1.0.0
 * @param  string $range The optional date range
 * @return object $range Holds the start and end
 */
function wpsl_calculate_date_range( $range = '' ) {
    
    $date = new stdClass();

    // If nothing is passed, then grab the $_GET values
    if ( ! $range ) {
        $range = wpsl_get_current_date_range();
    }

    switch ( $range ) {
        case 'today':
            $date->start = strtotime( 'today' );
            $date->end   = strtotime( 'tomorrow - 1 second' );

            break;
        case 'yesterday':
            $date->start = strtotime( 'yesterday' );
            $date->end   = strtotime( 'today - 1 second' );

            break;
        case 'week':
            $date = wpsl_get_week_range();

            break;
        case 'last_week':
            $date->start = strtotime( 'monday this week -7 days midnight' );
            $date->end   = strtotime( 'next monday -1 second', $date->start );

            break;
        case 'month':
            $date->start = strtotime( 'first day of this month midnight' );
            $date->end   = strtotime( 'first day of next month midnight - 1 second' );

            break;
        case 'last_month':
            $date->start = strtotime( 'first day of last month midnight' );
            $date->end   = strtotime( 'first day of this month midnight - 1 second' );

            break;
        case 'quarter':
            $year  = date( 'Y' );
            $month = date( 'n', current_time( 'timestamp' ) );
            
            if ( $month <= 3 ) {
                $date->start = strtotime( $year . '-01-01 00:00:00' );
                $date->end   = strtotime( $year . '-03-31 23:59:59' );
            } else if ( $month <= 6 ) {
                $date->start = strtotime( $year . '-04-01 00:00:00' );
                $date->end   = strtotime( $year . '-06-30 23:59:59' );
            } else if ( $month <= 9 ) {
                $date->start = strtotime( $year . '-07-01 00:00:00' );
                $date->end   = strtotime( $year . '-09-30 23:59:59' );   
            } else {
                $date->start = strtotime( $year . '-10-01 00:00:00' );
                $date->end   = strtotime( $year . '-12-31 23:59:59' );   
            }

            break;
        case 'last_quarter':
            $month = date( 'n', current_time( 'timestamp' ) );

            // If we are in the first quarter, and need to show data for the last quarter then we need to adjust the year.
            $year  = ( $month < 3 ) ? date('Y', strtotime( '-1 year' ) ) : date( 'Y' );

            if ( $month <= 3 ) {
                $date->start = strtotime( $year . '-10-01 00:00:00' );
                $date->end   = strtotime( $year . '-12-31 23:59:59' );
            } else if ( $month <= 6 ) {
                $date->start = strtotime( $year . '-01-01 00:00:00' );
                $date->end   = strtotime( $year . '-03-31 23:59:59' );
            } else if ( $month <= 9 ) {
                $date->start = strtotime( $year . '-04-01 00:00:00' );
                $date->end   = strtotime( $year . '-06-30 23:59:59' );   
            } else {
                $date->start = strtotime( $year . '-07-01 00:00:00' );
                $date->end   = strtotime( $year . '-09-31 23:59:59' );   
            }

            break;
        case 'custom':
            if ( ( isset( $_GET['start'] ) && $_GET['start'] ) && ( isset( $_GET['end'] ) && $_GET['end'] ) ) {
                $date->start = strtotime( urldecode( $_GET['start'] ) );
                $date->end   = strtotime( urldecode( $_GET['end'] ) . ' 23:59:59' );
            } else {
                $date = wpsl_get_week_range();   
            }

            break;
    }

    $date->start = date( 'Y-m-d H:i:s', $date->start );
    $date->end   = date( 'Y-m-d H:i:s', $date->end );

    return $date;
}

/**
 * Get the date range for the current week.
 * 
 * @since  1.0.0
 * @return object $date Date range from monday - sunday
 */
function wpsl_get_week_range() {
    
    $date = new stdClass();

    $date->start = strtotime( 'monday this week midnight' );
    $date->end   = strtotime( 'next monday - 1 second' );
    
    return $date;
}

/**
 * Create a date range that falls 
 * within the start and end dates.
 *
 * @since  1.0.0
 * @param  object $dates      The start and end date.
 * @param  string $type       Type of date range ( days or hours ).
 * @return array  $date_range The full date range.
 */
function wpsl_create_full_date_range( $dates, $type = 'days' ) {
    
    $date_range = array();
    $current    = strtotime( $dates->start );
    $last       = strtotime( $dates->end );
    $format     = 'Y-m-d';
    $increase   = '+1 day';
    
    // Only used if the shown stats are for a single day.
    if ( $type == 'hours' ) {
        $format   = 'Y-m-d H:00:00';
        $increase = '+1 hour';
    }
    
    while ( $current <= $last ) {
        $date_range[] = date( $format, $current );
        $current      = strtotime( $increase, $current );
    }
    
    return $date_range;
}

/**
 * Check if the start and end date are the same.
 * 
 * This only happens when the date range is 
 * set to 'today', 'yesterday', or if a custom
 * range date is selected with the same 
 * start / end data.
 * 
 * @since  1.0.0
 * @param  object $dates     The start and end date.
 * @return bool   $is_single True if they are the same, otherwise false.
 */
function wpsl_is_single_date( $dates ) {

    $is_single  = false;
    $start_time = date( 'Y-m-d', strtotime( $dates->start ) );
    $end_time   = date( 'Y-m-d', strtotime( $dates->end ) );

    if ( $start_time == $end_time ) {
        $is_single = true;
    }
    
    return $is_single;
}