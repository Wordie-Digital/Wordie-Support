<?php

defined( 'ABSPATH' ) or exit;

global $post;

$MIT_helpers = MIT_Core::instance()->helpers;
$last_term   = $MIT_helpers->get_last_term( $post->ID, $MIT_helpers->get_post_type_primary_tax( $post->post_type ) );
?>
<div class="post-product-professional wow animate__fadeIn">
  <?
  get_template_part( 'template-parts/section-heading/section-heading', null, [
    'heading'       => get_the_title(),
    'right_content' => get_field( 'subheading' ),
  ] );
  ?>

  <div class="row">
    <div class="col-xl-5">
      <div class="post-product-professional__image">
        <?php if ( has_post_thumbnail() ) : ?>
          <img loading="lazy" class="img-fluid" src="<?= get_the_post_thumbnail_url( $post, 'medium_large' ) ?>" alt="<?= esc_attr( get_the_title() ) ?>">
        <?php else: ?>
          <img loading="lazy" class="img-fluid" src="<?= $MIT_helpers->get_assets_path( 'images/placeholder.png' ) ?>" alt="<?= esc_attr( get_the_title() ) ?>">
        <?php endif; ?>
      </div>
    </div>

    <div class="col-xl-7 order-xl-first">
      <div class="mit-post-product-professional__content">
        <?php if ( empty( $other_features = get_field( 'other_features' ) ) ) : ?>
          <? the_excerpt(); ?>
        <?php else: ?>
          <div class="row">
            <div class="col-xl-6">
              <? the_excerpt(); ?>
            </div>

            <div class="col-xl-6">
              <?= wpautop( $other_features ); ?>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
