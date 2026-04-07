<?php

defined( 'ABSPATH' ) or exit;

global $post;

$POR_helpers = POR_Core::instance()->helpers;
$target      = $args['target'] ?? '';
$last_term   = $POR_helpers->get_last_term( $post->ID, $POR_helpers->get_post_type_primary_tax( $post->post_type ) );
?>
<div class="por-post-generic-large wow animate__fadeIn">
  <a target="<?= esc_attr( $target ) ?>" href="<? the_permalink(); ?>" class="por-post-generic-large__image">
    <?php if ( has_post_thumbnail() ) : ?>
      <img loading="lazy" src="<?= get_the_post_thumbnail_url( $post, 'medium_large' ) ?>" alt="<?= esc_attr( get_the_title() ) ?>">
    <?php else: ?>
      <img loading="lazy" src="<?= $POR_helpers->get_assets_path( 'images/placeholder.png' ) ?>" alt="<?= esc_attr( get_the_title() ) ?>">
    <?php endif; ?>
  </a>

  <div class="por-post-generic-large__content">
    <div class="por-post-generic-large__meta d-flex flex-nowrap justify-content-between">
      <?php if ( ! empty( $last_term ) ) : ?>
        <p><?= $last_term->name ?></p>
      <?php endif; ?>

      <p><? $POR_helpers->the_posted_on_date(); ?></p>
    </div>

    <h3 class="por-post-generic-large__heading"><a target="<?= esc_attr( $target ) ?>" href="<? the_permalink(); ?>"><? the_title() ?></a></h3>
    <? the_excerpt(); ?>
  </div>
</div>
