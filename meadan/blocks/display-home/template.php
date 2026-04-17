<?php
/**
 * Block: Display Home
 * Slug: acf/display-home
 */

defined( 'ABSPATH' ) || exit;

$section_label = get_field( 'section_label' );
$title         = get_field( 'title' );
$description   = get_field( 'description' );
$cta_label     = get_field( 'cta_label' );
$cta_url       = get_field( 'cta_url' );
$image         = get_field( 'image' );

$block_id = ! empty( $block['anchor'] ) ? ' id="' . esc_attr( $block['anchor'] ) . '"' : '';
?>
<section class="display-home"<?php echo $block_id; ?>>
    <div class="display-home__content">
        <?php if ( $section_label ) : ?>
            <p class="display-home__label"><?php echo esc_html( $section_label ); ?></p>
        <?php endif; ?>

        <?php if ( $title ) : ?>
            <h2 class="display-home__title"><?php echo esc_html( $title ); ?></h2>
        <?php endif; ?>

        <?php if ( $description ) : ?>
            <p class="display-home__description"><?php echo esc_html( $description ); ?></p>
        <?php endif; ?>

        <?php if ( $cta_label && $cta_url ) : ?>
            <a class="btn btn--primary" href="<?php echo esc_url( $cta_url ); ?>">
                <?php echo esc_html( $cta_label ); ?>
            </a>
        <?php endif; ?>
    </div>

    <?php if ( $image ) : ?>
        <figure class="display-home__image-wrap">
            <img
                class="display-home__image"
                src="<?php echo esc_url( $image['url'] ); ?>"
                alt="<?php echo esc_attr( $image['alt'] ); ?>"
                loading="lazy"
                width="<?php echo esc_attr( $image['width'] ); ?>"
                height="<?php echo esc_attr( $image['height'] ); ?>"
            >
        </figure>
    <?php endif; ?>
</section>
