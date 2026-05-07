<?php
/**
 * Meadan — templates/archive-offer.php
 *
 * Archive for the 'offer' CPT.
 * URL: /offers-and-partnerships/
 * Routed via archive_template filter in functions.php.
 *
 * Figma node: 4515:29328 — "Offers List"
 *
 * Sections (per Figma):
 *   1. Hero Image        — 400px full-width image (ACF or fallback gradient)
 *   2. Intro two-col     — left: label + h1; right: description + Enquire Now
 *   3. Search bar        — WP search restricted to offer CPT
 *   4. Offers list       — horizontal cards (image left, content right)
 *   5. Footer            — via get_footer()
 */

defined( 'ABSPATH' ) || exit;

get_header();

// ACF fields (set per the "Offers & Partnerships" page in WP Admin → Offers)
$hero_image   = function_exists( 'get_field' ) ? get_field( 'hero_image',   'option' ) : null;
$enquire_url  = function_exists( 'get_field' ) ? get_field( 'enquire_url',  'option' ) : '';
$enquire_url  = $enquire_url ?: home_url( '/contact' );
?>

<main class="site-main site-main--archive-offer" id="main" role="main">

    <!-- ── 1. Hero Image ─────────────────────────────────────────────── -->
    <section
        class="offer-hero-image"
        <?php if ( $hero_image && is_array( $hero_image ) ) : ?>
            style="background-image: url('<?php echo esc_url( $hero_image['url'] ); ?>')"
        <?php endif; ?>
        aria-hidden="true"
    ></section><!-- .offer-hero-image -->

    <!-- ── 2. Intro two-column ───────────────────────────────────────── -->
    <section class="offer-archive-intro">
        <div class="offer-archive-intro__inner">

            <div class="offer-archive-intro__left">
                <p class="offer-archive-intro__label"><?php esc_html_e( 'OFFERS', 'meadan' ); ?></p>
                <h1 class="offer-archive-intro__title">
                    <?php esc_html_e( 'Offers &amp; Partnerships', 'meadan' ); ?>
                </h1>
            </div>

            <div class="offer-archive-intro__right">
                <p class="offer-archive-intro__desc">
                    <?php esc_html_e( 'Our offers and partnerships are built around carefully selected collaborations that allow us to deliver greater value, deeper personalisation, and exceptional quality to Meadan clients. We focus on meaningful affiliations with premium suppliers who share our commitment and drive to deliver beautiful design-led outcomes.', 'meadan' ); ?>
                </p>
                <p class="offer-archive-intro__desc">
                    <?php esc_html_e( 'Each offer below reflects our belief that great project delivery comes from working with the right people – combining design insight with trusted craftsmanship to create something truly distinctive.', 'meadan' ); ?>
                </p>
                <a class="btn btn--primary" href="<?php echo esc_url( $enquire_url ); ?>">
                    <?php esc_html_e( 'Enquire Now', 'meadan' ); ?>
                </a>
            </div>

        </div><!-- .offer-archive-intro__inner -->
    </section><!-- .offer-archive-intro -->

    <!-- ── 3. Search bar ─────────────────────────────────────────────── -->
    <section class="offer-archive-search" aria-label="<?php esc_attr_e( 'Search offers', 'meadan' ); ?>">
        <div class="offer-archive-search__inner">
            <form
                class="offer-archive-search__form"
                method="get"
                action="<?php echo esc_url( get_post_type_archive_link( 'offer' ) ); ?>"
                role="search"
            >
                <input type="hidden" name="post_type" value="offer">
                <label class="screen-reader-text" for="offer-search">
                    <?php esc_html_e( 'Search for an offer', 'meadan' ); ?>
                </label>
                <div class="offer-archive-search__field-wrap">
                    <svg class="offer-archive-search__icon" width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true" focusable="false">
                        <circle cx="7.5" cy="7.5" r="5.5" stroke="currentColor" stroke-width="1.2"/>
                        <path d="M11.5 11.5L16 16" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
                    </svg>
                    <input
                        id="offer-search"
                        class="offer-archive-search__input"
                        type="search"
                        name="s"
                        placeholder="<?php esc_attr_e( 'Search for an offer', 'meadan' ); ?>"
                        value="<?php echo esc_attr( get_search_query() ); ?>"
                        autocomplete="off"
                    >
                </div>
            </form>
        </div>
    </section><!-- .offer-archive-search -->

    <!-- ── 4. Offers list ────────────────────────────────────────────── -->
    <section class="offer-archive-list" aria-label="<?php esc_attr_e( 'Offers listing', 'meadan' ); ?>">
        <div class="offer-archive-list__inner">

            <?php if ( have_posts() ) : ?>

                <div class="offer-list">
                    <?php while ( have_posts() ) : the_post();
                        $partner_name = function_exists( 'get_field' ) ? get_field( 'partner_name' ) : '';
                        $expiry_date  = function_exists( 'get_field' ) ? get_field( 'expiry_date' )  : '';
                    ?>
                        <article class="offer-list-card" id="offer-<?php the_ID(); ?>">

                            <?php if ( has_post_thumbnail() ) : ?>
                                <a class="offer-list-card__image-link" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
                                    <?php the_post_thumbnail(
                                        'large',
                                        [ 'class' => 'offer-list-card__image', 'loading' => 'lazy' ]
                                    ); ?>
                                </a>
                            <?php else : ?>
                                <div class="offer-list-card__image-placeholder"></div>
                            <?php endif; ?>

                            <div class="offer-list-card__body">

                                <header class="offer-list-card__header">
                                    <h2 class="offer-list-card__title">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </h2>
                                    <?php if ( $partner_name ) : ?>
                                        <p class="offer-list-card__partner"><?php echo esc_html( $partner_name ); ?></p>
                                    <?php endif; ?>
                                </header>

                                <?php if ( get_the_excerpt() ) : ?>
                                    <p class="offer-list-card__excerpt">
                                        <?php echo esc_html( get_the_excerpt() ); ?>
                                    </p>
                                <?php endif; ?>

                                <footer class="offer-list-card__footer">
                                    <?php if ( $expiry_date ) : ?>
                                        <p class="offer-list-card__date">
                                            <?php printf(
                                                esc_html__( 'Expires %s', 'meadan' ),
                                                esc_html( $expiry_date )
                                            ); ?>
                                        </p>
                                    <?php else : ?>
                                        <p class="offer-list-card__date">
                                            <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
                                                <?php echo esc_html( get_the_date() ); ?>
                                            </time>
                                        </p>
                                    <?php endif; ?>
                                    <a class="offer-list-card__link" href="<?php the_permalink(); ?>">
                                        <?php esc_html_e( 'Read More', 'meadan' ); ?>
                                    </a>
                                </footer>

                            </div><!-- .offer-list-card__body -->

                        </article><!-- .offer-list-card -->

                    <?php endwhile; ?>
                </div><!-- .offer-list -->

                <!-- Pagination -->
                <?php the_posts_pagination( [
                    'mid_size'  => 2,
                    'prev_text' => '<span class="screen-reader-text">' . esc_html__( 'Previous page', 'meadan' ) . '</span>',
                    'next_text' => '<span class="screen-reader-text">' . esc_html__( 'Next page', 'meadan' ) . '</span>',
                    'before_page_number' => '<span class="screen-reader-text">' . esc_html__( 'Page', 'meadan' ) . ' </span>',
                ] ); ?>

            <?php else : ?>

                <div class="offer-archive-empty">
                    <p><?php esc_html_e( 'No offers available at this time. Check back soon.', 'meadan' ); ?></p>
                    <a class="btn btn--primary" href="<?php echo esc_url( home_url( '/' ) ); ?>">
                        <?php esc_html_e( 'Back to Home', 'meadan' ); ?>
                    </a>
                </div>

            <?php endif; ?>

        </div><!-- .offer-archive-list__inner -->
    </section><!-- .offer-archive-list -->

</main><!-- .site-main -->

<?php get_footer();
