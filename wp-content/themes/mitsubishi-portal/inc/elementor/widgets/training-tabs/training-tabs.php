<?php

defined( 'ABSPATH' ) or exit;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

class Custom_Porel_Training_Tabs extends Widget_Base {
  public function __construct( $data = [], $args = null ) {
    parent::__construct( $data, $args );
  }

  public function get_name() {
    return 'Custom_Porel_Training_Tabs';
  }

  public function get_title() {
    return 'Training Tabs';
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
        'label'     => __( 'List all available trainings as tabs', 'plugin-name' ),
        'type'      => \Elementor\Controls_Manager::HEADING,
        'separator' => 'after',
      ]
    );

    $this->end_controls_section();
  }

  protected function render() {
    $uid = uniqid( 'porel-training-tabs-' );

    $trainings = get_posts( [
      'post_type'      => 'cpt-training',
      'post_status'    => 'publish',
      'posts_per_page' => - 1,
    ] );

    if ( ! empty( $trainings ) ) : ?>
      <div id="<?= $uid ?>" class="porel-training-tabs">
        <ul class="nav nav-tabs" role="tablist">
          <?php foreach ( $trainings as $index => $training ) : ?>
            <li class="nav-item" role="presentation">
              <button
                class="nav-link <?= 0 == $index ? 'active' : '' ?>"
                id="<?= $uid ?>-tab-<?= $training->post_name ?>"
                data-bs-toggle="tab"
                data-bs-target="#<?= $uid ?>-tab-content-<?= $training->post_name ?>"
                data-title="<?= $training->post_title ?>"
                type="button"
                role="tab"
                aria-controls="<?= $uid ?>-tab-content-<?= $training->post_name ?>"
                aria-selected="<?= 0 == $index ? 'true' : 'false' ?>"
              ><?= $training->post_title ?></button>
            </li>
          <?php endforeach; ?>
        </ul>

        <div class="tab-content">
          <?php foreach ( $trainings as $index => $training ) : ?>
            <div
              class="tab-pane fade <?= 0 == $index ? 'show active' : '' ?>"
              id="<?= $uid ?>-tab-content-<?= $training->post_name ?>"
              role="tabpanel"
              aria-labelledby="<?= $uid ?>-tab-<?= $training->post_name ?>"
            >
              <?= wpautop( $training->post_content ) ?>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>
    <?php
  }
}
