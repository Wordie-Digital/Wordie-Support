<?php

defined( 'ABSPATH' ) or exit;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;

class Custom_El_Timeline extends Widget_Base {
  public function __construct( $data = [], $args = null ) {
    parent::__construct( $data, $args );
  }

  public function get_name() {
    return 'Custom_El_Timeline';
  }

  public function get_title() {
    return 'Timeline';
  }

  public function get_icon() {
    return 'eicon-custom';
  }

  public function get_categories() {
    return [ 'custom' ];
  }

  public function get_script_depends() {
    return [ 'theme-swiper-js', 'js-OverlayScrollbars' ];
  }

  public function get_style_depends() {
    return [ 'theme-swiper-css', 'css-OverlayScrollbars' ];
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
        'label'     => __( 'The section containing this widget should be full width + no gap', 'plugin-name' ),
        'type'      => \Elementor\Controls_Manager::HEADING,
        'separator' => 'after',
      ]
    );

    $repeater = new Repeater();

    $repeater->add_control(
      'image',
      [
        'label'   => __( 'Image', 'plugin-domain' ),
        'type'    => \Elementor\Controls_Manager::MEDIA,
        'default' => [
          'url' => \Elementor\Utils::get_placeholder_image_src(),
        ],
      ]
    );

    $repeater->add_control(
      'subheading',
      [
        'label'       => __( 'Subheading', 'mit' ),
        'type'        => Controls_Manager::TEXT,
        'placeholder' => 'E.g. 1980',
      ]
    );

    $repeater->add_control(
      'content', [
        'label'      => __( 'Content', 'plugin-domain' ),
        'type'       => \Elementor\Controls_Manager::WYSIWYG,
        'show_label' => false,
      ]
    );

    $this->add_control(
      'slides',
      [
        'label'       => __( 'Slides', 'mit' ),
        'type'        => Controls_Manager::REPEATER,
        'fields'      => $repeater->get_controls(),
        'title_field' => '{{{ subheading }}}',
      ]
    );

    $this->end_controls_section();
  }

  protected function render() {
    $uid = uniqid( 'el-timeline-' );

    $MIT_helpers = MIT_Core::instance()->helpers;

    $slides = $this->get_settings_for_display( 'slides' );

    if ( ! empty( $slides ) ) : ?>
      <div id="<?= $uid ?>" class="el-timeline">
        <div class="container">
          <div class="swiper">
            <div class="swiper-wrapper">
              <?php foreach ( $slides as $slide ) : ?>
                <div class="swiper-slide">
                  <?
                  $image      = $slide['image'];
                  $subheading = $slide['subheading'];
                  $content    = $slide['content'];
                  ?>
                  <div
                    class="el-timeline__item row"
                    data-swiper-parallax-scale="0.8"
                    data-swiper-parallax-opacity="0.5"
                  >
                    <div class="col-lg-8">
                      <div class="el-timeline__item-image">
                        <img loading="lazy" src="<?= ! empty( $image['id'] ) ? wp_get_attachment_image_url( $image['id'], 'large' ) : $MIT_helpers->get_assets_path( 'images/placeholder.png' ) ?>" alt="<?= esc_attr( $subheading ) ?>"/>
                      </div>
                    </div>

                    <div class="col-lg-4 order-lg-last">
                      <div class="el-timeline__item-content h-100">
                        <?php if ( ! empty( $subheading ) ) : ?>
                          <div class="el-timeline__item-subheading"><?= $subheading ?></div>
                        <?php endif; ?>

                        <?php if ( ! empty( $content ) ) : ?>
                          <div class="pb-5 generic-content">
                            <?= wpautop( force_balance_tags( $content ) ) ?>
                          </div>
                        <?php endif; ?>
                      </div>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>

            <!-- If we need pagination -->
            <div class="swiper-pagination"></div>

            <!-- If we need navigation buttons -->
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
          </div>
        </div>

        <script>
          jQuery(document).ready(function($) {
            const swiper = new Swiper('#<?= $uid ?> .swiper', {
              parallax: true,
              loop: false,
              speed: 600,
              resizeObserver: true,
              observeParents: true,
              effect: 'coverflow',
              grabCursor: true,
              centeredSlides: true,
              slidesPerView: 'auto',
              pagination: {
                el: '#<?= $uid ?> .swiper-pagination',
                type: 'bullets',
                clickable: true,
              },
              coverflowEffect: {
                rotate: 50,
                stretch: 0,
                depth: 100,
                modifier: 1,
                slideShadows: false,
              },
              breakpoints: {
                992: {
                  navigation: {
                    nextEl: '#<?= $uid ?> .swiper-button-next',
                    prevEl: '#<?= $uid ?> .swiper-button-prev',
                  },
                }
              }
            });

            const instance = OverlayScrollbars($('<?="#{$uid}"?> .el-timeline__item-content'), {
              scrollbars: {
                autoHide: 'leave',
                autoHideDelay: 200,
              }
            });
          });
        </script>
      </div>
    <?php endif;
  }
}
