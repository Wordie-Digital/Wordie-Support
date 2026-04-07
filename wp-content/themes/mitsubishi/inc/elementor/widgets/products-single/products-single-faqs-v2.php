<?php

defined( 'ABSPATH' ) or exit;

use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Utils;
use Elementor\Widget_Base;
use Elementor\Widget_Accordion;

class Custom_El_Products_Single_Faqs_V2 extends Widget_Accordion {
  public function __construct( $data = [], $args = null ) {
    parent::__construct( $data, $args );
  }

  public function get_name() {
    return 'Custom_El_Products_Single_Faqs_V2';
  }

  public function get_title() {
    return 'Product | FAQs V2';
  }

  public function get_icon() {
    return 'eicon-custom';
  }

  public function get_categories() {
    return [ 'custom_woo' ];
  }

  public function get_script_depends() {
    return [];
  }

  public function get_style_depends(): array {
    return [];
  }

  protected function register_controls() {
    parent::register_controls();

    $this->add_control(
      'message',
      [
        'label' => __( 'Used on Products single page only', 'plugin-name' ),
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
    if ( ! is_product() || empty( $faqs_v2 = get_field( 'faqs_v2' ) ) ) {
      return;
    }

    $uid      = uniqid( 'el-products-single-faqs-v2-' );
    $settings = $this->get_settings_for_display();
    ?>
    <div class="faqs-v2-accordion" id="<?= $uid ?>">
      <?php
      get_template_part( 'template-parts/section-heading/section-heading', null, [
        'heading' => 'FREQUENTLY ASKED QUESTIONS',
      ] );
      
      foreach ( $faqs_v2 as $faq_v2 ) :
        $sub_heading = $faq_v2['sub_heading'];
      ?>
        <div class="faq-separator">
          <? if(!empty($sub_heading)) : ?>
            <h3><?= $sub_heading; ?></h3>
          <? endif; ?>
          <?
          $question_and_answer = $faq_v2['question_and_answer'];
          foreach($question_and_answer as $aq) :
          ?>
            <div class="faq-item">
              <div class="faq-question">
                <a><?= $aq['question']; ?></a>
                <span class="icons">
                  <span class="icon-closed">
                    <i class="fas fa-chevron-down"></i>
                  </span>
                  <span class="icon-opened">
                    <i class="fas fa-chevron-up"></i>
                  </span>
                </span>
              </div>
              <div class="faq-answer">
                <?= $aq['answer']; ?>
              </div>
            </div>
          <?php 
          endforeach;?>
        </div>
        <?php
      endforeach;
      ?>
      </div>

    <?php
      if ( isset( $settings['faq_schema'] ) && 'yes' === $settings['faq_schema'] ) {
        $json = [
          '@context'   => 'https://schema.org',
          '@type'      => 'FAQPage',
          'mainEntity' => [],
        ];

        foreach ($faqs_v2 as $faq_v2) {
          $question_and_answer = $faq_v2['question_and_answer'];
          foreach($question_and_answer as $aq){
            $json['mainEntity'][] = [
              '@type'          => 'Question',
              'name'           => wp_strip_all_tags( $aq['question'] ),
              'acceptedAnswer' => [
                '@type' => 'Answer',
                'text'  => wp_strip_all_tags( $this->parse_text_editor( $aq['answer'] ) ),
              ],
            ];
          }
        }
      ?>
      <script type="application/ld+json"><?php echo wp_json_encode( $json ); ?></script>
    <?php } ?>

    <style>
      .faq-answer,
      .icon-opened{
        display: none;
      }
      .faq-open .icon-opened{
        display: inline-block!important;
      }
      .faq-open .icon-closed{
        display: none!important;
      }
      .faq-separator{
        border-bottom: 1px solid #d5d8dc;
        margin-bottom: 40px;
      }
      .faq-item{
        border: 1px solid #d5d8dc;
        border-bottom: 0;
      }
      .faq-question{
        padding: 25px 25px 25px 25px;
        cursor: pointer;
      }
      .faq-answer{
        padding: 15px 20px;
        border-top: 1px solid #d5d8dc;
      }
      .faq-answer a{
        text-decoration: underline;
      }
      .faq-question a{
        font-size: 22px;
      }
      .faq-question .icons{
        position: absolute;
        right: 26px;
        margin-top: 5px;
      }
    </style>
    <script defer>
      jQuery(function($) {
        $('.faq-question').on('click', function (){

          $(this).toggleClass('faq-open');
          $(this).parent().siblings().find('.faq-question').removeClass('faq-open');

          // Collapse all other sections
          $('.faq-answer').not($(this).next()).slideUp();
          // Toggle the clicked section
          $(this).next().slideToggle();
        });
      });
    </script>
    </div>
    <?php
  }

  protected function content_template() {
  }
}