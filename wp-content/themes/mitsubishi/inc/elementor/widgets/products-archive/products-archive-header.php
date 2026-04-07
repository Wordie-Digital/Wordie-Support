<?php

defined( 'ABSPATH' ) or exit;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

class Custom_El_Products_Archive_Header extends Widget_Base {
  public function __construct( $data = [], $args = null ) {
    parent::__construct( $data, $args );
  }

  public function get_name() {
    return 'Custom_El_Products_Archive_Header';
  }

  public function get_title() {
    return 'Products Archive | Header';
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
    $queried_object = get_queried_object();

    $thumbnail_id = get_term_meta( $queried_object->term_id, 'thumbnail_id', true );
    $image        = wp_get_attachment_image_src( $thumbnail_id, 'full' );
    $image_url    = $image ? $image[0] : false;
    ?>
    <div id="<?= $uid ?>" class="el-products-archive">
      <div class="el-products-archive__header <?= $image_url ? 'el-products-archive__header--has-bg' : '' ?>" <?= $image_url ? 'style="background-image: url(' . $image_url . ');"' : '' ?>>
        <div class="container">
          <? get_template_part( 'template-parts/breadcrumbs/breadcrumbs' ) ?>

          <h1><?= $queried_object->name ?></h1>

          <?php if ( ! empty( $queried_object->description ) ) : ?>
            <div class="row">
              <div class="col-xl-4 col-lg-6">
                <?= wpautop( $queried_object->description ) ?>
              </div>
            </div>
          <?php endif; ?>

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

              <script>
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
    </div>
    <?php
  }
}
