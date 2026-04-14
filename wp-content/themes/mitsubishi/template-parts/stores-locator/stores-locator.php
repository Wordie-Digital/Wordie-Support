<?php

global $wpsl_settings, $wpsl, $post;

$output         = $this->get_custom_css();
$autoload_class = ( ! $wpsl_settings['autoload'] ) ? 'class="wpsl-not-loaded"' : '';

////////// Breadcrumb START
ob_start();
?>
  <div class="container">
    <? get_template_part( 'template-parts/breadcrumbs/breadcrumbs' ); ?>
  </div>
<?
$output .= ob_get_clean();
////////// Breadcrumb END

$output .= '<div class="mit-stores-locator">' . "\r\n";
$output .= '<div class="container">' . "\r\n";
$output .= '<div id="wpsl-wrap">' . "\r\n";

$output .= "\t" . '<div id="wpsl-gmap" class="wpsl-gmap-canvas"></div>' . "\r\n";

$output .= "\t" . '<div id="wpsl-result-list-wrapper">' . "\r\n";

////////// page info START
ob_start();
?>
  <div class="mit-stores-locator__info">
    <? the_title( '<h1>', '</h1>' ) ?>
    <? the_excerpt(); ?>
  </div>
<?php
$output .= ob_get_clean();
////////// page info END

////////// wpsl-search START
$output .= "\t" . '<div class="wpsl-search wpsl-clearfix ' . $this->get_css_classes() . '">' . "\r\n";
$output .= "\t\t" . '<div id="wpsl-search-wrap">' . "\r\n";
$output .= "\t\t\t" . '<form autocomplete="off">' . "\r\n";
$output .= "\t\t\t" . '<div class="wpsl-input">' . "\r\n";
$output .= "\t\t\t\t" . '<div><label for="wpsl-search-input" class="visually-hidden">' . esc_html( $wpsl->i18n->get_translation( 'search_label', __( 'Your location', 'wpsl' ) ) ) . '</label></div>' . "\r\n";
$output .= "\t\t\t\t" . '<input id="wpsl-search-input" type="text" value="' . apply_filters( 'wpsl_search_input', '' ) . '" name="wpsl-search-input" placeholder="Enter postcode or suburb" aria-required="true" />' . "\r\n";
$output .= "\t\t\t" . '</div>' . "\r\n";

if ( $wpsl_settings['radius_dropdown'] || $wpsl_settings['results_dropdown'] ) {
  $output .= "\t\t\t" . '<div class="wpsl-select-wrap">' . "\r\n";

  if ( $wpsl_settings['radius_dropdown'] ) {
    $output .= "\t\t\t\t" . '<div id="wpsl-radius">' . "\r\n";
    $output .= "\t\t\t\t\t" . '<label for="wpsl-radius-dropdown">' . esc_html( $wpsl->i18n->get_translation( 'radius_label', __( 'Search radius', 'wpsl' ) ) ) . '</label>' . "\r\n";
    $output .= "\t\t\t\t\t" . '<select id="wpsl-radius-dropdown" class="wpsl-dropdown" name="wpsl-radius">' . "\r\n";
    $output .= "\t\t\t\t\t\t" . $this->get_dropdown_list( 'search_radius' ) . "\r\n";
    $output .= "\t\t\t\t\t" . '</select>' . "\r\n";
    $output .= "\t\t\t\t" . '</div>' . "\r\n";
  }

  if ( $wpsl_settings['results_dropdown'] ) {
    $output .= "\t\t\t\t" . '<div id="wpsl-results">' . "\r\n";
    $output .= "\t\t\t\t\t" . '<label for="wpsl-results-dropdown">' . esc_html( $wpsl->i18n->get_translation( 'results_label', __( 'Results', 'wpsl' ) ) ) . '</label>' . "\r\n";
    $output .= "\t\t\t\t\t" . '<select id="wpsl-results-dropdown" class="wpsl-dropdown" name="wpsl-results">' . "\r\n";
    $output .= "\t\t\t\t\t\t" . $this->get_dropdown_list( 'max_results' ) . "\r\n";
    $output .= "\t\t\t\t\t" . '</select>' . "\r\n";
    $output .= "\t\t\t\t" . '</div>' . "\r\n";
  }

  $output .= "\t\t\t" . '</div>' . "\r\n";
}

