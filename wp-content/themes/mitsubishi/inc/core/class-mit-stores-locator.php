<?php

defined( 'ABSPATH' ) or exit;

class MIT_Stores_Locator {
  public function __construct() {
    add_filter( 'wpsl_skip_cpt_template', '__return_true' );
    add_filter( 'wpsl_listing_template', [ $this, 'custom_wpsl_listing_template' ] );
    add_filter( 'wpsl_info_window_template', [ $this, 'custom_wpsl_info_window_template' ] );
    add_filter( 'wpsl_store_data', [ $this, 'custom_wpsl_store_data' ] );
    add_filter( 'wpsl_templates', [ $this, 'custom_templates' ] );
    add_filter( 'wpsl_sql', [ $this, 'custom_sql' ] );
    add_filter( 'wpsl_store_meta', [ $this, 'custom_store_meta' ], 10, 2 );
  }

  function custom_store_meta( $store_meta, $store_id ) {
    $categories = wp_get_post_terms( $store_id, 'wpsl_store_category' );
    if ( ! empty( $categories ) ) {
      foreach ( $categories as $term ) {
        if ( $map_marker = get_field( 'map_marker', $term ) ) {
          $store_meta['categoryMarkerUrl'] = $map_marker['url'];
          break;
        }
      }
    }

    return $store_meta;
  }

  function custom_sql( $sql ) {
    global $wpdb, $wpsl, $wpsl_settings;

    // The placeholder values for the prepared statement in the SQL query.
    if ( empty( $args ) ) {
      $args = $_GET;
    }

    // Check if we need to filter the results by category.
    if ( isset( $args['dealer-service'] ) && $args['dealer-service'] ) {
      $filter_ids = array_map( 'absint', explode( ',', $args['dealer-service'] ) );

      $dealer_service_filter = "
        INNER JOIN $wpdb->term_relationships AS term_rel2 ON posts.ID = term_rel2.object_id
        INNER JOIN $wpdb->term_taxonomy AS term_tax2 ON term_rel2.term_taxonomy_id = term_tax2.term_taxonomy_id
          AND term_tax2.taxonomy = 'dealer_service'
          AND term_tax2.term_id IN (" . implode( ',', $filter_ids ) . ")
      ";
    } else {
      $dealer_service_filter = '';
    }

    return str_replace( 'WHERE ', " $dealer_service_filter WHERE ", $sql );
  }

  function create_dealer_service_filter( $parent = MIT_STORES_SERVICE_GROUP_ID__STOCKIST ): string {
    global $wpsl, $wpsl_settings;

    $terms = get_terms( [
      'taxonomy'   => 'dealer_service',
      'hide_empty' => true,
      'parent'     => $parent,
    ] );

    if ( count( $terms ) > 0 ) {
      $category = '<div id="wpsl-dealer-service">' . "\r\n";
      $category .= '<label for="wpsl-dealer-service-list">' . esc_html( $wpsl->i18n->get_translation( 'category_label', __( 'Dealer Services', 'wpsl' ) ) ) . '</label>' . "\r\n";

      $args = apply_filters( 'wpsl_dropdown_category_args', array(
          'show_option_none'  => $wpsl->i18n->get_translation( 'category_default_label', __( 'Any', 'wpsl' ) ),
          'option_none_value' => '0',
          'orderby'           => 'menu_order',
          'order'             => 'ASC',
          'child_of'          => $parent,
          'echo'              => 0,
          'hierarchical'      => 1,
          'name'              => 'dealer-service',
          'id'                => 'wpsl-dealer-service-list',
          'class'             => 'wpsl-dropdown wpsl-custom-dropdown',
          'taxonomy'          => 'dealer_service',
          'hide_if_empty'     => true,
        )
      );

      $category .= wp_dropdown_categories( $args );

      $category .= '</div>' . "\r\n";

      return $category;
    }

    return '';
  }

