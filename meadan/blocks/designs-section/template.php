<?php
/**
 * Block: Designs Section
 * Slug: acf/designs-section
 * Dynamic: queries Design CPT.
 */

defined( 'ABSPATH' ) || exit;

$section_label = get_field( 'section_label' );
$heading       = get_field( 'heading' );
$description   = get_field( 'description' );
$count         = (int) ( get_field( 'count' ) ?: 2 );
$cta_label     = get_field( 'cta_label' );
$cta_url       = get_field( 'cta_url' );

$designs = new WP_Query( [
    'post_type'      => 'design',
    'posts_per_page' => $count,
    'post_status'    => 'publish',
    'orderby'        => 'menu_order',
    'order'          => 'ASC',
    'no_found_rows'  => true,
] );

$block_id = ! empty( $block['anchor'] ) ? ' id="' . esc_attr( $block['anchor'] ) . '"' : '';
?>
<section class="designs-section"<?php echo $block_id; ?>>
    <div class="designs-section__header">
        <div class="designs-section__header-text">
            <?php if ( $section_label ) : ?>
                <p class="designs-section__label"><?php echo esc_html( $section_label ); ?></p>
            <?php endif; ?>
            <?php if ( $heading ) : ?>
                <h2 class="designs-section__heading"><?php echo esc_html( $heading ); ?></h2>
            <?php endif; ?>
            <?php if ( $description ) : ?>
                <p class="designs-section__description"><?php echo esc_html( $description ); ?></p>
            <?php endif; ?>
        </div>

        <div class="designs-section__nav" role="group" aria-label="Design navigation">
            <button class="arrow-btn arrow-btn--prev js-slider-prev" aria-label="<?php esc_attr_e( 'Previous designs', 'meadan' ); ?>" disabled>
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false">
                    <path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
            <button class="arrow-btn arrow-btn--next js-slider-next" aria-label="<?php esc_attr_e( 'Next designs', 'meadan' ); ?>">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false">
                    <path d="M9 18l6-6-6-6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
        </div>
    </div>

    <?php if ( $designs->have_posts() ) : ?>
        <div class="designs-section__grid">
            <?php while ( $designs->have_posts() ) : $designs->the_post();
                $bedrooms  = get_post_meta( get_the_ID(), '_design_bedrooms', true );
                $bathrooms = get_post_meta( get_the_ID(), '_design_bathrooms', true );
                $sqm       = get_post_meta( get_the_ID(), '_design_sqm', true );
            ?>
                <article class="design-card">
                    <?php if ( has_post_thumbnail() ) : ?>
                        <a class="design-card__image-link" href="<?php the_permalink(); ?>">
                            <?php the_post_thumbnail( 'meadan-card', [ 'class' => 'design-card__image', 'loading' => 'lazy' ] ); ?>
                            <span class="design-card__overlay" aria-hidden="true">
                                <span class="design-card__overlay-btn"><?php esc_html_e( 'View Details +', 'meadan' ); ?></span>
                            </span>
                        </a>
                    <?php endif; ?>
                    <div class="design-card__body">
                        <h3 class="design-card__title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h3>
                        <?php if ( $bedrooms || $bathrooms || $sqm ) : ?>
                            <ul class="design-card__specs">
                                <?php if ( $bedrooms ) : ?><li><?php echo esc_html( $bedrooms ); ?> Bed</li><?php endif; ?>
                                <?php if ( $bathrooms ) : ?><li><?php echo esc_html( $bathrooms ); ?> Bath</li><?php endif; ?>
                                <?php if ( $sqm ) : ?><li><?php echo esc_html( $sqm ); ?> m²</li><?php endif; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endwhile;
            wp_reset_postdata(); ?>
        </div>
    <?php else : ?>
        <p class="designs-section__empty"><?php esc_html_e( 'No designs found.', 'meadan' ); ?></p>
    <?php endif; ?>

    <?php if ( $cta_label && $cta_url ) : ?>
        <div class="designs-section__footer">
            <a class="btn btn--primary" href="<?php echo esc_url( $cta_url ); ?>">
                <?php echo esc_html( $cta_label ); ?>
            </a>
        </div>
    <?php endif; ?>
</section>
