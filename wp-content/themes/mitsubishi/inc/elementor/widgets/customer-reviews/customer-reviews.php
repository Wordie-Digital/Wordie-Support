<?php

defined( 'ABSPATH' ) or exit;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

class Custom_El_Customer_Reviews extends Widget_Base {
  public function __construct( $data = [], $args = null ) {
    parent::__construct( $data, $args );
  }

  public function get_name() {
    return 'Custom_El_Products_Single_Reviews';
  }

  public function get_title() {
    return 'Customer Reviews';
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
      'identifier',
      [
        'label'   => __( 'ProductReview.com.au Identifier', 'mit' ),
        'type'    => Controls_Manager::TEXT,
        'dynamic' => [
          'active' => true,
        ],
      ]
    );

    $this->end_controls_section();
  }

  protected function render() {
    $uid = uniqid( 'el-customer-reviews-' );

    $identifier = $this->get_settings_for_display( 'identifier' );

    if ( ! empty( $identifier ) ) : ?>
      <div id="<?= $uid ?>" class="el-customer-reviews__reviews-slider elementor-menu-anchor" style="background-color: #F4F4F4;">
        <div class="container">
          <?
          get_template_part( 'template-parts/product-reviews/product-reviews', null, [
            'identifier' => $identifier,
          ] )
          ?>
        </div>
      </div>
    <?php endif; ?>
    <?php
  }
}
