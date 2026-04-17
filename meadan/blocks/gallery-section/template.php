<?php
/**
 * Block: Gallery Section
 * Template: blocks/gallery-section/template.php
 *
 * Fields:
 *  - images (repeater)
 *    - image   (image array)  Required
 *    - caption (text)
 *
 * Note: Lightbox is not included in this template.
 * To add lightbox functionality, enqueue a JS library (e.g. GLightbox or Fslightbox)
 * and add data-lightbox attributes to each <a> / <img> element.
 */

defined( 'ABSPATH' ) || exit;

// Retrieve fields
$images = get_field( 'images' );

// Block attributes
$block_id    = ! empty( $block['anchor'] ) ? $block['anchor'] : 'gallery-section-' . $block['id'];
$extra_class = ! empty( $block['className'] ) ? ' ' . $block['className'] : '';

// Editor placeholder if no images set
if ( empty( $images ) ) : ?>
    <section
        id="<?php echo esc_attr( $block_id ); ?>"
        class="gallery-section gallery-section--empty<?php echo esc_attr( $extra_class ); ?>"
        data-block-id="<?php echo esc_attr( $block['id'] ); ?>"
    >
        <p class="gallery-section__editor-notice">
            <?php esc_html_e( 'No images added yet. Use the block settings panel to add between 2 and 12 gallery images.', 'meadan' ); ?>
        </p>
    </section>
    <?php
    return;
endif;
?>

<section
    id="<?php echo esc_attr( $block_id ); ?>"
    class="gallery-section<?php echo esc_attr( $extra_class ); ?>"
    data-block-id="<?php echo esc_attr( $block['id'] ); ?>"
>
    <div class="gallery-section__grid">

        <?php foreach ( $images as $item ) :
            $image   = ! empty( $item['image'] )   ? $item['image']   : null;
            $caption = ! empty( $item['caption'] ) ? $item['caption'] : '';

            if ( empty( $image ) || empty( $image['url'] ) ) {
                continue;
            }
            ?>

            <figure class="gallery-item<?php echo $caption ? ' gallery-item--has-caption' : ''; ?>">

                <div class="gallery-item__image-wrap">
                    <?php
                    // Use wp_get_attachment_image for srcset + sizes support if we have an ID
                    if ( ! empty( $image['ID'] ) ) :
                        echo wp_get_attachment_image(
                            (int) $image['ID'],
                            'meadan-gallery',
                            false,
                            [
                                'class'   => 'gallery-item__image',
                                'loading' => 'lazy',
                                'alt'     => esc_attr( $image['alt'] ?: $caption ),
                            ]
                        );
                    else :
                        // Fallback to raw URL when no attachment ID
                        ?>
                        <img
                            class="gallery-item__image"
                            src="<?php echo esc_url( $image['url'] ); ?>"
                            alt="<?php echo esc_attr( $image['alt'] ?: $caption ); ?>"
                            width="<?php echo esc_attr( $image['width'] ); ?>"
                            height="<?php echo esc_attr( $image['height'] ); ?>"
                            loading="lazy"
                        >
                    <?php endif; ?>
                </div><!-- .gallery-item__image-wrap -->

                <?php if ( $caption ) : ?>
                    <figcaption class="gallery-item__caption">
                        <?php echo esc_html( $caption ); ?>
                    </figcaption>
                <?php endif; ?>

            </figure><!-- .gallery-item -->

        <?php endforeach; ?>

    </div><!-- .gallery-section__grid -->
</section><!-- .gallery-section -->
