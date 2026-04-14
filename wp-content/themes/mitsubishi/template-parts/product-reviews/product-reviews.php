<?php

defined( 'ABSPATH' ) or exit;

global $mit_product_review_scripts_loaded; // Tracking to make sure ProductReviews script is only loaded once

$brand_id   = $args['brand_id'] ?? '61516ba7-7c94-3bcc-ad2a-543ab4aa96da';
$type       = $args['type'] ?? 'carousel';
$identifier = $args['identifier'] ?? '';

$uid = uniqid( 'mit-product-reviews-' );
?>
<div class="mit-product-reviews">
  <?php if ( empty( $mit_product_review_scripts_loaded ) ) : $mit_product_review_scripts_loaded = 1; ?>
    <script>window.__productReviewSettings = {brandId: '<?=$brand_id?>'};</script>
    <script src="https://cdn.productreview.com.au/assets/widgets/loader.js" async></script>
  <?php endif; ?>

  <?
  switch ( $type ) {
  case 'inline':
  if ( ! empty( $identifier ) ) : ?>
    <div data-id="<?= esc_attr( $identifier ) ?>" class="pr-inline-rating">&nbsp;</div>
    <script>
      window.__productReviewCallbackQueue = window.__productReviewCallbackQueue || [];
      window.__productReviewCallbackQueue.push(function(ProductReview) {
        ProductReview.use('inline-rating', {
          'alias': 'me-product-page-info',
          'identificationStrategy': 'from-internal-entry-id'
        });
      });
    </script>
  <? endif; ?>
  <? break;

  case 'horizontal':
  if ( ! empty( $identifier ) ) : ?>
    <div id="<?= $uid ?>__pr-reviews-horizontal-widget"></div>

    <script>
      window.__productReviewCallbackQueue = window.__productReviewCallbackQueue || [];
      window.__productReviewCallbackQueue.push(function(ProductReview) {
        ProductReview.use('reviews-horizontal', {
          'identificationDetails': {
            'type': 'single',
            'strategy': 'from-internal-entry-id',
            'identifier': '<?=$identifier?>'
          },
          'container': '#<?=$uid?>__pr-reviews-horizontal-widget',
          'alias': 'me-product-page-tab'
        });
      });
    </script>
  <? endif; ?>
  <? break;

  default:
  if ( ! empty( $identifier ) ) : ?>
    <div id="<?= $uid ?>__pr-reviews-carousel-widget"></div>

    <script>
      window.__productReviewCallbackQueue = window.__productReviewCallbackQueue || [];
      window.__productReviewCallbackQueue.push(function(ProductReview) {
        ProductReview.use('reviews-carousel', {
          'identificationDetails': {
            'type': 'single',
            'strategy': 'from-internal-entry-id',
            'identifier': '<?=$identifier?>'
          },
          'container': '#<?=$uid?>__pr-reviews-carousel-widget',
          'alias': 'me-product-page'
        });
      });
    </script>
  <? endif; ?>
  <? } ?>
</div>
