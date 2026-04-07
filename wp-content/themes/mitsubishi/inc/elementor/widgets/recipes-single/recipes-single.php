<?php

defined( 'ABSPATH' ) or exit;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

class Custom_El_Recipes_Single extends Widget_Base {
  public function __construct( $data = [], $args = null ) {
    parent::__construct( $data, $args );
  }

  public function get_name() {
    return 'Custom_El_Recipes_Single';
  }

  public function get_title() {
    return 'Recipes | Single';
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
      'content_section',
      [
        'label' => 'Content',
        'tab'   => Controls_Manager::TAB_CONTENT,
      ]
    );

    $this->add_control(
      'message',
      [
        'label'     => __( 'Used on Recipes single page only', 'plugin-name' ),
        'type'      => \Elementor\Controls_Manager::HEADING,
        'separator' => 'after',
      ]
    );

    $this->end_controls_section();
  }

  protected function render() {
    global $post;

    $MIT_helpers = MIT_Core::instance()->helpers;

    $uid = uniqid( 'el-recipes-single-' );
    ?>
    <div id="<?= $uid ?>" class="el-recipes-single pb-5">
      <div class="container">
        <? get_template_part( 'template-parts/breadcrumbs/breadcrumbs' ) ?>

        <div class="el-recipes-single__content">
          <div class="row">
            <div class="col-lg-3">
              <?php if ( has_post_thumbnail() ) : ?>
                <a class="el-recipes-single__image" href="<?= get_the_post_thumbnail_url( $post, 'full' ) ?>">
                  <img loading="lazy" src="<?= get_the_post_thumbnail_url( $post, 'medium_large' ) ?>" alt="<?= esc_attr( get_the_title() ) ?>">
                </a>
              <?php endif; ?>

              <?php if ( ! empty( $ingredients = get_field( 'ingredients' ) ) ) : ?>
                <div class="el-recipes-single__ingredients">
                  <?
                  get_template_part( 'template-parts/section-heading/section-heading', null, [
                    'heading' => 'INGREDIENTS',
                  ] );
                  ?>

                  <ul class="list-unstyled">
                    <?php foreach ( $ingredients as $item ) : ?>
                      <li><?= wp_strip_all_tags( $item['ingredient'] ) ?></li>
                    <?php endforeach; ?>
                  </ul>
                </div>
              <?php endif; ?>
            </div>

            <div class="col-lg-9 ps-lg-5">
              <h1><? the_title() ?></h1>

              <?php
              $intro = get_field( 'intro' );
              $tag   = get_field( 'tag' );
              if ( ! empty( $intro ) || ! empty( $tag ) ) : ?>
                <div class="el-recipes-single__brief">
                  <ul class="list-inline">
                    <?php if ( ! empty( $intro ) ) : ?>
                      <?php foreach ( $intro as $item ) : ?>
                        <li class="list-inline-item"><?= wp_strip_all_tags( $item['label'] ) ?>: <strong><?= wp_strip_all_tags( $item['detail'] ) ?></strong></li>
                      <?php endforeach; ?>
                    <?php endif; ?>

                    <?php if ( ! empty( $tag ) ) : ?>
                      <li class="list-inline-item">
                        <span class="el-recipes-single__tag"><?= $tag ?></span>
                      </li>
                    <?php endif; ?>
                  </ul>
                </div>
              <?php endif; ?>

              <?php if ( ! empty( $steps = get_field( 'steps' ) ) ) : ?>
                <?
                get_template_part( 'template-parts/section-heading/section-heading', null, [
                  'heading' => count( $steps ) . '-STEP RECIPE',
                ] );
                ?>

                <div class="el-recipes-single__steps">
                  <?php foreach ( $steps as $index => $step ) : ?>
                    <div class="el-recipes-single__step">
                      <p class="el-recipes-single__step-name"><strong>Step <?= $index + 1 ?></strong></p>
                      <?= $step['content'] ?>
                    </div>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>

              <div class="d-flex justify-content-lg-end">
                <?php if ( ! empty( $pdf_download = get_field( 'pdf_download' ) ) ) : ?>
                  <a href="<?= $pdf_download['url'] ?>" class="el-recipes-single__download-link" target="_blank" download>Download Recipe</a>
                <?php endif; ?>

                <? get_template_part( 'template-parts/share/share' ) ?>
              </div>
            </div>
          </div>
        </div>

        <?php if ( ! empty( $related_recipes = $MIT_helpers->get_related_posts( get_the_ID(), 5, 'cpt-recipe' ) ) ) : ?>
          <div class="el-recipes-single__recommends">
            <?
            get_template_part( 'template-parts/section-heading/section-heading', null, [
              'heading' => 'SUGGESTED FOR YOU',
            ] );
            ?>
            <div class="row row-cols-2 row-cols-xl-4 row-cols-xxl-5">
              <?php foreach ( $related_recipes as $related_recipe ) : ?>
                <?php
                $post = $related_recipe;
                setup_postdata( $post );

                // Use ALM template in case this grid is converted to ALM
                get_template_part( 'alm_templates/default' );
                ?>
              <?php endforeach; ?>
              <? wp_reset_postdata(); ?>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>
    <?php
  }
}
