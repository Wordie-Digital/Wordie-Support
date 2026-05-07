<?php
/**
 * Meadan — templates/single-offer.php
 *
 * Single offer/partnership article.
 * URL: /offers-and-partnerships/{slug}/
 * Routed via single_template filter in functions.php.
 *
 * Figma node: 4515:29930 — "Offers Article"
 *
 * Sections (per Figma):
 *   1. Hero          — stone bg, centered breadcrumbs / h1 / date  (400px)
 *   2. Content       — 2:1 featured image + 874px text body + CTA
 *   3. Terms         — divider line + "Terms and Conditions" + body
 *   4. Other Offers  — "YOU MIGHT BE INTERESTED", "Other Offers", 2-col card grid
 *   5. Footer        — via get_footer()
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
    the_post();

    $has_acf      = function_exists( 'get_field' );
    $cta_label    = $has_acf ? get_field( 'cta_label' )    : '';
    $cta_url      = $has_acf ? get_field( 'cta_url' )      : '';
    $terms_title  = $has_acf ? get_field( 'terms_title' )  : __( 'Terms and Conditions', 'meadan' );
    $terms_body   = $has_acf ? get_field( 'terms_body' )   : '';

    // Other offers — 2 posts from same CPT, exclude current
    $other_offers = new WP_Query( [
        'post_type'      => 'offer',
        'posts_per_page' => 2,
        'post_status'    => 'publish',
        'post__not_in'   => [ get_the_ID() ],
        'orderby'        => 'date',
        'order'          => 'DESC',
        'no_found_rows'  => true,
    ] );
?>

<main class="site-main site-main--single-offer" id="main" role="main">

    <!-- ── 1. Hero: stone bg, centered content ───────────────────────── -->
    <section class="offer-single-hero">
        <div class="offer-single-hero__inner">

            <!-- Breadcrumbs -->
            <nav class="offer-single-hero__breadcrumbs" aria-label="<?php esc_attr_e( 'Breadcrumb', 'meadan' ); ?>">
                <ol class="breadcrumbs__list">
                    <li class="breadcrumbs__item">
                        <a class="breadcrumbs__link" href="<?php echo esc_url( home_url( '/' ) ); ?>">
                            <?php esc_html_e( 'Home', 'meadan' ); ?>
                        </a>
                    </li>
                    <li class="breadcrumbs__separator" aria-hidden="true">/</li>
                    <li class="breadcrumbs__item">
                        <a class="breadcrumbs__link" href="<?php echo esc_url( get_post_type_archive_link( 'offer' ) ); ?>">
                            <?php esc_html_e( 'Offers', 'meadan' ); ?>
                        </a>
                    </li>
                </ol>
            </nav>

            <!-- Title -->
            <h1 class="offer-single-hero__title"><?php the_title(); ?></h1>

            <!-- Date -->
            <p class="offer-single-hero__date">
                <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
                    <?php echo esc_html( get_the_date( 'M j, Y' ) ); ?>
                </time>
            </p>

        </div><!-- .offer-single-hero__inner -->
    </section><!-- .offer-single-hero -->

    <!-- ── 2. Content: 2:1 image + 874px body ────────────────────────── -->
    <article class="offer-single-content" id="offer-content">
        <div class="offer-single-content__inner">

            <?php if ( has_post_thumbnail() ) : ?>
                <figure class="offer-single-content__image-wrap">
                    <?php the_post_thumbnail(
                        'full',
                        [ 'class' => 'offer-single-content__image', 'loading' => 'eager' ]
                    ); ?>
                </figure>
            <?php endif; ?>

            <div class="offer-single-content__body">
                <?php the_content(); ?>

                <?php if ( $cta_label && $cta_url ) : ?>
                    <a
                        class="btn btn--outline offer-single-content__cta"
                        href="<?php echo esc_url( $cta_url ); ?>"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        <?php echo esc_html( $cta_label ); ?>
                    </a>
                <?php endif; ?>
            </div>

        </div><!-- .offer-single-content__inner -->
    </article><!-- .offer-single-content -->

    <!-- ── 3. Terms and Conditions ───────────────────────────────────── -->
    <?php if ( $terms_body ) : ?>
        <section class="offer-single-terms">
            <div class="offer-single-terms__inner">
                <hr class="offer-single-terms__rule" aria-hidden="true">
                <h2 class="offer-single-terms__title">
                    <?php echo esc_html( $terms_title ?: __( 'Terms and Conditions', 'meadan' ) ); ?>
                </h2>
                <div class="offer-single-terms__body">
                    <?php echo wp_kses_post( $terms_body ); ?>
                </div>
            </div>
        </section><!-- .offer-single-terms -->
    <?php endif; ?>

    <!-- ── 4. Other Offers ───────────────────────────────────────────── -->
    <?php if ( $other_offers->have_posts() ) : ?>
        <section class="offer-other">
            <div class="offer-other__inner">

                <!-- Header -->
                <div class="offer-other__header">
                    <div class="offer-other__header-text">
                        <p class="offer-other__label">
                            <?php esc_html_e( 'YOU MIGHT BE INTERESTED', 'meadan' ); ?>
                        </p>
                        <h2 class="offer-other__title">
                            <?php esc_html_e( 'Other Offers', 'meadan' ); ?>
                        </h2>
                    </div>
                    <a
                        class="btn btn--primary offer-other__view-all"
                        href="<?php echo esc_url( get_post_type_archive_link( 'offer' ) ); ?>"
                    >
                        <?php esc_html_e( 'View All Offers', 'meadan' ); ?>
                    </a>
                </div><!-- .offer-other__header -->

                <!-- 2-col card grid -->
                <div class="offer-other__grid">
                    <?php while ( $other_offers->have_posts() ) : $other_offers->the_post();
                        $rel_partner = function_exists( 'get_field' ) ? get_field( 'partner_name' ) : '';
                    ?>
                        <article class="offer-other-card">

                            <?php if ( has_post_thumbnail() ) : ?>
                                <a class="offer-other-card__image-link" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
                                    <?php the_post_thumbnail(
                                        'medium_large',
                                        [ 'class' => 'offer-other-card__image', 'loading' => 'lazy' ]
                                    ); ?>
                                </a>
                            <?php endif; ?>

                            <div class="offer-other-card__body">
                                <h3 class="offer-other-card__title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h3>
                                <?php if ( get_the_excerpt() ) : ?>
                                    <p class="offer-other-card__excerpt">
                                        <?php echo esc_html( get_the_excerpt() ); ?>
                                    </p>
                                <?php endif; ?>
                                <p class="offer-other-card__date">
                                    <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
                                        <?php echo esc_html( get_the_date( 'M j, Y' ) ); ?>
                                    </time>
                                </p>
                                <a class="offer-other-card__read-more" href="<?php the_permalink(); ?>">
                                    <?php esc_html_e( 'Read More', 'meadan' ); ?>
                                </a>
                            </div>

                        </article><!-- .offer-other-card -->

                    <?php endwhile;
                    wp_reset_postdata(); ?>
                </div><!-- .offer-other__grid -->

            </div><!-- .offer-other__inner -->
        </section><!-- .offer-other -->
    <?php endif; ?>

</main><!-- .site-main -->

<?php endwhile; ?>

<?php get_footer();
