<?php

defined( 'ABSPATH' ) or exit;

$layout         = $args['layout'] ?? 'image-right';
$position_image = 'image-left' == $layout ? 'start' : 'end';
$position_text  = 'image-right' == $layout ? 'start' : 'end';

$image_url = $args['image_url'] ?? MIT_Core::instance()->helpers->get_assets_path( 'images/placeholder.png' );
$image_alt = $args['image_alt'] ?? '';
$content   = $args['content'] ?? '';

$button_link  = $args['button_link'] ?? '';
$button_label = $args['button_label'] ?? 'Read More';

$button_link_2  = $args['button_link_2'] ?? '';
$button_label_2 = $args['button_label_2'] ?? 'Read More';
?>
<div class="mit-text-image-v2">
  <? if ( ! empty( $image_url ) ) : ?>
    <div class="mit-text-image-v2__image overflow-hidden">
      <div class="row justify-content-<?= $position_image ?>">
        <div class="col-lg-9">
          <div class="mit-text-image-v2__image-wrapper">
            <img loading="lazy" src="<?= esc_url( $image_url ) ?>" alt="<?= $image_alt ?>"/>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <?php if ( ! empty( $content ) ) : ?>
    <div class="mit-text-image-v2__text overflow-hidden">
      <div class="row align-items-center h-100 justify-content-<?= $position_text ?>">
        <div class="col-lg-6">
          <div
            class="mit-text-image-v2__text-box d-flex flex-nowrap align-items-center wow animate__fadeInUp"
            data-swiper-parallax="100%"
          >
            <div class="mit-text-image-v2__text-box-inner">
              <div class="mb-4">
                <?= wpautop( $content ) ?>
              </div>

              <?php if ( ! empty( $button_link ) ) : ?>
                <a href="<?= esc_url( $button_link ) ?>" class="btn btn--default"><?= $button_label ?></a>
              <?php endif; ?>

              <?php if ( ! empty( $button_link_2 ) ) : ?>
                <a href="<?= esc_url( $button_link_2 ) ?>" class="btn btn--default"><?= $button_label_2 ?></a>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>
</div>
