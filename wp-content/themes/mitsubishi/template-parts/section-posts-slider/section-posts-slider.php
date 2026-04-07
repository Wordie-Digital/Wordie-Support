<?php

defined( 'ABSPATH' ) or exit;

global $post;

$uid         = uniqid( 'mit-section-posts-slider-' );
$MIT_helpers = MIT_Core::instance()->helpers;
$posts_list  = $args['posts_list'] ?? [];

if ( ! empty( $posts_list ) ) : ?>
  <div class="mit-section-posts-slider" id="<?= $uid ?>">
    <div class="swiper">
      <div class="swiper-wrapper">
        <? foreach ( $posts_list as $p ): ?>
          <div class="swiper-slide">
            <?php
            $post = $p;
            setup_postdata( $post );

            $last_term = $MIT_helpers->get_last_term( $post->ID, $MIT_helpers->get_post_type_primary_tax( $post->post_type ) );

            switch ( $post->post_type ) {
              case 'cpt-recipe':
                ob_start();
                ?>
                <p style="color: var(--e-global-color-secondary);" class="mb-2">New Recipes!</p>
                <h6 class="mb-3 fw-bold text-uppercase"><?= ( ! empty( $last_term ) ? $last_term->name : '' ) ?></h6>
                <? the_title( '<h3>', '</h3>' ) ?>
                <?= wpautop( get_the_excerpt() ) ?>
                <?
                get_template_part( 'template-parts/text-image-v1/text-image-v1', null, [
                  'image_url'    => has_post_thumbnail() ? get_the_post_thumbnail_url( $post, 'full' ) : $MIT_helpers->get_assets_path( 'images/placeholder.png' ),
                  'image_alt'    => get_the_title(),
                  'content'      => ob_get_clean(),
                  'button_link'  => get_the_permalink(),
                  'button_label' => 'Read Recipe',
                ] );
                break;

              default:
                get_template_part( 'template-parts/text-image-v1/text-image-v1', null, [
                  'image_url'   => has_post_thumbnail() ? get_the_post_thumbnail_url( $post, 'full' ) : $MIT_helpers->get_assets_path( 'images/placeholder.png' ),
                  'image_alt'   => get_the_title(),
                  'meta_left'   => ! empty( $last_term ) ? $last_term->name : '',
                  'meta_right'  => $MIT_helpers->get_posted_on_date_html(),
                  'content'     => the_title( '<h3>', '</h3>', false ) . wpautop( get_the_excerpt() ),
                  'button_link' => get_the_permalink(),
                ] );
            }
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
          parallax: true,
          loop: true,
          speed: 600,
          resizeObserver: true,
          observeParents: true,
          navigation: {
            nextEl: '#<?= $uid ?> .swiper-button-next',
            prevEl: '#<?= $uid ?> .swiper-button-prev',
          },
        });
      });
    </script>
  </div>
<?php endif;
