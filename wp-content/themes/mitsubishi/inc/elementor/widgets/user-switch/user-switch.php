<?php

defined( 'ABSPATH' ) or exit;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

class Custom_El_User_Switch extends Widget_Base {
  public function __construct( $data = [], $args = null ) {
    parent::__construct( $data, $args );
  }

  public function get_name() {
    return 'Custom_El_User_Switch';
  }

  public function get_title() {
    return 'User Switch';
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
      'message',
      [
        'label'     => __( 'User switch bar used for header only', 'plugin-name' ),
        'type'      => \Elementor\Controls_Manager::HEADING,
        'separator' => 'after',
      ]
    );

    $this->end_controls_section();
  }

  protected function render() {
    $uid = uniqid( 'el-user-switch-' );

    $types = [
      MIT_USER_TYPE__HO  => "Homeowner",
      MIT_USER_TYPE__PRO => "Commercial & Industrial",
    ];

    $MIT_helpers       = MIT_Core::instance()->helpers;
    $current_user_type = $MIT_helpers->get_current_user_type();

    ?>
    <div id="<?= $uid ?>" class="el-user-switch">
      <div class="el-user-switch__list">
        <? foreach ( $types as $type => $label ) : ?>
          <a class="el-user-switch--<?= $type . ( $type == $current_user_type ? ' active' : '' ) ?>" href="<?= $MIT_helpers->get_home_url_switch( $type ) ?>"><?= $label ?></a> 
        <? endforeach; ?>

        <a href="<?= $MIT_helpers->get_portal_home_url() ?>"><?= MIT_TEXT_PARTNER ?></a>
      </div>

      <div class="el-user-switch__dropdown">
        <select class="form-select" aria-label="Portal Switch">
          <? foreach ( $types as $type => $label ) : ?>
            <option value="<?= $MIT_helpers->get_home_url_switch( $type ) ?>" <?= ( $type == $current_user_type ? ' selected' : '' ) ?>><?= $label ?></option>
          <? endforeach; ?>

          <option value="<?= $MIT_helpers->get_portal_home_url() ?>"><?= MIT_TEXT_PARTNER ?></option>
        </select>
      </div>

      <script>
        jQuery(document).ready(function($) {
          const $select = $('<?="#{$uid}"?> select');

          $select.on('change', function() {
            location.href = $(this).val();
          });
        });
      </script>
    </div>
    <?php
  }
}
