<?php

defined( 'ABSPATH' ) or exit;

$MIT_helpers = MIT_Core::instance()->helpers;

$image_url    = $args['image_url'] ?? MIT_Core::instance()->helpers->get_assets_path( 'images/placeholder.png' );
$image_alt    = $args['image_alt'] ?? '';
$content      = $args['content'] ?? '';
$button_link  = $args['button_link'] ?? '';
$button_label = $args['button_label'] ?? 'Read More';
$hide_button  = $args['hide_button'] ?? false;
?>
<div class="mit-card wow animate__fadeIn">
  <div class="mit-card__image">
    <? ob_start(); ?>
    <img loading="lazy" src="<?= esc_url( $image_url ) ?>" alt="<?= esc_attr( $image_alt ) ?>">
    <?= $MIT_helpers->get_maybe_wrapped_link( $button_link, ob_get_clean() ) ?>
  </div>

  <?php if ( ! empty( $content ) ) : ?>
    <div class="mit-card__content-box">
      <div class="mit-card__content balance-elements"><?= wpautop( $content ); ?></div>

      <?php if ( ! empty( $button_link ) && ! $hide_button ) : ?>
        <a href="<?= esc_url( $button_link ) ?>" class="btn btn--link"><?= $button_label ?></a>
      <?php endif; ?>
    </div>
  <?php endif; ?>
</div>
