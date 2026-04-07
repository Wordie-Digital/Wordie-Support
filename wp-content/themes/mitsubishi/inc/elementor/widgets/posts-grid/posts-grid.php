<?php

defined( 'ABSPATH' ) or exit;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

class Custom_El_Posts_Grid extends Widget_Base {
  public function __construct( $data = [], $args = null ) {
    parent::__construct( $data, $args );
  }

  public function get_name() {
    return 'Custom_El_Posts_Grid';
  }

  public function get_title() {
    return 'Posts Grid';
  }

  public function get_icon() {
    return 'eicon-custom';
  }

  public function get_categories() {
    return [ 'custom' ];
  }

  public function get_script_depends() {
    return [];
  }

  public function get_style_depends() {
    return [];
  }

  protected function register_controls() {
    $this->start_controls_section(
      'setting_section',
      [
        'label' => 'Settings',
        'tab'   => Controls_Manager::TAB_CONTENT,
      ]
    );

    $this->add_control(
      'post_type',
      [
        'label'    => __( 'Post Type', 'plugin-domain' ),
        'type'     => \Elementor\Controls_Manager::SELECT2,
        'multiple' => false,
        'default'  => 'post',
        'options'  => MIT_Core::instance()->helpers->get_post_types_options(),
      ]
    );

    $this->add_control(
      'taxonomy',
      [
        'label'    => __( 'Taxonomy', 'plugin-domain' ),
        'type'     => \Elementor\Controls_Manager::SELECT2,
        'multiple' => false,
        'default'  => '',
        'options'  => MIT_Core::instance()->helpers->get_taxonomies_options(),
      ]
    );

    $this->add_control(
      'term_slug',
      [
        'label'       => __( 'Term slug', 'mit' ),
        'type'        => Controls_Manager::TEXT,
        'placeholder' => '',
        'condition'   => [
          'taxonomy' => array_keys( MIT_Core::instance()->helpers->get_taxonomies_options() ),
        ],
      ]
    );

    $this->add_control(
      'tag',
      [
        'label'       => __( 'Tag', 'mit' ),
        'type'        => Controls_Manager::TEXT,
        'description' => 'Comma delimiter',
        'placeholder' => '',
        'condition'   => [
          'taxonomy' => array_keys( MIT_Core::instance()->helpers->get_taxonomies_options() ),
        ],
      ]
    );

    $this->add_control(
      'filter_id',
      [
        'label'     => __( 'ALM filter ID', 'mit' ),
        'type'      => Controls_Manager::TEXT,
        'separator' => 'before',
      ]
    );

    $this->add_control(
      'filter_after_hook',
      [
        'label'       => __( 'After filter hook', 'mit' ),
        'type'        => Controls_Manager::TEXT,
        'description' => 'For developers. "el_posts_grid_[hook_name]"',
      ]
    );

    $this->end_controls_section();

    $this->start_controls_section(
      'content_section',
      [
        'label' => 'Content',
        'tab'   => Controls_Manager::TAB_CONTENT,
      ]
    );

    $this->add_control(
      'heading',
      [
        'label' => __( 'Grid Heading', 'mit' ),
        'type'  => Controls_Manager::TEXT,
      ]
    );

    $this->add_control(
      'description',
      [
        'label' => __( 'Grid description', 'mit' ),
        'type'  => Controls_Manager::TEXT,
      ]
    );

    $this->add_control(
      'posts_rows',
      [
        'label'   => __( 'Number of rows', 'mit' ),
        'type'    => Controls_Manager::NUMBER,
        'step'    => 1,
        'min'     => 1,
        'max'     => 24,
        'default' => 4,
      ]
    );

    $this->add_control(
      'posts_per_row',
      [
        'label'   => __( 'Posts per row', 'mit' ),
        'type'    => Controls_Manager::NUMBER,
        'min'     => 1,
        'max'     => 6,
        'default' => 3,
      ]
    );

    $this->add_control(
      'posts_per_row_mobile',
      [
        'label'   => __( 'Posts per row | Mobile', 'mit' ),
        'type'    => Controls_Manager::NUMBER,
        'min'     => 1,
        'max'     => 6,
        'default' => 2,
      ]
    );

    $this->end_controls_section();
  }

