<?php
/**
 * Meadan — template-parts/page-contact-section.php
 *
 * Shared contact component used by Our Process page template.
 * Reuses the existing .contact-section CSS from assets/css/blocks/contact-section.css
 * (enqueued separately via functions.php when is_page_template() matches).
 *
 * Figma: "Design Begins with You." — left col heading + contact details,
 *        right col Gravity / CF7 form.
 *
 * ACF fields:
 *   Options page (theme-settings):
 *     contact_address     textarea   Full postal address
 *     contact_phone       text       Primary phone number
 *     contact_email       email      Primary email address
 *   Current page:
 *     contact_form_shortcode  text   [gravityforms id="1"] or [contact-form-7 …]
 */

defined( 'ABSPATH' ) || exit;

$has_acf = function_exists( 'get_field' );

// Contact details from ACF options (fallback to blank — editor sets these in Theme Settings)
$address        = $has_acf ? get_field( 'contact_address', 'option' ) : '';
$phone          = $has_acf ? get_field( 'contact_phone',   'option' ) : '';
$email          = $has_acf ? get_field( 'contact_email',   'option' ) : '';
$form_shortcode = $has_acf ? get_field( 'contact_form_shortcode' )   : '';
?>

<section class="contact-section" id="contact">
    <div class="contact-section__inner">

        <!-- Left: heading + contact details -->
        <div class="contact-section__content">

            <p class="contact-section__label">
                <?php esc_html_e( 'CONTACT US', 'meadan' ); ?>
            </p>

            <h2 class="contact-section__heading">
                <?php esc_html_e( 'Design Begins with You.', 'meadan' ); ?>
            </h2>

            <?php if ( $address || $phone || $email ) : ?>
                <div class="contact-section__details">

                    <?php if ( $address ) : ?>
                        <div class="contact-section__detail-group">
                            <span class="contact-section__detail-label">
                                <?php esc_html_e( 'Address', 'meadan' ); ?>
                            </span>
                            <span class="contact-section__detail-value">
                                <?php echo nl2br( esc_html( $address ) ); ?>
                            </span>
                        </div>
                    <?php endif; ?>

                    <?php if ( $phone ) : ?>
                        <div class="contact-section__detail-group">
                            <span class="contact-section__detail-label">
                                <?php esc_html_e( 'Phone', 'meadan' ); ?>
                            </span>
                            <a class="contact-section__detail-value" href="tel:<?php echo esc_attr( preg_replace( '/\s+/', '', $phone ) ); ?>">
                                <?php echo esc_html( $phone ); ?>
                            </a>
                        </div>
                    <?php endif; ?>

                    <?php if ( $email ) : ?>
                        <div class="contact-section__detail-group">
                            <span class="contact-section__detail-label">
                                <?php esc_html_e( 'Email', 'meadan' ); ?>
                            </span>
                            <a class="contact-section__detail-value" href="mailto:<?php echo esc_attr( $email ); ?>">
                                <?php echo esc_html( $email ); ?>
                            </a>
                        </div>
                    <?php endif; ?>

                </div><!-- .contact-section__details -->
            <?php endif; ?>

        </div><!-- .contact-section__content -->

        <!-- Right: form -->
        <div class="contact-section__form">
            <?php if ( $form_shortcode ) : ?>
                <?php echo do_shortcode( wp_kses_post( $form_shortcode ) ); ?>
            <?php else : ?>
                <p class="contact-section__form-placeholder">
                    <?php esc_html_e( 'Add a form shortcode to the "Contact Form Shortcode" field on this page.', 'meadan' ); ?>
                </p>
            <?php endif; ?>
        </div><!-- .contact-section__form -->

    </div><!-- .contact-section__inner -->
</section><!-- .contact-section -->
