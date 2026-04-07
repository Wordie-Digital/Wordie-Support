<?php
if ( !defined( 'ABSPATH' ) ) exit;

global $wpsl_stats, $wpsl_settings;

?>
<script id="wpsl-nearby-locations-template" type="text/template">
    <?php
        $nearby_locations_template = '<li data-store-id="<%= id %>">' . "\r\n";
        $nearby_locations_template .= "\t\t" . '<p>' . "\r\n";
        $nearby_locations_template .= "\t\t" .  wpsl_store_header_template( 'listing' ) . "\r\n";
        $nearby_locations_template .= "\t\t" . '<span class="wpsl-street"><%= address %></span>' . "\r\n";
        $nearby_locations_template .= "\t\t" . '<span>' . wpsl_address_format_placeholders() . '</span>' . "\r\n"; // Use the correct address format 
        $nearby_locations_template .= "\t\t" . '</p>' . "\r\n";

        /*
         * Check if the terms exist, and if we should include the nearby terms in the template data.
         * The 'show_nearby_terms' default to true, but can be changed with a filter to false.
         */
        if ( $wpsl_stats->statistics_report->terms_exist && $wpsl_stats->statistics_report->show_nearby_terms ) {
            $nearby_locations_template .= "\t\t\t" . '<% if ( terms ) { %>' . "\r\n";
            $nearby_locations_template .= "\t\t\t" . '<p>' . __( 'Categories:', 'wpsl-statistics' ) . ' <%= terms %></p>' . "\r\n";
            $nearby_locations_template .= "\t\t\t" . '<% } %>' . "\r\n";
        }

        $nearby_locations_template .= "\t\t" . '<%= distance %> ' . esc_html( $wpsl_settings['distance_unit'] ) . '' . "\r\n";
        $nearby_locations_template .= "\t" . '</li>';

        echo apply_filters( 'wpsl_stats_nearby_locations_template', $nearby_locations_template . "\n" );
    ?>
</script>