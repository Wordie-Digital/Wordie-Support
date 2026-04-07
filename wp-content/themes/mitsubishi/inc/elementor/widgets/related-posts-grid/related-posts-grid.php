<?php

defined( 'ABSPATH' ) or exit;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

class Custom_El_Related_Posts_Grid extends Widget_Base {
  public function __construct( $data = [], $args = null ) {
    parent::__construct( $data, $args );
  }

  public function get_name() {
    return 'Custom_El_Related_Posts_Grid';
  }

  public function get_title() {
    return 'Related Posts Grid';
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
      'setting_section',
      [
        'label' => 'Settings',
        'tab'   => Controls_Manager::TAB_CONTENT,
      ]
    );

    $this->add_control(
      'message',
      [
        'label' => __( 'Related posts', 'plugin-name' ),
        'type'  => \Elementor\Controls_Manager::HEADING,
      ]
    );

    $this->end_controls_section();
  }

  protected function render() {
    global $post;

    if ( ! $post ) {
      return;
    }

    $uid = uniqid( 'el-related-posts-grid-' );

    $related_posts = MIT_Core::instance()->helpers->get_related_posts( get_the_ID(), 3, $post->post_type );

    if ( ! empty( $related_posts ) ) : ?>
      <div id="<?= $uid ?>" class="el-related-posts-grid">
        <div class="el-related-posts-grid__posts">
          <div class="row row-cols-3">
            <?php foreach ( $related_posts as $related_post ) : ?>
              <?
              $post = $related_post;
              setup_postdata( $post );
              get_template_part( 'alm_templates/default' );
              ?>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    <?php endif; ?>
    <?php

    wp_reset_postdata();
  }
}
