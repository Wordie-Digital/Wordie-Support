<?php

defined( 'ABSPATH' ) or exit;

// CONSTANTS
defined( 'ASSETS_VERSION' ) or define( 'ASSETS_VERSION', md5( filemtime( get_theme_file_path( 'dist/main.min.css' ) ) . filemtime( get_theme_file_path( 'dist/main.min.js' ) ) ) );
defined( 'POR_ALM_NO_RESULTS_TEXT' ) or define( 'POR_ALM_NO_RESULTS_TEXT', 'Sorry, nothing found in this search' );
defined( 'POR_TEXT_ADDED_TO_ETA_REQUEST' ) or define( 'POR_TEXT_ADDED_TO_ETA_REQUEST', 'Added to ETA Request' );
defined( 'POR_TEXT_ADD_TO_ETA_REQUEST' ) or define( 'POR_TEXT_ADD_TO_ETA_REQUEST', 'Add To ETA Request' );

// CLASSES AUTOLOAD
try {
  spl_autoload_register( function ( $class ) {
    if ( false === strpos( $class, 'POR_' ) ) {
      return;
    }

    $filename = dirname( __FILE__ ) . '/inc/core/class-' . str_replace( '_', '-', strtolower( $class ) ) . '.php';

    if ( is_readable( $filename ) ) {
      include_once( $filename );
    }
  } );
} catch ( Exception $e ) {
};

// EXTRA MODULES
require_once 'inc/elementor/elementor.php';
require_once 'vendor/autoload.php';

// PRIMARY CLASS
final class POR_Core {
  private static ?POR_Core $_instance = null;

  public POR_Pdf $pdf;
  public POR_Excel $excel;
  public POR_Helpers $helpers;
  public POR_Setup $setup;
  public POR_Admin $admin;
  public POR_Woocommerce $woo;

  public function __construct() {
    $this->helpers = new POR_Helpers();
    $this->setup   = new POR_Setup();
    $this->admin   = new POR_Admin();
    $this->woo     = new POR_Woocommerce();
    $this->pdf     = new POR_Pdf();
    $this->excel   = new POR_Excel();
  }

  public static function instance(): ?POR_Core {
    return ( is_null( self::$_instance ) ? self::$_instance = new POR_Core() : self::$_instance );
  }
}

// NO MORE CODE BELOW THIS LINE
POR_Core::instance();


if ( ! function_exists('write_log')) {
  function write_log ( $log )  {
     if ( is_array( $log ) || is_object( $log ) ) {
        error_log( print_r( $log, true ) );
     } else {
        error_log( $log );
     }
  }
}