  function create_dealer_types_filter( $parent = MIT_STORES_DEALER_TYPE_GROUP_ID__STOCKIST ): string {
    global $wpsl, $wpsl_settings;

    $terms = get_terms( [
      'taxonomy'   => 'wpsl_store_category',
      'hide_empty' => true,
      'parent'     => $parent,
    ] );

    if ( count( $terms ) > 0 ) {
      $column_count = 3;

      $category = '<ul id="wpsl-checkbox-filter" class="wpsl-checkbox-' . $column_count . '-columns">';

      foreach ( $terms as $term ) {
        $category .= '<li>';
        $category .= '<label>';
        $category .= '<input type="checkbox" value="' . esc_attr( $term->term_id ) . '" ' . checked( $parent == MIT_STORES_DEALER_TYPE_GROUP_ID__REPAIR && 'asp' == $term->slug, true, false ) . '/>';
        $category .= esc_html( $term->name );
        $category .= '</label>';
        $category .= '</li>';
      }

      $category .= '</ul>';

      return $category;
    }

    return '';
  }

  function create_dealer_types_checkbox_filter( $parent = MIT_STORES_DEALER_TYPE_GROUP_ID__STOCKIST ): string {
    global $wpsl, $wpsl_settings;

    $terms = get_terms( [
      'taxonomy'   => 'wpsl_store_category',
      'hide_empty' => true,
      'parent'     => $parent,
    ] );

    if ( count( $terms ) > 0 ) {
      $column_count = 3;

      $category = '<ul id="wpsl-checkbox-filter" class="wpsl-checkbox-' . $column_count . '-columns">';

      foreach ( $terms as $term ) {
        $category .= '<li>';
        $category .= '<label>';
        $category .= '<input type="radio" name="wpsl-checkbox-filter" value="' . esc_attr( $term->term_id ) . '" ' . checked( $parent == MIT_STORES_DEALER_TYPE_GROUP_ID__REPAIR && 'asp' == $term->slug, true, false ) . '/>';
        $category .= esc_html( $term->name );
        $category .= '</label>';
        $category .= '</li>';
      }

      $category .= '</ul>';

      return $category;
    }

    return '';
  }

  function custom_wpsl_info_window_template( $listing_template ): string {
    global $wpsl_settings, $wpsl;

    $info_window_template = '<div data-store-id="<%= id %>" class="wpsl-info-window">' . "\r\n";
    $info_window_template .= "\t\t" . '<p>' . "\r\n";
    $info_window_template .= "\t\t\t" . wpsl_store_header_template() . "\r\n";  // Check which header format we use
    $info_window_template .= "\t\t\t" . '<span><%= address %></span>' . "\r\n";
    $info_window_template .= "\t\t\t" . '<% if ( address2 ) { %>' . "\r\n";
    $info_window_template .= "\t\t\t" . '<span><%= address2 %></span>' . "\r\n";
    $info_window_template .= "\t\t\t" . '<% } %>' . "\r\n";
    $info_window_template .= "\t\t\t" . '<span>' . wpsl_address_format_placeholders() . '</span>' . "\r\n"; // Use the correct address format
    $info_window_template .= "\t\t" . '</p>' . "\r\n";
    $info_window_template .= "\t\t" . '<% if ( phone ) { %>' . "\r\n";
    $info_window_template .= "\t\t" . '<span><strong>' . esc_html( $wpsl->i18n->get_translation( 'phone_label', __( 'Phone', 'wpsl' ) ) ) . '</strong>: <%= formatPhoneNumber( phone ) %></span>' . "\r\n";
    $info_window_template .= "\t\t" . '<% } %>' . "\r\n";
    // $info_window_template .= "\t\t" . '<% if ( fax ) { %>' . "\r\n";
    // $info_window_template .= "\t\t" . '<span><strong>' . esc_html( $wpsl->i18n->get_translation( 'fax_label', __( 'Fax', 'wpsl' ) ) ) . '</strong>: <%= fax %></span>' . "\r\n";
    // $info_window_template .= "\t\t" . '<% } %>' . "\r\n";
    $info_window_template .= "\t\t" . '<% if ( email ) { %>' . "\r\n";
    $info_window_template .= "\t\t" . '<span><strong>' . esc_html( $wpsl->i18n->get_translation( 'email_label', __( 'Email', 'wpsl' ) ) ) . '</strong>: <%= formatEmail( email ) %></span>' . "\r\n";
    $info_window_template .= "\t\t" . '<% } %>' . "\r\n";
    $info_window_template .= "\t\t" . '<% if ( url ) { %>' . "\r\n";
    $info_window_template .= "\t\t" . '<span style="word-break:break-all;"><strong>' . esc_html( $wpsl->i18n->get_translation( 'url_label', __( 'Url', 'wpsl' ) ) ) . '</strong>: <a href="<%= url %>" target="_blank"><%= url %></a></span>' . "\r\n";
    $info_window_template .= "\t\t" . '<% } %>' . "\r\n";
    $info_window_template .= "\t\t" . '<%= createInfoWindowActions( id ) %>' . "\r\n";
    $info_window_template .= "\t" . '</div>';

    return $info_window_template . "\n";
  }

