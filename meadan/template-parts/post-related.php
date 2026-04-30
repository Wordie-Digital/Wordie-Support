<?php
/**
 * Component: Blog Post — Related Articles
 * Figma node: 4520:33630
 *
 * bg: #c8bbab (Stone), padding: 80px 60px, gap: 40px
 * Header row (align-items: flex-end): eyebrow · H2 · description (left) + "View All" (right)
 * Cards row: 3 article cards (4:3 image · metadata · title · excerpt · "Read More" link)
 *
 * Queries 3 posts from the same category (or latest posts as fallback).
 *
 * @package MeadanHomes
 */

defined( 'ABSPATH' ) || exit;

// Resolve category for the current post
$cats       = get_the_category( get_the_ID() );
$cat        = ! empty( $cats ) ? $cats[0] : null;
$blog_url   = ( $blog_pid = (int) get_option( 'page_for_posts' ) ) ? get_permalink( $blog_pid ) : home_url( '/blog/' );

// Query 3 related posts (same category, exclude current)
$query_args = [
    'post_type'           => 'post',
    'posts_per_page'      => 3,
    'post__not_in'        => [ get_the_ID() ],
    'ignore_sticky_posts' => true,
];
if ( $cat ) {
    $query_args['category__in'] = [ $cat->term_id ];
}
$related = new WP_Query( $query_args );

if ( ! $related->have_posts() ) {
    return;
}
?>
<section class="post-related">
    <div class="post-related__inner">

        <!-- ── Header row ─────────────────────────────────────────────── -->
        <div class="post-related__header">
            <div class="post-related__header-text">
                <p class="post-related__eyebrow">You might be interested</p>
                <h2 class="post-related__heading">Related Articles</h2>
            </div>
            <a class="btn btn--primary" href="<?php echo esc_url( $blog_url ); ?>">View All</a>
        </div>

        <!-- ── Cards ──────────────────────────────────────────────────── -->
        <div class="post-related__cards">
            <?php while ( $related->have_posts() ) : $related->the_post();
                $pid        = get_the_ID();
                $cats_r     = get_the_category( $pid );
                $cat_r      = ! empty( $cats_r ) ? $cats_r[0] : null;
                $wc         = str_word_count( strip_tags( get_the_content() ) );
                $mins       = max( 1, (int) ceil( $wc / 250 ) );
            ?>
            <article class="post-card">
                <!-- Image -->
                <?php if ( has_post_thumbnail() ) : ?>
                <a class="post-card__image-link" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
                    <figure class="post-card__image-wrap">
                        <?php the_post_thumbnail( 'medium_large', [ 'class' => 'post-card__image', 'loading' => 'lazy', 'alt' => get_the_title() ] ); ?>
                    </figure>
                </a>
                <?php endif; ?>

                <!-- Card body -->
                <div class="post-card__body">

                    <!-- Metadata -->
                    <div class="post-card__meta">
                        <?php if ( $cat_r ) : ?>
                        <a class="post-card__meta-cat" href="<?php echo esc_url( get_category_link( $cat_r->term_id ) ); ?>">
                            <?php echo esc_html( $cat_r->name ); ?>
                        </a>
                        <?php endif; ?>
                        <span class="post-card__meta-date"><?php echo get_the_date( 'M j, Y' ); ?></span>
                        <span class="post-card__meta-read"><?php echo esc_html( $mins . '-min read' ); ?></span>
                    </div>

                    <!-- Text -->
                    <div class="post-card__text">
                        <h3 class="post-card__title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h3>
                        <p class="post-card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 20 ) ); ?></p>
                    </div>

                    <!-- Read More link -->
                    <a class="post-card__link" href="<?php the_permalink(); ?>">Read More</a>

                </div><!-- .post-card__body -->
            </article><!-- .post-card -->
            <?php endwhile; wp_reset_postdata(); ?>
        </div><!-- .post-related__cards -->

    </div><!-- .post-related__inner -->
</section><!-- .post-related -->
