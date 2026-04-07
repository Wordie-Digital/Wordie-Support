<?php

defined( 'ABSPATH' ) or exit;

class POR_Admin {
  public function __construct() {
    add_filter( 'acf/settings/show_admin', ( defined( 'SHOW_ACF' ) && SHOW_ACF ? '__return_true' : '__return_false' ) );
    add_action( 'acf/init', [ $this, 'site_settings_page' ] );
    add_filter( 'map_meta_cap', [ $this, 'unfiltered_html_capability' ], 1, 3 );
    add_action( 'admin_head', [ $this, 'admin_head' ] );
    add_filter( 'get_user_option_admin_color', [ $this, 'update_user_option_admin_color' ], 5 );
    add_filter( 'the_privacy_policy_link', [ $this, 'custom_the_privacy_policy_link' ] );
    remove_filter( 'lostpassword_url', 'wc_lostpassword_url' );
    add_action( 'admin_head', [ $this, 'admin_and_public_head' ] );
    add_action( 'wp_head', [ $this, 'admin_and_public_head' ] );
    add_action( 'admin_menu', [ $this, 'remove_menus' ], 100 );

    // Modify default post type
    add_action( 'init', [ $this, 'change_post_object' ] );
    add_filter( 'register_post_type_args', [ $this, 'remove_from_public' ], PHP_INT_MAX, 2 );

    // Fix Rank Math not saved settings
    add_filter( 'cmb2_can_save', [ $this, 'fix_rank_math_can_save' ], 10, 2 );

    // To fix issue of user role not saving for profile_builder plugin
    add_filter( 'signup_user_meta', [ $this, 'fix_signup_user_meta_for_profile_builder' ], 10, 4 );
    add_filter( 'woocommerce_current_user_can_edit_customer_meta_fields', [ $this, 'allow_edit_profile' ], 10, 2 );
  }

  function allow_edit_profile( $is_allowed, $user_id ) {
    $roles = get_user_by( 'id', $user_id )->roles;

    if ( in_array( 'me-portal-user-admin', $roles ) ) {
      $is_allowed = true;
    }

    return $is_allowed;
  }

  function admin_and_public_head() {
    $roles = wp_get_current_user()->roles;

    if (
      in_array( 'me_product_team', $roles )
    ) : ?>
      <style>
          #menu-posts-cpt-training,
          #menu-posts-cpt-resource,
          #wp-admin-bar-new-content {
              display: none;
          }
      </style>
    <?php endif;

    if (
      count( $roles ) <= 1 &&
      in_array( 'me-portal-user-admin', $roles )
    ) : ?>
      <style>
          #toplevel_page_woocommerce,
          .wp-menu-separator,
          #menu-posts-elementor_library,
          #menu-posts,
          #wp-admin-bar-new-content,
          [id^="menu-posts-cpt-"] {
              display: none;
          }
      </style>
    <?php endif;
  }

  function remove_menus() {
    $roles = wp_get_current_user()->roles;

    if (
      in_array( 'me_marketing', $roles ) ||
      in_array( 'me_product_team', $roles )
    ) {
      remove_menu_page( 'edit.php?post_type=elementor_library' );
      remove_menu_page( 'elementor' );
    }
  }

  function fix_signup_user_meta_for_profile_builder( $meta, $user, $user_email, $key ) {
    if (
      empty( $meta['new_role'] ) &&
      ! empty( $_POST['wppb_re_user_roles'] ) &&
      is_array( $_POST['wppb_re_user_roles'] )
    ) {
      $role = $_POST['wppb_re_user_roles'][0];
      $role = sanitize_text_field( $role );

      $meta['new_role'] = $role;
    }

    return $meta;
  }

  function custom_the_privacy_policy_link( $link ) {
    return str_replace( 'class="privacy-policy-link"', 'class="privacy-policy-link" target="_blank"', $link );
  }

  function remove_from_public( $args, $post_type ) {
    if ( 'post' == $post_type ) {
      $args['public']  = false;
      $args['show_ui'] = true;

      $args['show_in_rest'] = false;
      $args['rewrite']      = false;
      $args['rest_base']    = false;
    }

    return $args;
  }

  function change_post_object() {
    $get_post_type              = get_post_type_object( 'post' );
    $labels                     = $get_post_type->labels;
    $labels->name               = 'Updates';
    $labels->singular_name      = 'Update';
    $labels->add_new            = 'Add Update';
    $labels->add_new_item       = 'Add Update';
    $labels->edit_item          = 'Edit Update';
    $labels->new_item           = 'Update';
    $labels->view_item          = 'View Update';
    $labels->search_items       = 'Search Updates';
    $labels->not_found          = 'No Update found';
    $labels->not_found_in_trash = 'No Updates found in Trash';
    $labels->all_items          = 'All Updates';
    $labels->menu_name          = 'Updates';
    $labels->name_admin_bar     = 'Updates';
  }

  function fix_rank_math_can_save( $can_save, $cmb ) {
    if (
      ! $can_save &&
      is_multisite() &&
      ms_is_switched() &&
      current_user_can( 'manage_options' )
    ) {
      return true;
    }

    return $can_save;
  }

  function update_user_option_admin_color( $color_scheme ): string {
    return 'midnight';
  }

  function admin_head() {
    ?>
    <style>
        .bfu-upload-notice,
        .alm-err-notice {
            display: none;
        }

        #adminmenu li.wp-menu-separator + .wp-menu-separator {
            display: none !important;
        }

        #adminmenu li.wp-menu-separator {
            border-bottom: 1px solid #6a6a6a;
        }

        .plugins tr[data-plugin='admin-columns-pro/admin-columns-pro.php'] th,
        .plugins tr[data-plugin='admin-columns-pro/admin-columns-pro.php'] td {
            box-shadow: inset 0 -1px 0 rgba(0, 0, 0, 0.1) !important;
        }

        .plugins .plugin-update-tr[data-slug="admin-columns-pro.php"],
        .plugins [data-slug^="ajax-load-more-"] + .plugin-update-tr {
            display: none;
        }
    </style>
    <?php
  }

  function site_settings_page() {
    if ( ( current_user_can( 'manage_options' ) || current_user_can( 'editor' ) ) && function_exists( 'acf_add_options_page' ) ) {
      acf_add_options_page( array(
        'page_title'    => 'Portal Settings',
        'menu_title'    => 'Portal Settings',
        'menu_slug'     => 'site-settings',
        'capability'    => 'edit_posts',
        'icon_url'      => 'dashicons-art',
        'update_button' => 'Save Settings',
        'redirect'      => false,
      ) );
    }
  }

  function unfiltered_html_capability( $caps, $cap, $user_id ) {
    if ( 'unfiltered_html' === $cap && user_can( $user_id, 'edit_posts' ) ) {
      $caps = [ 'unfiltered_html' ];
    }

    return $caps;
  }
}
