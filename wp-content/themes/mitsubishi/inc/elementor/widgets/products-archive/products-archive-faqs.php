<?php

defined( 'ABSPATH' ) or exit;

use Elementor\Icons_Manager;
use Elementor\Utils;
use Elementor\Widget_Accordion;

class Custom_El_Products_Archive_Faqs extends Widget_Accordion {
  public function __construct( $data = [], $args = null ) {
    parent::__construct( $data, $args );
  }

  public function get_name() {
    return 'Custom_El_Products_Archive_Faqs';
  }

  public function get_title() {
    return 'Products Archive | FAQs';
  }

  public function get_icon() {
    return 'eicon-custom';
  }

  public function get_style_depends(): array {
		return [ 'widget-accordion' ];
	}
  
  public function get_categories() {
    return [ 'custom_woo' ];
  }

  public function get_script_depends() {
    return [];
  }

  protected function register_controls() {
    parent::register_controls();

    $this->add_control(
      'message',
      [
        'label' => __( 'Used on Products archive page only', 'plugin-name' ),
        'type'  => \Elementor\Controls_Manager::HEADING,
      ],
      [
        'overwrite' => true,
        'position'  => [
          'at' => 'before',
          'of' => 'tabs',
        ],
      ]
    );

    $this->remove_control( 'tabs' );
  }

  protected function render() {
    if ( ! is_product_category() || empty( $faqs = get_field( 'faqs', get_queried_object() ) ) ) {
      return;
    }

    $uid      = uniqid( 'el-products-archive-faqs-' );
    $settings = $this->get_settings_for_display();
    $migrated = isset( $settings['__fa4_migrated']['selected_icon'] );

    if ( ! isset( $settings['icon'] ) && ! Icons_Manager::is_migration_allowed() ) {
      // @todo: remove when deprecated
      // added as bc in 2.6
      // add old default
      $settings['icon']        = 'fa fa-plus';
      $settings['icon_active'] = 'fa fa-minus';
      $settings['icon_align']  = $this->get_settings( 'icon_align' );
    }

    $is_new   = empty( $settings['icon'] ) && Icons_Manager::is_migration_allowed();
    $has_icon = ( ! $is_new || ! empty( $settings['selected_icon']['value'] ) );
    $id_int   = substr( $this->get_id_int(), 0, 3 );
    ?>
    <div class="elementor-accordion py-4 py-xl-5 my-xl-5" role="tablist" id="<?= $uid ?>">
      <?php
      get_template_part( 'template-parts/section-heading/section-heading', null, [
        'heading' => 'FREQUENTLY ASKED QUESTIONS',
      ] );

      foreach ( $faqs

      as $index => $item ) :
      $tab_count = $index + 1;

      $tab_title_setting_key = $this->get_repeater_setting_key( 'tab_title', 'tabs', $index );

      $tab_content_setting_key = $this->get_repeater_setting_key( 'tab_content', 'tabs', $index );

      $this->add_render_attribute( $tab_title_setting_key, [
        'id'            => 'elementor-tab-title-' . $id_int . $tab_count,
        'class'         => [ 'elementor-tab-title' ],
        'data-tab'      => $tab_count,
        'role'          => 'tab',
        'aria-controls' => 'elementor-tab-content-' . $id_int . $tab_count,
        'aria-expanded' => 'false',
      ] );

      $this->add_render_attribute( $tab_content_setting_key, [
        'id'              => 'elementor-tab-content-' . $id_int . $tab_count,
        'class'           => [ 'elementor-tab-content', 'elementor-clearfix' ],
        'data-tab'        => $tab_count,
        'role'            => 'tabpanel',
        'aria-labelledby' => 'elementor-tab-title-' . $id_int . $tab_count,
      ] );

      $this->add_inline_editing_attributes( $tab_content_setting_key, 'advanced' );
      ?>
      <div class="elementor-accordion-item">
        <<?php Utils::print_validated_html_tag( $settings['title_html_tag'] ); ?> <?php $this->print_render_attribute_string( $tab_title_setting_key ); ?>>
        <?php if ( $has_icon ) : ?>
          <span class="elementor-accordion-icon elementor-accordion-icon-<?php echo esc_attr( $settings['icon_align'] ); ?>" aria-hidden="true">
							<?php
              if ( $is_new || $migrated ) { ?>
                <span class="elementor-accordion-icon-closed"><?php Icons_Manager::render_icon( $settings['selected_icon'] ); ?></span>
                <span class="elementor-accordion-icon-opened"><?php Icons_Manager::render_icon( $settings['selected_active_icon'] ); ?></span>
              <?php } else { ?>
                <i class="elementor-accordion-icon-closed <?php echo esc_attr( $settings['icon'] ); ?>"></i>
                <i class="elementor-accordion-icon-opened <?php echo esc_attr( $settings['icon_active'] ); ?>"></i>
              <?php } ?>
							</span>
        <?php endif; ?>
        <span class="elementor-accordion-title"><?php
          echo wp_strip_all_tags( $item['question'] );
          ?></span>
      </<?php Utils::print_validated_html_tag( $settings['title_html_tag'] ); ?>>
      <div <?php $this->print_render_attribute_string( $tab_content_setting_key ); ?>><?php
        $this->print_text_editor( $item['answer'] );
        ?></div>
    </div>
  <?php endforeach; ?>
    <?php
    if ( isset( $settings['faq_schema'] ) && 'yes' === $settings['faq_schema'] ) {
      $json = [
        '@context'   => 'https://schema.org',
        '@type'      => 'FAQPage',
        'mainEntity' => [],
      ];

      foreach ( $faqs as $index => $item ) {
        $json['mainEntity'][] = [
          '@type'          => 'Question',
          'name'           => wp_strip_all_tags( $item['question'] ),
          'acceptedAnswer' => [
            '@type' => 'Answer',
            'text'  => wp_strip_all_tags( $this->parse_text_editor( $item['answer'] ) ),
          ],
        ];
      }
      ?>
      <script type="application/ld+json"><?php echo wp_json_encode( $json ); ?></script>
    <?php } ?>

    <script defer>
      jQuery(function($) {
        const $element = $('<?="#{$uid}"?>');

        $element.on('click', '.elementor-tab-title', function() {
          $(this).toggleClass('elementor-active');
          const nextContent = $(this).next('.elementor-tab-content');
          nextContent.toggleClass('elementor-active').slideToggle();

          $element.find('.elementor-tab-title').not($(this)).removeClass('elementor-active');
          $element.find('.elementor-tab-content').not(nextContent).removeClass('elementor-active').slideUp();
        });
      });
    </script>
    </div>
    <?php
  }

  protected function content_template() {
  }
}
