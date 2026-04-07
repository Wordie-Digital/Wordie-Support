<?php

defined( 'ABSPATH' ) or exit;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

class Custom_El_Products_Single_Recommend extends Widget_Base {
  public function __construct( $data = [], $args = null ) {
    parent::__construct( $data, $args );
  }

  public function get_name() {
    return 'Custom_El_Products_Single_Recommend';
  }

  public function get_title() {
    return 'Product | Recommend';
  }

  public function get_icon() {
    return 'eicon-custom';
  }

  public function get_categories() {
    return [ 'custom_woo' ];
  }

  public function get_script_depends() {
    return [];
  }

  public function get_style_depends() {
    return [];
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

    $this->end_controls_section();
  }

  protected function render() {
    if ( ! is_product() ) {
      return;
    }

    global $product, $post;

    $uid = uniqid( 'el-products-single-recommend-' );

    $related_args = array(
      'posts_per_page' => 3,
      'orderby'        => 'date', // @codingStandardsIgnoreLine.
      'order'          => 'desc',
    );

    $related_products = array_filter( array_map( 'wc_get_product', wc_get_related_products( $product->get_id(), $related_args['posts_per_page'], $product->get_upsell_ids() ) ), 'wc_products_array_filter_visible' );
    $related_products = wc_products_array_orderby( $related_products, $related_args['orderby'], $related_args['order'] );

    if ( ! empty( $related_products ) ) : ?>
      <div class="el-products-single__recommend elementor-menu-anchor" id="section-product-recommend">
        <div class="container">
          <?
          get_template_part( 'template-parts/section-heading/section-heading', null, [
            'heading' => get_field( 'custom_suggested_for_you_heading' ) ?: 'SUGGESTED FOR YOU',
          ] );
          ?>

          <div class="row row-cols-1 row-cols-lg-3">
            <? foreach ( $related_products as $related_product ) {
              /** @var WC_Product $related_product */
              $post = get_post( $related_product->get_id() );
              setup_postdata( $post );
              ?>
              <div class="col mb-5">
                <?
                get_template_part( 'template-parts/post-product/post-product' );
                ?>
              </div>
            <? } ?>
            <? wp_reset_postdata(); ?>
          </div>
        </div>
      </div>
    <? endif; ?>
    <?php
  }
}
