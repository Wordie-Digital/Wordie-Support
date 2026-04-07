<?php

defined( 'ABSPATH' ) or exit;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

class Custom_El_Posts_Slider extends Widget_Base {
  public function __construct( $data = [], $args = null ) {
    parent::__construct( $data, $args );
  }

  public function get_name() {
    return 'Custom_El_Posts_Slider';
  }

  public function get_title() {
    return 'Latest Posts Slider';
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
      'heading',
      [
        'label' => __( 'Grid Heading', 'mit' ),
        'type'  => Controls_Manager::TEXT,
      ]
    );

    $this->add_control(
      'number_of_posts',
      [
        'label'   => __( 'Number of posts', 'mit' ),
        'type'    => Controls_Manager::NUMBER,
        'min'     => 4,
        'max'     => 30,
        'step'    => 1,
        'default' => 10,
      ]
    );

    $this->end_controls_section();
  }

  protected function render() {
    global $post;

    $uid = uniqid( 'el-posts-slider-' );

    $heading         = $this->get_settings_for_display( 'heading' );
    $post_type       = $this->get_settings_for_display( 'post_type' );
    $number_of_posts = $this->get_settings_for_display( 'number_of_posts' );
    ?>
    <div id="<?= $uid ?>" class="el-posts-slider">
      <div class="container">
        <?
        get_template_part( 'template-parts/section-heading/section-heading', null, [
          'heading' => $heading,
        ] );

        $posts_list = get_posts( [
          'post_type'      => $post_type,
          'post_status'    => 'publish',
          'posts_per_page' => $number_of_posts,
        ] );

        ?>
        <div class="el-posts-slider__posts">
          <div class="swiper">
            <div class="swiper-wrapper">
              <? foreach ( $posts_list as $p ): ?>
                <div class="swiper-slide pb-5 pb-lg">
                  <?php
                  $post = $p;
                  setup_postdata( $post );

                  get_template_part( 'template-parts/post-generic/post-generic' )
                  ?>
                </div>
              <?php endforeach; ?>
              <? wp_reset_postdata(); ?>
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
                resizeObserver: true,
                observeParents: true,
                slidesPerView: 1,
                spaceBetween: 15,
                speed: 600,
                freeMode: {
                  enabled: true,
                  sticky: true,
                },
                navigation: {
                  nextEl: '#<?= $uid ?> .swiper-button-next',
                  prevEl: '#<?= $uid ?> .swiper-button-prev',
                },
                pagination: {
                  el: '#<?= $uid ?> .swiper-pagination',
                  type: 'bullets',
                },
                breakpoints: {
                  992: {
                    spaceBetween: 40,
                    pagination: false,
                    slidesPerView: 3,
                  },
                  1336: {
                    spaceBetween: 30,
                    pagination: false,
                    slidesPerView: 4,
                  },
                }
              });
            });
          </script>
        </div>
      </div>
    </div>
    <?php
  }
}
