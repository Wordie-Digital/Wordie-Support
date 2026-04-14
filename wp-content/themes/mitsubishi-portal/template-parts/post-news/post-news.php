<?php

defined( 'ABSPATH' ) or exit;

global $post;

$taxonomy  = $args['taxonomy'] ?? POR_Core::instance()->helpers->get_post_type_primary_tax( $post->post_type );
$last_term = POR_Core::instance()->helpers->get_last_term( $post->ID, $taxonomy );
?>
<div class="por-post-news wow animate__fadeIn">
  <div class="por-post-news__content">
    <div class="por-post-news__meta d-flex flex-nowrap justify-content-between">
      <?php if ( ! empty( $last_term ) ) : ?>
        <p><?= $last_term->name ?></p>
      <?php endif; ?>

      <p><? POR_Core::instance()->helpers->the_posted_on_date(); ?></p>
    </div>

    <h3 class="por-post-news__heading"><a href="<? the_permalink(); ?>"><? the_title() ?></a></h3>
    <div class="por-post-news__excerpt"><? the_excerpt(); ?></div>

    <? get_template_part( 'template-parts/share/share' ) ?>

    <a href="<? the_permalink(); ?>" class="btn btn--default btn-fullwidth">Read More</a>
  </div>
</div>
