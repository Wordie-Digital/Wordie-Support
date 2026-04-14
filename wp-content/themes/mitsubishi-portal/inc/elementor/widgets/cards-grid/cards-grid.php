<?php

defined( 'ABSPATH' ) or exit;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;

class Custom_Porel_Cards_Grid extends Widget_Base {
  public function __construct( $data = [], $args = null ) {
    parent::__construct( $data, $args );
  }

  public function get_name() {
    return 'Custom_Porel_Cards_Grid';
  }

  public function get_title() {
    return 'Cards Grid';
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
      'type',
      [
        'label'   => __( 'Grid Type', 'plugin-domain' ),
        'type'    => Controls_Manager::SELECT,
        'options' => [
          'free_cards'        => 'Free Cards',
          'product_cat_child' => 'Products Category\'s Children',
        ],
        'default' => 'free_cards',
      ]
    );

    $repeater = new Repeater();

    $repeater->add_control(
      'image',
      [
        'label'   => __( 'Image', 'plugin-domain' ),
        'type'    => \Elementor\Controls_Manager::MEDIA,
        'default' => [
          'url' => \Elementor\Utils::get_placeholder_image_src(),
        ],
      ]
    );

    $repeater->add_control(
      'content', [
        'label'      => __( 'Content', 'plugin-domain' ),
        'type'       => \Elementor\Controls_Manager::WYSIWYG,
        'show_label' => false,
      ]
    );

    $repeater->add_control(
      'link',
      [
        'label'       => __( 'Link', 'elementor' ),
        'type'        => Controls_Manager::URL,
        'dynamic'     => [
          'active' => true,
        ],
        'placeholder' => __( 'https://your-link.com', 'elementor' ),
      ]
    );

    $repeater->add_control(
      'classes',
      [
        'label' => __( 'Custom classes', 'elementor' ),
        'type'  => Controls_Manager::TEXT,
      ]
    );

    $this->add_control(
      'cards',
      [
        'label'     => __( 'Cards', 'mit' ),
        'type'      => Controls_Manager::REPEATER,
        'fields'    => $repeater->get_controls(),
        'condition' => [
          'type' => [ 'free_cards' ],
        ],
      ]
    );

    $this->add_control(
      'product_cat_parent',
      [
        'label'       => __( 'Parent Category', 'plugin-domain' ),
        'description' => 'All chosen category\'s children will be shown as cards',
        'type'        => \Elementor\Controls_Manager::SELECT2,
        'multiple'    => false,
        'condition'   => [
          'type' => [ 'product_cat_child' ],
        ],
        'options'     => call_user_func( function () {
          $terms = get_terms( [
            'taxonomy'   => 'product_cat',
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
    $uid = uniqid( 'porel-cards-grid-' );

    $MIT_helpers = POR_Core::instance()->helpers;

    $type = $this->get_settings_for_display( 'type' );

    switch ( $type ) {
      case 'product_cat_child':
        $product_cat_parent = $this->get_settings_for_display( 'product_cat_parent' );

        $child_terms = get_terms( [
          'taxonomy'   => 'product_cat',
          'hide_empty' => false,
          'parent'     => @get_term_by( 'slug', $product_cat_parent, 'product_cat' )->term_id,
        ] );

        $cards = array_map( function ( $term ) {
          // Mimic Elementor fields
          return [
            'image'   => [
              'id'  => get_term_meta( $term->term_id, 'thumbnail_id', true ),
              'alt' => $term->name,
            ],
            'content' => "<h3>{$term->name}</h3>" . wp_trim_words( $term->description ),
            'link'    => [
              'url' => get_term_link( $term, 'product_cat' ),
            ],
          ];
        }, $child_terms );

        break;

      default:
        $cards = $this->get_settings_for_display( 'cards' );
    }

    if ( ! empty( $cards ) ) : ?>
      <div id="<?= $uid ?>" class="porel-cards-grid">
        <div class="row row-cols-<?= min( count( $cards ), 3 ) ?>">
          <?php foreach ( $cards as $card ) : ?>
            <div class="col mt-3 mb-1 <?= esc_attr( $card['classes'] ) ?>">
              <?
              $image   = $card['image'];
              $content = $card['content'];
              $link    = $card['link'];

              get_template_part( 'template-parts/card/card', null, [
                'image_url'    => ! empty( $image['id'] ) ? wp_get_attachment_image_url( $image['id'], 'medium_large' ) : $MIT_helpers->get_assets_path( 'images/placeholder.png' ),
                'image_alt'    => get_the_title( $image['id'] ),
                'content'      => $content,
                'is_landscape' => count( $cards ) < 3,
                'link'         => ! empty( $link['url'] ) ? $link['url'] : '#',
              ] );
              ?>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif;
  }
}
