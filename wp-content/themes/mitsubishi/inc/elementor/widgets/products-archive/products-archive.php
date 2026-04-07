<?php

defined( 'ABSPATH' ) or exit;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

class Custom_El_Products_Archive extends Widget_Base {
  public function __construct( $data = [], $args = null ) {
    parent::__construct( $data, $args );
  }

  public function get_name() {
    return 'Custom_El_Products_Archive';
  }

  public function get_title() {
    return 'Products | Category Pages';
  }

  public function get_icon() {
    return 'eicon-custom';
  }

  public function get_categories() {
    return [ 'custom' ];
  }

  public function get_script_depends() {
    return [ 'theme-swiper-js' ];
  }

  public function get_style_depends() {
    return [ 'theme-swiper-css' ];
  }

  protected function _register_controls() {
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
        'label'     => __( 'Used on Products archive page only', 'plugin-name' ),
        'type'      => \Elementor\Controls_Manager::HEADING,
        'separator' => 'after',
      ]
    );

    $this->end_controls_section();
  }

  protected function render() {
    if ( ! is_product_taxonomy() ) {
      return;
    }

    $uid            = uniqid( 'el-products-archive-' );
    $MIT_helpers    = MIT_Core::instance()->helpers;
    $queried_object = get_queried_object();
    $alm_filter_id  = 'mit_products_catalog_filter';

    $child_terms = get_terms( [
      'taxonomy'   => $queried_object->taxonomy,
      'parent'     => $queried_object->term_id,
      'hide_empty' => true,
    ] );

    $thumbnail_id = get_term_meta( $queried_object->term_id, 'thumbnail_id', true );
    $image        = wp_get_attachment_image_src( $thumbnail_id, 'full' );
    $image_url    = $image ? $image[0] : false;
    ?>
    <div id="<?= $uid ?>" class="el-products-archive">
      <div class="el-products-archive__header <?= $image_url ? 'el-products-archive__header--has-bg' : '' ?>" <?= $image_url ? 'style="background-image: url(' . $image_url . ');"' : '' ?>>
        <div class="container">
          <? get_template_part( 'template-parts/breadcrumbs/breadcrumbs' ) ?>

          <h1><?= $queried_object->name ?></h1>

          <?/*
          <? if ( ! is_wp_error( $child_terms ) && ! empty( $child_terms ) ) : ?>
            <div class="el-products-archive__terms">
              <div class="swiper">
                <div class="swiper-wrapper">
                  <?php foreach ( $child_terms as $term ) : ?>
                    <div class="swiper-slide">
                      <?
                      $thumbnail_id = get_term_meta( $term->term_id, 'thumbnail_id', true );
                      $image        = wp_get_attachment_image_src( $thumbnail_id, 'medium' );

                      get_template_part( 'template-parts/card/card', null, [
                        'image_url'   => $image ? $image[0] : $MIT_helpers->get_assets_path( 'images/placeholder.png' ),
                        'image_alt'   => $term->name,
                        'content'     => "<h3>$term->name</h3>" . wp_trim_words( $term->description, 20 ),
                        'button_link' => get_term_link( $term->term_id, $queried_object->taxonomy ),
                        'hide_button' => true,
                      ] );
                      ?>
                    </div>
                  <?php endforeach; ?>
                </div>

                <!-- If we need pagination -->
                <div class="swiper-pagination"></div>

                <!-- If we need navigation buttons -->
                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>
              </div>

              <script defer>
                jQuery(document).ready(function($) {
                  const swiper = new Swiper('#<?= $uid ?> .swiper', {
                    loop: false,
                    speed: 600,
                    resizeObserver: true,
                    observeParents: true,
                    slidesPerView: 5,
                    spaceBetween: 30,
                    freeMode: {
                      enabled: true,
                      sticky: true,
                    },
                    navigation: {
                      nextEl: '#<?= $uid ?> .swiper-button-next',
                      prevEl: '#<?= $uid ?> .swiper-button-prev',
                    },
                    on: {
                      init: function() {
                        setTimeout(function() {
                          Mitsubishi.balanceAllElements();
                        }, 500);
                      },
                    },
                  });
                });
              </script>
            </div>
          <? endif; ?>
          */ ?>
        </div>
      </div>

      <div class="el-products-archive__products py-3 py-lg-5">
        <div class="container">
          <div class="row row-cols-1 row-cols-xl-4 row-cols-xxl-5">
            <div class="col">
              <div class="el-products-archive__filters">
                <? if ( ! is_wp_error( $child_terms ) && ! empty( $child_terms ) ) : ?>
                  <h6>Filter by</h6>
                  <?
                  add_filter( "alm_filters_{$alm_filter_id}_product_cat_args", function ( $args ) use ( $queried_object ) {
                    $args['child_of'] = $queried_object->term_id;
                    $args['orderby']  = 'menu_order';

                    return $args;
                  } );

                  add_filter( "alm_filters_{$alm_filter_id}_product_cat_default", function () use ( $queried_object ) {
                    return $queried_object->slug;
                  } );

                  echo do_shortcode( '[ajax_load_more_filters id="' . $alm_filter_id . '" target="' . $uid . '"]' )
                  ?>
                <? endif; ?>
              </div>

              <div class="d-none d-xl-block">
                <?= do_shortcode( '[elementor-template id="558"]' ) ?>
              </div>
            </div>

            <div class="col flex-xl-grow-1">
              <?
              $args = [
                'id'                           => $uid,
                'container_type'               => 'div',
                'post_type'                    => 'product',
                'posts_per_page'               => 8,
                'preloaded'                    => 'true',
                'preloaded_amount'             => 12,
                'transition_container_classes' => "row row-cols-1 row-cols-md-2 row-cols-xxl-4",
                'images_loaded'                => 'true',
                'taxonomy'                     => 'product_cat',
                'taxonomy_terms'               => $queried_object->slug,
                'taxonomy_operator'            => 'IN',
                'pause'                        => 'true',
                'scroll'                       => 'false',
                'filters'                      => 'true',
                'target'                       => $alm_filter_id,
                'no_results_text'              => MIT_ALM_NO_RESULTS_TEXT,
              ];

              // Render
              if ( function_exists( 'alm_render' ) ) {
                alm_render( $args );
              }
              ?>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php
  }
}
