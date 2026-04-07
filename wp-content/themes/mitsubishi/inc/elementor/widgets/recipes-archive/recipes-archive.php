<?php

defined( 'ABSPATH' ) or exit;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

class Custom_El_Archive_Recipes extends Widget_Base {
  public function __construct( $data = [], $args = null ) {
    parent::__construct( $data, $args );
  }

  public function get_name() {
    return 'Custom_El_Archive_Recipes';
  }

  public function get_title() {
    return 'Recipes | Archive';
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
        'label'     => __( 'Used on Recipes archive page only', 'plugin-name' ),
        'type'      => \Elementor\Controls_Manager::HEADING,
        'separator' => 'after',
      ]
    );

    $this->end_controls_section();
  }

  protected function render() {
    global $post;

    $uid = uniqid( 'el-recipes-grid-' );

    $terms = get_terms( [
      'taxonomy'   => 'recipe_category',
      'hide_empty' => true,
    ] );

    if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) : ?>
      <div id="<?= $uid ?>" class="el-recipes-grid">
        <div class="el-recipes-grid__nav">
          <?
          get_template_part( 'template-parts/anchor-nav/anchor-nav', null, [
            'list' => array_map( function ( $term ) {
              return [
                'link'  => "#recipes-term-{$term->slug}",
                'label' => $term->name,
              ];
            }, $terms ),
          ] );
          ?>
        </div>

        <div class="el-recipes-grid__featured">
          <?
          get_template_part( 'template-parts/section-posts-slider/section-posts-slider', null, [
            'taxonomy'   => 'recipe_category',
            'posts_list' => get_posts( [
              'post_type'      => 'cpt-recipe',
              'post_status'    => 'publish',
              'posts_per_page' => 3,
            ] ),
          ] );
          ?>
        </div>

        <div class="el-recipes-grid__posts">
          <?php foreach ( $terms as $term ) : ?>
            <div class="container el-recipes-grid__container" id="recipes-term-<?= $term->slug ?>">
              <?
              get_template_part( 'template-parts/section-heading/section-heading', null, [
                'heading' => $term->name,
              ] );
              ?>
              <div class="row row-cols-2 row-cols-xl-4 row-cols-xxl-5">
                <?
                $recipes = get_posts( [
                  'post_type'      => 'cpt-recipe',
                  'post_status'    => 'publish',
                  'posts_per_page' => - 1,
                  'tax_query'      => [
                    [
                      'taxonomy' => 'recipe_category',
                      'field'    => 'slug',
                      'terms'    => $term->slug,
                    ],
                  ],
                ] );

                foreach ( $recipes as $recipe ) : ?>
                  <?php
                  $post = $recipe;
                  setup_postdata( $post );

                  // Use ALM template in case this grid is converted to ALM
                  get_template_part( 'alm_templates/default' );
                  ?>
                <?php endforeach; ?>
                <? wp_reset_postdata(); ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif;
  }
}
