<?

global $post;

if ( is_search() ) : ?>
  <div class="col mb-3 mb-lg-4 mb-xxl-5">
    <? get_template_part( 'template-parts/post-search-result/post-search-result' ) ?>
  </div>
  <?php return; ?>
<?php endif;

switch ( $post->post_type ) {
  case 'cpt-news':
    ?>
    <div class="col mb-3 mb-lg-4 mb-xxl-5">
      <? get_template_part( 'template-parts/post-news/post-news' ) ?>
    </div>
    <?php
    break;

  case 'cpt-career':
    ?>
    <div class="col mb-3 mb-lg-4 mb-xxl-5">
      <? get_template_part( 'template-parts/post-career/post-career' ) ?>
    </div>
    <?php
    break;

  case 'cpt-download':
    ?>
    <div class="col mb-3 mb-lg-4 mb-xxl-5">
      <? get_template_part( 'template-parts/post-download/post-download' ) ?>
    </div>
    <?php
    break;

  case 'cpt-video':
    ?>
    <div class="col mb-3 mb-lg-4 mb-xxl-5">
      <? get_template_part( 'template-parts/post-video/post-video' ) ?>
    </div>
    <?php
    break;

  case 'cpt-recipe':
    ?>
    <div class="col mb-3 mb-lg-4 mb-xxl-5">
      <? get_template_part( 'template-parts/post-recipe/post-recipe' ) ?>
    </div>
    <?php
    break;

  case 'product':
    $MIT_helpers = MIT_Core::instance()->helpers;

    if (
      ( isset( $_GET['post_id'] ) && $MIT_helpers->is_product_cat_belongs_to_industrial( $_GET['post_id'] ) ) || // For ALM load more
      ( is_product_category() && $MIT_helpers->is_product_cat_belongs_to_industrial( get_queried_object()->term_id ) ) // For main category query
    ) : ?>
      <div class="col mb-4 mb-lg-5 pb-lg-3">
        <? get_template_part( 'template-parts/post-product/post-product-professional' ) ?>
      </div>
    <?php else: ?>
      <div class="col mb-3 mb-lg-4 mb-xxl-5">
        <? get_template_part( 'template-parts/post-product/post-product' ) ?>
      </div>
    <?php endif;
    break;

  default:
    ?>
    <div class="col mb-3 mb-lg-4 mb-xxl-5">
      <? get_template_part( 'template-parts/post-generic/post-generic' ) ?>
    </div>
  <?php
}