  function custom_wpsl_listing_template( $listing_template ): string {
    global $wpsl, $wpsl_settings, $post;

    $listing_template = '<li data-store-id="<%= id %>">' . "\r\n";
    $listing_template .= "\t\t" . '<div class="wpsl-store-location">' . "\r\n";
    $listing_template .= "\t\t\t" . '<p class="wpsl-store-full-address"><%= thumb %>' . "\r\n";
    $listing_template .= "\t\t\t\t<span class='wpsl-store-name'>" . wpsl_store_header_template( 'listing' ) . "</span>\r\n"; // Check which header format we use
    $listing_template .= "\t\t\t\t" . '<span class="wpsl-street"><%= address %></span>' . "\r\n";
    $listing_template .= "\t\t\t\t" . '<% if ( address2 ) { %>' . "\r\n";
    $listing_template .= "\t\t\t\t" . '<span class="wpsl-street"><%= address2 %></span>' . "\r\n";
    $listing_template .= "\t\t\t\t" . '<% } %>' . "\r\n";
    $listing_template .= "\t\t\t\t" . '<span>' . wpsl_address_format_placeholders() . '</span>' . "\r\n"; // Use the correct address format

    if ( ! $wpsl_settings['hide_country'] ) {
      $listing_template .= "\t\t\t\t" . '<span class="wpsl-country"><%= country %></span>' . "\r\n";
    }

    $listing_template .= "\t\t\t" . '</p>' . "\r\n";

    // Extra content
    $listing_template .= "\t\t\t" . '<%= mit_extra_content %>' . "\r\n";

    // Show the phone, fax or email data if they exist.
    if ( $wpsl_settings['show_contact_details'] ) {
      $listing_template .= "\t\t\t" . '<p class="wpsl-contact-details">' . "\r\n";
      $listing_template .= "\t\t\t" . '<% if ( phone ) { %>' . "\r\n";
      $listing_template .= "\t\t\t" . '<span><strong>' . esc_html( $wpsl->i18n->get_translation( 'phone_label', __( 'Phone', 'wpsl' ) ) ) . '</strong>: <%= formatPhoneNumber( phone ) %></span>' . "\r\n";
      $listing_template .= "\t\t\t" . '<% } %>' . "\r\n";
      // $listing_template .= "\t\t\t" . '<% if ( fax ) { %>' . "\r\n";
      // $listing_template .= "\t\t\t" . '<span><strong>' . esc_html( $wpsl->i18n->get_translation( 'fax_label', __( 'Fax', 'wpsl' ) ) ) . '</strong>: <%= fax %></span>' . "\r\n";
      // $listing_template .= "\t\t\t" . '<% } %>' . "\r\n";
      $listing_template .= "\t\t\t" . '<% if ( email ) { %>' . "\r\n";
      $listing_template .= "\t\t\t" . '<span><strong>' . esc_html( $wpsl->i18n->get_translation( 'email_label', __( 'Email', 'wpsl' ) ) ) . '</strong>: <%= formatEmail( email ) %></span>' . "\r\n";
      $listing_template .= "\t\t\t" . '<% } %>' . "\r\n";
      $listing_template .= "\t\t\t" . '<% if ( url ) { %>' . "\r\n";
      $listing_template .= "\t\t\t" . '<span><strong>' . esc_html( $wpsl->i18n->get_translation( 'url_label', __( 'Url', 'wpsl' ) ) ) . '</strong>: <a href="<%= url %>" target="_blank"><%= url %></a></span>' . "\r\n";
      $listing_template .= "\t\t\t" . '<% } %>' . "\r\n";
      $listing_template .= "\t\t\t" . '</p>' . "\r\n";
    }

    $listing_template .= "\t\t\t" . wpsl_more_info_template() . "\r\n"; // Check if we need to show the 'More Info' link and info
    $listing_template .= "\t\t" . '</div>' . "\r\n";

    if ( MIT_STORES_REPAIR_SERVICE_PAGE_ID != $post->ID ) {
      $listing_template .= "\t\t" . '<div class="wpsl-direction-wrap">' . "\r\n";

      if ( ! $wpsl_settings['hide_distance'] ) {
        $listing_template .= "\t\t\t<span class='wpsl-distance'>" . '<%= distance %> ' . esc_html( wpsl_get_distance_unit() ) . '' . "</span>\r\n";
      }

      $listing_template .= "\t\t\t" . '<%= createDirectionUrl() %>' . "\r\n";
      $listing_template .= "\t\t" . '</div>' . "\r\n";
    }

    $listing_template .= "\t\t" . '<div>' . "\r\n";
    $listing_template .= "\t\t\t" . '<%= mit_enquire_button %>' . "\r\n";
    $listing_template .= "\t\t" . '</div>' . "\r\n";

    $listing_template .= "\t" . '</li>';

    return $listing_template;
  }

