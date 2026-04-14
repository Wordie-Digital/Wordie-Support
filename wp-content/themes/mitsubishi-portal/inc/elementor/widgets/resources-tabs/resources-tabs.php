<?php

defined( 'ABSPATH' ) or exit;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

class Custom_Porel_Resources_Tabs extends Widget_Base {
  public function __construct( $data = [], $args = null ) {
    parent::__construct( $data, $args );
  }

  public function get_name() {
    return 'Custom_Porel_Resources_Tabs';
  }

  public function get_title() {
    return 'Resources Tabs';
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
      'layout',
      [
        'label'    => __( 'Layout', 'plugin-domain' ),
        'type'     => \Elementor\Controls_Manager::SELECT,
        'multiple' => false,
        'default'  => 'cards',
        'options'  => [
          'cards' => 'Cards',
          'list'  => 'List',
        ],
      ]
    );

    $this->add_control(
      'term_slug',
      [
        'label'    => __( 'Parent Resources Category', 'plugin-domain' ),
        'type'     => \Elementor\Controls_Manager::SELECT2,
        'multiple' => false,
        'options'  => call_user_func( function () {
          $terms = get_terms( [
            'taxonomy'   => 'resource_category',
            'hide_empty' => false,
            'parent'     => 0,
          ] );

          $options = [];

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
    $uid = uniqid( 'porel-resources-tabs-' );

    $layout           = $this->get_settings_for_display( 'layout' ) ?: 'cards';
    $parent_term_slug = $this->get_settings_for_display( 'term_slug' );

    if ( ! empty( $parent_term_slug ) ) :
      $child_terms = get_terms( [
        'taxonomy'   => 'resource_category',
        'hide_empty' => true,
        'parent'     => @get_term_by( 'slug', $parent_term_slug, 'resource_category' )->term_id,
      ] );

      if ( ! is_wp_error( $child_terms ) && ! empty( $child_terms ) ) : ?>
        <div id="<?= $uid ?>" class="porel-resources-tabs">
          <ul class="nav nav-tabs" role="tablist">
            <?php foreach ( array_values( $child_terms ) as $index => $child_term ) : ?>
              <li class="nav-item" role="presentation">
                <input type="radio" class="btn-check" name="resourceTerm" id="<?= $uid ?>-option-<?= $child_term->slug ?>" value="<?= $child_term->slug ?>" autocomplete="off" <?= 0 == $index ? 'checked' : '' ?>>
                <label data-title="<?= $child_term->name ?>" class="nav-link <?= 0 == $index ? 'active' : '' ?>" for="<?= $uid ?>-option-<?= $child_term->slug ?>" data-bs-toggle="tab" type="button" role="tab"><?= $child_term->name ?></label>
              </li>
            <?php endforeach; ?>
          </ul>

          <div class="tab-content">
            <form class="porel-resources-tabs__search-form porel-generic__search-form" onsubmit="return false;">
              <div class="input-group input-group-navbar">
                <button type="button"><i class="align-middle" data-feather="search"></i></button>
                <input type="search" class="form-control" placeholder="Search…" aria-label="Search">
              </div>
            </form>

            <?
            ////////////////////////////////////////////////////////////////
            // Infinite ajax load more posts
            $args = [
              'id'                           => $uid,
              'container_type'               => 'div',
              'post_type'                    => 'cpt-resource',
              'posts_per_page'               => ( 'cards' == $layout ? 8 : 10 ),
              'preloaded'                    => 'true',
              'preloaded_amount'             => ( 'cards' == $layout ? 8 : 10 ),
              'transition_container_classes' => "row row-cols-2 row-cols-xl-" . ( 'cards' == $layout ? '4' : '1' ),
              'images_loaded'                => 'true',
              'archive'                      => 'false',
              'pause'                        => 'true',
              'scroll'                       => 'false',
              'vars'                         => "layout:{$layout}",
              'no_results_text'              => POR_ALM_NO_RESULTS_TEXT,
            ];

            // Filters
            if ( ! empty( $child_terms ) ) {
              $args['taxonomy']          = 'resource_category';
              $args['taxonomy_terms']    = array_values( $child_terms )[0]->slug;
              $args['taxonomy_operator'] = "IN";
            }

            // Render
            if ( function_exists( 'alm_render' ) ) {
              alm_render( $args );
            }
            ?>

            <script>
              /* global ajaxloadmore */
              jQuery(document).ready(function($) {
                const reloadAjaxLoadMore = function(e) {
                  e.preventDefault();

                  const selectedTerm = $('<?="#{$uid} [name=\"resourceTerm\"]:checked"?>').val();
                  const search = $('<?="#{$uid} .porel-resources-tabs__search-form [type=\"search\"]"?>').val();

                  if ('ajaxloadmore' in window) {
                    const transition = 'fade';
                    const speed = 250;
                    let meta = {
                      search: search,
                      taxonomy: 'resource_category',
                      'taxonomy-terms': selectedTerm,
                      'taxonomy-operator': 'IN',
                    };

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
                $('<?="#{$uid} [name=\"resourceTerm\"]"?>').on('change', reloadAjaxLoadMore);

                // Triggered on search input throttle
                const throttleSearching = _.debounce(reloadAjaxLoadMore, 1000);
                $('<?="#{$uid} .porel-resources-tabs__search-form [type=\"search\"]"?>').bind('input', throttleSearching);
              });
            </script>
          </div>
        </div>
      <?php endif; ?>
    <?php endif;
  }
}
