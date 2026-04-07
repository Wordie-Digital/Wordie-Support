<?php

defined( 'ABSPATH' ) or exit;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

class Custom_Porel_News_Updates extends Widget_Base {
  public function __construct( $data = [], $args = null ) {
    parent::__construct( $data, $args );
  }

  public function get_name() {
    return 'Custom_Porel_News_Updates';
  }

  public function get_title() {
    return 'News + Updates';
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
      'setting_section',
      [
        'label' => 'Settings',
        'tab'   => Controls_Manager::TAB_CONTENT,
      ]
    );

    $this->add_control(
      'message',
      [
        'label'     => __( 'Used on Portal Homepage only. News are pulled from Marketing site.', 'plugin-name' ),
        'type'      => \Elementor\Controls_Manager::HEADING,
        'separator' => 'after',
      ]
    );

    $this->add_control(
      'hide_news',
      [
        'label'        => __( 'Hide News', 'plugin-domain' ),
        'type'         => \Elementor\Controls_Manager::SWITCHER,
        'label_on'     => __( 'Yes', 'your-plugin' ),
        'label_off'    => __( 'No', 'your-plugin' ),
        'return_value' => 'yes',
        'default'      => 'no',
      ]
    );

    $this->add_control(
      'term_slug',
      [
        'label'    => __( 'Update Category', 'plugin-domain' ),
        'type'     => \Elementor\Controls_Manager::SELECT2,
        'default'  => 'uncategorized',
        'multiple' => false,
        'options'  => call_user_func( function () {
          $terms = get_terms( [
            'taxonomy'   => 'category',
            'hide_empty' => false,
            'parent'     => 0,
          ] );

          $options = [
            'uncategorized' => 'Uncategorised',
          ];

          foreach ( $terms as $term ) {
            $options[ $term->slug ] = $term->name;
          }

          return $options;
        } ),
      ]
    );

    $this->end_controls_section();
  }

  protected function render() {
    global $post;

    $uid         = uniqid( 'porel-news-updates-' );
    $POR_helpers = POR_Core::instance()->helpers;
    $hide_news   = $this->get_settings_for_display( 'hide_news' ) == 'yes';
    $term_slug   = $this->get_settings_for_display( 'term_slug' );

    ?>
    <div id="<?= $uid ?>" class="porel-news-updates">
      <div class="row">
        <?
        switch_to_blog( BLOG_ID_CURRENT_SITE );
        $news = get_posts( [
          'post_type'      => 'post',
          'post_status'    => 'publish',
          'posts_per_page' => 5,
        ] );

        if ( ! empty( $news ) && ! $hide_news ) : ?>
          <div class="col-lg-8">
            <div class="card">
              <div class="card-body">
                <h2 class="mb-3">MITSUBISHI ELECTRIC NEWS</h2>

                <div class="porel-news-updates__news">
                  <div class="swiper">
                    <div class="swiper-wrapper">
                      <?php foreach ( $news as $p ) : ?>
                        <div class="swiper-slide">
                          <?
                          $post = $p;
                          setup_postdata( $post );
                          ?>
                          <? get_template_part( 'template-parts/post-generic-large/post-generic-large', null, [ 'target' => '_blank' ] ) ?>
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
                    jQuery(document).ready(function ($) {
                      const swiper = new Swiper('#<?= $uid ?> .swiper', {
                        loop: false,
                        resizeObserver: true,
                        observeParents: true,
                        slidesPerView: 1,
                        spaceBetween: 10,
                        pagination: {
                          el: '#<?= $uid ?> .swiper-pagination',
                          type: 'bullets',
                          clickable: true,
                        },
                        navigation: {
                          nextEl: '#<?= $uid ?> .swiper-button-next',
                          prevEl: '#<?= $uid ?> .swiper-button-prev',
                        },
                      });
                    });
                  </script>
                </div>
              </div>
            </div>
          </div>
        <? endif; ?>
        <? restore_current_blog(); ?>

        <?
        // Query updates with Uncategorised category
        if ( $term_slug !== 'uncategorized' ) {
          $updates = get_posts( [
            'post_type'      => 'post',
            'post_status'    => 'publish',
            'posts_per_page' => 20,
            'category_name'  => $term_slug,
          ] );
        } else {
          $updates = get_posts( [
            'post_type'        => 'post',
            'post_status'      => 'publish',
            'posts_per_page'   => 20,
            'category__not_in' => get_terms( 'category', array(
              'fields'  => 'ids',
              'exclude' => [ 1 ],
            ) ),
          ] );
        }

        ?>
        <div class="col-lg-<?= $hide_news ? 12 : 4 ?>">
          <div class="card h-100">
            <div class="card-body card-body--updates">
              <h2 class="mb-3">UPDATES</h2>

              <?php if ( ! empty( $updates ) ) : ?>
                <div class="porel-news-updates__updates h-100">
                  <?php foreach ( $updates as $update ) : ?>
                    <?
                    $post = $update;
                    setup_postdata( $post );
                    ?>
                    <div class="porel-news-updates__updates-row">
                      <div class="row">
                        <div class="col col-xxl-1" style="max-width:50px;">
                          <img loading="lazy" src="<? POR_Core::instance()->helpers->the_assets_path( 'images/speaker.svg' ); ?>" class="img-fluid" width="20px" alt="Speaker">
                        </div>

                        <div class="col-10  col-xxl-11">
                          <p class="porel-news-updates__date mb-1"><? $POR_helpers->the_posted_on_date(); ?></p>
                          <h3><? the_title() ?></h3>
                          <? the_content(); ?>
                        </div>
                      </div>
                    </div>
                  <?php endforeach; ?>
                  <? wp_reset_postdata(); ?>
                </div>
              <?php else: ?>
                <p>No updates yet</p>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>

      <script>
        jQuery(document).ready(function ($) {
          <?php if ( ! $hide_news ) : ?>
          OverlayScrollbars($('.porel-news-updates__updates'), {
            overflowBehavior: {
              x: 'hidden',
              y: 'scroll',
            },
            scrollbars: {
              autoHide: 'leave',
              autoHideDelay: 200,
            }
          });
          <?php endif; ?>
        });
      </script>
    </div>
    <?php
  }
}
