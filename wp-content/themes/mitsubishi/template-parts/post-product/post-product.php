<?php

defined( 'ABSPATH' ) or exit;

global $post;

$MIT_helpers = MIT_Core::instance()->helpers;
$last_term   = $MIT_helpers->get_last_term( $post->ID, $MIT_helpers->get_post_type_primary_tax( $post->post_type ) );
$external_link = get_field('external_link', $post->ID);
?>
<div class="mit-post-product wow animate__fadeIn">
  <?php /*<a href="<? the_permalink(); ?>" class="mit-post-product__image">*/ ?>
  <a href="<?php echo esc_url( !empty( $external_link ) ? $external_link : get_the_permalink() ); ?>" <?php echo !empty( $external_link ) ? 'target="_blank"' : ''; ?> class="mit-post-product__image">
  <?php echo AWL_Label_Display::instance()->show_label( 'on_image' ); ?>
  <?php echo AWL_Label_Display::instance()->show_label( 'before_title' ); ?>
    <?php if ( has_post_thumbnail() ) : ?>
      <img loading="lazy" src="<?= get_the_post_thumbnail_url( $post, 'medium_large' ) ?>" alt="<?= esc_attr( get_the_title() ) ?>">
    <?php else: ?>
      <img loading="lazy" src="<?= $MIT_helpers->get_assets_path( 'images/placeholder.png' ) ?>" alt="<?= esc_attr( get_the_title() ) ?>">
    <?php endif; ?>
  </a>

  <div class="mit-post-product__content">
    <div class="balance-elements">
      <? if ( has_term( 'discontinued', 'product_tag', $post->ID ) ) : ?>
        <p class="el-products-single__discontinued"><? _e( MIT_DISCONTINUED_TEXT, 'woocommerce' ); ?></p>
      <? endif; ?>

      <div class="mit-post-product__meta d-flex flex-nowrap justify-content-between">
        <?php if ( ! empty( $last_term ) ) : ?>
          <p><a href="<?php echo esc_url( !empty( $external_link ) ? $external_link : get_the_permalink() ); ?>" <?php echo !empty( $external_link ) ? 'target="_blank"' : ''; ?>><?= $last_term->name ?></a></p>
        <?php endif; ?>
      </div>

      <h3 class="mit-post-product__heading"><a href="<?php echo esc_url( !empty( $external_link ) ? $external_link : get_the_permalink() ); ?>" <?php echo !empty( $external_link ) ? 'target="_blank"' : ''; ?>><? the_title() ?></a></h3>
      <? if ( ! empty( $subheading = get_field( 'subheading' ) ) ) : ?>
        <p class="mit-post-product__info-subheading"><?= $subheading ?></p>
      <? endif; ?>

      <div class="mit-post-product__excerpt">
        <? the_excerpt(); ?>
      </div>
    </div>

    <a href="<?php echo esc_url( !empty( $external_link ) ? $external_link : get_the_permalink() ); ?>" <?php echo !empty( $external_link ) ? 'target="_blank"' : ''; ?> class="btn btn--default btn-fullwidth">Read More</a>
    <?php /*<a href="<? the_permalink(); ?>" class="btn btn--default btn-fullwidth">Read More</a>*/ ?>
  </div>
</div>
