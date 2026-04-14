<?php

defined( 'ABSPATH' ) or exit;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

class Custom_El_Products_Single_Downloads extends Widget_Base {
  public function __construct( $data = [], $args = null ) {
    parent::__construct( $data, $args );
  }

  public function get_name() {
    return 'Custom_El_Products_Single_Downloads';
  }

  public function get_title() {
    return 'Product | Downloads';
  }

  public function get_icon() {
    return 'eicon-custom';
  }

  public function get_categories() {
    return [ 'custom_woo' ];
  }

  public function get_script_depends() {
    return [];
  }

  public function get_style_depends() {
    return [];
  }

  protected function register_controls() {
    $this->start_controls_section(
      'content_section',
      [
        'label' => 'Content',
        'tab'   => Controls_Manager::TAB_CONTENT,
      ]
    );

    $this->add_control(
      'message',
      [
        'label'     => __( 'Used on Products single page only', 'plugin-name' ),
        'type'      => \Elementor\Controls_Manager::HEADING,
        'separator' => 'after',
      ]
    );

    $this->end_controls_section();
  }

  protected function render() {
    if ( ! is_product() ) {
      return;
    }

    $downloads_alm_uid       = uniqid( 'el-products-single-downloads-' );
    $downloads_alm_filter_id = 'mit_product_download_filter';
    $taxonomy                = 'pa_available-models';
    ?>
    <div class="el-products-single__downloads elementor-menu-anchor" id="section-product-downloads">
      <div class="container">
        <?
        add_filter( "alm_filters_{$downloads_alm_filter_id}_{$taxonomy}_args", function ( $args ) {
          global $product;

          $downloads = get_posts( array(
            'post_type'      => 'cpt-download',
            'post_status'    => 'publish',
            'posts_per_page' => - 1,
            'meta_query'     => [
              [
                'key'     => 'series_products',
                'compare' => 'LIKE',
                'value'   => $product->get_id(),
              ],
            ],
          ) );

          if ( ! empty( $downloads ) ) {
            $args['object_ids'] = wp_list_pluck( $downloads, 'ID' );
          }

          return $args;
        } );

        get_template_part( 'template-parts/section-heading/section-heading', null, [
          'heading'       => get_field( 'custom_download_heading' ) ?: 'DOWNLOADS',
          'right_content' => do_shortcode( '[ajax_load_more_filters id="' . $downloads_alm_filter_id . '" target="' . $downloads_alm_uid . '"]' ),
        ] );

        $downloads_args = [
          'id'                           => $downloads_alm_uid,
          'container_type'               => 'div',
          'post_type'                    => 'cpt-download',
          'posts_per_page'               => 8,
          'transition_container_classes' => "row row-cols-2 row-cols-xl-4",
          'images_loaded'                => 'true',
          'pause'                        => 'true',
          'scroll'                       => 'false',
          'no_results_text'              => MIT_ALM_NO_RESULTS_TEXT,

          'preloaded'        => 'true',
          'preloaded_amount' => 8,

          'meta_key'     => "series_products",
          'meta_compare' => "LIKE",
          'meta_value'   => get_the_ID(),

          'filters' => 'true',
          'target'  => $downloads_alm_filter_id,
        ];

        if ( function_exists( 'alm_render' ) ) {
          alm_render( $downloads_args );
        }
        ?>
      </div>
    </div>
    <?php
  }
}
