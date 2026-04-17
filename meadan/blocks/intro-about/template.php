<?php
/**
 * Block: Intro / About
 * Slug: acf/intro-about
 */

defined( 'ABSPATH' ) || exit;

$section_label    = get_field( 'section_label' );
$title            = get_field( 'title' );
$description      = get_field( 'description' );
$cta_primary_label = get_field( 'cta_primary_label' );
$cta_primary_url   = get_field( 'cta_primary_url' );
$cta_sec_label     = get_field( 'cta_secondary_label' );
$cta_sec_url       = get_field( 'cta_secondary_url' );

$block_id = ! empty( $block['anchor'] ) ? ' id="' . esc_attr( $block['anchor'] ) . '"' : '';
?>
<section class="intro-about"<?php echo $block_id; ?>>
    <div class="intro-about__inner">

        <?php if ( $section_label ) : ?>
            <p class="intro-about__label"><?php echo esc_html( $section_label ); ?></p>
        <?php endif; ?>

        <?php if ( $title ) : ?>
            <h2 class="intro-about__title"><?php echo esc_html( $title ); ?></h2>
        <?php endif; ?>

        <?php if ( $description ) : ?>
            <p class="intro-about__description"><?php echo esc_html( $description ); ?></p>
        <?php endif; ?>

        <?php if ( $cta_primary_label || $cta_sec_label ) : ?>
            <div class="intro-about__ctas">
                <?php if ( $cta_primary_label && $cta_primary_url ) : ?>
                    <a class="btn btn--primary" href="<?php echo esc_url( $cta_primary_url ); ?>">
                        <?php echo esc_html( $cta_primary_label ); ?>
                    </a>
                <?php endif; ?>
                <?php if ( $cta_sec_label && $cta_sec_url ) : ?>
                    <a class="btn btn--secondary" href="<?php echo esc_url( $cta_sec_url ); ?>">
                        <?php echo esc_html( $cta_sec_label ); ?>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    </div>
</section>
