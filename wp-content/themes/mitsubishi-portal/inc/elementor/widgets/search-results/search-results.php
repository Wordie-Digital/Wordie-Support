<?php

defined( 'ABSPATH' ) or exit;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

class Custom_Porel_Search_Results extends Widget_Base {
  public function __construct( $data = [], $args = null ) {
    parent::__construct( $data, $args );
  }

  public function get_name() {
    return 'Custom_Porel_Search_Results';
  }

  public function get_title() {
    return 'Search Results';
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
        'label'     => __( 'Use on search page only', 'plugin-name' ),
        'type'      => \Elementor\Controls_Manager::HEADING,
        'separator' => 'after',
      ]
    );

    $this->end_controls_section();
  }

  protected function render() {
    $uid = uniqid( 'porel-search-results-' );

    ?>
    <div id="<?= $uid ?>" class="porel-search-results">
      <?php if ( have_posts() ) : ?>
        <div class="row row-cols-2 row-cols-lg-3 row-cols-xxl-4">
          <?php while ( have_posts() ) : the_post(); ?>
            <? get_template_part( 'alm_templates/default' ); ?>
          <? endwhile; ?>
        </div>

        <?php
        the_posts_navigation( [
          'prev_text' => __( 'Next &raquo;' ),
          'next_text' => __( '&laquo; Previous' ),
        ] );
      else : ?>
        <p class="py-4 text-center">No results found!</p>
      <? endif; ?>
    </div>
    <?php
  }
}
