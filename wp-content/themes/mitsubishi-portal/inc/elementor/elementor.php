<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

if ( ! POR_Core::instance()->helpers->is_elementor_active() ) {
  return;
}

use Elementor\Controls_Manager;
use Elementor\Elements_Manager;
use Elementor\Plugin;

final class Portal_Elementor {
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
    add_action( 'elementor/element/wp-page/document_settings/before_section_end', [ $this, 'init_page_settings_controls' ] );
  }

  function init_categories( Elements_Manager $categories_manager ) {
    $categories_manager->add_category(
      'custom',
      [
        'title' => 'Portal Widgets',
        'icon'  => 'fa fa-plug',
      ]
    );
  }

  public function init_widgets() {
    // Include Widget files
    require_once( __DIR__ . '/widgets/posts-grid/posts-grid.php' );
    require_once( __DIR__ . '/widgets/marketo-form/marketo-form.php' );
    require_once( __DIR__ . '/widgets/cards-grid/cards-grid.php' );
    require_once( __DIR__ . '/widgets/news-updates/news-updates.php' );
    require_once( __DIR__ . '/widgets/products-docs/products-docs.php' );
    require_once( __DIR__ . '/widgets/resources-tabs/resources-tabs.php' );
    require_once( __DIR__ . '/widgets/training-tabs/training-tabs.php' );
    require_once( __DIR__ . '/widgets/search-results/search-results.php' );
    require_once( __DIR__ . '/widgets/product-docs/product-docs.php' );
    require_once( __DIR__ . '/widgets/spare-parts/spare-parts.php' );

    // Register widget
    Plugin::instance()->widgets_manager->register( new Custom_Porel_Posts_Grid() );
    Plugin::instance()->widgets_manager->register( new Custom_Porel_Marketo_Form() );
    Plugin::instance()->widgets_manager->register( new Custom_Porel_Cards_Grid() );
    Plugin::instance()->widgets_manager->register( new Custom_Porel_News_Updates() );
    Plugin::instance()->widgets_manager->register( new Custom_Porel_Products_Docs() );
    Plugin::instance()->widgets_manager->register( new Custom_Porel_Product_Docs() );
    Plugin::instance()->widgets_manager->register( new Custom_Porel_Resources_Tabs() );
    Plugin::instance()->widgets_manager->register( new Custom_Porel_Training_Tabs() );
    Plugin::instance()->widgets_manager->register( new Custom_Porel_Search_Results() );
    Plugin::instance()->widgets_manager->register( new Custom_Porel_Spare_Parts() );
  }

  public function init_controls() {
    // Include Control files
    // require_once( __DIR__ . '/controls/test-control.php' );

    // Register control
    // \Elementor\Plugin::$instance->controls_manager->register_control( 'control-type-', new \Test_Control() );
  }

  function init_page_settings_controls( Elementor\Core\DocumentTypes\PageBase $page ) {
    $page->add_control(
      'stretch_last_block',
      [
        'label'        => __( 'Stretch last block', 'mitsubishi' ),
        'type'         => Controls_Manager::SWITCHER,
        'label_on'     => __( 'Yes', 'mitsubishi' ),
        'label_off'    => __( 'No', 'mitsubishi' ),
        'description'  => 'Stretch last block to fill whole page',
        'return_value' => 'yes',
        'default'      => 'no',
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

Portal_Elementor::instance();
