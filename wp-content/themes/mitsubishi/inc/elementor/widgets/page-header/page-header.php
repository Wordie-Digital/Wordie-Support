<?php

defined( 'ABSPATH' ) or exit;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

class Custom_El_Page_Header extends Widget_Base {
  public function __construct( $data = [], $args = null ) {
    parent::__construct( $data, $args );
  }

  public function get_name() {
    return 'Custom_El_Page_Header';
  }

  public function get_title() {
    return 'Page Header';
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
      'image',
      [
        'label'       => __( 'Background image', 'plugin-domain' ),
        'type'        => \Elementor\Controls_Manager::MEDIA,
        'description' => 'This will override post/page thumbnail (if exists)',
        'default'     => [
          'url' => \Elementor\Utils::get_placeholder_image_src(),
        ],
      ]
    );

    $this->add_control(
      'excerpt',
      [
        'label'       => __( 'Excerpt', 'mit' ),
        'type'        => Controls_Manager::TEXTAREA,
        'description' => 'This will override post/page excerpt (if exists)',
        'dynamic'     => [
          'active' => true,
        ],
      ]
    );

    $this->end_controls_section();

    $this->start_controls_section(
      'button_section',
      [
        'label' => 'Extra Button',
        'tab'   => Controls_Manager::TAB_CONTENT,
      ]
    );

    $this->add_control(
      'button_label',
      [
        'label'       => __( 'Button label', 'mit' ),
        'type'        => Controls_Manager::TEXT,
        'default'     => 'Read more',
        'placeholder' => '',
      ]
    );

    $this->add_control(
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

    $this->end_controls_section();
  }

  protected function render() {
    global $post;

    $uid = uniqid( 'el-page-header-' );

    $image        = $this->get_settings_for_display( 'image' );
    $excerpt      = $this->get_settings_for_display( 'excerpt' );
    $button_label = $this->get_settings_for_display( 'button_label' );
    $button_link  = $this->get_settings_for_display( 'button_link' );

    $bg_image       = false;
    $page_for_posts = get_option( 'page_for_posts', true );
    $hide_title     = is_page() && 'yes' == ( Elementor\Core\Settings\Manager::get_settings_managers( 'page' )->get_model( get_the_ID() )->get_settings_for_display( 'hide_title' ) );

    // Image URL
    if ( ! empty( $image['id'] ) ) {
      $bg_image = wp_get_attachment_image_url( $image['id'], 'full' );
    } elseif ( is_home() && $page_for_posts && has_post_thumbnail( $page_for_posts ) ) {
      $bg_image = get_the_post_thumbnail_url( $page_for_posts, 'full' );
    } elseif ( is_singular() && has_post_thumbnail() ) {
      $bg_image = get_the_post_thumbnail_url( $post, 'full' );
    }

    ?>
    <div id="<?= $uid ?>" class="el-page-header <?= $bg_image ? 'el-page-header--has-bg' : '' ?> <?= $hide_title ? 'el-page-header--hide-title' : '' ?>" <?php if ( $bg_image ) : ?>style="background-image: url('<?= $bg_image ?>');"<?php endif; ?>>
      <div class="el-page-header__inner">
        <div class="container">
          <? get_template_part( 'template-parts/breadcrumbs/breadcrumbs' ) ?>
        </div>

        <div class="el-page-header__content">
          <div class="container text-center">
            <h1 class="wow animate__fadeInUp"><?= MIT_Core::instance()->helpers->get_page_title() ?></h1>

            <div class="el-page-header__excerpt wow animate__fadeInUp">
              <?
              // Excerpt
              if ( ! empty( $excerpt ) ) {
                echo wpautop( $excerpt );
              } elseif ( is_home() && $page_for_posts ) {
                echo wpautop( get_the_excerpt( $page_for_posts ) );
              } elseif ( ! empty( $post->post_excerpt ) ) {
                echo wpautop( get_the_excerpt() );
              }
              ?>

              <?php if ( ! empty( $button_link['url'] ) ) : ?>
                <div class="mt-3">
                  <a href="<?= esc_url( $button_link['url'] ) ?>" class="btn btn--default"><?= $button_label ?></a>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php
  }
}
