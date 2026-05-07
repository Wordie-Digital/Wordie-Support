<?php
/**
 * Meadan — templates/archive-offer.php
 *
 * Archive template for the 'offer' CPT.
 * URL: /offers-and-partnerships/
 *
 * Routed via archive_template filter in functions.php.
 *
 * Sections:
 *   1. Archive hero    — stone bg, label, heading, intro copy
 *   2. Offers grid     — 3-col article cards (image, tag, title, excerpt, CTA)
 *   3. Pagination      — prev/next
 *   4. CTA strip       — sand bg, heading + button
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main class="site-main site-main--archive-offer" id="main" role="main">

    <!-- ── 1. Archive hero ───────────────────────────────────────────── -->
    <section class="offer-archive-hero">
        <div class="offer-archive-hero__inner">
            <p class="offer-archive-hero__label"><?php esc_html_e( 'OFFERS &amp; PARTNERSHIPS', 'meadan' ); ?></p>
            <h1 class="offer-archive-hero__title">
                <?php esc_html_e( 'Exclusive Offers &amp; Partner Benefits', 'meadan' ); ?>
            </h1>
            <p class="offer-archive-hero__description">
                <?php esc_html_e( 'Discover exclusive deals and partnerships available to Meadan Homes clients — from finance and insurance to interior design and landscaping.', 'meadan' ); ?>
            </p>
        </div>
    </section><!-- .offer-archive-hero -->

    <!-- ── 2. Offers grid ───────────────────────────────────────────── -->
    <section class="offer-archive-grid" aria-label="<?php esc_attr_e( 'Offers listing', 'meadan' ); ?>">
        <div class="offer-archive-grid__inner">

            <?php if ( have_posts() ) : ?>

                <div class="offer-grid">
                    <?php while ( have_posts() ) : the_post();

                        // Optional ACF: partner_name, partner_logo, expiry_date
                        $partner_name = function_exists( 'get_field' ) ? get_field( 'partner_name' ) : '';
                        $expiry_date  = function_exists( 'get_field' ) ? get_field( 'expiry_date' )  : '';
                    ?>
                        <article class="offer-card" id="offer-<?php the_ID(); ?>">

                            <?php if ( has_post_thumbnail() ) : ?>
                                <a class="offer-card__image-link" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
                                    <?php the_post_thumbnail(
                                        'meadan-card',
                                        [ 'class' => 'offer-card__image', 'loading' => 'lazy' ]
                                    ); ?>
                                </a>
                            <?php endif; ?>

                            <div class="offer-card__body">

                                <?php if ( $partner_name ) : ?>
                                    <p class="offer-card__partner"><?php echo esc_html( $partner_name ); ?></p>
                                <?php endif; ?>

                                <h2 class="offer-card__title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h2>

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
                                    <?php if ( $expiry_date ) : ?>
                                        <p class="offer-card__expiry">
                                            <?php
                                            printf(
                                                /* translators: %s: expiry date */
                                                esc_html__( 'Expires %s', 'meadan' ),
                                                esc_html( $expiry_date )
                                            );
                                            ?>
                                        </p>
                                    <?php endif; ?>
                                </div>

                            </div><!-- .offer-card__body -->

                        </article><!-- .offer-card -->

                    <?php endwhile; ?>
                </div><!-- .offer-grid -->

                <!-- Pagination -->
                <nav class="offer-archive-pagination" aria-label="<?php esc_attr_e( 'Offers pages', 'meadan' ); ?>">
                    <?php
                    the_posts_pagination( [
                        'mid_size'  => 2,
                        'prev_text' => sprintf(
                            '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M12 5l-5 5 5 5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg><span>%s</span>',
                            esc_html__( 'Previous', 'meadan' )
                        ),
                        'next_text' => sprintf(
                            '<span>%s</span><svg width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M8 5l5 5-5 5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>',
                            esc_html__( 'Next', 'meadan' )
                        ),
                    ] );
                    ?>
                </nav>

            <?php else : ?>

                <div class="offer-archive-empty">
                    <p><?php esc_html_e( 'No offers available at this time. Check back soon.', 'meadan' ); ?></p>
                    <a class="btn btn--primary" href="<?php echo esc_url( home_url( '/' ) ); ?>">
                        <?php esc_html_e( 'Back to Home', 'meadan' ); ?>
                    </a>
                </div>

            <?php endif; ?>

        </div><!-- .offer-archive-grid__inner -->
    </section><!-- .offer-archive-grid -->

    <!-- ── 3. CTA strip ─────────────────────────────────────────────── -->
    <section class="offer-archive-cta">
        <div class="offer-archive-cta__inner">
            <div class="offer-archive-cta__text">
                <h2 class="offer-archive-cta__heading">
                    <?php esc_html_e( 'Ready to Build Your Dream Home?', 'meadan' ); ?>
                </h2>
                <p class="offer-archive-cta__subheading">
                    <?php esc_html_e( 'Talk to our team about which offers suit your build.', 'meadan' ); ?>
                </p>
            </div>
            <a class="btn btn--primary" href="<?php echo esc_url( home_url( '/contact' ) ); ?>">
                <?php esc_html_e( 'Get in Touch', 'meadan' ); ?>
            </a>
        </div>
    </section><!-- .offer-archive-cta -->

</main><!-- .site-main -->

<?php get_footer();
