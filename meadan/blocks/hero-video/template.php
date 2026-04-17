<?php
/**
 * Block: Hero Video
 * Slug: acf/hero-video
 */

defined( 'ABSPATH' ) || exit;

$subtitle          = get_field( 'subtitle' );
$title             = get_field( 'title' );
$description       = get_field( 'description' );
$video_url         = get_field( 'video_url' );
$video_poster      = get_field( 'video_poster' );
$cta_primary_label = get_field( 'cta_primary_label' );
$cta_primary_url   = get_field( 'cta_primary_url' );
$cta_sec_label     = get_field( 'cta_secondary_label' );
$cta_sec_url       = get_field( 'cta_secondary_url' );
$scroll_label      = get_field( 'scroll_label' ) ?: 'Learn more';

$poster_src = $video_poster ? esc_url( $video_poster['url'] ) : '';
$poster_alt = $video_poster ? esc_attr( $video_poster['alt'] ) : '';

$block_id = ! empty( $block['anchor'] ) ? ' id="' . esc_attr( $block['anchor'] ) . '"' : '';
?>
<section class="hero-video"<?php echo $block_id; ?> aria-label="<?php echo esc_attr( $title ); ?>">

    <?php if ( $video_url ) : ?>
        <video
            class="hero-video__video"
            src="<?php echo esc_url( $video_url ); ?>"
            <?php if ( $poster_src ) echo 'poster="' . $poster_src . '"'; ?>
            autoplay
            muted
            loop
            playsinline
            aria-hidden="true"
        ></video>
        <?php if ( $poster_src && ! $video_url ) : ?>
            <img class="hero-video__fallback" src="<?php echo $poster_src; ?>" alt="<?php echo $poster_alt; ?>" loading="eager">
        <?php endif; ?>
    <?php elseif ( $poster_src ) : ?>
        <img class="hero-video__fallback" src="<?php echo $poster_src; ?>" alt="<?php echo $poster_alt; ?>" loading="eager">
    <?php endif; ?>

    <div class="hero-video__overlay" aria-hidden="true"></div>

    <div class="hero-video__content">
        <?php if ( $subtitle ) : ?>
            <p class="hero-video__subtitle"><?php echo esc_html( $subtitle ); ?></p>
        <?php endif; ?>

        <?php if ( $title ) : ?>
            <h1 class="hero-video__title"><?php echo esc_html( $title ); ?></h1>
        <?php endif; ?>

        <?php if ( $description ) : ?>
            <p class="hero-video__description"><?php echo esc_html( $description ); ?></p>
        <?php endif; ?>

        <?php if ( $cta_primary_label || $cta_sec_label ) : ?>
            <div class="hero-video__ctas">
                <?php if ( $cta_primary_label && $cta_primary_url ) : ?>
                    <a class="btn btn--primary" href="<?php echo esc_url( $cta_primary_url ); ?>">
                        <?php echo esc_html( $cta_primary_label ); ?>
                    </a>
                <?php endif; ?>
                <?php if ( $cta_sec_label && $cta_sec_url ) : ?>
                    <a class="btn btn--outline-light" href="<?php echo esc_url( $cta_sec_url ); ?>">
                        <?php echo esc_html( $cta_sec_label ); ?>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="hero-video__scroll-indicator" aria-hidden="true">
        <span class="hero-video__scroll-label"><?php echo esc_html( $scroll_label ); ?></span>
        <svg class="hero-video__scroll-icon" width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true" focusable="false">
            <path d="M8 2v12M2 9l6 6 6-6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </div>

</section>
