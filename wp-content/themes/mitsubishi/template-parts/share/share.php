<?php

defined( 'ABSPATH' ) or exit;

$permalink = $args['url'] ?? get_permalink();

if ( $permalink ) : ?>
  <div class="mit-post-share">
    <?= do_shortcode( '[addtoany url="' . $permalink . '"]' ); ?>
  </div>
<?php endif;
