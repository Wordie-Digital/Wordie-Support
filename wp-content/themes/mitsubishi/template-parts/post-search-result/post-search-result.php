<?php

defined( 'ABSPATH' ) or exit;

global $post;

$MIT_helpers = MIT_Core::instance()->helpers;
?>
<a class="mit-post-search-result wow animate__fadeIn d-block" href="<? the_permalink(); ?>">
  <div class="mit-post-search-result__content balance-elements">
    <h4 class="mit-post-search-result__heading fw-bold"><? the_title() ?></h4>
    <? the_excerpt(); ?>
  </div>
</a>
