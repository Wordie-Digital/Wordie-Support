<?php

defined( 'ABSPATH' ) or exit;

$meta_left  = $args['meta_left'] ?? '';
$meta_right = $args['meta_right'] ?? '';

$layout        = $args['layout'] ?? 'image-left';
$position_text = 'image-left' == $layout ? 'end' : 'start';

$image_url   = $args['image_url'] ?? MIT_Core::instance()->helpers->get_assets_path( 'images/placeholder.png' );
$image_alt   = $args['image_alt'] ?? '';
$content     = $args['content'] ?? '';
$img_loading = ! empty( $args['is_lcp'] ) ? 'eager' : 'lazy';
$img_fetch   = ! empty( $args['is_lcp'] ) ? ' fetchpriority="high"' : '';

$button_link  = $args['button_link'] ?? '';
$button_label = $args['button_label'] ?? 'Read More';

$button_link_2  = $args['button_link_2'] ?? '';
$button_label_2 = $args['button_label_2'] ?? 'Read More';
?>
<div class="mit-text-image-v1">
  <?php if ( empty( $content ) && ! empty( $button_link ) ) : ?>
    <a class="mit-text-image-v1__bg d-block" href="<?= esc_url( $button_link ) ?>">
      <img loading="<?= $img_loading ?>"<?= $img_fetch ?> src="<?= esc_url( $image_url ) ?>" alt="<?= esc_attr( $image_alt ) ?>">
    </a>
  <?php else: ?>
    <div class="mit-text-image-v1__bg">
      <img loading="<?= $img_loading ?>"<?= $img_fetch ?> src="<?= esc_url( $image_url ) ?>" alt="<?= esc_attr( $image_alt ) ?>">
    </div>
  <?php endif; ?>

  <div class="mit-text-image-v1__content">
    <div class="container d-flex align-items-center flex-nowrap justify-content-<?= $position_text ?>">
      <?php if ( ! empty( $content ) ) : ?>
        <div
          class="mit-text-image-v1__content-box wow animate__fadeInUp"
          data-swiper-parallax="25%"
        >
          <?php if ( ! empty( $meta_left ) || ! empty( $meta_right ) ) : ?>
            <div class="mit-text-image-v1__meta d-flex flex-nowrap justify-content-between">
              <?php if ( ! empty( $meta_left ) ) : ?>
                <p><?= $meta_left ?></p>
              <?php endif; ?>

              <?php if ( ! empty( $meta_right ) ) : ?>
                <p class="ms-auto"><?= $meta_right ?></p>
              <?php endif; ?>
            </div>
          <?php endif; ?>

          <div class="mit-text-image-v1__inner-content"><?= wpautop( $content ); ?></div>

          <?php if ( ! empty( $button_link ) ) : ?>
            <a href="<?= esc_url( $button_link ) ?>" class="btn btn--default"><?= $button_label ?></a>
          <?php endif; ?>

          <?php if ( ! empty( $button_link_2 ) ) : ?>
            <a href="<?= esc_url( $button_link_2 ) ?>" class="btn btn--default"><?= $button_label_2 ?></a>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>
