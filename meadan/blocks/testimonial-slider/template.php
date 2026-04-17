<?php
/**
 * Block: Testimonial Slider
 * Template: blocks/testimonial-slider/template.php
 *
 * Fields:
 *  - testimonials (repeater)
 *    - quote        (textarea)  Required
 *    - author_name  (text)      Required
 *    - author_image (image array)
 *
 * Requires: assets/js/testimonial-slider.js
 * Note: testimonial-slider.js is enqueued globally via functions.php.
 * If you prefer per-block enqueueing, use wp_enqueue_script() here instead.
 */

defined( 'ABSPATH' ) || exit;

// Retrieve fields
$testimonials = get_field( 'testimonials' );

// Block attributes
$block_id    = ! empty( $block['anchor'] ) ? $block['anchor'] : 'testimonial-slider-' . $block['id'];
$extra_class = ! empty( $block['className'] ) ? ' ' . $block['className'] : '';

// Bail early with a placeholder if no testimonials are added
if ( empty( $testimonials ) ) : ?>
    <section
        id="<?php echo esc_attr( $block_id ); ?>"
        class="testimonial-slider testimonial-slider--empty<?php echo esc_attr( $extra_class ); ?>"
        data-block-id="<?php echo esc_attr( $block['id'] ); ?>"
    >
        <p class="testimonial-slider__editor-notice">
            <?php esc_html_e( 'No testimonials added yet. Use the block settings panel to add at least 2 testimonials.', 'meadan' ); ?>
        </p>
    </section>
    <?php
    return;
endif;

$count = count( $testimonials );
?>

<section
    id="<?php echo esc_attr( $block_id ); ?>"
    class="testimonial-slider<?php echo esc_attr( $extra_class ); ?>"
    data-block-id="<?php echo esc_attr( $block['id'] ); ?>"
>
    <!-- Slider Track -->
    <div
        class="testimonial-slider__track"
        data-slider
        aria-live="polite"
        aria-atomic="false"
        role="region"
        aria-label="<?php esc_attr_e( 'Testimonials', 'meadan' ); ?>"
    >

        <?php foreach ( $testimonials as $index => $item ) :
            $quote        = ! empty( $item['quote'] )        ? $item['quote']        : '';
            $author_name  = ! empty( $item['author_name'] )  ? $item['author_name']  : '';
            $author_image = ! empty( $item['author_image'] ) ? $item['author_image'] : null;

            $is_first = ( 0 === $index );
            ?>

            <article
                class="testimonial-slide<?php echo $is_first ? ' testimonial-slide--active' : ''; ?>"
                role="group"
                aria-label="<?php printf( esc_attr__( 'Testimonial %1$d of %2$d', 'meadan' ), $index + 1, $count ); ?>"
                aria-hidden="<?php echo $is_first ? 'false' : 'true'; ?>"
                data-slide-index="<?php echo esc_attr( $index ); ?>"
            >
                <blockquote class="testimonial-slide__quote">
                    <p class="testimonial-slide__quote-text">
                        &ldquo;<?php echo esc_html( $quote ); ?>&rdquo;
                    </p>

                    <footer class="testimonial-slide__author">
                        <?php if ( $author_image && ! empty( $author_image['url'] ) ) : ?>
                            <img
                                class="testimonial-slide__author-photo"
                                src="<?php echo esc_url( $author_image['url'] ); ?>"
                                alt="<?php echo esc_attr( $author_image['alt'] ?: $author_name ); ?>"
                                width="<?php echo esc_attr( $author_image['width'] ); ?>"
                                height="<?php echo esc_attr( $author_image['height'] ); ?>"
                                loading="lazy"
                            >
                        <?php endif; ?>

                        <cite class="testimonial-slide__author-name">
                            <?php echo esc_html( $author_name ); ?>
                        </cite>
                    </footer>
                </blockquote>
            </article><!-- .testimonial-slide -->

        <?php endforeach; ?>

    </div><!-- .testimonial-slider__track -->

    <?php if ( $count > 1 ) : ?>

        <!-- Navigation Controls -->
        <div class="testimonial-slider__controls" aria-label="<?php esc_attr_e( 'Slider controls', 'meadan' ); ?>">

            <!-- Previous -->
            <button
                class="testimonial-slider__btn testimonial-slider__btn--prev"
                type="button"
                aria-label="<?php esc_attr_e( 'Previous testimonial', 'meadan' ); ?>"
                data-slider-prev
            >
                <span aria-hidden="true">&#8592;</span>
            </button>

            <!-- Dot Navigation -->
            <div class="testimonial-slider__dots" role="tablist" aria-label="<?php esc_attr_e( 'Testimonial slides', 'meadan' ); ?>">
                <?php foreach ( $testimonials as $index => $item ) : ?>
                    <button
                        class="testimonial-slider__dot<?php echo 0 === $index ? ' testimonial-slider__dot--active' : ''; ?>"
                        type="button"
                        role="tab"
                        aria-selected="<?php echo 0 === $index ? 'true' : 'false'; ?>"
                        aria-label="<?php printf( esc_attr__( 'Go to testimonial %d', 'meadan' ), $index + 1 ); ?>"
                        data-slider-dot="<?php echo esc_attr( $index ); ?>"
                    ></button>
                <?php endforeach; ?>
            </div><!-- .testimonial-slider__dots -->

            <!-- Next -->
            <button
                class="testimonial-slider__btn testimonial-slider__btn--next"
                type="button"
                aria-label="<?php esc_attr_e( 'Next testimonial', 'meadan' ); ?>"
                data-slider-next
            >
                <span aria-hidden="true">&#8594;</span>
            </button>

        </div><!-- .testimonial-slider__controls -->

    <?php endif; ?>

</section><!-- .testimonial-slider -->
