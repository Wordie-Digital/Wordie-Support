<div class="mit-section-strip">
  <div class="mit-section-strip__header py-3">
    <div class="container">
      <div class="row">
        <div class="col-xl-3">
          <? get_template_part( 'template-parts/breadcrumbs/breadcrumbs' ) ?>

          <h1 class="wow animate__fadeInUp mt-4"><?= MIT_Core::instance()->helpers->get_page_title() ?></h1>
        </div>
      </div>
    </div>
  </div>

  <div class="mit-section-strip__content">
    <div class="container">
      <div class="row">
        <div class="col-xl-3">
          <div class="mit-section-strip__sidebar wow animate__fadeInUp">
            <?= force_balance_tags( do_shortcode( get_field( 'sidebar_content', get_the_ID(), false ) ) ) ?>
          </div>
        </div>

        <div class="col-xl-9 px-0">
          <div class="mit-section-strip__inner-content wow animate__fadeInUp">
            <? the_content(); ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
