<?php

defined( 'ABSPATH' ) or exit;

global $post;

$MIT_helpers = POR_Core::instance()->helpers;
$last_term   = $MIT_helpers->get_last_term( $post->ID, $MIT_helpers->get_post_type_primary_tax( $post->post_type ) );
?>
<div class="por-post-product animated animate__fadeIn">
  <a href="<?= ( is_search() ? get_permalink() : '#' ) ?>" class="por-post-product__image por-post-product__open-docs" data-product-id="<?= get_the_ID() ?>">
    <?php if ( has_post_thumbnail() ) : ?>
      <img loading="lazy" src="<?= get_the_post_thumbnail_url( $post, 'medium_large' ) ?>" alt="<?= esc_attr( get_the_title() ) ?>">
    <?php else: ?>
      <img loading="lazy" src="<?= $MIT_helpers->get_assets_path( 'images/logo-au-white.svg' ) ?>" alt="<?= esc_attr( get_the_title() ) ?>" style="object-fit:none;background-color:#3e3e3e;">
    <?php endif; ?>
  </a>

  <div class="por-post-product__content">
    <div class="balance-elements">
      <?php if ( ! empty( $subheading = get_field( 'subheading' ) ) ) : ?>
        <p class="por-post-product__subheading"><?= $subheading ?></p>
      <?php endif; ?>

      <h3 class="por-post-product__heading"><a href="<?= ( is_search() ? get_permalink() : '#' ) ?>" class="por-post-product__open-docs" data-product-id="<?= get_the_ID() ?>"><? the_title() ?></a></h3>

      <?= wpautop( wc_get_product( $post->ID )->get_description() ) ?>
    </div>
  </div>
</div>
