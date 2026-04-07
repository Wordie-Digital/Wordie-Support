<?php

defined( 'ABSPATH' ) or exit;

class MIT_Rest_Controller extends WP_REST_Controller {
  public function __construct() {
    add_action( 'rest_api_init', array( $this, 'register_routes' ) );
  }

  public function register_routes() {
    register_rest_route( untrailingslashit( 'meaust/v1' ),
      '/stores-search-stats/',
      array(
        'methods'             => WP_REST_Server::READABLE,
        'callback'            => array( $this, 'stores_search_stats' ),
        'permission_callback' => array( $this, 'validate_request' ),
        'args'                => $this->_get_endpoint_args( [
          'type',
        ] ),
      )
    );
  }

  public function stores_search_stats( WP_REST_Request $request ): array {
    global $wpdb;

    $wpdb->wpsl_stats_searches           = $wpdb->prefix . 'wpsl_stats_searches';
    $wpdb->wpsl_stats_terms              = $wpdb->prefix . 'wpsl_stats_terms';
    $wpdb->wpsl_stats_term_relationships = $wpdb->prefix . 'wpsl_stats_term_relationships';

    $excludes_dealer_type = implode( ',', get_terms( array(
      'taxonomy'   => [ 'dealer_service' ],
      'fields'     => 'ids',
      'hide_empty' => false,
    ) ) ?: [] );

    $excludes_product_type = implode( ',', get_terms( array(
      'taxonomy'   => [ 'wpsl_store_category' ],
      'fields'     => 'ids',
      'hide_empty' => false,
    ) ) ?: [] );

    $product_type = $wpdb->get_results(
      $wpdb->prepare( "
        SELECT stats.search_id, stats.search_location, stats.search_date, stats.lat, stats.lng, stats_rel.term_id, stats_terms.name
                FROM $wpdb->wpsl_stats_searches AS stats
          INNER JOIN $wpdb->wpsl_stats_term_relationships AS stats_rel ON stats_rel.search_id = stats.search_id
          INNER JOIN $wpdb->wpsl_stats_terms AS stats_terms ON stats_terms.term_id = stats_rel.term_id
          WHERE stats_rel.term_id NOT IN ($excludes_product_type) AND stats.search_date >= (NOW() - INTERVAL 180 DAY)
          ORDER BY `search_date` ASC
        " ), ARRAY_A
    );

    foreach ( $product_type as $index => $p ) {
      $product_type[ $index ]['type'] = 'product_type';
    }

    $dealer_type = $wpdb->get_results(
      $wpdb->prepare( "
        SELECT stats.search_id, stats.search_location, stats.search_date, stats.lat, stats.lng, stats_rel.term_id, stats_terms.name
                FROM $wpdb->wpsl_stats_searches AS stats
          INNER JOIN $wpdb->wpsl_stats_term_relationships AS stats_rel ON stats_rel.search_id = stats.search_id
          INNER JOIN $wpdb->wpsl_stats_terms AS stats_terms ON stats_terms.term_id = stats_rel.term_id
          WHERE stats_rel.term_id NOT IN ($excludes_dealer_type) AND stats.search_date >= (NOW() - INTERVAL 180 DAY)
          ORDER BY `search_date` ASC
        " ), ARRAY_A
    );

    foreach ( $dealer_type as $index => $p ) {
      $dealer_type[ $index ]['type'] = 'dealer_type';
    }

    return array_merge( $product_type, $dealer_type );
  }

  public function validate_request( WP_REST_Request $request ): bool {
    return true;
    // return isset( $_SERVER['HTTP_X_WP_NONCE'] ); // Basic REST Auth via cookie: nonce will be handled automatically as long as it set in header
  }

  public function validate_arg( $value, WP_REST_Request $request, $param ) {
    $attributes = $request->get_attributes();

    if ( isset( $attributes['args'][ $param ] ) ) {
      $argument = $attributes['args'][ $param ];
      switch ( $argument['type'] ) {
        case 'string':
          if ( ! is_string( $value ) ) {
            return new WP_Error( 'REST_INVALID_PARAM', sprintf( esc_html__( '%1$s is not of type %2$s' ), $param, 'string' ), array( 'status' => 400 ) );
          }
          break;

        case 'number':
          if ( ! is_numeric( $value ) ) {
            return new WP_Error( 'REST_INVALID_PARAM', sprintf( esc_html__( '%1$s is not of type %2$s' ), $param, 'number' ), array( 'status' => 400 ) );
          }
          break;
      }
    } else {
      return new WP_Error( 'REST_INVALID_PARAM', sprintf( esc_html__( '%s was not registered as a request argument.' ), $param ), array( 'status' => 400 ) );
    }

    return true;
  }

  public function sanitize_arg( $value, WP_REST_Request $request, $param ) {
    $attributes = $request->get_attributes();

    if ( isset( $attributes['args'][ $param ] ) ) {
      $argument = $attributes['args'][ $param ];
      switch ( $argument['type'] ) {
        case 'string':
          return sanitize_text_field( $value );
        case 'number':
          return absint( $value );
      }
    } else {
      return new WP_Error( 'REST_INVALID_PARAM', sprintf( esc_html__( '%s was not registered as a request argument.' ), $param ), array( 'status' => 400 ) );
    }

    return new WP_Error( 'REST_API_SAD', esc_html__( 'Something went wrong.' ), array( 'status' => 500 ) );
  }

  private function _get_endpoint_args( array $keys ): array {
    $args = array();

    $args['type'] = array(
      'description'       => esc_html__( 'Query type' ),
      'type'              => 'string',
      'validate_callback' => array( $this, 'validate_arg' ),
      'sanitize_callback' => array( $this, 'sanitize_arg' ),
      'required'          => false,
    );

    return array_filter( $args,
      function ( $key ) use ( $keys ) {
        return in_array( $key, $keys );
      },
      ARRAY_FILTER_USE_KEY
    );
  }
}
