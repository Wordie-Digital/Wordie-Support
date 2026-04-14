<?php

defined( 'ABSPATH' ) or exit;

global $post;

$taxonomy  = $args['taxonomy'] ?? MIT_Core::instance()->helpers->get_post_type_primary_tax( $post->post_type );
$last_term = MIT_Core::instance()->helpers->get_last_term( $post->ID, $taxonomy );
?>
<div class="mit-post-news wow animate__fadeIn">
  <div class="mit-post-news__content">
    <div class="balance-elements mb-3">
      <div class="mit-post-news__meta d-flex flex-nowrap justify-content-between">
        <?php if ( ! empty( $last_term ) ) : ?>
          <p><strong><?= $last_term->name ?></strong></p>
        <?php endif; ?>

        <p><? MIT_Core::instance()->helpers->the_posted_on_date(); ?></p>
      </div>

      <h3 class="mit-post-news__heading"><a href="<? the_permalink(); ?>"><? the_title() ?></a></h3>
      <div class="mit-post-news__excerpt"><? the_excerpt(); ?></div>
    </div>

    <? get_template_part( 'template-parts/share/share' ) ?>

    <a href="<? the_permalink(); ?>" class="btn btn--default btn-fullwidth">Read More</a>
  </div>
</div>
