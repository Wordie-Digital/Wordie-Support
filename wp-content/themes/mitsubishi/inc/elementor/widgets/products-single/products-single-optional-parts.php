<?php

defined( 'ABSPATH' ) or exit;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

class Custom_El_products_Single_Optional_Parts extends Widget_Base {
  public function __construct( $data = [], $args = null ) {
    parent::__construct( $data, $args );
  }

  public function get_name() {
    return 'Custom_El_products_Single_Optional_Parts';
  }

  public function get_title() {
    return 'Product | Optional Parts';
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

    global $post;

    $uid         = uniqid( 'el-products-single-optional-parts-' );
    $MIT_helpers = MIT_Core::instance()->helpers;

    // TODO: Get back to this when it's ready
    return;
    ?>
    <div class="el-products-single__optional-parts elementor-menu-anchor" id="section-product-optional-parts">
      <div class="container">
        <?
        get_template_part( 'template-parts/section-heading/section-heading', null, [
          'heading' => 'Optional Parts',
        ] );
        ?>

        TBD
      </div>
    </div>
    <?php
  }
}
