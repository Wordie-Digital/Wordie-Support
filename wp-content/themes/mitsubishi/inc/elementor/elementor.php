<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

if ( ! MIT_Core::instance()->helpers->is_elementor_active() ) {
  return;
}

use Elementor\Controls_Manager;
use Elementor\Elements_Manager;
use Elementor\Plugin;

final class Custom_Elementor {
  const VERSION = '1.0.0';
  const MINIMUM_ELEMENTOR_VERSION = '2.0.0';
  const MINIMUM_PHP_VERSION = '7.0';

  private static $_instance = null;

  public static function instance() {
    if ( is_null( self::$_instance ) ) {
      self::$_instance = new self();
    }

    return self::$_instance;
  }

  public function __construct() {
    add_action( 'after_setup_theme', [ $this, 'init' ] );
    add_action( 'elementor/dynamic_tags/register_tags', [ $this, 'init_dynamic_tags' ] );
  }

  public function init_dynamic_tags( $dynamic_tags ) {
    \Elementor\Plugin::$instance->dynamic_tags->register_group( 'mitsubishi', [
      'title' => 'Mitsubishi',
    ] );

    // Include the Dynamic tag class file
    include_once( 'tags/home-url.php' );

    // Finally register the tag
    $dynamic_tags->register_tag( 'Custom_Tag_Home_Url' );
  }

  public function init() {
    // Check if Elementor installed and activated
    if ( ! did_action( 'elementor/loaded' ) ) {
      add_action( 'admin_notices', [ $this, 'admin_notice_missing_main_plugin' ] );

      return;
    }

    // Check for required Elementor version
    if ( ! version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
      add_action( 'admin_notices', [ $this, 'admin_notice_minimum_elementor_version' ] );

      return;
    }

    // Check for required PHP version
    if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
      add_action( 'admin_notices', [ $this, 'admin_notice_minimum_php_version' ] );

      return;
    }

