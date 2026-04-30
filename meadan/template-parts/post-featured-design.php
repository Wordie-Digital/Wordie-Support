<?php
/**
 * Component: Blog Post — Featured Design
 * Figma node: 4520:33923
 *
 * bg: #f0ebdf (Chalk), padding: 100px 60px
 * Left (538px): "FEATURED PRODUCTS" eyebrow · H2 design title · bed/bath/sqm icons
 *              · excerpt/description · "Learn more" CTA · prev/next arrows (Swiper)
 * Right (669px × 536px): design featured image
 *
 * ACF field: post_featured_designs (relationship to design CPT, multiple allowed)
 *
 * @package MeadanHomes
 */

defined( 'ABSPATH' ) || exit;

// ACF relationship field — returns array of WP_Post objects
$designs = get_field( 'post_featured_designs' );
if ( empty( $designs ) ) {
    return;
}

$multiple = count( $designs ) > 1;
?>
<section class="post-featured-design">

    <?php if ( $multiple ) : ?>
    <div class="swiper post-featured-design__swiper">
        <div class="swiper-wrapper">
    <?php endif; ?>

    <?php foreach ( $designs as $design ) :
        $design_id   = $design->ID;
        $title       = get_the_title( $design_id );
        $permalink   = get_permalink( $design_id );
        $thumb_url   = get_the_post_thumbnail_url( $design_id, 'full' );
        $thumb_alt   = get_post_meta( $design_id, '_thumbnail_alt', true ) ?: $title;

        // Design specs — registered post meta
        $beds  = (int) get_post_meta( $design_id, '_design_bedrooms',  true );
        $baths = (int) get_post_meta( $design_id, '_design_bathrooms', true );
        $sqm   = get_post_meta( $design_id, '_design_sqm', true );

        // Excerpt / description
        $description = get_the_excerpt( $design_id );
    ?>
    <div class="<?php echo $multiple ? 'swiper-slide ' : ''; ?>post-featured-design__inner">

        <!-- ── Left ─────────────────────────────────────────────────────── -->
        <div class="post-featured-design__left">

            <div class="post-featured-design__text">
                <p class="post-featured-design__eyebrow">Featured Products</p>
                <h2 class="post-featured-design__heading"><?php echo esc_html( $title ); ?></h2>

                <?php if ( $beds || $baths || $sqm ) : ?>
                <div class="post-featured-design__icons">
                    <?php if ( $beds ) : ?>
                    <div class="post-featured-design__icon-group">
                        <!-- Bed icon -->
                        <svg class="post-featured-design__icon" width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M2.333 18.667V9.333A2.333 2.333 0 0 1 4.667 7h18.666a2.333 2.333 0 0 1 2.334 2.333v9.334M2.333 14h23.334M2.333 21h23.334" stroke="#1C1C1C" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M7 14V10.5a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1V14" stroke="#1C1C1C" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span class="post-featured-design__icon-value"><?php echo esc_html( $beds ); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ( $baths ) : ?>
                    <div class="post-featured-design__icon-group">
                        <!-- Bath icon -->
                        <svg class="post-featured-design__icon" width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M4.667 14h18.666v2.333A7 7 0 0 1 16.333 23H11.667A7 7 0 0 1 4.667 16.333V14z" stroke="#1C1C1C" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M7 14V7a2.333 2.333 0 0 1 4.667 0v1.167" stroke="#1C1C1C" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M9.333 23.333L8.167 25.667M18.667 23.333l1.166 2.334" stroke="#1C1C1C" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                        <span class="post-featured-design__icon-value"><?php echo esc_html( $baths ); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ( $sqm ) : ?>
                    <div class="post-featured-design__icon-group">
                        <!-- Area icon -->
                        <svg class="post-featured-design__icon" width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <rect x="3.5" y="3.5" width="21" height="21" rx="1" stroke="#1C1C1C" stroke-width="1.5"/>
                            <path d="M9 14h10M14 9v10" stroke="#1C1C1C" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                        <span class="post-featured-design__icon-value"><?php echo esc_html( $sqm ); ?> sq</span>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <?php if ( $description ) : ?>
                <p class="post-featured-design__description"><?php echo esc_html( $description ); ?></p>
                <?php endif; ?>
            </div>

            <div class="post-featured-design__actions">
                <a class="btn btn--primary" href="<?php echo esc_url( $permalink ); ?>">Learn more</a>
            </div>

            <?php if ( $multiple ) : ?>
            <div class="post-featured-design__nav">
                <button class="post-featured-design__nav-btn post-featured-design__nav-btn--prev" aria-label="Previous design">
                    <svg width="27" height="8" viewBox="0 0 27 8" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M26.354 4.354a.5.5 0 0 0 0-.708L23.172.464a.5.5 0 1 0-.708.708L25.293 4l-2.829 2.828a.5.5 0 1 0 .708.708l3.182-3.182zM0 4.5h26v-1H0v1z" fill="#1C1C1C"/>
                    </svg>
                </button>
                <button class="post-featured-design__nav-btn post-featured-design__nav-btn--next" aria-label="Next design">
                    <svg width="27" height="8" viewBox="0 0 27 8" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" style="transform: scaleX(-1);">
                        <path d="M26.354 4.354a.5.5 0 0 0 0-.708L23.172.464a.5.5 0 1 0-.708.708L25.293 4l-2.829 2.828a.5.5 0 1 0 .708.708l3.182-3.182zM0 4.5h26v-1H0v1z" fill="#1C1C1C"/>
                    </svg>
                </button>
            </div>
            <?php endif; ?>

        </div><!-- .post-featured-design__left -->

        <!-- ── Right: design image ─────────────────────────────────────── -->
        <?php if ( $thumb_url ) : ?>
        <figure class="post-featured-design__image-wrap">
            <img
                class="post-featured-design__image"
                src="<?php echo esc_url( $thumb_url ); ?>"
                alt="<?php echo esc_attr( $thumb_alt ); ?>"
                loading="lazy"
            />
        </figure>
        <?php endif; ?>

    </div><!-- .post-featured-design__inner -->
    <?php endforeach; ?>

    <?php if ( $multiple ) : ?>
        </div><!-- .swiper-wrapper -->
    </div><!-- .swiper -->
    <?php endif; ?>

</section><!-- .post-featured-design -->
