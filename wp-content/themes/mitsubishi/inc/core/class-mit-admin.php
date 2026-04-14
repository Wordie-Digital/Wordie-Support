<?php

defined( 'ABSPATH' ) or exit;

class MIT_Admin {
  public function __construct() {
    //add_filter( 'acf/settings/show_admin', ( defined( 'SHOW_ACF' ) && SHOW_ACF ? '__return_true' : '__return_false' ) );
    add_action( 'acf/init', [ $this, 'site_settings_page' ] );
    add_filter( 'map_meta_cap', [ $this, 'unfiltered_html_capability' ], 1, 3 );
    add_action( 'admin_head', [ $this, 'admin_head' ] );
    add_action( 'admin_head', [ $this, 'admin_and_public_head' ] );
    add_action( 'wp_head', [ $this, 'admin_and_public_head' ] );
    add_filter( 'tag_row_actions', [ $this, 'remove_row_actions_term' ], 10, 2 );
    add_action( 'pre_delete_term', [ $this, 'restrict_taxonomy_deletion' ], 10, 2 );
    add_filter( 'relevanssi_search_ok', [ $this, 'fix_relevanssi_search_elementor_library' ], 10, 2 );
    add_filter( 'relevanssi_custom_field_value', [ $this, 'rlv_expand_category_in_fields' ], 10, 2 );
    //add_action( 'admin_menu', [ $this, 'remove_menus' ], 100 );
  }

  function admin_and_public_head() {
    $roles = wp_get_current_user()->roles;

    if (
      in_array( 'me_careers', $roles ) ||
      in_array( 'me_locator', $roles )
    ) : ?>
      <style>
          #menu-posts-cpt-recipe,
          #menu-posts-cpt-video,
          #menu-posts-cpt-download,
          #menu-posts-cpt-news,
          #menu-posts,
          #wp-admin-bar-new-content {
              display: none;
          }
      </style>
    <?php endif;
  }

  function remove_menus() {
    $roles = wp_get_current_user()->roles;

    if (
      in_array( 'me_locator', $roles ) ||
      in_array( 'me_marketing', $roles ) ||
      in_array( 'me_careers', $roles )
    ) {
      remove_menu_page( 'edit.php?post_type=elementor_library' );
      remove_menu_page( 'elementor' );
    }
  }

  function rlv_expand_category_in_fields( $values, $field ) {
    if ( ! $values ) {
      return $values;
    }

    if ( 'key_features' == $field ) {
      $values = array_map(
        function ( $value ) {
          if ( is_array( $value ) ) {
            $final_values = [];
            foreach ( $value as $key_feature_id ) {
              /** @var WP_Term $key_feature */
              $key_feature = get_term( $key_feature_id, 'pa_key-features' );

              $final_values[] = ( @explode( '_', $key_feature->name )[0] ) . '; ' . $key_feature->description;
            }

            return $final_values;
          }

          return $value;
        },
        $values
      );
    }

    return $values;
  }

  function fix_relevanssi_search_elementor_library( $ok, $query ) {
    if ( 'elementor_library' === $query->query_vars['post_type'] ) {
      $ok = false;
    }

    return $ok;
  }

  function remove_row_actions_term( $actions, $term ) {
    if ( 'doc_category' === $term->taxonomy && in_array( $term->term_id, [ 116, 138, 118, 131 ] ) ) {
      unset( $actions['delete'] );
    }

    return $actions;
  }

  function restrict_taxonomy_deletion( $term, $taxonomy ) {
    if ( 'doc_category' === $taxonomy && in_array( $term->term_id, [ 116, 138, 118, 131 ] ) ) {
      wp_die( 'The taxonomy you were trying to delete is protected.' );
    }
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
            border-bottom: 1px solid #5f5f5f;
        }

        tr.type-page#post-575 {
            background-color: #dbebde;
        }

        tr.type-page#post-2 {
            background-color: #dbdceb;
        }

        [name="edittag"] [value="116"] ~ .edit-tag-actions #delete-link,
        [name="edittag"] [value="118"] ~ .edit-tag-actions #delete-link,
        [name="edittag"] [value="131"] ~ .edit-tag-actions #delete-link,
        [name="edittag"] [value="138"] ~ .edit-tag-actions #delete-link,
        tr.type-page#post-575 .submitdelete,
        tr.type-page#post-2 .submitdelete {
            display: none !important;
        }

        .plugins tr[data-plugin='admin-columns-pro/admin-columns-pro.php'] th,
        .plugins tr[data-plugin='admin-columns-pro/admin-columns-pro.php'] td {
            box-shadow: inset 0 -1px 0 rgba(0, 0, 0, 0.1) !important;
        }

        .plugins .plugin-update-tr[data-slug="admin-columns-pro.php"],
        .plugins [data-slug^="ajax-load-more-"] + .plugin-update-tr {
            display: none;
        }

        .ac-image:not(body) img[src*=".svg"] {
            min-height: 60px;
            min-width: 60px;
        }
    </style>
    <?php
  }

  function site_settings_page() {
    if ( ( current_user_can( 'manage_options' ) || current_user_can( 'editor' ) ) && function_exists( 'acf_add_options_page' ) ) {
      acf_add_options_page( array(
        'page_title'    => 'Site Settings',
        'menu_title'    => 'Site Settings',
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
