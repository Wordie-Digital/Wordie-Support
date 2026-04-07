<?php

defined( 'ABSPATH' ) or exit;

global $post;

$MIT_helpers = MIT_Core::instance()->helpers;
$taxonomy    = $args['taxonomy'] ?? MIT_Core::instance()->helpers->get_post_type_primary_tax( $post->post_type );
$last_term   = $MIT_helpers->get_last_term( $post->ID, $taxonomy );
$video_url   = get_field( 'video_url' );
?>
<div class="mit-post-video wow animate__fadeIn">
  <div class="mit-post-video__content">
    <?php
    if ( ! empty( $video_url ) ) {
      echo $MIT_helpers->get_video_iframe( $video_url );
    }
    ?>

    <p class="mit-post-video__meta">
      <?php if ( ! empty( $last_term ) ) : ?>
        <?= $last_term->name ?>
      <?php else: ?>
        &nbsp;
      <?php endif; ?>
    </p>

    <div class="mit-post-video__excerpt mb-3">
      <h3><? the_title() ?></h3>

      <? the_content(); ?>
    </div>

    <? get_template_part( 'template-parts/share/share', null, [
      'url' => ! empty( $video_url ) ? $video_url : false,
    ] ) ?>
  </div>
</div>
