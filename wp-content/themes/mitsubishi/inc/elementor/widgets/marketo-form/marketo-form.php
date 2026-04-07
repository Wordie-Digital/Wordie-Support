<?php

defined( 'ABSPATH' ) or exit;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

class Custom_El_Marketo_Form extends Widget_Base {
  public function __construct( $data = [], $args = null ) {
    parent::__construct( $data, $args );

    wp_register_script( 'theme-marketo-form-js', '//app-sn04.marketo.com/js/forms2/js/forms2.min.js', [], false, true );
  }

  public function get_name() {
    return 'Custom_El_Marketo_Form';
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

    $this->add_control(
      'fullwidth_elements',
      [
        'label'       => __( 'Fullwidth elements', 'mit' ),
        'type'        => Controls_Manager::TEXT,
        'placeholder' => 'Eg: 1,3,10',
        'description' => 'Index of elements to expand full width, comma delimiter',
      ]
    );

    $this->end_controls_section();
  }

  protected function render() {
    $uid = uniqid( 'el-marketo-form-' );

    $form_id            = $this->get_settings_for_display( 'form_id' );
    $fullwidth_elements = $this->get_settings_for_display( 'fullwidth_elements' );

    if ( ! empty( $form_id ) ) : ?>
      <div id="<?= $uid ?>" class="el-marketo-form">
        <form id="mktoForm_<?= esc_attr( $form_id ) ?>"></form>
        <script>
          jQuery(document).ready(function($) {
            MktoForms2.loadForm('//app-sn04.marketo.com', '610-PUL-113', <?=esc_attr( $form_id )?>);

            (function() {
              var userConfig = {
                apiKeys: {
                  recaptcha: '6LdrM-QhAAAAALOl1ZVt0QJSQ0DMIe1M4UfIneY_'
                },
                fields: {
                  recaptchaFinger: 'reCAPTCHALastUserFingerprint'
                },
                actions: {
                  formSubmit: 'form'
                },
                debug: {
                  testBadFinger: false
                }
              };

              /* --- NO NEED TO TOUCH BELOW THIS LINE --- */

              MktoForms2.whenReady(function(mktoForm) {
                var formEl = mktoForm.getFormElem()[0],
                  submitButtonEl = formEl.querySelector('button[type=\'submit\']');

                /* pending reCAPTCHA widget ready */
                submitButtonEl.disabled = true;

                /* pending reCAPTCHA verify */
                mktoForm.submittable(false);
                mktoForm.locked = false;

                mktoForm.onValidate(function(native) {
                  if (!native) return;

                  grecaptcha.ready(function() {
                    grecaptcha.execute(userConfig.apiKeys.recaptcha, {
                      action: userConfig.actions.formSubmit
                    }).then(function(recaptchaFinger) {
                      var mktoFields = {};
                      if (mktoForm.locked == false) {
                        console.log('primary recaptcha response resolved');
                        mktoForm.locked = true;
                        if (!userConfig.debug.testBadFinger) {
                          mktoFields[userConfig.fields.recaptchaFinger] = recaptchaFinger;
                        } else {
                          mktoFields[userConfig.fields.recaptchaFinger] = 'known bad fingerprint ' + Math.random();
                        }
                        mktoForm.addHiddenFields(mktoFields);
                        mktoForm.submittable(true);
                        mktoForm.submit();
                      } else {
                        console.log('secondary recaptcha response resolved');
                      }
                    });
                  });
                });
              });

              var recaptchaListeners = {
                ready: function() {
                  MktoForms2.whenReady(function(mktoForm) {
                    var formEl = mktoForm.getFormElem()[0],
                      submitButtonEl = formEl.querySelector('button[type=\'submit\']');

                    submitButtonEl.disabled = false;
                  });
                }
              };
              Object.keys(recaptchaListeners).forEach(function globalize(fnName) {
                window['grecaptchaListeners_' + fnName] = recaptchaListeners[fnName];
              });

              /* inject the reCAPTCHA library */
              recaptchaLib = document.createElement('script');
              recaptchaLib.src = 'https://www.google.com/recaptcha/api.js?render=' + userConfig.apiKeys.recaptcha + '&onload=grecaptchaListeners_ready';
              document.head.appendChild(recaptchaLib);

            })();
          });
        </script>

        <?php if ( ! empty( $fullwidth_elements ) ) : ?>
          <?
          $fullwidth_elements_arr = explode( ',', $fullwidth_elements );
          $fullwidth_elements_arr = array_map( function ( $el ) use ( $uid ) {
            return "#{$uid} .mktoForm .mktoFormRow:nth-of-type(" . absint( trim( $el ) ) . ")";
          }, $fullwidth_elements_arr )
          ?>
          <style><?= implode( ',', $fullwidth_elements_arr ) . '{width:100%!important;}' ?></style>
        <?php endif; ?>
      </div>
    <?php endif;
  }
}
