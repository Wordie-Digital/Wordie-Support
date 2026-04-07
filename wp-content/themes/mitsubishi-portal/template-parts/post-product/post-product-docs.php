<?php

defined( 'ABSPATH' ) or exit;

$hide_back_link = $args['hide_back_link'] ?? false;
$hide_heading   = $args['hide_heading'] ?? false;

global $post;

if ( ! $hide_back_link ) : ?>
  <a href="#" class="por-post-product__close-docs"><i class="align-middle" data-feather="chevron-left"></i> Back</a>
<?php endif; ?>

<?php if ( ! $hide_heading ) : ?>
  <h3 class="mt-4 mb-3 h4"><? the_title() ?></h3>
<?php endif; ?>

<?php
// Show the woocommerce product description
$_product = wc_get_product( $post->ID );
if ( $_product->get_description() ) : ?>
  <div class="por-post-product__description mb-4">
    <?= wpautop( $_product->get_description() ) ?>

    <?
    // Show the woocommerce "product short description"
    if ( $_product->get_short_description() ) : ?>
      <?= wpautop( $_product->get_short_description() ) ?>
    <? endif; ?>
  </div>
<?php endif; ?>

<div class="por-post-product__docs-list">
  <?
  $doc_posts = get_posts( array(
    'post_type'      => 'cpt-technical-doc',
    'post_status'    => 'publish',
    'posts_per_page' => - 1,
    'meta_query'     => [
      [
        'key'     => 'series_products',
        'compare' => 'LIKE',
        'value'   => '"' . get_the_ID() . '"',
      ],
    ],
  ) );

  if ( ! empty( $doc_posts ) ) : ?>
    <?
    $organised_doc_terms = [];

    // To maintain the terms order
    foreach ( get_terms( [ 'taxonomy' => 'doc_category' ] ) as $term ) {
      $organised_doc_terms[ $term->name ] = [];
    }

    foreach ( $doc_posts as $doc ) {
      $doc_terms = wp_get_post_terms( $doc->ID, 'doc_category' );

      foreach ( $doc_terms as $doc_term ) {
        if ( has_term( $doc_term->slug, 'doc_category', $doc->ID ) ) {
          $organised_doc_terms[ $doc_term->name ][] = $doc;
        } else {
          $organised_doc_terms['Others'][] = $doc;
        }
      }
    }

    foreach ( $organised_doc_terms as $term_name => $docs ) :
      if ( empty( $docs ) ) {
        continue;
      }
      ?>
      <div class="por-post-product__docs-group">
        <h4><?= $term_name ?></h4>

        <ul class="list-unstyled">
          <?php foreach ( $docs as $doc ) : ?>
            <?php if ( ! empty( $file = get_field( 'file', $doc->ID ) ) ) : ?>
              <?
              $post = $doc;
              setup_postdata( $post );
              ?>
              <li><? get_template_part( 'template-parts/download-line/download-line' ); ?></li>
            <?php endif; ?>
          <?php endforeach; ?>
          <? wp_reset_postdata(); ?>
        </ul>
      </div>
    <?php endforeach; ?>
  <? else: ?>
    <p>No docs available yet</p>
  <? endif; ?>
</div>
