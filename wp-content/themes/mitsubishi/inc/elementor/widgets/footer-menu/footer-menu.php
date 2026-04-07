<?php

defined( 'ABSPATH' ) or exit;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

class Custom_El_Footer_Menu extends Widget_Base {
  public function __construct( $data = [], $args = null ) {
    parent::__construct( $data, $args );
  }

  public function get_name() {
    return 'Custom_El_Footer_Menu';
  }

  public function get_title() {
    return 'Footer Menu';
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
        'label'     => __( 'Global footer menu', 'plugin-name' ),
        'type'      => \Elementor\Controls_Manager::HEADING,
        'separator' => 'after',
      ]
    );

    $this->end_controls_section();
  }

  protected function render() {
    $uid = uniqid( 'el-footer-menu-' );

    $MIT_helpers       = MIT_Core::instance()->helpers;
    $current_user_type = $MIT_helpers->get_current_user_type();

    ?>
    <div id="<?= $uid ?>" class="el-footer-menu">
      <? if ( has_nav_menu( "footer_menu_{$current_user_type}" ) ) : ?>
        <?
        wp_nav_menu( array(
          'theme_location' => "footer_menu_{$current_user_type}",
        ) );
        ?>
      <? endif; ?>

      <script>
        jQuery(document).ready(function($) {
          const $menu = $('<?="#{$uid}"?>');

          $menu.find('.menu > li > a').after('<a href="#" class="el-footer-menu__toggle">&nbsp;</a>');

          $menu.on('click', '.el-footer-menu__toggle', function(e) {
            e.preventDefault();

            $(this).closest('li').find('ul').slideToggle();
          });
        });
      </script>
    </div>
    <?php
  }
}
