<?php

defined( 'ABSPATH' ) or exit;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

class Custom_El_Header extends Widget_Base {
  public function __construct( $data = [], $args = null ) {
    parent::__construct( $data, $args );
  }

  public function get_name() {
    return 'Custom_El_Header';
  }

  public function get_title() {
    return 'Header';
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
        'label'     => __( 'Global header', 'plugin-name' ),
        'type'      => \Elementor\Controls_Manager::HEADING,
        'separator' => 'after',
      ]
    );

    $this->end_controls_section();
  }

  protected function render() {
    $uid = uniqid( 'el-header-' );

    $MIT_helpers       = MIT_Core::instance()->helpers;
    $current_user_type = $MIT_helpers->get_current_user_type();

    ?>
    <div id="<?= $uid ?>" class="el-header">
      <div class="row align-items-center">
        <div class="col">
          <div class="el-header__logo">
            <a href="<?= home_url( MIT_HOME_SLUGS[ $MIT_helpers->get_current_user_type() ] ) ?>">
              <?php /*<img src="<? $MIT_helpers->the_assets_path( 'images/logo-full-au.svg' ) ?>" alt="<?= get_bloginfo( 'title' ) ?>" width="190" class="img-fluid"> */ ?>
              <img src="/wp-content/uploads/2025/08/mitsubishi-electric-australia-website-logo-1.svg" alt="<?= get_bloginfo( 'title' ) ?>" class="img-fluid">
            </a>
          </div>
        </div>

        <div class="col-1 col-lg-7">
          <div class="el-header__menu">
            <div class="el-header__search-form-menu">
              <?= get_search_form() ?>
            </div>

            <? if ( has_nav_menu( "primary_menu_{$current_user_type}" ) ) : ?>
              <?
              wp_nav_menu( array(
                'theme_location' => "primary_menu_{$current_user_type}",
              ) );
              ?>
            <? endif; ?>
          </div>
        </div>

        <div class="col header-search">
          <div class="el-header__search">
            <div class="el-header__search-form">
              <?= get_search_form() ?>
            </div>

            <button class="el-header__search-toggle d-flex align-items-center ms-auto">
              <img src="<? $MIT_helpers->the_assets_path( 'images/chev-left.svg' ); ?>" alt="Left icon" class="img-fluid icon-arrow" width="10">
              <img src="<? $MIT_helpers->the_assets_path( 'images/search.svg' ); ?>" alt="Search icon" class="img-fluid icon-search" width="25">
            </button>
          </div>

          <button class="el-header__menu-toggle ms-auto me-0">
            <img src="<? $MIT_helpers->the_assets_path( 'images/menu.svg' ); ?>" alt="Menu icon" class="img-fluid" width="25">
          </button>
        </div>
      </div>

      <script>
        jQuery(document).ready(function($) {
          const $header = $('<?="#{$uid}"?>');
          const $body = $('body');
          let maxSubmenuItems = 3;

          // Add close icon to each submenu
          $header.find('.menu > li > .sub-menu').append('<a class="el-header__close-submenu" href="#">&nbsp;</a>');

          // Click events
          $header.on('click', '.el-header__search-toggle', function(e) {
            e.preventDefault();

            $header.toggleClass('search-active');

            if ($header.hasClass('search-active')) {
              setTimeout(function() {
                $header.find('[type="search"]').focus();
              }, 200);
            }
          });

          $header.on('click', '.el-header__menu-toggle', function(e) {
            e.preventDefault();

            $body.toggleClass('menu-active');

            if ($body.hasClass('menu-active')) {
              $body.addClass('scroll-lock');
            } else {
              $body.removeClass('scroll-lock');
            }
          });

          $header.on('click', '.menu > li > a', function(e) {
            if ($(this).parent().hasClass('menu-item-has-children')) {
              e.preventDefault();

              // Return others to original states
              $(this).parent().siblings('li').children('.sub-menu').slideUp();
              $(this).parent().siblings('li').find('.sub-menu > li > .sub-menu > li > a').css('width', 'auto');
              $(this).parent().siblings('li').find('.toggled').removeClass('toggled');

              // Toggle current submenu
              $(this).parent().children('.sub-menu').slideToggle();

              // Calculate width of 2nd level menu, ready for animation
              $(this).parent().find('.sub-menu > li > .sub-menu > li > a').each(function() {
                $(this).css('width', $(this).outerWidth());
              });
            }
          });

          $header.on('click', '.menu > li > .sub-menu > li > .sub-menu > li > a', function(e) {
            if ($(this).parent().hasClass('menu-item-has-children')) {
              e.preventDefault();

              $(this).parent().siblings().children('a').removeClass('toggled');
              $(this).parent().siblings().children('.sub-menu').removeClass('toggled');

              $(this).parent().children('.sub-menu').toggleClass('toggled');
              $(this).toggleClass('toggled');
            }
          });

          $header.on('click', '.el-header__close-submenu', function(e) {
            e.preventDefault();
            $(this).parent().slideUp();
          });

          // CLose menus when clicking outside
          $header.on('click', function(event) {event.stopPropagation();});
          $(window).click(function() {
            $header.find('.menu > li > .sub-menu').slideUp();
          });

          // Calculate min height for menu
          $header.find('.sub-menu').each(function() {
            if ($(this).children('li').length > maxSubmenuItems) {
              maxSubmenuItems = $(this).children('li').length;
            }
          });
          $header.get(0).style.setProperty('--el-header-max-submenu-items', maxSubmenuItems);
        });
      </script>
    </div>
    <?php
  }
}
