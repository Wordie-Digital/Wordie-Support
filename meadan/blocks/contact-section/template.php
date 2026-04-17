<?php
/**
 * Block: Contact Section
 * Slug: acf/contact-section
 */

defined( 'ABSPATH' ) || exit;

$section_label   = get_field( 'section_label' );
$heading         = get_field( 'heading' );
$description     = get_field( 'description' );
$form_shortcode  = get_field( 'form_shortcode' );

$block_id = ! empty( $block['anchor'] ) ? ' id="' . esc_attr( $block['anchor'] ) . '"' : '';
?>
<section class="contact-section"<?php echo $block_id; ?>>
    <div class="contact-section__inner">
        <div class="contact-section__content">
            <?php if ( $section_label ) : ?>
                <p class="contact-section__label"><?php echo esc_html( $section_label ); ?></p>
            <?php endif; ?>

            <?php if ( $heading ) : ?>
                <h2 class="contact-section__heading"><?php echo esc_html( $heading ); ?></h2>
            <?php endif; ?>

            <?php if ( $description ) : ?>
                <p class="contact-section__description"><?php echo esc_html( $description ); ?></p>
            <?php endif; ?>
        </div>

        <div class="contact-section__form">
            <?php if ( $form_shortcode ) : ?>
                <?php echo do_shortcode( $form_shortcode ); ?>
            <?php else : ?>
                <p class="contact-section__form-placeholder">
                    <?php esc_html_e( 'Add a Contact Form 7 shortcode in the block settings.', 'meadan' ); ?>
                </p>
            <?php endif; ?>
        </div>
    </div>
</section>