  protected function render() {
    $uid = uniqid( 'el-posts-grid-' );

    $posts_per_row        = $this->get_settings_for_display( 'posts_per_row' );
    $posts_per_row_mobile = $this->get_settings_for_display( 'posts_per_row_mobile' );
    $posts_rows           = $this->get_settings_for_display( 'posts_rows' );

    $heading     = $this->get_settings_for_display( 'heading' );
    $description = $this->get_settings_for_display( 'description' );
    $filter_id   = $this->get_settings_for_display( 'filter_id' );
    $post_type   = $this->get_settings_for_display( 'post_type' );

    $taxonomy  = $this->get_settings_for_display( 'taxonomy' );
    $term_slug = $this->get_settings_for_display( 'term_slug' );
    $tag       = $this->get_settings_for_display( 'tag' );

    $filter_after_hook = $this->get_settings_for_display( 'filter_after_hook' );

    $posts_per_page = absint( $posts_per_row ) * absint( $posts_rows );
    ?>
    <div id="<?= $uid ?>" class="el-posts-grid">
      <div class="el-posts-grid__posts">
        <?php
        // Only shown on archive pages
        if ( ! is_tax() && ! is_category() ) {
          ob_start();

          // ALM filter
          if ( ! empty( $filter_id ) ) {
            if ( ! empty( $taxonomy ) && ! empty( $term_slug ) ) {
              add_filter( "alm_filters_{$filter_id}_{$taxonomy}_args", function ( $args ) use ( $term_slug, $taxonomy ) {
                $term = get_term_by( 'slug', $term_slug, $taxonomy );

                if ( ! is_wp_error( $term ) && ! empty( $term ) ) {
                  $args['child_of'] = $term->term_id;
                  $args['orderby']  = 'menu_order';
                }

                return $args;
              } );

              add_filter( "alm_filters_{$filter_id}_{$taxonomy}_default", function () use ( $term_slug ) {
                return $term_slug;
              } );
            }

            echo do_shortcode( '[ajax_load_more_filters id="' . $filter_id . '" target="' . $uid . '"]' );
          }

          // Custom hooks
          if ( ! empty( $filter_after_hook ) ) {
            do_action( "el_posts_grid_{$filter_after_hook}", $taxonomy, $term_slug, $uid );
          }

          $right_content = ob_get_clean();

          if ( ! empty( $heading ) || ! empty( $right_content ) ) {
            get_template_part( 'template-parts/section-heading/section-heading', null, [
              'heading'       => $heading,
              'right_content' => $right_content,
              'description'   => $description,
            ] );
          }
        }

        ////////////////////////////////////////////////////////////////
        // Infinite ajax load more posts
        $args = [
          'id'                           => $uid,
          'container_type'               => 'div',
          'post_type'                    => $post_type,
          'posts_per_page'               => $posts_per_page,
          'preloaded'                    => 'true',
          'preloaded_amount'             => $posts_per_page,
          'transition_container_classes' => "row row-cols-$posts_per_row_mobile row-cols-xl-$posts_per_row",
          'images_loaded'                => 'true',
          'archive'                      => ! empty( $taxonomy ) ? 'false' : 'true',
          'pause'                        => 'true',
          'scroll'                       => 'false',
          'no_results_text'              => MIT_ALM_NO_RESULTS_TEXT,
        ];

        // Sort alphabetical for Warranty Cards
        if ( 'warranty-cards' == $term_slug ) {
          $args['orderby'] = 'title';
          $args['order']   = 'ASC';
        }

        // Filters
        if ( ! empty( $taxonomy ) && ! empty( $term_slug ) ) {
          $args['taxonomy']          = $taxonomy;
          $args['taxonomy_terms']    = $term_slug;
          $args['taxonomy_operator'] = "IN";
        }

        if ( ! empty( $tag ) ) {
          $args['tag'] = $tag;
        }

        if ( ! empty( $filter_id ) ) {
          $args['filters'] = 'true';
          $args['target']  = $filter_id;
        }

        // Render
        if ( function_exists( 'alm_render' ) ) {
          alm_render( $args );
        }
        ?>
      </div>
    </div>
    <?php
  }
}
