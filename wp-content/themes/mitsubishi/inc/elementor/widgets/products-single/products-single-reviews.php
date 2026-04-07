<?php

defined( 'ABSPATH' ) or exit;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

class Custom_El_Products_Single_Reviews extends Widget_Base {
  public function __construct( $data = [], $args = null ) {
    parent::__construct( $data, $args );
  }

  public function get_name() {
    return 'Custom_El_Products_Single_Reviews';
  }

  public function get_title() {
    return 'Product | Reviews';
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

  protected function _register_controls() {
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

    $uid = uniqid( 'el-products-single-reviews-' );

    if ( ! empty( $productreview_identifier = get_field( 'productreviewcomau_identifier' ) ) ) : ?>
      <div id="<?= $uid ?>" class="el-products-single__reviews-slider elementor-menu-anchor" style="background-color: #F4F4F4;">
        <div class="container">
          <?
          get_template_part( 'template-parts/product-reviews/product-reviews', null, [
            'identifier' => $productreview_identifier,
          ] )
          ?>
        </div>
      </div>
    <?php endif; ?>
    <?php
  }
}
