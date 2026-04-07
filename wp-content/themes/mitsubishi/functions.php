<?php

defined( 'ABSPATH' ) or exit;

// CONSTANTS
defined( 'ASSETS_VERSION' ) or define( 'ASSETS_VERSION', md5( '1.0' . filemtime( get_theme_file_path( 'dist/main.min.css' ) ) . filemtime( get_theme_file_path( 'dist/main.min.js' ) ) ) );
defined( 'MIT_USER_TYPE__HO' ) or define( 'MIT_USER_TYPE__HO', 'homeowner' );
defined( 'MIT_USER_TYPE__PRO' ) or define( 'MIT_USER_TYPE__PRO', 'professional' );
defined( 'MIT_USER_TYPE__COOKIE' ) or define( 'MIT_USER_TYPE__COOKIE', 'wordpress_mit_user_type' ); // WPE cache will exclude "wordpress_" cookies from caching
defined( 'MIT_USER_TYPE__GET_PARAM' ) or define( 'MIT_USER_TYPE__GET_PARAM', 'u' );
defined( 'MIT_TEXT_PARTNER' ) or define( 'MIT_TEXT_PARTNER', 'Partner' );
defined( 'MIT_ALM_NO_RESULTS_TEXT' ) or define( 'MIT_ALM_NO_RESULTS_TEXT', 'Sorry, nothing found in this search' );
defined( 'MIT_DISCONTINUED_TEXT' ) or define( 'MIT_DISCONTINUED_TEXT', 'This Product Has Been Discontinued' );
defined( 'MIT_PORTAL_BLOG_ID' ) or define( 'MIT_PORTAL_BLOG_ID', 2 );
defined( 'MIT_STORES_REPAIR_SERVICE_PAGE_ID' ) or define( 'MIT_STORES_REPAIR_SERVICE_PAGE_ID', 1062 );
defined( 'MIT_STORES_DEALER_TYPE_GROUP_ID__STOCKIST' ) or define( 'MIT_STORES_DEALER_TYPE_GROUP_ID__STOCKIST', 1209 );
defined( 'MIT_STORES_DEALER_TYPE_GROUP_ID__REPAIR' ) or define( 'MIT_STORES_DEALER_TYPE_GROUP_ID__REPAIR', 1210 );
defined( 'MIT_STORES_SERVICE_GROUP_ID__STOCKIST' ) or define( 'MIT_STORES_SERVICE_GROUP_ID__STOCKIST', 1212 );
defined( 'MIT_STORES_SERVICE_GROUP_ID__REPAIR' ) or define( 'MIT_STORES_SERVICE_GROUP_ID__REPAIR', 1211 );

defined( 'MIT_USER_TYPES' ) or define( 'MIT_USER_TYPES', [
  MIT_USER_TYPE__HO,
  MIT_USER_TYPE__PRO,
] );

defined( 'MIT_HOME_SLUGS' ) or define( 'MIT_HOME_SLUGS', [
  MIT_USER_TYPE__HO  => "homeowner",
  MIT_USER_TYPE__PRO => "industry-professional",
] );

