<?php

defined( 'ABSPATH' ) or exit;

class POR_Helpers {
  function the_posted_on_date() {
    echo $this->get_posted_on_date_html();
  }

  function get_posted_on_date_html(): string {
    $time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';

    return sprintf( $time_string,
      esc_attr( get_the_date( 'c' ) ),
      esc_html( get_the_date( 'd.m.Y' ) )
    );
  }

  function the_posted_date_with_author() {
    $time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
    if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
      $time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
    }

    $time_string = sprintf( $time_string,
      esc_attr( get_the_date( 'c' ) ),
      esc_html( get_the_date() ),
      esc_attr( get_the_modified_date( 'c' ) ),
      esc_html( get_the_modified_date() )
    );

    $posted_on = sprintf(
    /* translators: %s: post date. */
      esc_html_x( 'Posted on %s', 'post date', 'mit' ),
      '<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
    );

    $byline = sprintf(
    /* translators: %s: post author. */
      esc_html_x( 'by %s', 'post author', 'mit' ),
      '<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
    );

    echo '<span class="posted-on">' . $posted_on . '</span><span class="byline"> ' . $byline . '</span>'; // WPCS: XSS OK.
  }

  function get_assets_path( $filename = '' ): string {
    $dist_path = get_template_directory_uri() . '/assets/';

    if ( empty( $filename ) ) {
      return $dist_path;
    }

    $directory = dirname( $filename ) . '/';
    $file      = basename( $filename );

    return esc_url( $dist_path . $directory . $file );
  }

  function the_assets_path( $filename ) {
    echo $this->get_assets_path( $filename );
  }

  function get_page_id_by_template( $template_file_name ) {
    $pages = get_posts( array(
      'post_type'      => 'page',
      'posts_per_page' => - 1,
      'offset'         => 0,
      'orderby'        => 'date',
      'order'          => 'DESC',
      'post_status'    => 'publish',
      'meta_key'       => '_wp_page_template',
      'meta_value'     => $template_file_name,
    ) );
    if ( isset( $pages[0] ) ) {
      return $pages[0]->ID;
    }

    return false;
  }

  function get_page_title() {
    if ( is_home() ) {
      if ( get_option( 'page_for_posts', true ) ) {
        return get_the_title( get_option( 'page_for_posts', true ) );
      } else {
        return __( 'Latest Posts', 'sage' );
      }
    } elseif ( is_archive() ) {
      return str_replace( [ 'Archives: ' ], [ '' ], get_the_archive_title() );
    } elseif ( is_search() ) {
      return sprintf( __( 'Search Results for %s', 'sage' ), get_search_query() );
    } elseif ( is_404() ) {
      return __( 'Not Found', 'sage' );
    } else {
      return get_the_title();
    }
  }

  function get_spare_part_column_sort_button_html( $key ): string {
    return '<button class="porel-spare-parts__sort" onclick="BOM.sortBomResults(this); return false;" data-bom-sort="' . esc_attr( $key ) . '"><i class="align-middle" data-feather="code" style="transform:rotate(90deg);width:10px;"></i></button>';
  }

  function get_post_type_primary_tax( $post_type ): string {
    switch ( $post_type ) {
      case 'cpt-news':
        return 'category_news';

      case 'cpt-technical-doc':
        return 'doc_category';

      case 'cpt-resource':
        return 'resource_category';

      case 'product':
        return 'product_cat';

      default:
        return 'category';
    }
  }

  function get_post_types_options(): array {
    $options = [
      'post' => 'Posts',
    ];

    $post_types = get_post_types( array( '_builtin' => false ), 'objects' );

    foreach ( $post_types as $post_type ) {
      if ( false !== strpos( $post_type->name, 'cpt-' ) ) {
        $options[ $post_type->name ] = $post_type->label;
      }
    }

    return $options;
  }

  function get_taxonomies_options(): array {
    $options = [];

    $args = array(
      'public' => true,
    );

    if ( ! empty( $taxonomies = get_taxonomies( $args, 'objects' ) ) ) {
      foreach ( $taxonomies as $taxonomy => $obj ) {
        $options[ $taxonomy ] = $obj->label;
      }
    }

    return $options;
  }

  function get_last_term( $post_id, $taxonomy ) {
    if ( ! $post = get_post( $post_id ) ) {
      return false;
    }

    if ( class_exists( 'WPSEO_Primary_Term' ) ) {
      $term    = new WPSEO_Primary_Term( $taxonomy, $post->ID );
      $term_id = $term->get_primary_term();

      if ( $term_id ) {
        return get_term( $term_id, $taxonomy );
      }
    }

    $terms = get_the_terms( $post->ID, $taxonomy );
    if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
      return array_pop( $terms );
    }

    return null;
  }

  function get_spare_parts_compatible_models( $material_name ): array {
    $spare_parts = get_posts( [
      'post_type'      => 'cpt-spare-part',
      'posts_per_page' => - 1,
      'post_status'    => 'publish',
      'meta_query'     => [
        [
          'key'     => 'compatible_models',
          'compare' => 'LIKE',
          'value'   => $material_name,
        ],
      ],
    ] );

    $spare_parts_array = array_map( function ( $part ) use ( $material_name ) {
      return $this->get_part_array( $part->ID, $material_name );
    }, $spare_parts );

    // Double check if the material name is in the compatible models
    return array_values( array_filter( $spare_parts_array, function ( $part ) use ( $material_name ) {
      $compatible_models = explode( '|', $part['compatibleModels'] );

      return in_array( $material_name, $compatible_models );
    } ) );
  }

  function get_part_array( $post_id, $material_name = '' ): array {
    $part = get_post( $post_id );
    $qty  = 1;

    if ( $material_name ) {
      // Get qty for material from $part's "bom_qty" custom field. Example value MP-2120-22M:1|MP286L:1

      $bom = get_field( 'bom_qty', $part->ID );
      if ( $bom ) {
        $bom = explode( '|', $bom );
        if ( ! empty( $bom ) ) {
          foreach ( $bom as $bom_item ) {
            $bom_item = explode( ':', $bom_item );
            if ( strtolower( $bom_item[0] ) === strtolower( $material_name ) ) {
              $qty = $bom_item[1];
              break;
            }
          }
        }
      }
    }

    $compatible_models = get_field( 'compatible_models', $part->ID );

    return [
      'id'               => $part->ID,
      'material'         => $material_name ?: ( $compatible_models ? str_replace( '|', ', ', $compatible_models ) : '' ),
      'partNumber'       => get_field( 'part_number', $part->ID ) ?: 0,
      'partDescription'  => $part->post_title,
      'partPrice1'       => get_field( 'price_1', $part->ID ) ?: '',
      'partPrice2'       => get_field( 'price_2', $part->ID ) ?: '',
      'partQuantity'     => $qty,
      'partType'         => wp_strip_all_tags( get_the_term_list( $part->ID, 'part_type', '', ', ' ) ),
      'availability'     => get_field( 'available', $part->ID ) ?: '',
      'compatibleModels' => $compatible_models,
    ];
  }

  function get_size_as_kb( $yoursize ): string {
    if ( $yoursize < 1024 ) {
      return "{$yoursize} bytes";
    } elseif ( $yoursize < 1048576 ) {
      $size_kb = round( $yoursize / 1024 );

      return "{$size_kb} KB";
    } else {
      $size_mb = round( $yoursize / 1048576, 1 );

      return "{$size_mb} MB";
    }
  }

  function is_elementor_active(): bool {
    return class_exists( 'Elementor\Plugin' );
  }
}
