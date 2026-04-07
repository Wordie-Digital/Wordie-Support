<?php

defined( 'ABSPATH' ) or exit;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;

class Custom_El_Text_Image extends Widget_Base {
  public function __construct( $data = [], $args = null ) {
    parent::__construct( $data, $args );
  }

  public function get_name() {
    return 'Custom_El_Text_Image';
  }

  public function get_title() {
    return 'Text + Image';
  }

  public function get_icon() {
    return 'eicon-custom';
  }

  public function get_categories() {
    return [ 'custom' ];
  }

  public function get_script_depends() {
    return [ 'theme-swiper-js' ];
  }

  public function get_style_depends() {
    return [ 'theme-swiper-css' ];
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
      'message',
      [
        'label'     => __( 'The section containing this widget should be full width + no gap', 'plugin-name' ),
        'type'      => \Elementor\Controls_Manager::HEADING,
        'separator' => 'after',
      ]
    );

    $this->add_control(
      'enable_breadcrumb',
      [
        'label'        => __( 'Enable breadcrumb', 'plugin-domain' ),
        'type'         => \Elementor\Controls_Manager::SWITCHER,
        'label_on'     => __( 'Yes', 'your-plugin' ),
        'label_off'    => __( 'No', 'your-plugin' ),
        'return_value' => 'yes',
        'default'      => 'no',
      ]
    );

    $this->add_control(
      'is_slider',
      [
        'label'        => __( 'Turn to slider', 'plugin-domain' ),
        'type'         => \Elementor\Controls_Manager::SWITCHER,
        'label_on'     => __( 'Yes', 'your-plugin' ),
        'label_off'    => __( 'No', 'your-plugin' ),
        'return_value' => 'yes',
        'default'      => 'no',
      ]
    );

    $repeater = new Repeater();

    $repeater->add_control(
      'style',
      [
        'label'    => __( 'Styles', 'plugin-domain' ),
        'type'     => \Elementor\Controls_Manager::SELECT,
        'multiple' => false,
        'default'  => 'style-1',
        'options'  => [
          'style-1' => 'Style 1',
          'style-2' => 'Style 2',
        ],
      ]
    );

    $repeater->add_control(
      'layout',
      [
        'label'    => __( 'Layout', 'plugin-domain' ),
        'type'     => \Elementor\Controls_Manager::SELECT,
        'multiple' => false,
        'default'  => 'image-left',
        'options'  => [
          'image-left'  => 'Image | Text',
          'image-right' => 'Text | Image',
        ],
      ]
    );

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
        'label'   => __( 'Button Label', 'mit' ),
        'type'    => Controls_Manager::TEXT,
        'default' => 'Learn More',
      ]
    );

    $repeater->add_control(
      'button_link',
      [
        'label'       => __( 'Button Link', 'elementor' ),
        'type'        => Controls_Manager::URL,
        'dynamic'     => [
          'active' => true,
        ],
        'separator'   => 'after',
        'placeholder' => __( 'https://your-link.com', 'elementor' ),
      ]
    );

    $repeater->add_control(
      'button_label_2',
      [
        'label'   => __( 'Button Label | Secondary', 'mit' ),
        'type'    => Controls_Manager::TEXT,
        'default' => 'Learn More',
      ]
    );

    $repeater->add_control(
      'button_link_2',
      [
        'label'       => __( 'Button Link | Secondary', 'elementor' ),
        'type'        => Controls_Manager::URL,
        'dynamic'     => [
          'active' => true,
        ],
        'placeholder' => __( 'https://your-link.com', 'elementor' ),
      ]
    );

    $this->add_control(
      'slides',
      [
        'label'  => __( 'Slides', 'mit' ),
        'type'   => Controls_Manager::REPEATER,
        'fields' => $repeater->get_controls(),
      ]
    );

    $this->end_controls_section();
  }

  protected function render() {
    $uid = uniqid( 'el-text-image-' );

    $MIT_helpers = MIT_Core::instance()->helpers;

    $slides            = $this->get_settings_for_display( 'slides' );
    $is_slider         = $this->get_settings_for_display( 'is_slider' ) == 'yes' && count( $slides ) > 1;
    $enable_breadcrumb = $this->get_settings_for_display( 'enable_breadcrumb' ) == 'yes';

    if ( ! empty( $slides ) ) : ?>
      <div id="<?= $uid ?>" class="el-text-image">
        <?php if ( $enable_breadcrumb ) : ?>
          <div class="el-text-image__breadcrumb">
            <div class="container">
              <? get_template_part( 'template-parts/breadcrumbs/breadcrumbs' ) ?>
            </div>
          </div>
        <?php endif; ?>

        <div class="<?= $is_slider ? 'swiper' : '' ?>">
          <div class="<?= $is_slider ? 'swiper-wrapper' : '' ?>">
            <?php foreach ( $slides as $slide ) : ?>
              <div class="<?= $is_slider ? 'swiper-slide' : '' ?>">
                <?
                $style   = $slide['style'];
                $layout  = $slide['layout'];
                $image   = $slide['image'];
                $content = $slide['content'];

                $button_label = $slide['button_label'];
                $button_link  = $slide['button_link'];

                $button_label_2 = $slide['button_label_2'];
                $button_link_2  = $slide['button_link_2'];

                switch ( $style ) {
                  case 'style-1':
                    get_template_part( 'template-parts/text-image-v1/text-image-v1', null, [
                      'layout'         => $layout,
                      'image_url'      => ! empty( $image['id'] ) ? wp_get_attachment_image_url( $image['id'], 'full' ) : $MIT_helpers->get_assets_path( 'images/placeholder.png' ),
                      'image_alt'      => get_the_title( $image['id'] ),
                      'content'        => $content,
                      'button_link'    => ! empty( $button_link['url'] ) ? $button_link['url'] : '',
                      'button_label'   => $button_label,
                      'button_link_2'  => ! empty( $button_link_2['url'] ) ? $button_link_2['url'] : '',
                      'button_label_2' => $button_label_2,
                    ] );
                    break;
                  case 'style-2':
                    get_template_part( 'template-parts/text-image-v2/text-image-v2', null, [
                      'layout'         => $layout,
                      'image_url'      => ! empty( $image['id'] ) ? wp_get_attachment_image_url( $image['id'], 'full' ) : $MIT_helpers->get_assets_path( 'images/placeholder.png' ),
                      'image_alt'      => get_the_title( $image['id'] ),
                      'content'        => $content,
                      'button_link'    => ! empty( $button_link['url'] ) ? $button_link['url'] : '',
                      'button_label'   => $button_label,
                      'button_link_2'  => ! empty( $button_link_2['url'] ) ? $button_link_2['url'] : '',
                      'button_label_2' => $button_label_2,
                    ] );
                    break;
                }
                ?>
              </div>
            <?php endforeach; ?>
          </div>

          <?php if ( $is_slider ) : ?>
            <!-- If we need pagination -->
            <div class="swiper-pagination"></div>

            <!-- If we need navigation buttons -->
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
          <?php endif; ?>
        </div>

        <?php if ( $is_slider ) : ?>
          <script>
            jQuery(document).ready(function($) {
              const swiper = new Swiper('#<?= $uid ?> .swiper', {
                parallax: true,
                loop: false,
                speed: 600,
                resizeObserver: true,
                observeParents: true,
                navigation: {
                  nextEl: '#<?= $uid ?> .swiper-button-next',
                  prevEl: '#<?= $uid ?> .swiper-button-prev',
                },
              });
            });
          </script>
        <?php endif; ?>
      </div>
    <?php endif;
  }
}
