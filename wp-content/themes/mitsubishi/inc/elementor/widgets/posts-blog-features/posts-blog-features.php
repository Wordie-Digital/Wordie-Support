<?php

defined( 'ABSPATH' ) or exit;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

class Custom_El_Posts_Blog_Features extends Widget_Base {
  public function __construct( $data = [], $args = null ) {
    parent::__construct( $data, $args );
  }

  public function get_name() {
    return 'Custom_El_Featured_Posts';
  }

  public function get_title() {
    return 'Featured Posts';
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
      'message',
      [
        'label'     => __( 'Used on Blog page only', 'plugin-name' ),
        'type'      => \Elementor\Controls_Manager::HEADING,
        'separator' => 'after',
      ]
    );

    $this->end_controls_section();
  }

  protected function render() {
    $uid            = uniqid( 'el-posts-blog-features-' );
    $blog_page      = get_option( 'page_for_posts', true );
    $featured_posts = get_field( 'featured_posts', $blog_page );

    ?>
    <div id="<?= $uid ?>" class="el-posts-blog-features">
      <?
      if ( is_home() && ! empty( $featured_posts ) ) {
        get_template_part( 'template-parts/section-posts-slider/section-posts-slider', null, [
          'posts_list' => array_map( function ( $featured_post ) {
            return $featured_post['post'];
          }, $featured_posts ),
        ] );
      }
      ?>
    </div>
    <?php
  }
}