  function custom_wpsl_store_data( $stores ): array {
    foreach ( $stores as $index => $store ) {
      $stores[ $index ]['mit_extra_content']  = '';
      $stores[ $index ]['mit_enquire_button'] = '';

      if ( ! empty( $post_content = get_post( $store['id'] )->post_content ) ) {
        $stores[ $index ]['mit_extra_content'] = '<div class="generic-content mb-2">' . wpautop( $post_content ) . '</div>';
      }

      if (
        ( $request_a_quote_page = get_field( 'request_a_quote_page', 'options' ) ) &&
        has_term( 'specialist', 'wpsl_store_category', $store['id'] )
      ) {
        $stores[ $index ]['mit_enquire_button'] = '<a target="_blank" href="' . add_query_arg( 'storeID', $store['id'], get_permalink( $request_a_quote_page ) ) . '" class="btn btn--link">Request a quote</a>';;
      }
    }

    $repair_stores = get_posts( [
      'post_type'      => 'wpsl_stores',
      'post_status'    => 'publish',
      'posts_per_page' => - 1,
      'fields'         => 'ids',
      'tax_query'      => array(
        [
          'taxonomy'         => 'wpsl_store_category',
          'terms'            => [ MIT_STORES_DEALER_TYPE_GROUP_ID__REPAIR ],
          'field'            => 'term_id',
          'operator'         => 'IN',
          'include_children' => true,
        ],
      ),
    ] );

    $stores = array_filter( $stores, function ( $store ) use ( $repair_stores ) {
      return (
        ! empty( $_SERVER['HTTP_REFERER'] ) &&
        get_post( MIT_STORES_REPAIR_SERVICE_PAGE_ID )->post_name == basename( $_SERVER['HTTP_REFERER'] )
      ) ? in_array( $store['id'], $repair_stores ) // For Repair Service page only
        : ! in_array( $store['id'], $repair_stores );
    } );

    return array_values( $stores );
  }

  function custom_templates( $templates ) {
    /**
     * The 'id' is for internal use and must be unique ( since 2.0 ).
     * The 'name' is used in the template dropdown on the settings page.
     * The 'path' points to the location of the custom template,
     * in this case the folder of your active theme.
     */
    $templates[] = array(
      'id'   => 'custom',
      'name' => 'Mitsubishi Store Locator',
      'path' => get_theme_file_path( 'template-parts/stores-locator/stores-locator.php' ),
    );

    return $templates;
  }
}
