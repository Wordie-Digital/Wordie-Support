<?php

defined( 'ABSPATH' ) or exit;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

class Custom_Porel_Marketo_Form extends Widget_Base {
  public function __construct( $data = [], $args = null ) {
    parent::__construct( $data, $args );

    wp_register_script( 'theme-marketo-form-js', '//app-sn04.marketo.com/js/forms2/js/forms2.min.js', [], false, true );
  }

  public function get_name() {
    return 'Custom_Porel_Marketo_Form';
  }

  public function get_title() {
    return 'Marketo Form';
  }

  public function get_icon() {
    return 'eicon-custom';
  }

  public function get_categories() {
    return [ 'custom' ];
  }

  public function get_script_depends() {
    return [ 'theme-marketo-form-js' ];
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
      'form_id',
      [
        'label' => __( 'Form ID', 'mit' ),
        'type'  => Controls_Manager::TEXT,
      ]
    );

    $this->end_controls_section();
  }

  protected function render() {
    $uid = uniqid( 'porel-marketo-form-' );

    $form_id = $this->get_settings_for_display( 'form_id' );

    if ( ! empty( $form_id ) ) : ?>
      <div id="<?= $uid ?>" class="porel-marketo-form">
        <form id="mktoForm_<?= esc_attr( $form_id ) ?>"></form>
        <script>
          jQuery(document).ready(function($) {
            MktoForms2.loadForm('//app-sn04.marketo.com', '610-PUL-113', <?=esc_attr( $form_id )?>);
          });
        </script>
      </div>
    <?php endif;
  }
}
