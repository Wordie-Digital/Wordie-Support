<?php

defined( 'ABSPATH' ) or exit;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

class Custom_El_User_List extends Widget_Base {
  public function __construct( $data = [], $args = null ) {
    parent::__construct( $data, $args );
  }

  public function get_name() {
    return 'Custom_El_User_List';
  }

  public function get_title() {
    return 'User List';
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
      'content_' . MIT_USER_TYPE__HO, [
        'label' => __( 'Content | Homeowner', 'plugin-domain' ),
        'type'  => \Elementor\Controls_Manager::WYSIWYG,
      ]
    );

    $this->add_control(
      'content_' . MIT_USER_TYPE__PRO, [
        'label' => __( 'Content | Professional', 'plugin-domain' ),
        'type'  => \Elementor\Controls_Manager::WYSIWYG,
      ]
    );

    $this->add_control(
      'content_dealer', [
        'label' => __( 'Content | ' . MIT_TEXT_PARTNER, 'plugin-domain' ),
        'type'  => \Elementor\Controls_Manager::WYSIWYG,
      ]
    );

    $this->end_controls_section();
  }

  protected function render() {
    $uid = uniqid( 'el-user-list-' );

    $content_dealer = $this->get_settings_for_display( 'content_dealer' );

    $types = [
      MIT_USER_TYPE__HO  => "I am a Homeowner",
      MIT_USER_TYPE__PRO => "I am an Industry Professional",
    ];

    $MIT_helpers       = MIT_Core::instance()->helpers;
    $current_user_type = $MIT_helpers->get_current_user_type();

    ?>
    <div id="<?= $uid ?>" class="el-user-list">
      <ul class="list-unstyled">
        <? foreach ( $types as $type => $label ) : ?>
          <?php if ( $type != $current_user_type ) : ?>
            <li>
              <a class="el-user-list__heading" href="<?= $MIT_helpers->get_home_url_switch( $type ) ?>"><?= $label ?></a>
              <?php if ( ! empty( $content = $this->get_settings_for_display( "content_{$type}" ) ) ) : ?>
                <div class="mb-5">
                  <?= wpautop( $content ) ?>
                </div>
              <?php endif; ?>
            </li>
          <?php endif; ?>
        <?php endforeach; ?>

        <li>
          <a class="el-user-list__heading" href="<?= $MIT_helpers->get_portal_home_url() ?>">I am a Mitsubishi Electric <?= MIT_TEXT_PARTNER ?></a>
          <?php if ( ! empty( $content_dealer ) ) : ?>
            <div>
              <?= wpautop( $content_dealer ) ?>
            </div>
          <?php endif; ?>
        </li>
      </ul>
    </div>
    <?php
  }
}
