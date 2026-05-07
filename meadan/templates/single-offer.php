<?php
/**
 * Meadan — templates/single-offer.php
 *
 * Single offer/partnership article template.
 * URL: /offers-and-partnerships/{slug}/
 *
 * Routed via single_template filter in functions.php.
 *
 * Sections:
 *   1. offer-hero      — stone bg, breadcrumbs, partner label, title, excerpt
 *   2. offer-content   — featured image + article body
 *   3. offer-related   — 2-col grid, same post type, excluding current
 *   4. offer-cta       — sand bg, "Ready to Build?" strip
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
    the_post();

    // ACF fields
    $partner_name   = function_exists( 'get_field' ) ? get_field( 'partner_name' )   : '';
    $partner_logo   = function_exists( 'get_field' ) ? get_field( 'partner_logo' )   : null;
    $expiry_date    = function_exists( 'get_field' ) ? get_field( 'expiry_date' )     : '';
    $terms_url      = function_exists( 'get_field' ) ? get_field( 'terms_url' )       : '';
    $offer_code     = function_exists( 'get_field' ) ? get_field( 'offer_code' )      : '';
    $cta_label      = function_exists( 'get_field' ) ? get_field( 'cta_label' )       : __( 'Claim This Offer', 'meadan' );
    $cta_url        = function_exists( 'get_field' ) ? get_field( 'cta_url' )         : '';

    // Related offers (same CPT, exclude current)
    $related = new WP_Query( [
        'post_type'      => 'offer',
        'posts_per_page' => 2,
        'post_status'    => 'publish',
        'post__not_in'   => [ get_the_ID() ],
        'orderby'        => 'date',
        'order'          => 'DESC',
        'no_found_rows'  => true,
    ] );
?>

    <!-- ── 1. Offer hero ──────────────────────────────────────────────── -->
    <section class="offer-hero">
        <div class="offer-hero__inner">

            <!-- Breadcrumbs -->
            <nav class="offer-hero__breadcrumbs" aria-label="<?php esc_attr_e( 'Breadcrumb', 'meadan' ); ?>">
                <ol class="breadcrumbs__list">
                    <li class="breadcrumbs__item">
                        <a class="breadcrumbs__link" href="<?php echo esc_url( home_url( '/' ) ); ?>">
                            <?php esc_html_e( 'Home', 'meadan' ); ?>
                        </a>
                    </li>
                    <li class="breadcrumbs__separator" aria-hidden="true">/</li>
                    <li class="breadcrumbs__item">
                        <a class="breadcrumbs__link" href="<?php echo esc_url( get_post_type_archive_link( 'offer' ) ); ?>">
                            <?php esc_html_e( 'Offers &amp; Partnerships', 'meadan' ); ?>
                        </a>
                    </li>
                    <li class="breadcrumbs__separator" aria-hidden="true">/</li>
                    <li class="breadcrumbs__item breadcrumbs__item--current" aria-current="page">
                        <?php the_title(); ?>
                    </li>
                </ol>
            </nav>

            <div class="offer-hero__content">
                <?php if ( $partner_name ) : ?>
                    <p class="offer-hero__partner"><?php echo esc_html( $partner_name ); ?></p>
                <?php else : ?>
                    <p class="offer-hero__label"><?php esc_html_e( 'OFFERS &amp; PARTNERSHIPS', 'meadan' ); ?></p>
                <?php endif; ?>

                <h1 class="offer-hero__title"><?php the_title(); ?></h1>

                <?php if ( get_the_excerpt() ) : ?>
                    <p class="offer-hero__excerpt"><?php echo esc_html( get_the_excerpt() ); ?></p>
                <?php endif; ?>

                <div class="offer-hero__meta">
                    <?php if ( $expiry_date ) : ?>
                        <span class="offer-hero__meta-item">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                <circle cx="8" cy="8" r="6.5" stroke="currentColor" stroke-width="1.2"/>
                                <path d="M8 4.5V8l2.5 1.5" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
                            </svg>
                            <?php printf( esc_html__( 'Expires %s', 'meadan' ), esc_html( $expiry_date ) ); ?>
                        </span>
                    <?php endif; ?>
                    <?php if ( $offer_code ) : ?>
                        <span class="offer-hero__meta-item">
                            <?php esc_html_e( 'Code:', 'meadan' ); ?>
                            <strong><?php echo esc_html( $offer_code ); ?></strong>
                        </span>
                    <?php endif; ?>
                </div>

                <?php if ( $cta_url && $cta_label ) : ?>
                    <div class="offer-hero__ctas">
                        <a class="btn btn--primary" href="<?php echo esc_url( $cta_url ); ?>" target="_blank" rel="noopener noreferrer">
                            <?php echo esc_html( $cta_label ); ?>
                        </a>
                        <?php if ( $terms_url ) : ?>
                            <a class="offer-hero__terms" href="<?php echo esc_url( $terms_url ); ?>" target="_blank" rel="noopener noreferrer">
                                <?php esc_html_e( 'Terms &amp; Conditions', 'meadan' ); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div><!-- .offer-hero__content -->

            <?php if ( $partner_logo && is_array( $partner_logo ) ) : ?>
                <div class="offer-hero__partner-logo">
                    <img
                        src="<?php echo esc_url( $partner_logo['url'] ); ?>"
                        alt="<?php echo esc_attr( $partner_logo['alt'] ?: $partner_name ); ?>"
                        width="<?php echo esc_attr( $partner_logo['width'] ); ?>"
                        height="<?php echo esc_attr( $partner_logo['height'] ); ?>"
                        loading="lazy"
                    >
                </div>
            <?php endif; ?>

        </div><!-- .offer-hero__inner -->
    </section><!-- .offer-hero -->

    <!-- ── 2. Offer content ───────────────────────────────────────────── -->
    <article class="offer-content" id="offer-content">
        <div class="offer-content__inner">

            <?php if ( has_post_thumbnail() ) : ?>
                <figure class="offer-content__featured-image">
                    <?php the_post_thumbnail(
                        'large',
                        [ 'class' => 'offer-content__image', 'loading' => 'eager' ]
                    ); ?>
                </figure>
            <?php endif; ?>

            <div class="offer-content__body">
                <?php the_content(); ?>
            </div>

            <?php if ( $cta_url && $cta_label ) : ?>
                <div class="offer-content__action">
                    <a class="btn btn--primary" href="<?php echo esc_url( $cta_url ); ?>" target="_blank" rel="noopener noreferrer">
                        <?php echo esc_html( $cta_label ); ?>
                    </a>
                    <?php if ( $terms_url ) : ?>
                        <a class="offer-content__terms" href="<?php echo esc_url( $terms_url ); ?>" target="_blank" rel="noopener noreferrer">
                            <?php esc_html_e( 'Terms &amp; Conditions apply', 'meadan' ); ?>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        </div><!-- .offer-content__inner -->
    </article><!-- .offer-content -->

    <!-- ── 3. Related offers ──────────────────────────────────────────── -->
    <?php if ( $related->have_posts() ) : ?>
        <section class="offer-related">
            <div class="offer-related__inner">
                <h2 class="offer-related__heading"><?php esc_html_e( 'More Offers &amp; Partnerships', 'meadan' ); ?></h2>
                <div class="offer-related__grid">
                    <?php while ( $related->have_posts() ) : $related->the_post();
                        $rel_partner = function_exists( 'get_field' ) ? get_field( 'partner_name' ) : '';
                    ?>
                        <article class="offer-card offer-card--related">
                            <?php if ( has_post_thumbnail() ) : ?>
                                <a class="offer-card__image-link" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
                                    <?php the_post_thumbnail(
                                        'meadan-card',
                                        [ 'class' => 'offer-card__image', 'loading' => 'lazy' ]
                                    ); ?>
                                </a>
                            <?php endif; ?>
                            <div class="offer-card__body">
                                <?php if ( $rel_partner ) : ?>
                                    <p class="offer-card__partner"><?php echo esc_html( $rel_partner ); ?></p>
                                <?php endif; ?>
                                <h3 class="offer-card__title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h3>
                                <?php if ( get_the_excerpt() ) : ?>
                                    <p class="offer-card__excerpt"><?php echo esc_html( get_the_excerpt() ); ?></p>
                                <?php endif; ?>
                                <div class="offer-card__footer">
                                    <a class="offer-card__cta" href="<?php the_permalink(); ?>">
                                        <?php esc_html_e( 'View Offer', 'meadan' ); ?>
                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true" focusable="false">
                                            <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </article>
                    <?php endwhile;
                    wp_reset_postdata(); ?>
                </div>
                <div class="offer-related__all">
                    <a class="btn btn--outline" href="<?php echo esc_url( get_post_type_archive_link( 'offer' ) ); ?>">
                        <?php esc_html_e( 'View All Offers', 'meadan' ); ?>
                    </a>
                </div>
            </div><!-- .offer-related__inner -->
        </section><!-- .offer-related -->
    <?php endif; ?>

    <!-- ── 4. CTA strip ───────────────────────────────────────────────── -->
    <section class="offer-archive-cta">
        <div class="offer-archive-cta__inner">
            <div class="offer-archive-cta__text">
                <h2 class="offer-archive-cta__heading">
                    <?php esc_html_e( 'Ready to Build Your Dream Home?', 'meadan' ); ?>
                </h2>
                <p class="offer-archive-cta__subheading">
                    <?php esc_html_e( 'Our team is here to guide you through every step.', 'meadan' ); ?>
                </p>
            </div>
            <a class="btn btn--primary" href="<?php echo esc_url( home_url( '/contact' ) ); ?>">
                <?php esc_html_e( 'Get in Touch', 'meadan' ); ?>
            </a>
        </div>
    </section><!-- .offer-archive-cta -->

<?php endwhile; ?>

<?php get_footer();
