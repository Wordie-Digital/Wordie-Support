<?php

defined( 'ABSPATH' ) or exit;

global $post;

$MIT_helpers = MIT_Core::instance()->helpers;
$last_term   = $MIT_helpers->get_last_term( $post->ID, $MIT_helpers->get_post_type_primary_tax( $post->post_type ) );
?>
<div class="mit-post-recipe wow animate__fadeIn">
  <a href="<? the_permalink(); ?>">
    <div class="mit-post-recipe__image">
      <?php if ( ! empty( $tag = get_field( 'tag' ) ) ) : ?>
        <div class="mit-post-recipe__tag"><?= force_balance_tags( $tag ) ?></div>
      <?php endif; ?>

      <?php if ( has_post_thumbnail() ) : ?>
        <img loading="lazy" src="<?= get_the_post_thumbnail_url( $post, 'medium_large' ) ?>" alt="<?= esc_attr( get_the_title() ) ?>">
      <?php else: ?>
        <img loading="lazy" src="<?= $MIT_helpers->get_assets_path( 'images/placeholder.png' ) ?>" alt="<?= esc_attr( get_the_title() ) ?>">
      <?php endif; ?>
    </div>

    <h5 class="mit-post-recipe__heading balance-elements"><? the_title() ?></h5>
  </a>
</div>
