<?php

defined( 'ABSPATH' ) or exit;

class MIT_Woocommerce {
  public function __construct() {
    add_action( 'wp', [ $this, 'customize_woocommerce' ], 99 );
    add_action( 'after_setup_theme', [ $this, 'setup_theme' ] );
    add_filter( 'woocommerce_helper_suppress_admin_notices', '__return_true' );
    add_filter( 'woocommerce_product_thumbnails_columns', [ $this, 'change_gallery_columns' ] );
    add_filter( 'woocommerce_single_product_carousel_options', [ $this, 'update_woo_flexslider_options' ] );
    add_filter( 'woocommerce_gallery_thumbnail_size', [ $this, 'thumbnail_size' ] );
  }

  function thumbnail_size(): string {
    return 'medium_large';
  }

  function update_woo_flexslider_options( $options ) {
    // $options['directionNav'] = true;

    return $options;
  }

  function customize_woocommerce() {
  }

  function change_gallery_columns(): int {
    return 1;
  }

  function setup_theme() {
    add_theme_support( 'wc-product-gallery-slider' );
    add_theme_support( 'wc-product-gallery-zoom' );
    add_theme_support( 'wc-product-gallery-lightbox' );
    add_theme_support( 'woocommerce' );
  }

  function mit_get_gallery_image_html( $attachment_id, $main_image = false ) {
    $flexslider        = (bool) apply_filters( 'woocommerce_single_product_flexslider_enabled', get_theme_support( 'wc-product-gallery-slider' ) );
    $gallery_thumbnail = wc_get_image_size( 'gallery_thumbnail' );
    $thumbnail_size    = apply_filters( 'woocommerce_gallery_thumbnail_size', array( $gallery_thumbnail['width'], $gallery_thumbnail['height'] ) );
    $image_size        = apply_filters( 'woocommerce_gallery_image_size', $flexslider || $main_image ? 'woocommerce_single' : $thumbnail_size );
    $full_size         = apply_filters( 'woocommerce_gallery_full_size', apply_filters( 'woocommerce_product_thumbnails_large_size', 'full' ) );
    $thumbnail_src     = wp_get_attachment_image_src( $attachment_id, $thumbnail_size );
    $full_src          = wp_get_attachment_image_src( $attachment_id, $full_size );
    $alt_text          = trim( wp_strip_all_tags( get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) ) );
    $image             = wp_get_attachment_image(
      $attachment_id,
      $image_size,
      false,
      apply_filters(
        'woocommerce_gallery_image_html_attachment_image_params',
        array(
          'title'                   => _wp_specialchars( get_post_field( 'post_title', $attachment_id ), ENT_QUOTES, 'UTF-8', true ),
          'data-caption'            => _wp_specialchars( get_post_field( 'post_excerpt', $attachment_id ), ENT_QUOTES, 'UTF-8', true ),
          'data-src'                => esc_url( $full_src[0] ),
          'data-large_image'        => esc_url( $full_src[0] ),
          'data-large_image_width'  => esc_attr( $full_src[1] ),
          'data-large_image_height' => esc_attr( $full_src[2] ),
          'class'                   => esc_attr( $main_image ? 'wp-post-image' : '' ),
        ),
        $attachment_id,
        $image_size,
        $main_image
      )
    );
  
    return '<div data-thumb="' . esc_url( $thumbnail_src[0] ) . '" data-thumb-alt="' . esc_attr( $alt_text ) . '" class="woocommerce-product-gallery__image"><a href="' . esc_url( $full_src[0] ) . '" title="' . esc_attr( $alt_text ) . '">' . $image . '</a></div>';
  }
}
