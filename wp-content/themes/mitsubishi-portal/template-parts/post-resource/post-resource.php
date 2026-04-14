<?php

defined( 'ABSPATH' ) or exit;

global $post;

$POR_helpers = POR_Core::instance()->helpers;
$file        = get_field( 'file' );

?>
<div class="por-post-resource por-post-resource--cards wow animate__fadeIn">
  <div class="por-post-resource__image">
    <?php if ( has_post_thumbnail() ) : ?>
      <img loading="lazy" src="<?= get_the_post_thumbnail_url( $post, 'medium_large' ) ?>" alt="<?= esc_attr( get_the_title() ) ?>">
    <?php else: ?>
      <img loading="lazy" src="<?= $POR_helpers->get_assets_path( 'images/placeholder.png' ) ?>" alt="<?= esc_attr( get_the_title() ) ?>">
    <?php endif; ?>
  </div>

  <div class="por-post-resource__content">
    <div class="balance-elements">
      <p class="por-post-resource__meta mb-3">
        <?php if ( ! empty( $file ) ) : ?>
          <?= POR_Core::instance()->helpers->get_size_as_kb( $file['filesize'] ) ?>
        <?php endif; ?>
      </p>

      <?php if ( ! empty( $file ) ) : ?>
        <h3 class="por-post-resource__heading"><a href="<?= $file['url'] ?>"><? the_title() ?></a></h3>
      <?php else: ?>
        <h3 class="por-post-resource__heading"><? the_title() ?></h3>
      <?php endif; ?>

      <?php if ( ! empty( $post->post_excerpt ) ) : ?>
        <div class="por-post-resource__excerpt">
          <? the_excerpt(); ?>
        </div>
      <?php endif; ?>
    </div>

    <?php if ( ! empty( $file ) ) : ?>
      <a href="<?= $file['url'] ?>" target="_blank" class="btn btn--default btn-fullwidth">Download</a>
    <?php endif; ?>
  </div>
</div>
