<?php
/**
 * Block: About Section
 * Template: blocks/about-section/template.php
 *
 * Fields:
 *  - heading (text)    Required
 *  - text    (wysiwyg)
 *  - image   (image array)
 */

defined( 'ABSPATH' ) || exit;

// Retrieve fields
$heading = get_field( 'heading' );
$text    = get_field( 'text' );
$image   = get_field( 'image' );

// Block attributes
$block_id    = ! empty( $block['anchor'] ) ? $block['anchor'] : 'about-section-' . $block['id'];
$extra_class = ! empty( $block['className'] ) ? ' ' . $block['className'] : '';

// Has image?
$has_image = ! empty( $image ) && ! empty( $image['url'] );
?>

<section
    id="<?php echo esc_attr( $block_id ); ?>"
    class="about-section<?php echo $has_image ? '' : ' about-section--no-image'; ?><?php echo esc_attr( $extra_class ); ?>"
    data-block-id="<?php echo esc_attr( $block['id'] ); ?>"
>
    <div class="about-section__inner">

        <!-- Text Column -->
        <div class="about-section__content">

            <?php if ( $heading ) : ?>
                <h2 class="about-section__heading">
                    <?php echo esc_html( $heading ); ?>
                </h2>
            <?php else : ?>
                <p class="about-section__placeholder">
                    <?php esc_html_e( 'Set a heading in the block settings panel.', 'meadan' ); ?>
                </p>
            <?php endif; ?>

            <?php if ( $text ) : ?>
                <div class="about-section__text">
                    <?php
                    // wysiwyg field returns HTML — apply wpautop for paragraph wrapping
                    echo wp_kses_post( wpautop( $text ) );
                    ?>
                </div><!-- .about-section__text -->
            <?php endif; ?>

        </div><!-- .about-section__content -->

        <!-- Image Column -->
        <?php if ( $has_image ) : ?>
            <figure class="about-section__figure">
                <img
                    class="about-section__image"
                    src="<?php echo esc_url( $image['url'] ); ?>"
                    alt="<?php echo esc_attr( $image['alt'] ?: $heading ); ?>"
                    width="<?php echo esc_attr( $image['width'] ); ?>"
                    height="<?php echo esc_attr( $image['height'] ); ?>"
                    loading="lazy"
                >
            </figure><!-- .about-section__figure -->
        <?php else : ?>
            <!-- Editor placeholder when no image is set -->
            <div class="about-section__figure about-section__figure--placeholder" aria-hidden="true">
                <span class="about-section__placeholder-label">
                    <?php esc_html_e( 'Image placeholder — set via block settings', 'meadan' ); ?>
                </span>
            </div>
        <?php endif; ?>

    </div><!-- .about-section__inner -->
</section><!-- .about-section -->
