<?php

defined( 'ABSPATH' ) or exit;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

class Custom_Porel_Products_Docs extends Widget_Base {
  public function __construct( $data = [], $args = null ) {
    parent::__construct( $data, $args );
  }

  public function get_name() {
    return 'Custom_Porel_Products_Docs';
  }

  public function get_title() {
    return 'Products Docs Grid';
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
      'term_slug',
      [
        'label'    => __( 'Parent Products Category', 'plugin-domain' ),
        'type'     => \Elementor\Controls_Manager::SELECT2,
        'multiple' => false,
        'options'  => call_user_func( function () {
          $terms = get_terms( [
            'taxonomy'   => 'product_cat',
            'hide_empty' => false,
            'parent'     => 0,
          ] );

          $options = [
            '__current_query' => 'Current Query',
          ];

          foreach ( $terms as $term ) {
            $options[ $term->slug ] = $term->name;
          }

          return $options;
        } ),
      ]
    );

    $this->end_controls_section();
  }

  protected function render() {
    $uid = uniqid( 'porel-products-docs-' );

    $parent_term_slug = $this->get_settings_for_display( 'term_slug' );
    $filter_id        = 'por_products_tech_docs_filter';

    // For current query option
    if ( '__current_query' == $parent_term_slug ) {
      $queried_object   = get_queried_object();
      $parent_term_slug = $queried_object->slug;
    }

    if ( ! empty( $parent_term_slug ) ) : ?>
      <div id="<?= $uid ?>" class="porel-products-docs">
        <div class="row">
          <div class="col-xl-4">
            <ul class="nav nav-tabs nav-tabs--full-width" role="tablist">
              <li class="nav-item" role="presentation">
                <input type="radio" class="btn-check" name="archivedModels" id="<?= $uid ?>-option-current" value="0" autocomplete="off" checked>
                <label class="nav-link active" data-title="Current Models" for="<?= $uid ?>-option-current" data-bs-toggle="tab" type="button" role="tab">Current Models</label>
              </li>

              <li class="nav-item" role="presentation">
                <input type="radio" class="btn-check" name="archivedModels" id="<?= $uid ?>-option-archive" value="1" autocomplete="off">
                <label class="nav-link" data-title="Archived Models" for="<?= $uid ?>-option-archive" data-bs-toggle="tab" type="button" role="tab">Archived Models</label>
              </li>
            </ul>

            <div class="tab-content">
              <?
              add_filter( "alm_filters_{$filter_id}_product_cat_args", function ( $args ) use ( $parent_term_slug ) {
                $queried_object = get_term_by( 'slug', $parent_term_slug, 'product_cat' );

                $args['child_of'] = $queried_object->term_id;
                $args['orderby']  = 'menu_order';

                return $args;
              } );

              add_filter( "alm_filters_{$filter_id}_product_cat_default", function () use ( $parent_term_slug ) {
                return $parent_term_slug;
              } );

              echo do_shortcode( '[ajax_load_more_filters id="' . $filter_id . '" target="' . $uid . '"]' );
              ?>
            </div>
          </div>

          <div class="col-xl-8">
            <div class="porel-products-docs__docs-results" style="display: none;">
              <div class="porel-products-docs__docs-results-inner"></div>
            </div>

            <form class="porel-products-docs__search-form porel-generic__search-form" onsubmit="return false;">
              <div class="input-group input-group-navbar">
                <button type="button"><i class="align-middle" data-feather="search"></i></button>
                <input type="search" class="form-control" placeholder="Search…" aria-label="Search">
              </div>
            </form>

            <div class="porel-products-docs__products">
              <div class="porel-products-docs__products-inner">
                <?
                ////////////////////////////////////////////////////////////////
                // Infinite ajax load more posts
                $args = [
                  'id'                           => $uid,
                  'container_type'               => 'div',
                  'post_type'                    => 'product',
                  'posts_per_page'               => 24,
                  'preloaded'                    => 'true',
                  'preloaded_amount'             => 24,
                  'transition_container_classes' => "row row-cols-2 row-cols-lg-3 row-cols-xl-4",
                  'images_loaded'                => 'true',
                  'archive'                      => 'false',
                  'pause'                        => 'true',
                  'scroll'                       => 'false',
                  'no_results_text'              => POR_ALM_NO_RESULTS_TEXT,
                  'meta_key'                     => 'is_archived_model:is_archived_model:is_archived_model',
                  'meta_value'                   => '0: :0',
                  'meta_type'                    => 'CHAR:CHAR:CHAR',
                  'meta_compare'                 => '=:=:NOT EXISTS',
                  'meta_relation'                => 'OR',
                ];

                // Filters
                if ( ! empty( $parent_term_slug ) ) {
                  $args['taxonomy']          = 'product_cat';
                  $args['taxonomy_terms']    = $parent_term_slug;
                  $args['taxonomy_operator'] = "IN";
                }

                if ( ! empty( $filter_id ) ) {
                  $args['filters'] = 'true';
                  $args['target']  = $filter_id;
                }

                // Render
                if ( function_exists( 'alm_render' ) ) {
                  alm_render( $args );
                }
                ?>
              </div>
            </div>
          </div>
        </div>

        <script>
          /* global ajaxloadmore */
          jQuery(function($) {
            const $docsWrapper = $('<?="#$uid"?> .porel-products-docs__docs-results');
            const $docsWrapperInner = $docsWrapper.find('.porel-products-docs__docs-results-inner');

            const reloadAjaxLoadMore = function(e) {
              e.preventDefault();

              const isArchivedModels = $('<?="#{$uid} [name=\"archivedModels\"]:checked"?>').val();
              const search = $('<?="#{$uid} .porel-products-docs__search-form [type=\"search\"]"?>').val();

              if ('ajaxloadmore' in window) {
                const transition = 'fade';
                const speed = 250;
                let meta = {
                  search: search
                };

                if (isArchivedModels === '1') {
                  meta = {
                    'meta-key': 'is_archived_model',
                    'meta-value': '1',
                    'meta-type': 'CHAR',
                    'meta-compare': '=',
                    ...meta
                  };
                } else {
                  meta = {
                    'meta-key': 'is_archived_model:is_archived_model:is_archived_model',
                    'meta-value': '0: :0',
                    'meta-type': 'CHAR:CHAR:CHAR',
                    'meta-compare': '=:=:NOT EXISTS',
                    'meta-relation': 'OR',
                    ...meta
                  };
                }

                // Call core Ajax Load More `filter` function.
                // @see https://connekthq.com/plugins/ajax-load-more/docs/public-functions/#filter
                ajaxloadmore.filter(transition, speed, {
                  target: '<?= $uid ?>',
                  pause: 'false',
                  ...meta
                });
              }
            };

            // Triggered on tab changed
            $('<?="#{$uid} [name=\"archivedModels\"]"?>').on('change', reloadAjaxLoadMore);

            // Triggered on search input throttle
            const throttleSearching = _.debounce(reloadAjaxLoadMore, 1000);
            $('<?="#{$uid} .porel-products-docs__search-form [type=\"search\"]"?>').bind('input', throttleSearching);

            // Toggle downloads
            $('<?="#{$uid}"?>').on('click', '.por-post-product__open-docs', function(e) {
              e.preventDefault();

              $.ajax({
                type: 'GET',
                url: Portal.ajaxUrl,
                dataType: 'json',
                beforeSend: function() {
                  $docsWrapperInner.html('Loading...');
                  $docsWrapper.fadeIn('slow', function() {
                    $('html, body').animate({scrollTop: 0}, 'fast');
                  });
                },
                data: {
                  action: 'get_products_docs_html',
                  productId: $(this).data('product-id'),
                },
                success: function(response) {
                  if (response.success) {
                    $docsWrapperInner.html(response.data.html);
                  } else {
                    $docsWrapperInner.text('Something went wrong, please try again later!');
                  }

                  feather.replace();
                },
              });
            });

            $('<?="#{$uid}"?>').on('click', '.por-post-product__close-docs', function(e) {
              e.preventDefault();

              $docsWrapper.fadeOut();
            });

            $('<?="#{$uid}"?>').on('mousedown touchstart', '.alm-filter--link[data-type="checkbox"]', function(e) {
              const $parent = $(this).closest('ul').parent('.alm-filter--checkbox').children('.alm-filter--link[data-type="checkbox"].active');
              if ($parent.length) {
                $parent.removeClass('active');
                $parent.attr('aria-checked', 'false');
              }
            });

            // Scrollbars (removed as per https://app.asana.com/0/1202216047741109/1204055316444455)
            /*OverlayScrollbars($('.porel-products-docs__products-inner, .porel-products-docs__docs-results'), {
              overflowBehavior: {
                x: 'hidden',
                y: 'scroll',
              },
              scrollbars: {
                autoHide: 'leave',
                autoHideDelay: 200,
              }
            });*/
          });
        </script>
      </div>
    <?php endif;
  }
}