    // Init elements
    add_action( 'elementor/widgets/widgets_registered', [ $this, 'init_widgets' ] );
    add_action( 'elementor/controls/controls_registered', [ $this, 'init_controls' ] );
    add_action( 'elementor/elements/categories_registered', [ $this, 'init_categories' ] );
    // add_action( 'elementor/element/post/document_settings/before_section_end', [ $this, 'init_page_settings_controls' ] );
  }

  function init_categories( Elements_Manager $categories_manager ) {
    $categories_manager->add_category(
      'custom',
      [
        'title' => 'Mitsubishi Widgets',
        'icon'  => 'fa fa-plug',
      ]
    );

    $categories_manager->add_category(
      'custom_woo',
      [
        'title' => 'Mitsubishi Woocommerce',
        'icon'  => 'fa fa-plug',
      ]
    );
  }

  public function init_widgets() {
    // Include Widget files
    require_once( __DIR__ . '/widgets/header/header.php' );
    require_once( __DIR__ . '/widgets/footer-menu/footer-menu.php' );
    require_once( __DIR__ . '/widgets/user-switch/user-switch.php' );
    require_once( __DIR__ . '/widgets/user-list/user-list.php' );
    require_once( __DIR__ . '/widgets/page-header/page-header.php' );
    require_once( __DIR__ . '/widgets/posts-grid/posts-grid.php' );
    require_once( __DIR__ . '/widgets/posts-blog-features/posts-blog-features.php' );
    require_once( __DIR__ . '/widgets/border-heading/border-heading.php' );
    require_once( __DIR__ . '/widgets/recipes-archive/recipes-archive.php' );
    require_once( __DIR__ . '/widgets/recipes-single/recipes-single.php' );
    require_once( __DIR__ . '/widgets/text-image/text-image.php' );
    require_once( __DIR__ . '/widgets/posts-slider/posts-slider.php' );
    require_once( __DIR__ . '/widgets/cards-grid/cards-grid.php' );
    require_once( __DIR__ . '/widgets/timeline/timeline.php' );
    require_once( __DIR__ . '/widgets/marketo-form/marketo-form.php' );
    require_once( __DIR__ . '/widgets/links-nav/links-nav.php' );
    require_once( __DIR__ . '/widgets/share-post/share-post.php' );
    require_once( __DIR__ . '/widgets/search-results/search-results.php' );
    require_once( __DIR__ . '/widgets/customer-reviews/customer-reviews.php' );
    require_once( __DIR__ . '/widgets/related-posts-grid/related-posts-grid.php' );

    require_once( __DIR__ . '/widgets/products-archive/products-archive-grid.php' );
    require_once( __DIR__ . '/widgets/products-archive/products-archive-header.php' );
    require_once( __DIR__ . '/widgets/products-archive/products-archive-faqs.php' );
    require_once( __DIR__ . '/widgets/products-single/products-single-downloads.php' );
    require_once( __DIR__ . '/widgets/products-single/products-single-info.php' );
    require_once( __DIR__ . '/widgets/products-single/products-single-recommend.php' );
    require_once( __DIR__ . '/widgets/products-single/products-single-tabs.php' );
    require_once( __DIR__ . '/widgets/products-single/products-single-optional-parts.php' );
    require_once( __DIR__ . '/widgets/products-single/products-single-faqs.php' );
    require_once( __DIR__ . '/widgets/products-single/products-single-faqs-v2.php' );

    // Register widget
    Plugin::instance()->widgets_manager->register( new Custom_El_Header() );
    Plugin::instance()->widgets_manager->register( new Custom_El_Footer_Menu() );
    Plugin::instance()->widgets_manager->register( new Custom_El_User_Switch() );
    Plugin::instance()->widgets_manager->register( new Custom_El_User_List() );
    Plugin::instance()->widgets_manager->register( new Custom_El_Page_Header() );
    Plugin::instance()->widgets_manager->register( new Custom_El_Posts_Grid() );
    Plugin::instance()->widgets_manager->register( new Custom_El_Posts_Blog_Features() );
    Plugin::instance()->widgets_manager->register( new Custom_El_Border_Heading() );
    Plugin::instance()->widgets_manager->register( new Custom_El_Archive_Recipes() );
    Plugin::instance()->widgets_manager->register( new Custom_El_Recipes_Single() );
    Plugin::instance()->widgets_manager->register( new Custom_El_Text_Image() );
    Plugin::instance()->widgets_manager->register( new Custom_El_Posts_Slider() );
    Plugin::instance()->widgets_manager->register( new Custom_El_Cards_Grid() );
    Plugin::instance()->widgets_manager->register( new Custom_El_Timeline() );
    Plugin::instance()->widgets_manager->register( new Custom_El_Marketo_Form() );
    Plugin::instance()->widgets_manager->register( new Custom_El_Products_Archive_Grid() );
    Plugin::instance()->widgets_manager->register( new Custom_El_Products_Archive_Header() );
    Plugin::instance()->widgets_manager->register( new Custom_El_Products_Archive_Faqs() );
    Plugin::instance()->widgets_manager->register( new Custom_El_Links_Nav() );
    Plugin::instance()->widgets_manager->register( new Custom_El_Share_Post() );
    Plugin::instance()->widgets_manager->register( new Custom_El_Search_Results() );
    Plugin::instance()->widgets_manager->register( new Custom_El_Customer_Reviews() );
    Plugin::instance()->widgets_manager->register( new Custom_El_Related_Posts_Grid() );

    Plugin::instance()->widgets_manager->register( new Custom_El_Products_Single_Downloads() );
    Plugin::instance()->widgets_manager->register( new Custom_El_Products_Single_Info() );
    Plugin::instance()->widgets_manager->register( new Custom_El_Products_Single_Recommend() );
    Plugin::instance()->widgets_manager->register( new Custom_El_Products_Single_Tabs() );
    Plugin::instance()->widgets_manager->register( new Custom_El_products_Single_Optional_Parts() );
    Plugin::instance()->widgets_manager->register( new Custom_El_Products_Single_Faqs() );
    Plugin::instance()->widgets_manager->register( new Custom_El_Products_Single_Faqs_V2() );
  }

  public function init_controls() {
    // Include Control files
    // require_once( __DIR__ . '/controls/test-control.php' );

    // Register control
    // \Elementor\Plugin::$instance->controls_manager->register_control( 'control-type-', new \Test_Control() );
  }

  function init_page_settings_controls( Elementor\Core\DocumentTypes\PageBase $page ) {
    $page->add_control(
      'show_key_background',
      [
        'label'        => __( 'Show Key Background', 'mitsubishi' ),
        'type'         => Controls_Manager::SWITCHER,
        'label_on'     => __( 'Show', 'mitsubishi' ),
        'label_off'    => __( 'Hide', 'mitsubishi' ),
        'return_value' => 'yes',
        'default'      => 'yes',
      ]
    );
  }

  public function admin_notice_missing_main_plugin() {
    if ( isset( $_GET['activate'] ) ) {
      unset( $_GET['activate'] );
    }

    $message = sprintf(
    /* translators: 1: Plugin name 2: Elementor */
      esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'elementor-test-extension' ),
      '<strong>' . esc_html__( 'Elementor Test Extension', 'elementor-test-extension' ) . '</strong>',
      '<strong>' . esc_html__( 'Elementor', 'elementor-test-extension' ) . '</strong>'
    );

    printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
  }

  public function admin_notice_minimum_elementor_version() {
    if ( isset( $_GET['activate'] ) ) {
      unset( $_GET['activate'] );
    }

    $message = sprintf(
    /* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
      esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'elementor-test-extension' ),
      '<strong>' . esc_html__( 'Elementor Test Extension', 'elementor-test-extension' ) . '</strong>',
      '<strong>' . esc_html__( 'Elementor', 'elementor-test-extension' ) . '</strong>',
      self::MINIMUM_ELEMENTOR_VERSION
    );

    printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
  }

  public function admin_notice_minimum_php_version() {
    if ( isset( $_GET['activate'] ) ) {
      unset( $_GET['activate'] );
    }

    $message = sprintf(
    /* translators: 1: Plugin name 2: PHP 3: Required PHP version */
      esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'elementor-test-extension' ),
      '<strong>' . esc_html__( 'Elementor Test Extension', 'elementor-test-extension' ) . '</strong>',
      '<strong>' . esc_html__( 'PHP', 'elementor-test-extension' ) . '</strong>',
      self::MINIMUM_PHP_VERSION
    );

    printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
  }
}

Custom_Elementor::instance();
