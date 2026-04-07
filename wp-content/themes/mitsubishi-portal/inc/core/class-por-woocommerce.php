<?php

defined( 'ABSPATH' ) or exit;

class POR_Woocommerce {
  public function __construct() {
    add_action( 'after_setup_theme', [ $this, 'setup_theme' ] );
    add_filter( 'woocommerce_helper_suppress_admin_notices', '__return_true' );
  }

  function setup_theme() {
    add_theme_support( 'woocommerce' );
  }
}
