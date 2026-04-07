<?php

defined( 'ABSPATH' ) or exit;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;

class Custom_El_Links_Nav extends Widget_Base {
  public function __construct( $data = [], $args = null ) {
    parent::__construct( $data, $args );
  }

  public function get_name() {
    return 'Custom_El_Links_Nav';
  }

  public function get_title() {
    return 'Links Nav';
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
        'label'   => __( 'List Type', 'plugin-domain' ),
        'type'    => Controls_Manager::SELECT,
        'options' => [
          'free_items' => 'Free Items',
          'acf'        => 'ACF field',
        ],
        'default' => 'free_items',
      ]
    );

    $repeater = new Repeater();

    $repeater->add_control(
      'label',
      [
        'label' => __( 'Label', 'mit' ),
        'type'  => Controls_Manager::TEXT,
      ]
    );

    $repeater->add_control(
      'link',
      [
        'label'       => __( 'Link', 'elementor' ),
        'type'        => Controls_Manager::URL,
        'title_field' => '{{{ subheading }}}',
        'dynamic'     => [
          'active' => true,
        ],
        'placeholder' => __( 'https://your-link.com', 'elementor' ),
      ]
    );

    $this->add_control(
      'links',
      [
        'label'     => __( 'Links', 'mit' ),
        'type'      => Controls_Manager::REPEATER,
        'fields'    => $repeater->get_controls(),
        'condition' => [
          'type' => [ 'free_items' ],
        ],
      ]
    );

    $this->add_control(
      'message',
      [
        'label'     => __( 'Edit on Page Edit screen. The ACF custom field "Nav Links" must exist.', 'plugin-name' ),
        'type'      => \Elementor\Controls_Manager::HEADING,
        'separator' => 'after',
        'condition' => [
          'type' => [ 'acf' ],
        ],
      ]
    );

    $this->end_controls_section();
  }

  protected function render() {
    $uid = uniqid( 'links-nav-' );

    $type = $this->get_settings_for_display( 'type' );

    switch ( $type ) {
      case 'free_items':
        $links = $this->get_settings_for_display( 'links' );
        break;

      case 'acf':
        $links = get_field( 'nav_links' );
        break;
    }

    if ( ! empty( $links ) ) : ?>
      <div id="<?= $uid ?>" class="el-links-nav">
        <?
        get_template_part( 'template-parts/anchor-nav/anchor-nav', null, [
          'list' => array_map( function ( $link ) {
            return [
              'link'  => $link['link']['url'] ?? $link['link'],
              'label' => $link['label'],
            ];
          }, $links ),
        ] );
        ?>
      </div>
    <?php endif;
  }
}
