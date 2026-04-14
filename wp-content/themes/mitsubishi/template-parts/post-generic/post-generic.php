<?php

defined( 'ABSPATH' ) or exit;

global $post;

$MIT_helpers = MIT_Core::instance()->helpers;
$last_term   = $MIT_helpers->get_last_term( $post->ID, $MIT_helpers->get_post_type_primary_tax( $post->post_type ) );
?>
<div class="mit-post-generic wow animate__fadeIn">
  <a href="<? the_permalink(); ?>" class="mit-post-generic__image">
    <?php if ( has_post_thumbnail() ) : ?>
      <img loading="lazy" src="<?= get_the_post_thumbnail_url( $post, 'medium_large' ) ?>" alt="<?= esc_attr( get_the_title() ) ?>">
    <?php else: ?>
      <img loading="lazy" src="<?= $MIT_helpers->get_assets_path( 'images/placeholder.png' ) ?>" alt="<?= esc_attr( get_the_title() ) ?>">
    <?php endif; ?>
  </a>

  <div class="mit-post-generic__content balance-elements">
    <div class="mit-post-generic__meta d-lg-flex flex-nowrap justify-content-between mb-3">
      <?php if ( ! empty( $last_term ) ) : ?>
        <p class="mb-0"><?= $last_term->name ?></p>
      <?php endif; ?>

      <p class="mb-0"><? $MIT_helpers->the_posted_on_date(); ?></p>
    </div>

    <h4 class="mit-post-generic__heading"><a href="<? the_permalink(); ?>"><? the_title() ?></a></h4>
    <? the_excerpt(); ?>
  </div>
</div>