if ( $this->use_category_filter() ) {
  $output .= '<div class="mb-2 mt-4"><strong>Product Types:</strong></div>';

  if ( MIT_STORES_REPAIR_SERVICE_PAGE_ID == $post->ID ) {
    $output .= str_replace( '</label>', '<span class="mit-stores-locator__checkbox-box"></span></label>', MIT_Core::instance()->stores->create_dealer_service_filter( MIT_STORES_SERVICE_GROUP_ID__REPAIR ) );
  } else {
    $output .= str_replace( '</label>', '<span class="mit-stores-locator__checkbox-box"></span></label>', MIT_Core::instance()->stores->create_dealer_service_filter() );
  }


  $output .= '<div class="mb-2 mt-4"><strong>Dealer Types:</strong></div>';
  // $output .= str_replace( '</label>', '<span class="mit-stores-locator__checkbox-box"></span></label>', $this->create_category_filter() );

  if ( MIT_STORES_REPAIR_SERVICE_PAGE_ID == $post->ID ) {
    $output .= str_replace( '</label>', '<span class="mit-stores-locator__checkbox-box"></span></label>', MIT_Core::instance()->stores->create_dealer_types_checkbox_filter( MIT_STORES_DEALER_TYPE_GROUP_ID__REPAIR ) );
  } else {
    $output .= str_replace( '</label>', '<span class="mit-stores-locator__checkbox-box"></span></label>', MIT_Core::instance()->stores->create_dealer_types_filter() );
  }
}

$output .= "\t\t\t\t" . '<div class="wpsl-search-btn-wrap"><button id="wpsl-search-btn" class="btn btn--default" type="submit">' . $wpsl->i18n->get_translation( 'search_btn_label', __( 'Search', 'wpsl' ) ) . '</button></div>' . "\r\n";

$output .= "\t\t" . '</form>' . "\r\n";
$output .= "\t\t" . '</div>' . "\r\n";
$output .= "\t" . '</div>' . "\r\n";
////////// wpsl-search END

$output .= "\t" . '<div id="wpsl-result-list">' . "\r\n";
$output .= "\t\t" . '<div id="wpsl-stores" ' . $autoload_class . '>' . "\r\n";
$output .= "\t\t\t" . '<ul></ul>' . "\r\n";
$output .= "\t\t" . '</div>' . "\r\n";
$output .= "\t\t" . '<div id="wpsl-direction-details">' . "\r\n";
$output .= "\t\t\t" . '<ul></ul>' . "\r\n";
$output .= "\t\t" . '</div>' . "\r\n";
$output .= "\t" . '</div>' . "\r\n";

$output .= "\t" . '</div>' . "\r\n"; // END #wpsl-result-list-wrapper

if ( $wpsl_settings['show_credits'] ) {
  $output .= "\t" . '<div class="wpsl-provided-by">' . sprintf( __( "Search provided by %sWP Store Locator%s", "wpsl" ), "<a target='_blank' href='https://wpstorelocator.co'>", "</a>" ) . '</div>' . "\r\n";
}

$output .= '</div>' . "\r\n";
$output .= '</div>' . "\r\n";
$output .= '</div>' . "\r\n";

ob_start();
?>
  <script>
    jQuery(document).ready(function($) {
      OverlayScrollbars($('#wpsl-stores'), {
        scrollbars: {
          autoHide: 'leave',
          autoHideDelay: 200,
        }
      });
    });
  </script>
<?php
$output .= ob_get_clean();

return $output;
