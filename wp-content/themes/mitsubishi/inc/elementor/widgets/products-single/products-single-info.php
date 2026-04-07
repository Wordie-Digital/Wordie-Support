<?php

defined( 'ABSPATH' ) or exit;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

class Custom_El_Products_Single_Info extends Widget_Base {
  public function __construct( $data = [], $args = null ) {
    parent::__construct( $data, $args );
  }

  public function get_name() {
    return 'Custom_El_Products_Single_Info';
  }

  public function get_title() {
    return 'Product | Info';
  }

  public function get_icon() {
    return 'eicon-custom';
  }

  public function get_categories() {
    return [ 'custom_woo' ];
  }

  public function get_script_depends() {
    return [ 'theme-swiper-js', 'js-OverlayScrollbars' ];
  }

  public function get_style_depends() {
    return [ 'theme-swiper-css', 'css-OverlayScrollbars' ];
  }

  protected function register_controls() {
    $this->start_controls_section(
      'content_section',
      [
        'label' => 'Content',
        'tab'   => Controls_Manager::TAB_CONTENT,
      ]
    );

    $this->add_control(
      'message',
      [
        'label'     => __( 'Used on Products single page only', 'plugin-name' ),
        'type'      => \Elementor\Controls_Manager::HEADING,
        'separator' => 'after',
      ]
    );

    $this->add_control(
      'version',
      [
        'label'       => __( 'Product Style', 'plugin-domain' ),
        'type'        => \Elementor\Controls_Manager::SELECT2,
        'description' => "Commercial style is applicable in professional flow only",
        'multiple'    => false,
        'default'     => 'generic',
        'options'     => [
          'generic'    => 'Generic',
          'commercial' => 'Commercial',
        ],
      ]
    );

    $this->end_controls_section();
  }

