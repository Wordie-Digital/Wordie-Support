<?php

defined( 'ABSPATH' ) or exit;

$MIT_helpers = POR_Core::instance()->helpers;

$image_url    = $args['image_url'] ?? POR_Core::instance()->helpers->get_assets_path( 'images/placeholder.png' );
$image_alt    = $args['image_alt'] ?? '';
$content      = $args['content'] ?? '';
$is_landscape = $args['is_landscape'] ?? false;
$link         = $args['link'] ?? '#';

if ( ! empty( $link ) ) : ?>
  <a class="por-card wow animate__fadeIn" href="<?= esc_url( $link ) ?>">
    <div class="por-card__image <?= $is_landscape ? 'por-card__image--landscape' : '' ?>">
      <img loading="lazy" src="<?= esc_url( $image_url ) ?>" alt="<?= esc_attr( $image_alt ) ?>">
    </div>

    <?php if ( ! empty( $content ) ) : ?>
      <div class="por-card__content-box">
        <div class="por-card__content balance-elements"><?= wpautop( preg_replace( "/<\/?a(.|\\s)*?>/", '', $content ) ); ?></div>
      </div>
    <?php endif; ?>
  </a>
<?php endif;