// CLASSES AUTOLOAD
try {
  spl_autoload_register( function ( $class ) {
    if ( false === strpos( $class, 'MIT_' ) ) {
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

// PRIMARY CLASS
final class MIT_Core {
  private static ?MIT_Core $_instance = null;

  public MIT_Helpers $helpers;
  public MIT_Setup $setup;
  public MIT_Admin $admin;
  public MIT_Woocommerce $woo; 
  public MIT_Stores_Locator $stores;
  public MIT_Rest_Controller $api;

  public function __construct() {
    $this->helpers = new MIT_Helpers();
    $this->setup   = new MIT_Setup();
    $this->admin   = new MIT_Admin();
    $this->woo     = new MIT_Woocommerce();
    $this->stores  = new MIT_Stores_Locator();
    $this->api     = new MIT_Rest_Controller();
  }

  public static function instance(): ?MIT_Core {
    return ( is_null( self::$_instance ) ? self::$_instance = new MIT_Core() : self::$_instance );
  }
}

function allow_svg_upload($mimes) {
  $mimes['svg'] = 'image/svg+xml';
  return $mimes;
}
add_filter('upload_mimes', 'allow_svg_upload');

function get_product_category_types(){

  // Check if we're on a product category archive
  if (is_tax( 'product_cat') ) {
    $term = get_queried_object();
    if ( $term && ! is_wp_error( $term ) ) {
      $term_id = $term->term_id;
    }
  }
  
  $term_key = 'product_cat_' . $term_id;
  $products_types_contents = get_field('products_types_contents', $term_key);
  
  // Return debug output
  ob_start();
  ?>
  <div class="pt-cards d-flex mobile-convert-to-slide">  
    <?php foreach ($products_types_contents as $ptc){?>
      <div class="pt-card-item">
        <img src="<?php echo $ptc['image']['url']; ?>" alt="<?php echo $ptc['image']['alt']; ?>">
        <h3><?php echo esc_html( $ptc['name'] ); ?></h3>
        <?php echo $ptc['description']; ?>
      </div>
    <?php } ?>
  </div>
  <?php
  return ob_get_clean();
}
add_shortcode( 'product_category_types', 'get_product_category_types' );

function get_product_category_benefits(){

  // Check if we're on a product category archive
  if (is_tax( 'product_cat') ) {
    $term = get_queried_object();
    if ( $term && ! is_wp_error( $term ) ) {
      $term_id = $term->term_id;
    }
  }
  
  $term_key = 'product_cat_' . $term_id;
  $benefits_content = get_field('benefits_content', $term_key);
  
  // Return debug output
  ob_start();
  ?>
  <div class="bc-cards d-flex mobile-convert-to-slide">  
    <?php foreach ($benefits_content as $bc){?>
      <div class="bc-card-item">
        <img src="<?php echo $bc['icon']['url']; ?>" alt="<?php echo $bc['icon']['alt']; ?>">
        <h3><?php echo esc_html( $bc['title'] ); ?></h3>
        <?php echo $bc['description']; ?>
      </div>
    <?php } ?>
  </div>
  <?php
  return ob_get_clean();
}
add_shortcode( 'product_category_benefits', 'get_product_category_benefits' );


add_filter( 'wpsl_meta_box_fields', 'add_sap_id_custom_field_to_wpsl' );

function add_sap_id_custom_field_to_wpsl( $meta_fields ) {

    if ( isset( $meta_fields['Additional Information'] ) ) {
        $meta_fields['Additional Information']['sap_id'] = array(
            'label' => 'SAP ID',
            'type'  => 'text',
        );
    }

    return $meta_fields;
}

function mit_get_compliance_disclaimer() {
  global $product;

  if ( ! is_a( $product, 'WC_Product' ) ) {
      return '';
  }

  $disclaimers = [];

  // Get all product categories for this product
  $terms = get_the_terms( $product->get_id(), 'product_cat' );

  if ( ! $terms || is_wp_error( $terms ) ) {
      return '';
  }

  foreach ( $terms as $term ) {
      // Get ACF field from this category
      $disclaimer = get_field( 'compliance_disclaimer', $term );

      if ( ! empty( $disclaimer ) ) {
          // Optional: Wrap each one uniquely, or combine
          $disclaimers[] = '<div class="compliance-disclaimer" data-category="' . esc_attr( $term->slug ) . '">' 
                         . wp_kses_post( $disclaimer ) 
                         . '</div>';
      }
  }

  if ( empty( $disclaimers ) ) {
      return '';
  }

  // Combine if multiple (or just return first if you prefer priority)
  return implode( "\n", $disclaimers );
}

// NO MORE CODE BELOW THIS LINE
MIT_Core::instance();