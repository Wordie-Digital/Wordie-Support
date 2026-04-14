<?php

defined( 'ABSPATH' ) or exit;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

class Custom_El_Border_Heading extends Widget_Base {
  public function __construct( $data = [], $args = null ) {
    parent::__construct( $data, $args );
  }

  public function get_name() {
    return 'Custom_El_Border_Heading';
  }

  public function get_title() {
    return 'Border Heading';
  }

  public function get_icon() {
    return 'eicon-custom';
  }

  public function get_categories() {
    return [ 'custom' ];
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
      'heading',
      [
        'label' => __( 'Heading', 'mit' ),
        'type'  => Controls_Manager::TEXT,
      ]
    );

    $this->end_controls_section();
  }

  protected function render() {
    $uid = uniqid( 'el-border-heading-' );
    ?>
    <div id="<?= $uid ?>" class="el-border-heading">
      <?
      get_template_part( 'template-parts/section-heading/section-heading', null, [
        'heading' => $this->get_settings_for_display( 'heading' ),
      ] );
      ?>
    </div>
    <?php
  }
}
