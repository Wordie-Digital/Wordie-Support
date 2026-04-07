<?php

defined( 'ABSPATH' ) or exit;

global $post;

$taxonomy  = $args['taxonomy'] ?? MIT_Core::instance()->helpers->get_post_type_primary_tax( $post->post_type );
$last_term = MIT_Core::instance()->helpers->get_last_term( $post->ID, $taxonomy );
$file      = get_field( 'file' );
?>
<div class="mit-post-download wow animate__fadeIn">
  <?php if ( ( is_page( 'brochures' ) || is_page( 'case-studies' ) ) && has_post_thumbnail() ) : /* Show images on generic page only, not for products */
    $featured_image_id = get_post_thumbnail_id();
    $featured_image_size = wp_get_attachment_image_src( $featured_image_id, 'full' );
    $featured_image_width = $featured_image_size[1];
    $featured_image_height = $featured_image_size[2];
    ?>
    <div class="mit-post-download__image mit-post-download__image--<?= $featured_image_width > $featured_image_height ? 'landscape' : 'portrait' ?>">
      <img loading="lazy" src="<?= get_the_post_thumbnail_url( $post, 'medium_large' ) ?>" alt="<?= esc_attr( get_the_title() ) ?>">
    </div>
  <?php endif; ?>

  <div class="balance-elements mb-3">
    <p class="mit-post-download__meta mb-1">
      <?php if ( ! empty( $term_list = get_the_term_list( $post->ID, 'post_tag', '', ', ' ) ) ) : ?>
        <?= strip_tags( $term_list ) ?>
      <?php else: ?>
        <?php if ( ! empty( $last_term ) ) : ?>
          <?= $last_term->name ?>
        <?php else: ?>
          &nbsp;
        <?php endif; ?>
      <?php endif; ?>
    </p>

    <?php if ( ! empty( $file ) ) : ?>
      <h4 class="mit-post-download__heading fw-bold"><a href="<?= $file['url'] ?>"><? the_title() ?></a></h4>
    <?php else: ?>
      <h4 class="mit-post-download__heading fw-bold"><? the_title() ?></h4>
    <?php endif; ?>

    <?php if ( ! empty( $post->post_excerpt ) ) : ?>
      <div class="mit-post-download__excerpt">
        <? the_excerpt(); ?>
      </div>
    <?php endif; ?>
  </div>

  <? get_template_part( 'template-parts/share/share', null, [
    'url' => ! empty( $file ) ? $file['url'] : false,
  ] ) ?>

  <?php if ( ! empty( $file ) ) : ?>
    <a href="<?= $file['url'] ?>" target="_blank" class="btn btn--default btn-fullwidth">Download</a>
  <?php endif; ?>
</div>
