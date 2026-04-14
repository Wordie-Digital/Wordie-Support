<?php

defined( 'ABSPATH' ) or exit;

class POR_Setup {
  public function __construct() {
    add_action( 'wp_head', [ $this, 'pingback_header' ] );
    add_action( 'wp_head', [ $this, 'hook_head' ] );
    add_action( 'wp_footer', [ $this, 'wp_footer' ], 99 );
    add_action( 'after_setup_theme', [ $this, 'setup_theme' ] );
    add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ], 99 );
    add_action( 'init', [ $this, 'cpt_register_technical_doc' ] );
    add_action( 'init', [ $this, 'cpt_register_resource' ] );
    add_action( 'init', [ $this, 'cpt_register_training' ] );
    add_action( 'init', [ $this, 'cpt_register_spare_parts' ] );
    add_action( 'init', [ $this, 'cpt_register_material' ] );
    add_filter( 'body_class', [ $this, 'body_classes' ] );
    add_filter( 'excerpt_more', [ $this, 'excerpt_more' ] );
    add_filter( 'excerpt_length', [ $this, 'excerpt_length' ] );
    add_action( 'get_header', [ $this, 'remove_admin_top_gap' ] );

    // Customise table tags in content and ACF fields
    add_filter( 'the_content', [ $this, 'customise_table_tags' ] );
    add_filter( 'acf/format_value', [ $this, 'customise_table_tags' ] );

    // Plugins
    add_filter( 'alm_filters_public_taxonomies', '__return_false' );
    add_filter( 'alm_filters_edit', '__return_false' );

    // Ajax requests
    add_action( "wp_ajax_get_products_docs_html", [ $this, "ajax_get_products_docs_html" ] );
    add_action( "wp_ajax_nopriv_get_products_docs_html", [ $this, "ajax_get_products_docs_html" ] );

    // Update reset password form action
    add_filter( 'network_site_url', [ $this, 'network_site_url_reset_pass' ] );

    // Material parts list
    add_action( "wp_ajax_get_material_parts_list", [ $this, "get_material_parts_list_func" ] );
    add_action( "wp_ajax_nopriv_get_material_parts_list", [ $this, "get_material_parts_list_func" ] );
    add_action( "wp_ajax_send_eta_request", [ $this, "send_eta_request_func" ] );
    add_action( "wp_ajax_nopriv_send_eta_request", [ $this, "send_eta_request_func" ] );
    add_action( "init", [ $this, "register_exports" ] );

    // Redirect FA tech to /portal/fa-dashboard/ from main dashboard
    add_action( 'template_redirect', [ $this, 'template_redirect' ] );

    // Send email after spare parts import is complete
    add_action( 'pmxi_after_xml_import', [ $this, 'wpae_after_import' ], 10, 2 );

    
    // Spare parts WP ALl Import functions
    //add_action( 'spare_parts_import_trigger', [ $this, 'spare_parts_import_trigger_func' ] );
    //add_action( 'spare_parts_import_processing', [ $this, 'spare_parts_import_processing_func' ] );
    // Materials WP ALl Import functions
    //add_action( 'materials_import_trigger', [ $this, 'materials_import_trigger_func' ] );
    //add_action( 'materials_import_processing', [ $this, 'materials_import_processing_func' ] );

    // Fix long time error when reseting password
    add_filter( 'network_site_url', [ $this, 'fix_resetpass_url' ], 12, 3 );
  }

	function fix_resetpass_url ($url, $path, $scheme) {
		if (stripos($url, "action=resetpass") !== false) {
			return site_url("wp-login.php?action=resetpass");
		}

		return $url;
	}

  
  function wpae_after_import( $import_id, $obj ) {
    $recipient = ['nsingson@gsquared.com.au', 'parts@meaust.meap.com'];
    $headers = array(
      'Content-Type: text/html; charset=UTF-8',
    );
    // Run for export 7 only
    if ( $import_id == '7' ) {
      $subject = get_bloginfo( 'name' ) . __( ' - Spare Parts import complete', 'mitsubishi' );
      $body = sprintf(__( "Total items imported:  %d <br/>Updated:  %d <br/>New:  %d <br/>Skipped:  %d <br/>Trashed:  %d" , 'mitsubishi'), $obj->imported, $obj->updated, $obj->created, $obj->skipped, $obj->changed_missing );

      wp_mail( $recipient, $subject, $body, $headers );
    }
    
    // Run for export 5 only
    if ( $import_id == '5' ) {
      $subject = get_bloginfo( 'name' ) . __( ' - Materials import complete', 'mitsubishi' );
      $body = sprintf(__( "Total items imported:  %d <br/>Updated:  %d <br/>New:  %d <br/>Skipped:  %d <br/>Trashed:  %d" , 'mitsubishi'), $obj->imported, $obj->updated, $obj->created, $obj->skipped, $obj->changed_missing );
      wp_mail( $recipient, $subject, $body, $headers );
    }
  }

  // Spare parts WP ALl Import functions
  function spare_parts_import_trigger_func(): void {
    $import_id = 7;
    $import_key = 'TwLV3Uc';
    $action = 'trigger';
    $url = home_url( sprintf( 'wp-load.php?import_key=%s&import_id=%d&action=%s&rand=%d', $import_key, $import_id, $action, time() ) );
    write_log( $url );
    $output = file_get_contents( $url );
    write_log( $output );
  }

  function spare_parts_import_processing_func(): void {
    $import_id = 7;
    $import_key = 'TwLV3Uc';
    $action = 'processing';
    $url = home_url( sprintf( 'wp-load.php?import_key=%s&import_id=%d&action=%s&rand=%d', $import_key, $import_id, $action, time() ) );
    write_log( $url );
    $output = file_get_contents( $url );
    write_log( $output );
  }

  // Materials WP ALl Import functions
  function materials_import_trigger_func(): void {
    $import_id = 5;
    $import_key = 'TwLV3Uc';
    $action = 'trigger';
    $url = home_url( sprintf( 'wp-load.php?import_key=%s&import_id=%d&action=%s&rand=%d', $import_key, $import_id, $action, time() ) );
    write_log( $url );
    $output = file_get_contents( $url );
    write_log( $output );
  }

  function materials_import_processing_func(): void {
    $import_id = 5;
    $import_key = 'TwLV3Uc';
    $action = 'processing';
    $url = home_url( sprintf( 'wp-load.php?import_key=%s&import_id=%d&action=%s&rand=%d', $import_key, $import_id, $action, time() ) );
    write_log( $url );
    $output = file_get_contents( $url );
    write_log( $output );
  }

  function template_redirect(): void {
    $user = wp_get_current_user();
    if ( is_front_page() && str_starts_with( $user->roles[0], 'me-user-fa-' ) ) {
      wp_redirect( '/portal/fa-dashboard/' );
      exit;
    }
  }

  function get_material_parts_list_func(): void {
    $material_id = ! empty( $_POST['material_id'] ) ? absint( $_POST['material_id'] ) : 0;

    if ( ! ( $_post = get_post( $material_id ) ) ) {
      wp_send_json_error();
    }

    wp_send_json_success( [
      'partsList' => POR_Core::instance()->helpers->get_spare_parts_compatible_models( $_post->post_title ),
    ] );
  }

  function send_eta_request_func(): void {
    $eta_request = ! empty( $_POST['eta_request'] ) ? json_decode( stripslashes( $_POST['eta_request'] ), true ) : [];
    $eta_ref     = ! empty( $_POST['eta_reference'] ) ? sanitize_text_field( $_POST['eta_reference'] ) : '';
    $recipient   = ! empty( $_POST['recipient'] ) ? $_POST['recipient'] : '';

    if ( empty( $recipient ) ) {
      wp_send_json_error( [
        'message' => 'Recipient is empty',
      ] );
    }

    if ( empty( $eta_request ) ) {
      wp_send_json_error( [
        'message' => 'ETA request is empty',
      ] );
    }

    $logged_in_user            = wp_get_current_user();
    $logged_in_billing_company = get_user_meta( $logged_in_user->ID, 'billing_company', true );
    $logged_in_username        = $logged_in_user->user_login;
    $logged_in_user_email      = $logged_in_user->user_email;

    $email_header = "ETA Request for Parts [$logged_in_billing_company]" . ( ! empty( $eta_ref ) ? " - $eta_ref" : '' );
    $email_body   = '';

    $email_body .= "Username: $logged_in_username <br>Company: $logged_in_billing_company <br> Email: $logged_in_user_email <br> Reference Text: $eta_ref <br> <br> ETA request for parts: <br>";
    $email_body .= call_user_func( function () use ( $eta_request ) {
      $html = '<table><thead><tr><th>Model</th><th>Part #</th><th style="text-align:right;">Req. Qty</th></tr></thead><tbody>';

      foreach ( $eta_request as $item ) {
        $material = str_contains($item['material'], ',') ? '' : $item['material']; // If material are multiple, don't show it, as it maybe too long.
        $html .= "<tr><td>{$material}&nbsp;&nbsp;&nbsp;&nbsp;</td><td>{$item['partNumber']}</td><td style=\"text-align:right;\">{$item['partQuantity']}</td></tr>";
      }

      return $html . '</tbody></table><style>table{border-collapse:collapse}table,th,td{border:1px solid #000; text-align: left;}th,td{padding:5px}</style>';
    } );

    // Send email to recipient
    $headers = [
      'Content-Type: text/html; charset=UTF-8',
      "From: $logged_in_username <noreply@mitsubishielectric.com.au>",
      "Reply-To: $logged_in_user_email",
    ];

    if ( ! wp_mail( $recipient, $email_header, $email_body, $headers ) ) {
      wp_send_json_error( [
        'message' => 'Failed to send ETA request, please try again later',
      ] );
    }

    wp_send_json_success( [
      'message'     => 'ETA request sent successfully. We\'ll be in touch soon.',
      'eta_request' => $eta_request,
    ] );
  }

  /**
   * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
   * @throws \PhpOffice\PhpSpreadsheet\Exception
   */
  function register_exports(): void {
    if ( empty( $_GET['portal_export'] ) ) {
      return;
    }

    switch ( $_GET['portal_export'] ) {
      case 'parts_list_pdf':
        $nonce = $_GET['_wpnonce'] ?? '';

        if ( ! wp_verify_nonce( $nonce, 'parts_list_pdf' ) ) {
          exit( 'Security check error' );
        }

        $material_id = absint( $_GET['material_id'] );

        POR_Core::instance()->pdf->generate_parts_list_pdf( $material_id );
        exit();

      case 'parts_list_excel':
        $nonce = $_GET['_wpnonce'] ?? '';

        if ( ! wp_verify_nonce( $nonce, 'parts_list_excel' ) ) {
          exit( 'Security check error' );
        }

        $material_id = absint( $_GET['material_id'] );

        POR_Core::instance()->excel->generate_parts_list_spreadsheet( $material_id );
        exit();
    }
  }

  function network_site_url_reset_pass( $url ) {
    if (
      ( false !== strpos( $url, 'action=lostpassword' ) ) ||
      ( false !== strpos( $url, 'action=rp' ) )
    ) {
      return str_replace( 'wp-login.php', 'portal/wp-login.php', $url );
    }

    return $url;
  }

  function ajax_get_products_docs_html() {
    global $post;

    $product_id = ! empty( $_GET['productId'] ) ? absint( $_GET['productId'] ) : 0;

    if ( ! ( $_post = get_post( $product_id ) ) ) {
      wp_send_json_error();
    }

    $post = $_post;
    setup_postdata( $post );

    ob_start();

    get_template_part( 'template-parts/post-product/post-product-docs' );

    wp_reset_postdata();

    wp_send_json_success( [
      'html' => ob_get_clean(),
    ] );
  }

  function cpt_register_training() {
    $slug      = 'training';
    $post_type = 'cpt-training';
    $public    = false;

    $labels = array(
      'name'               => _x( 'Trainings', 'post type general name' ),
      'singular_name'      => _x( 'Training', 'post type singular name' ),
      'add_new'            => _x( 'Add Training', 'rep' ),
      'add_new_item'       => __( 'Add New Training' ),
      'edit_item'          => __( 'Edit Training' ),
      'new_item'           => __( 'New Training' ),
      'view_item'          => __( 'View Training' ),
      'search_items'       => __( 'Search Training' ),
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
      'rewrite'            => array( 'slug' => $slug ),
      'capability_type'    => 'post',
      'hierarchical'       => false,
      'menu_position'      => null,
      'menu_icon'          => 'dashicons-groups',
      'supports'           => array( 'title', 'editor' ),
      'has_archive'        => false,
    );

    register_post_type( $post_type, $args );
  }

  function cpt_register_spare_parts() {
    $slug      = 'spare-part';
    $post_type = 'cpt-spare-part';

    $labels = array(
      'name'               => _x( 'Spare Parts', 'post type general name' ),
      'singular_name'      => _x( 'Spare Part', 'post type singular name' ),
      'add_new'            => _x( 'Add Spare Part', 'rep' ),
      'add_new_item'       => __( 'Add New Spare Part' ),
      'edit_item'          => __( 'Edit Spare Part' ),
      'new_item'           => __( 'New Spare Part' ),
      'view_item'          => __( 'View Spare Part' ),
      'search_items'       => __( 'Search Spare Part' ),
      'not_found'          => __( 'Nothing found' ),
      'not_found_in_trash' => __( 'Nothing found in Trash' ),
      'parent_item_colon'  => '',
    );

    $args = array(
      'labels'             => $labels,
      'public'             => true,
      'publicly_queryable' => false,
      'show_ui'            => true,
      'query_var'          => true,
      'rewrite'            => array( 'slug' => $slug ),
      'capability_type'    => 'post',
      'hierarchical'       => false,
      'menu_position'      => null,
      'menu_icon'          => 'dashicons-block-default',
      'supports'           => array( 'title', 'editor' ),
      'has_archive'        => false,
    );

    register_post_type( $post_type, $args );

    register_taxonomy(
      'part_type',
      array( $post_type ),
      array(
        'query_var'          => true,
        'hierarchical'       => true,
        'show_ui'            => true,
        'show_admin_column'  => true,
        'publicly_queryable' => false,
        'rewrite'            => array( 'slug' => 'part-type' ),
        'label'              => __( 'Part Types' ),
      )
    );
  }

  function cpt_register_resource() {
    $slug      = 'resource';
    $post_type = 'cpt-resource';
    $public    = false;

    $labels = array(
      'name'               => _x( 'Resources', 'post type general name' ),
      'singular_name'      => _x( 'Resource', 'post type singular name' ),
      'add_new'            => _x( 'Add Resource', 'rep' ),
      'add_new_item'       => __( 'Add New Resource' ),
      'edit_item'          => __( 'Edit Resource' ),
      'new_item'           => __( 'New Resource' ),
      'view_item'          => __( 'View Resource' ),
      'search_items'       => __( 'Search Resource' ),
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
      'rewrite'            => array( 'slug' => $slug ),
      'capability_type'    => 'post',
      'hierarchical'       => false,
      'menu_position'      => null,
      'menu_icon'          => 'dashicons-download',
      'supports'           => array( 'title', 'excerpt', 'thumbnail' ),
      'has_archive'        => false,
    );

    register_post_type( $post_type, $args );

    register_taxonomy(
      'resource_category',
      array( $post_type ),
      array(
        'query_var'          => true,
        'hierarchical'       => true,
        'show_ui'            => true,
        'show_admin_column'  => true,
        'publicly_queryable' => false,
        'meta_box_cb'        => false,
        'rewrite'            => array( 'slug' => 'resource-category' ),
        'label'              => __( 'Resource Categories' ),
      )
    );
  }

  function cpt_register_technical_doc() {
    $slug      = 'technical-doc';
    $post_type = 'cpt-technical-doc';
    $public    = true;

    $labels = array(
      'name'               => _x( 'Tech Documents', 'post type general name' ),
      'singular_name'      => _x( 'Tech Document', 'post type singular name' ),
      'add_new'            => _x( 'Add Document', 'rep' ),
      'add_new_item'       => __( 'Add New Document' ),
      'edit_item'          => __( 'Edit Document' ),
      'new_item'           => __( 'New Document' ),
      'view_item'          => __( 'View Document' ),
      'search_items'       => __( 'Search Document' ),
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
      'rewrite'            => array( 'slug' => $slug ),
      'capability_type'    => 'post',
      'hierarchical'       => false,
      'menu_position'      => null,
      'menu_icon'          => 'dashicons-category',
      'supports'           => array( 'title' ),
      'has_archive'        => false,
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
        'rewrite'            => array( 'slug' => 'doc-category' ),
        'label'              => __( 'Doc Categories' ),
      )
    );
  }

  function cpt_register_material() {
    $slug      = 'material';
    $post_type = 'cpt-material';
    $public    = false;

    $labels = array(
      'name'               => _x( 'Materials', 'post type general name' ),
      'singular_name'      => _x( 'Material', 'post type singular name' ),
      'add_new'            => _x( 'Add Material', 'rep' ),
      'add_new_item'       => __( 'Add New Material' ),
      'edit_item'          => __( 'Edit Material' ),
      'new_item'           => __( 'New Material' ),
      'view_item'          => __( 'View Material' ),
      'search_items'       => __( 'Search Material' ),
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
      'rewrite'            => array( 'slug' => $slug ),
      'show_in_menu'       => 'edit.php?post_type=product',
      'capability_type'    => 'post',
      'hierarchical'       => false,
      'menu_position'      => null,
      'menu_icon'          => 'dashicons-category',
      'supports'           => array( 'title', 'editor' ),
      'has_archive'        => false,
    );

    register_post_type( $post_type, $args );
  }

  function remove_admin_top_gap() {
    remove_action( 'wp_head', '_admin_bar_bump_cb' );
  }

  function customise_table_tags( $content ) {
    if (
      is_string( $content ) &&
      strpos( $content, '<table' ) !== false
    ) {
      $content = preg_replace_callback( '/<table\s?[^>]*/', function ( $matches ) {
        $table = $matches[0];
        $attr  = 'class';
        $value = 'table';

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

  function body_classes( $classes ) {
    if ( ! is_singular() ) {
      $classes[] = 'hfeed';
    }

    if ( is_page() ) {
      $post_id               = get_the_ID();
      $page_settings_manager = Elementor\Core\Settings\Manager::get_settings_managers( 'page' );
      $page_settings_model   = $page_settings_manager->get_model( $post_id );
      $stretch_last_block    = 'yes' == $page_settings_model->get_settings_for_display( 'stretch_last_block' );

      if ( $stretch_last_block ) {
        $classes[] = 'porel-stretch-last-block';
      }
    }

    return $classes;
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
      'primary_menu' => __( 'Primary Menu' ),
    ) );
  }

  function enqueue_scripts() {
    $suffix = SCRIPT_DEBUG ? '' : '.min';

    /* Common assets */
    $assets_css = [
      'css-adminkit' => 'lib/adminkit/static/css/app.css',
    ];

    $assets_js = [
      'js-css-browser' => 'lib/css_browser_selector/css_browser_selector.js',
      'js-wow'         => 'lib/wow/dist/wow.min.js',
      'js-adminkit'    => 'lib/adminkit/static/js/app.js',
    ];

    /* Enqueue */
    wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'underscore' );
    wp_enqueue_style( 'select2' );
    wp_enqueue_script( 'select2' );

    foreach ( $assets_css as $handle => $path ) {
      wp_enqueue_style( $handle, POR_Core::instance()->helpers->get_assets_path( $path ), [], ASSETS_VERSION );
    }
    foreach ( $assets_js as $handle => $path ) {
      wp_enqueue_script( $handle, POR_Core::instance()->helpers->get_assets_path( $path ), [], ASSETS_VERSION, true );
    }

    /* Conditions */
    wp_script_add_data( 'theme-html5', 'conditional', 'lt IE 9' );
    wp_script_add_data( 'theme-respond', 'conditional', 'lt IE 9' );

    /* Register assets */
    wp_register_style( 'theme-swiper-css', get_theme_file_uri( 'assets/lib/swiper/swiper-bundle.min.css' ), [] );
    wp_register_script( 'theme-swiper-js', get_theme_file_uri( 'assets/lib/swiper/swiper-bundle.min.js' ), [], false, true );

    /* Main assets */
    wp_enqueue_style( 'theme-dashicons', includes_url( "css/dashicons$suffix.css" ), [], ASSETS_VERSION );
    wp_enqueue_style( 'theme-style', get_theme_file_uri( 'dist/main.min.css' ), [], ASSETS_VERSION );
    wp_enqueue_script( 'theme-js', get_theme_file_uri( 'dist/main.min.js' ), [], ASSETS_VERSION, true );

    wp_localize_script( 'theme-js',
      'Portal',
      [
        'ajaxUrl'                 => admin_url( 'admin-ajax.php' ),
        'addedToEtaRequestString' => POR_TEXT_ADDED_TO_ETA_REQUEST,
        'addToEtaRequestString'   => POR_TEXT_ADD_TO_ETA_REQUEST,
      ]
    );
  }

  function excerpt_more( $more ): string {
    return ' ...';
  }

  function excerpt_length( $length ): string {
    return 30;
  }

  function hook_head() {
    ?>
      <script type="text/javascript">var $ = jQuery.noConflict();</script>
    <?php
  }

  function wp_footer() {
  }
}
