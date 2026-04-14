<?php

defined( 'ABSPATH' ) or exit;
$store_id = $args['store_id'] ?? '';

if ( $store_id ) : ?>
  <?php
  add_filter( 'do_shortcode_tag', function ( $output, $tag, $attr ) use ( $store_id ) {
    if ( 'mit_dynamic_store_info' != $tag ) {
      return $output;
    }

    return str_replace( '<div class="wpsl-contact-details">', '<div class="generic-content">' . wpautop( get_post( $store_id )->post_content ) . '</div><div class="wpsl-contact-details">', $output );
  }, 10, 3 );
  ?>
  <div class="mit-store-info">
    <?php if ( ! empty( $stores_locator_page = get_field( 'stores_locator_page', 'options' ) ) ) : ?>
      <p class="mb-1">CHOSEN STOCKIST</p>
      <?= do_shortcode( '[wpsl_address id="' . $store_id . '" name="true" address="true" address2="true" city="true" state="false" zip="true" country="false" phone="true" fax="true" email="true" url="true"]' ) ?>

      <p class="mit-store-info__contact-other"><a class="color-red text-uppercase" href="<?= get_permalink( $stores_locator_page ) ?>"><strong>Contact Other Stockist</strong></a></p>
    <?php endif; ?>

    <script>
      jQuery(document).ready(function($) {
        jQuery(window).on('load', function() {
          setTimeout(function() {
            $('#MktoPersonNotes').val('\n\n----------\nChosen stockist: <?=wp_strip_all_tags( get_the_title( $store_id ) )?>');
          }, 1000);
        });
      });
    </script>
  </div>
<?php endif;
