<?php

defined( 'ABSPATH' ) or exit;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;

class Custom_El_Cards_Grid extends Widget_Base {
  public function __construct( $data = [], $args = null ) {
    parent::__construct( $data, $args );
  }

  public function get_name() {
    return 'Custom_El_Cards_Grid';
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
          'pages'             => 'Selected Pages',
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
      'button_label',
      [
        'label'   => __( 'Link Label', 'mit' ),
        'type'    => Controls_Manager::TEXT,
        'default' => 'Learn More',
      ]
    );

    $repeater->add_control(
      'button_link',
      [
        'label'       => __( 'Link', 'elementor' ),
        'type'        => Controls_Manager::URL,
        'dynamic'     => [
          'active' => true,
        ],
        'placeholder' => __( 'https://your-link.com', 'elementor' ),
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

    $this->add_control(
      'selected_pages',
      [
        'label'       => __( 'Selected Pages', 'plugin-domain' ),
        'description' => 'Select pages to show in grid',
        'type'        => \Elementor\Controls_Manager::SELECT2,
        'multiple'    => true,
        'condition'   => [
          'type' => [ 'pages' ],
        ],
        'options'     => call_user_func( function () {
          $pages = get_posts( [
            'post_type'      => 'page',
            'post_status'    => 'publish',
            'posts_per_page' => - 1,
          ] );

          $options = [];

          foreach ( $pages as $page ) {
            $options[ $page->ID ] = $page->post_title;
          }

          return $options;
        } ),
      ]
    );

    $this->end_controls_section();
  }

  protected function render() {
    global $post;

    $uid = uniqid( 'el-cards-grid-' );

    $MIT_helpers = MIT_Core::instance()->helpers;

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
            'image'        => [
              'id'  => get_term_meta( $term->term_id, 'thumbnail_id', true ),
              'alt' => $term->name,
            ],
            'content'      => "<h3 class='text-uppercase'>{$term->name}</h3>" . wp_trim_words( $term->description ),
            'button_label' => 'VIEW OUR RANGE',
            'button_link'  => [
              'url' => get_term_link( $term, 'product_cat' ),
            ],
          ];
        }, $child_terms );

        break;

      case 'pages':
        $cards = $this->get_settings_for_display( 'selected_pages' );
        break;

      default:
        $cards = $this->get_settings_for_display( 'cards' );
    }

    if ( ! empty( $cards ) ) : ?>
      <div id="<?= $uid ?>" class="el-cards-grid">
        <div class="row">
          <?php foreach ( $cards as $card ) : ?>
            <div class="col-lg-4 my-3 my-lg-4">
              <?
              switch ( $type ) {
                case 'pages':
                  $post = get_post( $card );
                  setup_postdata( $post );
                  get_template_part( 'template-parts/post-news/post-news' );
                  break;

                default:
                  $image        = $card['image'];
                  $content      = $card['content'];
                  $button_label = $card['button_label'];
                  $button_link  = $card['button_link'];

                  get_template_part( 'template-parts/card/card', null, [
                    'image_url'    => ! empty( $image['id'] ) ? wp_get_attachment_image_url( $image['id'], 'medium_large' ) : $MIT_helpers->get_assets_path( 'images/placeholder.png' ),
                    'image_alt'    => get_the_title( $image['id'] ),
                    'content'      => $content,
                    'button_link'  => ! empty( $button_link['url'] ) ? $button_link['url'] : '',
                    'button_label' => $button_label,
                  ] );
              }
              ?>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif;

    wp_reset_postdata();
  }
}
