<?php
/**
 * Block: Image Carousel
 * Slug: acf/image-carousel
 */

defined( 'ABSPATH' ) || exit;

$slides    = get_field( 'slides' );
$autoplay  = get_field( 'autoplay' );
$speed     = get_field( 'autoplay_speed' ) ?: 4000;

if ( empty( $slides ) ) {
    return;
}

$block_id = ! empty( $block['anchor'] ) ? ' id="' . esc_attr( $block['anchor'] ) . '"' : '';
?>
<section
    class="image-carousel"
    <?php echo $block_id; ?>
    data-autoplay="<?php echo $autoplay ? 'true' : 'false'; ?>"
    data-speed="<?php echo esc_attr( $speed ); ?>"
    aria-roledescription="carousel"
    aria-label="Image carousel"
>
    <div class="image-carousel__track" aria-live="<?php echo $autoplay ? 'off' : 'polite'; ?>">
        <?php foreach ( $slides as $index => $slide ) :
            $active = $index === 0 ? ' image-carousel__slide--active' : '';
            $img    = $slide['image'];
        ?>
            <div
                class="image-carousel__slide<?php echo $active; ?>"
                role="group"
                aria-roledescription="slide"
                aria-label="<?php echo esc_attr( $index + 1 . ' of ' . count( $slides ) ); ?>"
                <?php if ( $index > 0 ) echo 'aria-hidden="true"'; ?>
            >
                <img
                    class="image-carousel__image"
                    src="<?php echo esc_url( $img['url'] ); ?>"
                    alt="<?php echo ! empty( $slide['alt'] ) ? esc_attr( $slide['alt'] ) : esc_attr( $img['alt'] ); ?>"
                    loading="<?php echo $index === 0 ? 'eager' : 'lazy'; ?>"
                    width="<?php echo esc_attr( $img['width'] ); ?>"
                    height="<?php echo esc_attr( $img['height'] ); ?>"
                >
                <?php if ( ! empty( $slide['caption'] ) ) : ?>
                    <figcaption class="image-carousel__caption"><?php echo esc_html( $slide['caption'] ); ?></figcaption>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="image-carousel__dots" role="tablist" aria-label="Carousel slides">
        <?php foreach ( $slides as $index => $slide ) : ?>
            <button
                class="image-carousel__dot<?php echo $index === 0 ? ' image-carousel__dot--active' : ''; ?>"
                role="tab"
                aria-selected="<?php echo $index === 0 ? 'true' : 'false'; ?>"
                aria-label="<?php echo esc_attr( 'Go to slide ' . ( $index + 1 ) ); ?>"
                data-index="<?php echo esc_attr( $index ); ?>"
            ></button>
        <?php endforeach; ?>
    </div>
</section>
