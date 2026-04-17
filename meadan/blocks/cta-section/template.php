<?php
/**
 * Block: CTA Section
 * Template: blocks/cta-section/template.php
 *
 * Fields:
 *  - heading     (text)  Required
 *  - subheading  (text)
 *  - button_text (text)  Required
 *  - button_link (url)   Required
 */

defined( 'ABSPATH' ) || exit;

// Retrieve fields
$heading     = get_field( 'heading' );
$subheading  = get_field( 'subheading' );
$button_text = get_field( 'button_text' );
$button_link = get_field( 'button_link' );

// Block attributes
$block_id    = ! empty( $block['anchor'] ) ? $block['anchor'] : 'cta-section-' . $block['id'];
$extra_class = ! empty( $block['className'] ) ? ' ' . $block['className'] : '';
?>

<section
    id="<?php echo esc_attr( $block_id ); ?>"
    class="cta-section<?php echo esc_attr( $extra_class ); ?>"
    data-block-id="<?php echo esc_attr( $block['id'] ); ?>"
>
    <div class="cta-section__inner">

        <?php if ( $heading ) : ?>
            <h2 class="cta-section__heading">
                <?php echo esc_html( $heading ); ?>
            </h2>
        <?php else : ?>
            <p class="cta-section__placeholder">
                <?php esc_html_e( 'Set a heading in the block settings panel.', 'meadan' ); ?>
            </p>
        <?php endif; ?>

        <?php if ( $subheading ) : ?>
            <p class="cta-section__subheading">
                <?php echo esc_html( $subheading ); ?>
            </p>
        <?php endif; ?>

        <?php if ( $button_text && $button_link ) : ?>
            <a
                class="cta-section__btn btn btn--primary"
                href="<?php echo esc_url( $button_link ); ?>"
            >
                <?php echo esc_html( $button_text ); ?>
            </a>
        <?php elseif ( $button_text ) : ?>
            <!-- Button text without a link — show disabled state in editor -->
            <span class="cta-section__btn btn btn--primary btn--disabled">
                <?php echo esc_html( $button_text ); ?>
                <em class="cta-section__link-missing">
                    <?php esc_html_e( '(no URL set)', 'meadan' ); ?>
                </em>
            </span>
        <?php endif; ?>

    </div><!-- .cta-section__inner -->
</section><!-- .cta-section -->