  protected function render() {
    if ( ! is_product() ) {
      return;
    }

    global $post;

    $uid         = uniqid( 'el-products-single-info-' );
    $MIT_helpers = MIT_Core::instance()->helpers;
    $style       = $this->get_settings_for_display( 'version' ) ?: 'generic';
    $post_id     = get_the_ID();

    $is_discontinued = has_term( 'discontinued', 'product_tag', $post_id );
    $related_product = get_field( 'related_product', $post_id );

    if ( ! $MIT_helpers->is_flow_professional() ) {
      $style = 'generic';
    }

    ?>
    <div class="el-products-single__info" id="<?= $uid ?>">
      <div class="container">
        <? woocommerce_breadcrumb(); ?>

        <?php if ( ! empty( $promotion_banners = get_field( 'promotion_banners', 'options' ) ) ) : ?>
          <?php foreach ( $promotion_banners as $promotion_banner ) : ?>
            <?
            if ( ! $promotion_banner['enabled'] ) {
              continue;
            }

            $is_intersect = has_term( $promotion_banner['show_on_all_products_under'], 'product_cat', $post_id ) || $MIT_helpers->is_post_in_descendant_category( $promotion_banner['show_on_all_products_under'], $post_id, 'product_cat' );

            if ( $is_intersect ) : ?>
              <a href="<?= esc_url( $promotion_banner['link'] ) ?>" class="d-block mb-3" target="_blank">
                <img loading="lazy"
                     width="<?= esc_attr( $promotion_banner['banner']['width'] ) ?>"
                     height="<?= esc_attr( $promotion_banner['banner']['height'] ) ?>"
                     src="<?= $promotion_banner['banner']['url'] ?>"
                     alt="<?= esc_attr( $promotion_banner['banner']['alt'] ) ?>"
                     class="img-fluid"
                />
              </a>
            <? endif; ?>
          <?php endforeach; ?>
        <?php endif; ?>

        <?php 
          $select_promotion_banners = get_field('select_promotion_banner');
            if( $select_promotion_banners ) :
              foreach ( $select_promotion_banners as $select_promotion_banner ) :
                $thumbnail_id = get_post_thumbnail_id( $select_promotion_banner->ID );
                $thumbnail_url = wp_get_attachment_image_src( $thumbnail_id, 'full' );
                $thumbnail_meta = wp_get_attachment_metadata( $thumbnail_id );
                $promotion_link = get_field('promotion_link', $select_promotion_banner->ID);
            ?>
                  <a href="<?= esc_url($promotion_link) ?>" class="d-block mb-3" target="_blank">
                    <img loading="lazy" 
                      src="<?php echo esc_url( $thumbnail_url[0] ); ?>" 
                      width="<?php echo esc_attr( $thumbnail_meta['width'] ); ?>" 
                      height="<?php echo esc_attr( $thumbnail_meta['height'] ); ?>" 
                      alt="<?php the_title_attribute(); ?>" />
                  </a>
                <?php
              endforeach;
            endif;
        ?>

        <div class="row mt-3 mt-lg-5">
          <div class="col-lg-<?php echo $is_discontinued && ! empty( $related_product ) ? '6' : '7'; ?>">
            <div class="el-products-single__info-images mb-4">
              <?php
              /**
               * Hook: woocommerce_before_single_product_summary.
               *
               * @hooked woocommerce_show_product_sale_flash - 10
               * @hooked woocommerce_show_product_images - 20
               */
              do_action( 'woocommerce_before_single_product_summary' );
              ?>

              <script>
                jQuery(document).ready(function($) {
                  let bottomReached = false;
                  const $images = $('<?="#{$uid}"?> .el-products-single__info-images');

                  setTimeout(function() {
                    const instance = OverlayScrollbars($('<?="#{$uid}"?> .woocommerce-product-gallery .flex-control-nav'), {
                      scrollbars: {
                        autoHide: 'leave',
                        autoHideDelay: 200,
                      },
                      callbacks:
                        {
                          // gets fired when scroll is stopped
                          onScroll: function() {
                            // get the position of the scrollbar
                            const scrollInfo = instance.scroll();

                            // check if the scrollbar has reached the bottom
                            if (scrollInfo.position.y !== scrollInfo.max.y) {
                              $images.removeClass('bottom-reached');
                              bottomReached = false;
                            } else {
                              if (!bottomReached) {
                                $images.addClass('bottom-reached');
                                bottomReached = true;
                              }
                            }
                          },
                        }
                    });
                  }, 500);
                });
              </script>
            </div>
          </div>

          <div class="col-lg-<?php echo $is_discontinued && ! empty( $related_product ) ? '3' : '5'; ?>">
            <? if ( $is_discontinued ) : ?>
              <p class="el-products-single__discontinued"><? echo MIT_DISCONTINUED_TEXT; ?></p>
            <? endif;

            // Terms
            $last_term = $MIT_helpers->get_last_term( get_the_ID(), 'product_cat' );

            if ( ! is_wp_error( $last_term ) && $last_term ) : ?>
              <p class="el-products-single__info-term"><?= $last_term->name ?></p>
            <? endif; ?>

            <?
            // Title
            the_title( '<h1 class="el-products-single__info-title">', '</h1>' ); ?>

            <?
            // Subheading
            if ( ! empty( $subheading = get_field( 'subheading' ) ) ) : ?>
              <p class="el-products-single__info-subheading"><?= $subheading ?></p>
            <? endif; ?>

            <? if ( 'generic' == $style ) : ?>
              <?
              // Stores count
              ?>
              <? /*<p class="el-products-single__info-stores-count"><small>Available in 999 stores</small></p>*/ ?>

              <?
              // Reviews
              if ( ! empty( $productreview_identifier = get_field( 'productreviewcomau_identifier' ) ) ) : ?>
                <p class="el-products-single__info-reviews">
                  <a href="#section-product-reviews">
                    <? get_template_part( 'template-parts/product-reviews/product-reviews', null, [
                      'identifier' => $productreview_identifier,
                      'type'       => 'inline',
                    ] ); ?>
                  </a>
                </p>
              <? endif; ?>
            <? endif; ?>

            <?
            // Short description
            switch ( $style ) {
              case 'commercial':
                ?>
                <div class="row">
                  <div class="col-xl-8 pe-xl-5">
                    <div class="el-products-single__info-description mb-3">
                      <?= wpautop( $post->post_excerpt ) ?>
                    </div>
                  </div>

                  <div class="col-xl-4">
                    <div class="el-products-single__info-quick-links generic-content">
                      <p class="el-products-single__info-quick-links-header"><strong>Quick Links</strong></p>
                      <ul class="list-unstyled">
                        <? if ( ! empty( MIT_Core::instance()->helpers->get_personalised_field( 'specifications' ) ) ) : ?>
                          <li><a href="#section-product-specifications">Specifications</a></li>
                        <?php endif; ?>

                        <!-- TODO: Add conditions to show -->
                        <li><a href="#section-product-tabs">Features</a></li>
                        <li><a href="#section-product-downloads">Downloads</a></li>
                        <?/*<li><a href="#section-product-optional-parts">Optional Parts</a></li>*/ ?>
                      </ul>
                    </div>
                  </div>
                </div>
                <? break;

              default:
                ?>
                <div class="el-products-single__info-description mb-3">
                  <?= wpautop( $post->post_excerpt ) ?>
                </div>
              <?
            } ?>

            <?php if ( ! empty( $extra_description = get_field( 'extra_description' ) ) ) : ?>
              <div class="el-products-single__extra-description mb-3 p-3">
                <?= wpautop( $extra_description ) ?>
              </div>
            <?php endif; ?>

            <?php if ( ! $is_discontinued ) : ?>
              <div class="mt-3 d-flex gap-3">
                <?
                $stores_locator_page             = get_field( 'stores_locator_page', 'options' );
                $enable_find_stockist_cta        = get_field( 'enable_find_stockist_cta', 'options' );
                $enable_request_a_quote_cta      = get_field( 'enable_request_a_quote_cta', 'options' );
                $disable_default_enquire_now_cta = get_field( 'disable_default_enquire_now_cta', 'options' );

                $has_enquire_now =
                  empty( $disable_default_enquire_now_cta ) || (
                    ! has_term( $disable_default_enquire_now_cta, 'product_cat', $post_id ) &&
                    ! $MIT_helpers->is_post_in_descendant_category( $disable_default_enquire_now_cta, $post_id, 'product_cat' )
                  );

                $has_request_quote =
                  ! empty( $enable_request_a_quote_cta ) && (
                    has_term( $enable_request_a_quote_cta, 'product_cat', $post_id ) ||
                    $MIT_helpers->is_post_in_descendant_category( $enable_request_a_quote_cta, $post_id, 'product_cat' )
                  );

                $has_find_stockist =
                  ! empty( $stores_locator_page ) &&
                  ! empty( $enable_find_stockist_cta ) && (
                    has_term( $enable_find_stockist_cta, 'product_cat', $post_id ) ||
                    $MIT_helpers->is_post_in_descendant_category( $enable_find_stockist_cta, $post_id, 'product_cat' )
                  );

                // CTA buttons
                if ( $has_enquire_now ) : ?>
                  <a href="#" class="btn btn--default btn-fullwidth trigger-modal-enquire">Enquire Now</a>
                <?php endif; ?>

                <? if ( $has_request_quote ) : ?>
                  <a href="#" class="btn btn--<?= $has_enquire_now ? 'outline' : 'default' ?> btn-fullwidth trigger-modal-request-quote">Request a Quote</a>
                <?php endif;

                if ( $has_find_stockist ) : ?>
                  <a href="<?= get_permalink( $stores_locator_page ) ?>" class="btn btn--<?= ( $has_enquire_now || $has_request_quote ) ? 'outline' : 'default' ?> btn-fullwidth">Find a Stockist</a>
                <?php endif; ?>
              </div>
            <?php endif; ?>
          </div>

          <?php if ( $is_discontinued && ! empty( $related_product ) ) { ?>
            <div class="col-lg-3 mb-4">
              <p class="el-products-single__discontinued"><? _e( 'You Might Be Interested In', 'woocommerce' ); ?></p>
              <?
              $post = get_post( $related_product->ID );
              setup_postdata( $post );
              get_template_part( 'template-parts/post-product/post-product' );
              wp_reset_postdata();
              ?>
            </div>
          <?php } ?>
        </div>
      </div>
    </div>
    <?php
  }
}
