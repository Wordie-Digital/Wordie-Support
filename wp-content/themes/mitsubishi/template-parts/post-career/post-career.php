<?php

defined( 'ABSPATH' ) or exit;

global $post;

$MIT_helpers = MIT_Core::instance()->helpers;
?>
<div class="mit-post-career wow animate__fadeIn">
  <div class="mit-post-career__content mb-3 balance-elements">
    <h3 class="mit-post-career__heading"><a href="<? the_permalink(); ?>"><? the_title() ?></a></h3>
    <div class="mit-post-career__excerpt"><? the_excerpt(); ?></div>

    <?php if ( ! empty( $subheading = get_field( 'subheading' ) ) ) : ?>
      <p class="text-uppercase my-2"><strong><?= $subheading ?></strong></p>
    <?php endif; ?>

    <p class="mt-2">Posted: <? $MIT_helpers->the_posted_on_date(); ?></p>
  </div>

  <a href="<? the_permalink(); ?>" class="btn btn--default btn-fullwidth">Read More</a>
</div>
