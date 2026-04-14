<div class="por-page-header">
  <div class="card flex-fill">
    <div class="card-body">
      <h1><?= POR_Core::instance()->helpers->get_page_title() ?></h1>

      <? get_template_part( 'template-parts/breadcrumbs/breadcrumbs' ) ?>

      <?php if ( ! empty( $post->post_excerpt ) ) : ?>
        <div class="por-page-header__excerpt">
          <? the_excerpt(); ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>
