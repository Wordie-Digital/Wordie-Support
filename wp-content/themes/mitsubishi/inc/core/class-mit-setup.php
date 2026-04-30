<?php

defined( 'ABSPATH' ) or exit;

class MIT_Setup {
  public function __construct() {
    add_action( 'wp_head', [ $this, 'pingback_header' ] );
    add_action( 'wp_head', [ $this, 'hook_head' ] );
    add_action( 'wp_footer', [ $this, 'wp_footer' ], 99 );
    add_action( 'after_setup_theme', [ $this, 'setup_theme' ] );
    add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ], 99 );
    add_action( 'init', [ $this, 'cpt_register_release' ] );
    add_action( 'init', [ $this, 'cpt_register_career' ] );
    add_action( 'init', [ $this, 'cpt_register_download' ] );
    add_action( 'init', [ $this, 'cpt_register_video' ] );
    add_action( 'init', [ $this, 'cpt_register_recipe' ] );
    add_action( 'init', [ $this, 'tax_register_dealer_service' ] );
    add_action( 'init', [ $this, 'set_user_type_cookie' ] );
    add_action( 'init', [ $this, 'migrate_product_other_features' ] );
    add_filter( 'body_class', [ $this, 'body_classes' ] );
    add_filter( 'excerpt_more', [ $this, 'excerpt_more' ] );
    add_filter( 'excerpt_length', [ $this, 'excerpt_length' ] );
    add_action( 'init', [ $this, 'add_tags_to_pages' ] );

    // Shortcodes
    add_shortcode( 'mit_dynamic_store_info', [ $this, 'dynamic_store_info' ] );
    add_shortcode( 'mit_salesforce_forms', [ $this, 'salesforce_forms' ] );

    // Rankmath
    add_filter( 'rank_math/frontend/breadcrumb/items', [ $this, 'custom_breadcrumbs' ] );
    add_filter( 'woocommerce_get_breadcrumb', [ $this, 'custom_breadcrumbs' ] );

    // Customise table tags in content and ACF fields
    add_filter( 'the_content', [ $this, 'customise_table_tags' ] );
    add_filter( 'acf/format_value', [ $this, 'customise_table_tags' ] );

    // Performance: preconnect to third-party origins used on every page
    add_filter( 'wp_resource_hints', [ $this, 'add_preconnect_hints' ], 10, 2 );

    // Performance: dequeue WooCommerce CSS on non-WooCommerce pages
    add_action( 'wp_enqueue_scripts', [ $this, 'dequeue_woo_css' ], 100 );

    // Performance: strip slick carousel CSS hardcoded via Elementor custom code —
    // it cannot be dequeued (no WP handle), so intercept wp_head output instead.
    add_action( 'wp_head', [ $this, 'start_head_buffer' ], 1 );
    add_action( 'wp_head', [ $this, 'end_head_buffer' ], 999 );

    // Performance: disable WordPress emoji scripts and styles
    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    remove_action( 'wp_print_styles', 'print_emoji_styles' );
    remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
    remove_action( 'admin_print_styles', 'print_emoji_styles' );
    remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
    remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
    remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
    add_filter( 'wp_resource_hints', [ $this, 'remove_emoji_dns_prefetch' ], 10, 2 );
    add_filter( 'tiny_mce_plugins', [ $this, 'remove_tinymce_emoji' ] );

    // Plugins
    add_filter( 'rocket_cache_dynamic_cookies', [ $this, 'wp_rocket_dynamic_cookie' ] );
    add_filter( 'alm_filters_public_taxonomies', '__return_false' );
    add_filter( 'alm_filters_edit', '__return_false' );
    add_action( 'elementor_pro/forms/new_record', [ $this, 'create_user_from_form' ], 99, 2 );

    // Custom actions
    add_action( 'el_posts_grid_download_filter_professional', [ $this, 'download_filter_professional_func' ], 10, 3 );

    // Pages slug have priority over any other taxonomies
    // add_action( 'init', [ $this, 'page_priority_init' ] );
    // add_filter( 'rewrite_rules_array', [ $this, 'page_priority_prepend_page_rewrite_rules' ] );
    // add_filter( 'page_rewrite_rules', [ $this, 'page_priority_collect_page_rewrite_rules' ] );

    // Ajax requests
    add_action( "wp_ajax_submit_c4c_form", [ $this, "ajax_submit_c4c_form_func" ] );
    add_action( "wp_ajax_nopriv_submit_c4c_form", [ $this, "ajax_submit_c4c_form_func" ] );
  }

  function create_user_from_form( $record, $handler ) {
    $email = ( current( $record->get_field( [ 'id' => 'email', ] ) ?: [ 'value' => '' ] ) )['value'];

    if (
      empty( $email ) ||
      'portal_signup' != $record->get_form_settings( 'form_name' )
    ) {
      return;
    }

    $user_data = array(
      'user_login' => sanitize_email( $email ),
      'user_email' => false, // To disable sending notification email. Email will be added later
      'user_pass'  => wp_generate_password(),
      'first_name' => ( current( $record->get_field( [ 'id' => 'firstname', ] ) ?: [ 'value' => '' ] ) )['value'],
      'last_name'  => ( current( $record->get_field( [ 'id' => 'surname', ] ) ?: [ 'value' => '' ] ) )['value'],
      'meta_input' => array(
        'billing_first_name' => ( current( $record->get_field( [ 'id' => 'firstname', ] ) ?: [ 'value' => '' ] ) )['value'],
        'billing_last_name'  => ( current( $record->get_field( [ 'id' => 'surname', ] ) ?: [ 'value' => '' ] ) )['value'],
        'billing_phone'      => ( current( $record->get_field( [ 'id' => 'phone', ] ) ?: [ 'value' => '' ] ) )['value'],
        'billing_company'    => ( current( $record->get_field( [ 'id' => 'company', ] ) ?: [ 'value' => '' ] ) )['value'],
        'billing_address_1'  => ( current( $record->get_field( [ 'id' => 'address', ] ) ?: [ 'value' => '' ] ) )['value'],
        'billing_city'       => ( current( $record->get_field( [ 'id' => 'city', ] ) ?: [ 'value' => '' ] ) )['value'],
        'billing_state'      => ( current( $record->get_field( [ 'id' => 'state', ] ) ?: [ 'value' => '' ] ) )['value'],
        'billing_postcode'   => ( current( $record->get_field( [ 'id' => 'postcode', ] ) ?: [ 'value' => '' ] ) )['value'],
        'billing_country'    => ( current( $record->get_field( [ 'id' => 'country', ] ) ?: [ 'value' => '' ] ) )['value'],
      ),
    );

    $user_id = wp_insert_user( $user_data );

    if ( is_wp_error( $user_id ) ) {
      $handler->set_error( __( 'User creation failed, please contact us', '' ) );

      return;
    }

    // Add the user to portal with no specific role
    add_user_to_blog( 2, $user_id, '' );

    // Update user email
    wp_update_user( array(
      'ID'         => $user_id,
      'user_email' => $email,
    ) );
  }

  function add_tags_to_pages() {
    register_taxonomy_for_object_type( 'post_tag', 'page' );
  }

  function ajax_submit_c4c_form_func() {
    $all_forms_mapping = [
      'consumer-service-request' => [
        'ProductTypeLevel1'     => '00N9000000Dfrqi',
        'ProductTypeLevel2'     => '00N9000000Dfrqj',
        'WebTitle'              => '00N9000000DfrrB',
        'WebFirstName'          => '00N9000000Dfrqz',
        'WebLastName'           => '00N9000000Dfrr2',
        'WebPhone'              => 'phone',
        'WebMobile'             => '00N9000000Dfrr3',
        'WebEmail'              => 'email',
        'HouseNumber'           => '00N9000000Dfrr0',
        'Street'                => '00N9000000Dfrr9',
        'City'                  => '00N9000000DfrrA',
        'State'                 => '00N9000000Dfrr8',
        'PostalCode'            => '00N9000000Dfrr5',
        'WebProductModelNumber' => '00N9000000Dfrr6',
        'WebSerialNumber'       => '00N9000000Dfrr7',
        'WebDateofPurchase'     => '00N9000000DfrqR',
        'WebPlaceofPurchase'    => '00N9000000Dfrr4',
        'Subject'               => 'subject',
        'IncidentDescription'   => 'description',
        'WebAccountNumber'      => '',
        'WebAccountName'        => '',
      ],
      'partners-service-request' => [
        'ProductTypeLevel1'     => '00n9000000dfrqi',
        'ProductTypeLevel2'     => '00n9000000dfrqj',
        'WebTitle'              => '00n9000000dfrrb',
        'WebFirstName'          => '00n9000000dfrqz',
        'WebLastName'           => '00n9000000dfrr2',
        'WebPhone'              => 'phone',
        'WebMobile'             => '00n9000000dfrr3',
        'WebEmail'              => 'email',
        'HouseNumber'           => '00n9000000dfrr0',
        'Street'                => '00n9000000dfrr9',
        'City'                  => '00n9000000dfrra',
        'State'                 => '00n9000000dfrr8',
        'PostalCode'            => '00n9000000dfrr5',
        'WebProductModelNumber' => '00n9000000dfrr6',
        'WebSerialNumber'       => '00n9000000dfrr7',
        'WebDateofPurchase'     => '00n9000000dfrqr',
        'WebPlaceofPurchase'    => '00n9000000dfrr4',
        'Subject'               => 'subject',
        'IncidentDescription'   => 'description',
        'WebAccountNumber'      => '00n9000000dfrqw',
        'WebAccountName'        => '00n9000000dfrqv',
      ],
    ];

    $data      = (array) @$_POST['data'];
    $form_slug = sanitize_text_field( @$_POST['form'] );

    if (
      empty( $data ) ||
      empty( $form_slug ) ||
      ! isset( $all_forms_mapping[ $form_slug ] )
    ) {
      wp_send_json_error();
    }

    if ( 'production' == wp_get_environment_type() ) {
      $api_token_required = true;
      $api_request        = 'POST';
      $api_url            = 'https://cloud-integration-opqg8cqa.it-cpi002-rt.cfapps.ap10.hana.ondemand.com/http/ServiceTicketIntegration';
      $api_auth           = 'Basic c2ItY2IwNWQ3M2YtMWY3Ny00ZmQ5LTk0NzgtMzAxZGRkYjUxYWFjIWI4MTM3fGl0LXJ0LWNsb3VkLWludGVncmF0aW9uLW9wcWc4Y3FhIWI4MDoyNzc0MDIwZS05NTQ0LTQ1YTktYjYzOC0wN2M2MjcwZGNkZTEkZFkyS1hDSkJfQ0twUDZ6X1BWM1hNWkt0Tlo1Mk5IUjRFN2xZNjMwSWRrVT0=';
    } else {
      $api_token_required = true;
      $api_request        = 'POST';
      $api_url            = 'https://cloud-integration-test-pai3ufmi.it-cpi002-rt.cfapps.ap10.hana.ondemand.com/http/ServiceTicketIntegration';
      $api_auth           = 'Basic c2ItNTlmNzk3MzctY2Q1Ni00NzRlLWJiNTYtMTQzMWMyNWUwMjU0IWIzNjI5fGl0LXJ0LWNsb3VkLWludGVncmF0aW9uLXRlc3QtcGFpM3VmbWkhYjgwOjZhYTljZDU1LTcxNTYtNDAzNi04YjkzLTAyYTcxZjUxNjllMCRISm1xa3RDbnNHTFk0SEJiYkYwMjRwTVV2SG0wMDlnQXdGcDZZY0Q0cmg0PQ==';
    }

    $csrf_token = '';
    $set_cookie = '';
    
    // Token request
    if ( $api_token_required ) {
      $curl = curl_init();

      curl_setopt_array( $curl, array(
        CURLOPT_URL            => $api_url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER         => true,
        CURLOPT_ENCODING       => '',
        CURLOPT_MAXREDIRS      => 10,
        CURLOPT_TIMEOUT        => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST  => 'GET',
        CURLOPT_HTTPHEADER     => array(
          'x-csrf-token: fetch',
          "Authorization: {$api_auth}",
          'Content-Type: application/xml',
          'Accept: application/xml'
        ),
      ) );

      $response = curl_exec( $curl );

      $header_size = curl_getinfo( $curl, CURLINFO_HEADER_SIZE );
      $headers     = substr( $response, 0, $header_size );

      curl_close( $curl );

      $headers_indexed_arr = explode( "\r\n", $headers );

      $headers_arr = array();

      foreach ( $headers_indexed_arr as $value ) {
        if ( false !== ( $matches = explode( ':', $value, 2 ) ) ) {
          if ( empty( $headers_arr["{$matches[0]}"] ) ) {
            $headers_arr["{$matches[0]}"] = trim( $matches[1] );
          } else {
            $headers_arr["{$matches[0]}"]   = (array) $headers_arr["{$matches[0]}"];
            $headers_arr["{$matches[0]}"][] = trim( $matches[1] );
          }
        }
      }

      if ( ! empty( $headers_arr['x-csrf-token'] ) ) {
        $csrf_token = $headers_arr['x-csrf-token'];

        // Collect cookie for CSRF
        if ( ! is_array( $headers_arr['set-cookie'] ) ) {
          $set_cookie = $headers_arr['set-cookie'];
        } else {
          $set_cookie = [];
          foreach ( $headers_arr['set-cookie'] as $cookie ) {
            $cookie_parts = explode( ';', $cookie );
            $set_cookie[] = $cookie_parts[0];
          }
          $set_cookie = implode( '; ', $set_cookie );
        }
      }
    }

    if ( $api_token_required && empty( $csrf_token ) && empty( $set_cookie ) ) {
      wp_send_json_error( [
        'message' => 'We cannot connect to the server, please try again later.',
      ] );
    }

    // Ticket request
    $tags_mapping = $all_forms_mapping[ $form_slug ];

    ob_start();
    ?>
    <ServiceTickets>
      <ServiceTicket>
        <?php foreach ( $tags_mapping as $tag => $salesforce_key ) : ?>
          <?php
          if ( empty( $salesforce_key ) ) {
            continue;
          }

          if ( false === ( $index = array_search( $salesforce_key, array_column( $data, 'name' ) ) ) ) {
            continue;
          }

          echo "<{$tag}>{$data[$index]['value']}</{$tag}>";
          ?>
        <?php endforeach; ?>
      </ServiceTicket>
    </ServiceTickets>
    <?php
    $fields = ob_get_clean();
    $curl = curl_init();

    $request_headers = array(
      "Authorization: {$api_auth}"
    );

    if (
      $api_token_required &&
      ! empty( $csrf_token ) &&
      ! empty( $set_cookie )
    ) {
      $request_headers[] = "x-csrf-token: {$csrf_token}";
      $request_headers[] = "cookie: {$set_cookie}";
      $request_headers[] = "Content-Type: application/xml";
      $request_headers[] = "Accept: application/xml";
    }

    curl_setopt_array( $curl, array(
      CURLOPT_URL            => $api_url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING       => '',
      CURLOPT_MAXREDIRS      => 10,
      CURLOPT_TIMEOUT        => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HEADER         => true,
      CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST  => $api_request,
      CURLOPT_POSTFIELDS     => $fields,
      CURLOPT_HTTPHEADER     => $request_headers,
    ) );

    $response = curl_exec( $curl );

    $http_code = curl_getinfo( $curl, CURLINFO_HTTP_CODE );

    curl_close( $curl );

    if ( ! empty( $response ) && 201 == $http_code ) {
      wp_send_json_success( [
        'message' => 'Your request has been received, we\'ll be in touch with you soon.',
      ] );
    }

    wp_send_json_error();
  }

  function salesforce_forms( $atts ) {
    if ( ! empty( $form_slug = $atts['form'] ) ) {
      ob_start();
      get_template_part( "template-parts/salesforce-forms/{$form_slug}" );
      ?>
      <script>
        jQuery(function($) {
          const $form = $('#form-<?=$form_slug?>');

          $form.on('submit', function(e) {
            e.preventDefault();

            $.ajax({
              type: 'POST',
              url: Mitsubishi.ajaxUrl,
              dataType: 'json',
              beforeSend: function() {
                $form.find('[type=submit]').text('Submitting...').prop('disabled', true);
              },
              data: {
                action: 'submit_c4c_form',
                data: $form.serializeArray(),
                form: '<?=$form_slug?>',
              },
              success: function(response) {
                console.log(response);
                if (response.success) {
                  alert(response.data['message']);
                  $form.get(0).reset();
                } else {
                  alert('There are something wrong with your submission, please review your form and submit again!');
                }

                $form.find('[type=submit]').text('Submit').prop('disabled', false);
              },
            });
          });
        });
      </script>
      <?php

      return ob_get_clean();
    }

    return '';
  }

  function dynamic_store_info( $atts ) {
    if ( ! empty( $_GET['storeID'] ) ) {
      $store_id = absint( $_GET['storeID'] );

      if ( get_post( $store_id ) ) {
        ob_start();

        get_template_part( 'template-parts/store-info/store-info', null, [
          'store_id' => $store_id,
        ] );

        return ob_get_clean();
      }
    }

    return '';
  }

  function custom_breadcrumbs( $crumbs ): array {
    $MIT_helpers = MIT_Core::instance()->helpers;

    // Change Home url for Pro
    if ( $MIT_helpers->is_flow_professional() ) {
      $crumbs[0][1] = home_url( MIT_HOME_SLUGS[ MIT_USER_TYPE__PRO ] );
    }

    // Remove duplicated home crumb for Homeowner
    if (
      count( $crumbs ) > 1 &&
      trailingslashit( $crumbs[0][1] ) == trailingslashit( $crumbs[1][1] )
    ) {
      unset( $crumbs[1] );
    }

    // Remove duplicated home crumb for Professional
    if (
      count( $crumbs ) > 1 &&
      $MIT_helpers->is_flow_professional() &&
      trailingslashit( home_url( MIT_HOME_SLUGS[ MIT_USER_TYPE__PRO ] ) ) == trailingslashit( $crumbs[1][1] )
    ) {
      unset( $crumbs[1] );
    }

    // Add Products crumb to products category
    if (
      ( is_product_category() || is_product() ) &&
      ! empty( $products_page = $MIT_helpers->get_personalised_field( 'products_page', 'options' ) )
    ) {
      array_splice( $crumbs, 1, 0, [
        [
          $products_page->post_title,
          get_permalink( $products_page->ID ),
          'hide_in_schema' => false,
        ],
      ] );
    }

    // Add Our Difference crumb to news release
    if (
      (
        is_tax( 'category_news' ) ||
        is_singular( 'cpt-news' ) ||
        is_post_type_archive( 'cpt-news' )
      ) &&
      ! empty( $news_release_parent_breadcrumb = get_field( 'news_release_parent_breadcrumb', 'options' ) )
    ) {
      array_splice( $crumbs, 1, 0, [
        [
          $news_release_parent_breadcrumb->post_title,
          get_permalink( $news_release_parent_breadcrumb->ID ),
          'hide_in_schema' => false,
        ],
      ] );
    }

    // Add blog crumb to post
    if ( is_singular( 'post' ) ) {
      $page_for_posts = get_option( 'page_for_posts', true );
      array_splice( $crumbs, 1, 0, [
        [
          get_the_title( $page_for_posts ),
          get_permalink( $page_for_posts ),
          'hide_in_schema' => false,
        ],
      ] );
    }

    // Hide parent term for Homeowner
    if ( $MIT_helpers->is_flow_homeowner() ) {
      if ( $key = array_search( 'residential', array_map( 'strtolower', array_column( $crumbs, 0 ) ) ) ) {
        unset( $crumbs[ $key ] );
      }
    }

    return array_values( $crumbs );
  }

  function customise_table_tags( $content ) {
    if (
      is_string( $content ) &&
      strpos( $content, '<table' ) !== false
    ) {
      $content = preg_replace_callback( '/<table\s?[^>]*/', function ( $matches ) {
        $table = $matches[0];
        $attr  = 'class';
        $value = 'table table-striped';

        if ( strpos( $table, "{$attr}=" ) === false ) {
          $table = preg_replace( "%(<table)%i", "$1 {$attr}=\"$value\"", $table, - 1 );
        } else {
          $table = preg_replace( "%$attr=(\S)(?![^'\"]*{$value}\s[^'\"]*)%i", "$attr=$1{$value} ", $table, - 1 );
        }

        return $table;
      }, $content );

      $content = preg_replace( "%<table%i", "<div class=\"table-responsive\"><table", $content, - 1 );
      $content = preg_replace( "%</table>%i", "</table></div>", $content, - 1 );

      return force_balance_tags( $content );
    }

    return $content;
  }

  function download_filter_professional_func( $taxonomy, $parent_term_slug, $alm_uid ) {
    $parent_term = get_term_by( 'slug', $parent_term_slug, $taxonomy );

    if ( is_wp_error( $parent_term ) || empty( $parent_term ) ) {
      return;
    }

    get_template_part( 'template-parts/filters-form/filters-form', null, [
      'taxonomy'         => $taxonomy,
      'parent_term_slug' => $parent_term_slug,
      'alm_uid'          => $alm_uid,
    ] );
  }

  function page_priority_init() {
    $GLOBALS['wp_rewrite']->use_verbose_page_rules = true;
  }

  function page_priority_collect_page_rewrite_rules( $page_rewrite_rules ): array {
    $GLOBALS['mit_page_rewrite_rules'] = $page_rewrite_rules;

    return array();
  }

  function page_priority_prepend_page_rewrite_rules( $rewrite_rules ) {
    return $GLOBALS['mit_page_rewrite_rules'] + $rewrite_rules;
  }

  function cpt_register_download() {
    $slug      = 'download';
    $post_type = 'cpt-download';
    $public    = false;

    $labels = array(
      'name'               => _x( 'Downloads', 'post type general name' ),
      'singular_name'      => _x( 'Download', 'post type singular name' ),
      'add_new'            => _x( 'Add Download', 'rep' ),
      'add_new_item'       => __( 'Add New Download' ),
      'edit_item'          => __( 'Edit Download' ),
      'new_item'           => __( 'New Download' ),
      'view_item'          => __( 'View Download' ),
      'search_items'       => __( 'Search Download' ),
      'not_found'          => __( 'Nothing found' ),
      'not_found_in_trash' => __( 'Nothing found in Trash' ),
      'parent_item_colon'  => '',
    );

    $args = array(
      'labels'             => $labels,
      'public'             => $public,
      'publicly_queryable' => $public,
      'show_ui'            => true,
      'query_var'          => true,
      'rewrite'            => array( 'slug' => $slug, 'with_front' => false ),
      'capability_type'    => 'post',
      'hierarchical'       => false,
      'menu_position'      => null,
      'menu_icon'          => 'dashicons-category',
      'supports'           => array( 'title', 'excerpt', 'thumbnail' ),
      'has_archive'        => false,
      'taxonomies'         => [ 'pa_available-models', 'post_tag' ],
    );

    register_post_type( $post_type, $args );

    register_taxonomy(
      'doc_category',
      array( $post_type ),
      array(
        'query_var'          => true,
        'hierarchical'       => true,
        'show_ui'            => true,
        'show_admin_column'  => true,
        'publicly_queryable' => false,
        'meta_box_cb'        => false,
        'rewrite'            => array( 'slug' => 'doc-category', 'with_front' => false ),
        'label'              => __( 'Doc Categories' ),
      )
    );
  }

  function cpt_register_video() {
    $slug      = 'video';
    $post_type = 'cpt-video';
    $public    = false;

    $labels = array(
      'name'               => _x( 'Videos', 'post type general name' ),
      'singular_name'      => _x( 'Video', 'post type singular name' ),
      'add_new'            => _x( 'Add Video', 'rep' ),
      'add_new_item'       => __( 'Add New Video' ),
      'edit_item'          => __( 'Edit Video' ),
      'new_item'           => __( 'New Video' ),
      'view_item'          => __( 'View Video' ),
      'search_items'       => __( 'Search Video' ),
      'not_found'          => __( 'Nothing found' ),
      'not_found_in_trash' => __( 'Nothing found in Trash' ),
      'parent_item_colon'  => '',
    );

    $args = array(
      'labels'             => $labels,
      'public'             => $public,
      'publicly_queryable' => $public,
      'show_ui'            => true,
      'query_var'          => true,
      'rewrite'            => array( 'slug' => $slug, 'with_front' => false ),
      'capability_type'    => 'post',
      'hierarchical'       => false,
      'menu_position'      => null,
      'menu_icon'          => 'dashicons-format-video',
      'supports'           => array( 'title', 'editor' ),
      'has_archive'        => false,
    );

    register_post_type( $post_type, $args );

    register_taxonomy(
      'video_category',
      array( $post_type ),
      array(
        'query_var'          => true,
        'hierarchical'       => true,
        'show_ui'            => true,
        'show_admin_column'  => true,
        'publicly_queryable' => false,
        'meta_box_cb'        => false,
        'rewrite'            => array( 'slug' => 'video-category', 'with_front' => false ),
        'label'              => __( 'Video Categories' ),
      )
    );
  }

  function cpt_register_career() {
    $slug      = 'job';
    $post_type = 'cpt-career';
    $public    = true;

    $labels = array(
      'name'               => _x( 'Careers', 'post type general name' ),
      'singular_name'      => _x( 'Careers', 'post type singular name' ),
      'add_new'            => _x( 'Add Career', 'rep' ),
      'add_new_item'       => __( 'Add New Career' ),
      'edit_item'          => __( 'Edit Career' ),
      'new_item'           => __( 'New Career' ),
      'view_item'          => __( 'View Career' ),
      'search_items'       => __( 'Search Career' ),
      'not_found'          => __( 'Nothing found' ),
      'not_found_in_trash' => __( 'Nothing found in Trash' ),
      'parent_item_colon'  => '',
    );

    $args = array(
      'labels'             => $labels,
      'public'             => $public,
      'publicly_queryable' => $public,
      'show_ui'            => true,
      'query_var'          => true,
      'rewrite'            => array( 'slug' => $slug, 'with_front' => false ),
      'capability_type'    => 'post',
      'hierarchical'       => false,
      'menu_position'      => null,
      'menu_icon'          => 'dashicons-businessperson',
      'supports'           => array( 'title', 'editor', 'excerpt' ),
      'has_archive'        => 'careers',
      'capabilities'       => array(
        'publish_posts'      => 'manage_careers',
        'edit_posts'         => 'manage_careers',
        'edit_others_posts'  => 'manage_careers',
        'read_private_posts' => 'manage_careers',
        'edit_post'          => 'manage_careers',
        'delete_post'        => 'manage_careers',
        'read_post'          => 'manage_careers',
      ),
    );

    register_post_type( $post_type, $args );

    register_taxonomy(
      'job_location',
      array( $post_type ),
      array(
        'query_var'          => true,
        'hierarchical'       => true,
        'show_ui'            => true,
        'show_admin_column'  => true,
        'publicly_queryable' => false,
        'rewrite'            => array( 'slug' => 'job-location', 'with_front' => false ),
        'label'              => __( 'Locations' ),
      )
    );
  }

  function cpt_register_recipe() {
    $slug         = 'recipe';
    $archive_slug = 'recipes';
    $post_type    = 'cpt-recipe';
    $public       = true;

    $labels = array(
      'name'               => _x( 'Recipes', 'post type general name' ),
      'singular_name'      => _x( 'Recipes', 'post type singular name' ),
      'add_new'            => _x( 'Add Recipe', 'rep' ),
      'add_new_item'       => __( 'Add New Recipe' ),
      'edit_item'          => __( 'Edit Recipe' ),
      'new_item'           => __( 'New Recipe' ),
      'view_item'          => __( 'View Recipe' ),
      'search_items'       => __( 'Search Recipe' ),
      'not_found'          => __( 'Nothing found' ),
      'not_found_in_trash' => __( 'Nothing found in Trash' ),
      'parent_item_colon'  => '',
    );

    $args = array(
      'labels'             => $labels,
      'public'             => $public,
      'publicly_queryable' => $public,
      'show_ui'            => true,
      'query_var'          => true,
      'rewrite'            => array( 'slug' => $slug, 'with_front' => false ),
      'capability_type'    => 'post',
      'hierarchical'       => false,
      'menu_position'      => null,
      'menu_icon'          => 'dashicons-carrot',
      'supports'           => array( 'title', 'thumbnail' ),
      'has_archive'        => $archive_slug,
    );

    register_post_type( $post_type, $args );

    register_taxonomy(
      'recipe_category',
      array( $post_type ),
      array(
        'query_var'          => true,
        'hierarchical'       => true,
        'show_ui'            => true,
        'show_admin_column'  => true,
        'publicly_queryable' => false,
        'rewrite'            => array( 'slug' => $archive_slug, 'with_front' => false ),
        'label'              => __( 'Recipe Categories' ),
      )
    );
  }

  function cpt_register_release() {
    $slug         = 'news-release';
    $archive_slug = 'news-releases';
    $post_type    = 'cpt-news';
    $public       = true;

    $labels = array(
      'name'               => _x( 'News Releases', 'post type general name' ),
      'singular_name'      => _x( 'News Releases', 'post type singular name' ),
      'add_new'            => _x( 'Add Release', 'rep' ),
      'add_new_item'       => __( 'Add New Release' ),
      'edit_item'          => __( 'Edit Release' ),
      'new_item'           => __( 'New Release' ),
      'view_item'          => __( 'View Release' ),
      'search_items'       => __( 'Search Release' ),
      'not_found'          => __( 'Nothing found' ),
      'not_found_in_trash' => __( 'Nothing found in Trash' ),
      'parent_item_colon'  => '',
    );

    $args = array(
      'labels'             => $labels,
      'public'             => $public,
      'publicly_queryable' => $public,
      'show_ui'            => true,
      'query_var'          => true,
      'rewrite'            => array( 'slug' => $slug, 'with_front' => false ),
      'capability_type'    => 'post',
      'hierarchical'       => false,
      'menu_position'      => null,
      'menu_icon'          => 'dashicons-welcome-widgets-menus',
      'supports'           => array( 'title', 'editor', 'excerpt', 'thumbnail' ),
      'has_archive'        => $archive_slug,
    );

    register_post_type( $post_type, $args );

    register_taxonomy(
      'category_news',
      array( $post_type ),
      array(
        'query_var'         => true,
        'hierarchical'      => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'rewrite'           => array( 'slug' => $archive_slug, 'with_front' => false ),
        'label'             => __( 'Topics' ),
      )
    );
  }

  function tax_register_dealer_service() {
    register_taxonomy(
      'dealer_service',
      array( 'wpsl_stores' ),
      array(
        'query_var'         => true,
        'hierarchical'      => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'rewrite'           => array( 'slug' => 'dealer-service', 'with_front' => false ),
        'label'             => __( 'Dealer Services' ),
      )
    );
  }

  function body_classes( $classes ) {
    if ( ! is_singular() ) {
      $classes[] = 'hfeed';
    }

    $classes[] = 'user-type--' . MIT_Core::instance()->helpers->get_current_user_type();

    return $classes;
  }

  function set_user_type_cookie() {
    // Helper closures — only emit Set-Cookie when the value actually changes.
    // Emitting Set-Cookie unconditionally causes WP Engine to mark every response
    // as non-cacheable (x-cacheable: NO:Set Known Cookie), bypassing the full-page
    // cache even for anonymous visitors who don't need a personalised response.
    $set_type = function( $type ) {
      $type = sanitize_key( $type );
      if ( ( $_COOKIE[ MIT_USER_TYPE__COOKIE ] ?? '' ) === $type ) {
        return; // Value unchanged — no Set-Cookie header needed.
      }
      $_COOKIE[ MIT_USER_TYPE__COOKIE ] = $type;
      setcookie( MIT_USER_TYPE__COOKIE, $type, time() + ( 7 * DAY_IN_SECONDS ), '/' );
    };

    $clear_type = function() {
      if ( ! isset( $_COOKIE[ MIT_USER_TYPE__COOKIE ] ) ) {
        return; // Already absent — no Set-Cookie header needed.
      }
      unset( $_COOKIE[ MIT_USER_TYPE__COOKIE ] );
      setcookie( MIT_USER_TYPE__COOKIE, null, - 1, '/' );
    };

    if (
      isset( $_GET[ MIT_USER_TYPE__GET_PARAM ] ) &&
      in_array( $_GET[ MIT_USER_TYPE__GET_PARAM ], MIT_USER_TYPES )
    ) {
      // Params base
      if ( $_GET[ MIT_USER_TYPE__GET_PARAM ] != MIT_USER_TYPE__HO ) {
        $set_type( $_GET[ MIT_USER_TYPE__GET_PARAM ] ); // Not HO
      } else {
        $clear_type(); // HO
      }
    } else {
      // For front page, always as HO
      if ( '/' == $_SERVER['REQUEST_URI'] ) {
        $clear_type();
      }

      // Slugs base
      foreach ( MIT_USER_TYPES as $user_type ) {
        if (
          MIT_Core::instance()->helpers->get_current_user_type() != $user_type &&
          (
            ( 0 === strpos( $_SERVER['REQUEST_URI'], sprintf( "/%s/", MIT_HOME_SLUGS[ $user_type ] ) ) ) ||
            ( 0 === strpos( MIT_Core::instance()->helpers->get_relative_permalink(), sprintf( "/%s/", MIT_HOME_SLUGS[ $user_type ] ) ) )
          )
        ) {
          if ( $user_type != MIT_USER_TYPE__HO ) {
            $set_type( $user_type ); // Not HO
          } else {
            $clear_type(); // HO
          }
        }
      }
    }
  }

  function migrate_product_other_features() {
    if (
      ! isset( $_GET['migrate_product_other_features'] ) ||
      ! current_user_can( 'manage_options' ) ||
      1 != get_current_user_id()
    ) {
      return;
    }

    $_products = get_posts( [
      'post_type'      => 'product',
      'post_status'    => 'publish',
      'posts_per_page' => - 1,
    ] );

    foreach ( $_products as $_product ) {
      // update_post_meta( $_product->ID, 'other_features', $_product->post_content );

      /*$my_post = array(
        'ID'           => $_product->ID,
        'post_content' => '',
      );
      wp_update_post( $my_post );*/
    }
  }

  function wp_rocket_dynamic_cookie( $cookies ) {
    $cookies[] = MIT_USER_TYPE__COOKIE;

    return $cookies;
  }

  function pingback_header() {
    if ( is_singular() && pings_open() ) {
      echo '<link rel="pingback" href="', esc_url( get_bloginfo( 'pingback_url' ) ), '">';
    }
  }

  function setup_theme() {
    add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', ) );
    add_theme_support( 'automatic-feed-links' );
    add_theme_support( 'customize-selective-refresh-widgets' );

    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );

    // add_image_size( 'thumbnails_600_516', 600, 516, true );

    add_post_type_support( 'post', 'excerpt' );
    add_post_type_support( 'page', 'excerpt' );

    register_nav_menus( array(
      'primary_menu_' . MIT_USER_TYPE__HO  => __( 'Primary Menu | Homeowner' ),
      'primary_menu_' . MIT_USER_TYPE__PRO => __( 'Primary Menu | Industry Professional' ),
      'footer_menu_' . MIT_USER_TYPE__HO   => __( 'Footer Menu | Homeowner' ),
      'footer_menu_' . MIT_USER_TYPE__PRO  => __( 'Footer Menu | Industry Professional' ),
    ) );
  }

  function enqueue_scripts() {
    $suffix = SCRIPT_DEBUG ? '' : '.min';

    /* Register assets */
    wp_register_style( 'theme-swiper-css', get_theme_file_uri( "assets/lib/swiper/swiper-bundle{$suffix}.css" ), [] );
    wp_register_script( 'theme-swiper-js', get_theme_file_uri( "assets/lib/swiper/swiper-bundle{$suffix}.js" ), [], false, true );
    wp_register_style( 'css-OverlayScrollbars', get_theme_file_uri( "assets/lib/overlayscrollbars/css/OverlayScrollbars{$suffix}.css" ), [] );
    wp_register_script( 'js-OverlayScrollbars', get_theme_file_uri( "assets/lib/overlayscrollbars/js/OverlayScrollbars{$suffix}.js" ), [], false, true );

    /* Common assets */
    // css-font-awesome (FA v4.7.0) removed — Elementor loads FA v5 + v4 CSS shim
    // which covers all fa/fa-* classes used in templates. Saves ~37KB render-blocking CSS.
    // js-wow (WOW.js) removed — scroll-animation library causes measurable TBT on every page load.
    // The theme's .wow CSS already hides elements even after WOW marks them "visible" (both rules
    // set visibility:hidden), so animations were not rendering anyway. Saves ~8.4KB of always-loaded JS.
    // css_browser_selector.js removed — 2010-era browser-detection library, unused by any CSS/JS.
    $assets_css = [];
    $assets_js  = [];

    if ( has_shortcode( get_the_content(), 'wpsl' ) ) {
      wp_enqueue_style( 'css-OverlayScrollbars' );
      wp_enqueue_script( 'js-OverlayScrollbars' );
    }

    /* Enqueue */
    wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'underscore' );
    foreach ( $assets_css as $handle => $path ) {
      wp_enqueue_style( $handle, MIT_Core::instance()->helpers->get_assets_path( $path ), [], ASSETS_VERSION );
    }
    foreach ( $assets_js as $handle => $path ) {
      wp_enqueue_script( $handle, MIT_Core::instance()->helpers->get_assets_path( $path ), [], ASSETS_VERSION, true );
    }

    /* Conditions */
    wp_script_add_data( 'theme-html5', 'conditional', 'lt IE 9' );
    wp_script_add_data( 'theme-respond', 'conditional', 'lt IE 9' );

    /* Main assets */
    // Dashicons only needed in WP admin — not in frontend templates.
    if ( is_admin() ) {
      wp_enqueue_style( 'theme-dashicons', includes_url( "css/dashicons$suffix.css" ), [], ASSETS_VERSION );
    }
    wp_enqueue_style( 'theme-style', get_theme_file_uri( 'dist/main.min.css' ), [], ASSETS_VERSION );
    wp_enqueue_script( 'theme-js', get_theme_file_uri( 'dist/main.min.js' ), [], ASSETS_VERSION, true );

    wp_localize_script( 'theme-js',
      'Mitsubishi',
      [
        'ajaxUrl' => admin_url( 'admin-ajax.php' ),
      ]
    );
  }

  function excerpt_more( $more ): string {
    return ' ...';
  }

  function excerpt_length( $length ): string {
    return 30;
  }

  function start_head_buffer() {
    ob_start();
  }

  function end_head_buffer() {
    $head = ob_get_clean();
    // Remove slick carousel CSS injected via Elementor custom code.
    // Uses a CDN link with SRI hash — no WordPress handle, so ob_start is the only option.
    $head = preg_replace(
      '/<link[^>]*cdnjs\.cloudflare\.com\/ajax\/libs\/slick-carousel[^>]*>\s*/i',
      '',
      $head
    );
    echo $head;
  }

  function dequeue_woo_css() {
    if (
      function_exists( 'is_woocommerce' ) &&
      ! is_woocommerce() &&
      ! is_cart() &&
      ! is_checkout() &&
      ! is_account_page()
    ) {
      wp_dequeue_style( 'woocommerce-general' );
      wp_dequeue_style( 'woocommerce-layout' );
      wp_dequeue_style( 'woocommerce-smallscreen' );
      wp_dequeue_style( 'woocommerce-inline' );
      wp_dequeue_style( 'wc-blocks-style' );
      wp_dequeue_style( 'wc-blocks-vendors-style' );
    }
  }

  function remove_emoji_dns_prefetch( $urls, $relation_type ) {
    if ( 'dns-prefetch' === $relation_type ) {
      $urls = array_values( array_diff( $urls, [ 'https://s.w.org' ] ) );
    }
    return $urls;
  }

  function remove_tinymce_emoji( $plugins ) {
    return array_diff( $plugins, [ 'wpemoji' ] );
  }

  function add_preconnect_hints( $urls, $relation_type ) {
    if ( 'preconnect' !== $relation_type ) {
      return $urls;
    }
    $urls[] = 'https://www.googletagmanager.com';
    $urls[] = 'https://www.google-analytics.com';
    return $urls;
  }

  function hook_head() {
    ?>
    <script>document.addEventListener('DOMContentLoaded', function() {
      if (typeof jQuery !== 'undefined') { window.$ = jQuery.noConflict(); }
    });</script>
    <?php
  }

  function wp_footer() {
  }
}
