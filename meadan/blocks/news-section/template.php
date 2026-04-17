<?php
/**
 * Block: News Section
 * Slug: acf/news-section
 * Dynamic: queries WordPress Posts.
 */

defined( 'ABSPATH' ) || exit;

$section_label = get_field( 'section_label' );
$heading       = get_field( 'heading' );
$description   = get_field( 'description' );
$count         = (int) ( get_field( 'count' ) ?: 3 );
$cta_label     = get_field( 'cta_label' );
$cta_url       = get_field( 'cta_url' );

$news = new WP_Query( [
    'post_type'      => 'post',
    'posts_per_page' => $count,
    'post_status'    => 'publish',
    'orderby'        => 'date',
    'order'          => 'DESC',
    'no_found_rows'  => true,
] );

$block_id = ! empty( $block['anchor'] ) ? ' id="' . esc_attr( $block['anchor'] ) . '"' : '';
?>
<section class="news-section"<?php echo $block_id; ?>>
    <div class="news-section__header">
        <div class="news-section__header-text">
            <?php if ( $section_label ) : ?>
                <p class="news-section__label"><?php echo esc_html( $section_label ); ?></p>
            <?php endif; ?>
            <?php if ( $heading ) : ?>
                <h2 class="news-section__heading"><?php echo esc_html( $heading ); ?></h2>
            <?php endif; ?>
            <?php if ( $description ) : ?>
                <p class="news-section__description"><?php echo esc_html( $description ); ?></p>
            <?php endif; ?>
        </div>

        <div class="news-section__nav" role="group" aria-label="News navigation">
            <button class="arrow-btn arrow-btn--prev js-slider-prev" aria-label="<?php esc_attr_e( 'Previous articles', 'meadan' ); ?>" disabled>
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false">
                    <path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
            <button class="arrow-btn arrow-btn--next js-slider-next" aria-label="<?php esc_attr_e( 'Next articles', 'meadan' ); ?>">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false">
                    <path d="M9 18l6-6-6-6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
        </div>
    </div>

    <?php if ( $news->have_posts() ) : ?>
        <div class="news-section__grid">
            <?php while ( $news->have_posts() ) : $news->the_post(); ?>
                <article class="article-card">
                    <?php if ( has_post_thumbnail() ) : ?>
                        <a class="article-card__image-link" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
                            <?php the_post_thumbnail( 'meadan-card', [ 'class' => 'article-card__image', 'loading' => 'lazy' ] ); ?>
                        </a>
                    <?php endif; ?>
                    <div class="article-card__body">
                        <p class="article-card__date">
                            <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
                                <?php echo esc_html( get_the_date() ); ?>
                            </time>
                        </p>
                        <h3 class="article-card__title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h3>
                        <?php if ( get_the_excerpt() ) : ?>
                            <p class="article-card__excerpt"><?php echo esc_html( get_the_excerpt() ); ?></p>
                        <?php endif; ?>
                        <a class="article-card__link" href="<?php the_permalink(); ?>">
                            <?php esc_html_e( 'Read More', 'meadan' ); ?>
                        </a>
                    </div>
                </article>
            <?php endwhile;
            wp_reset_postdata(); ?>
        </div>
    <?php else : ?>
        <p class="news-section__empty"><?php esc_html_e( 'No posts found.', 'meadan' ); ?></p>
    <?php endif; ?>

    <?php if ( $cta_label && $cta_url ) : ?>
        <div class="news-section__footer">
            <a class="btn btn--primary" href="<?php echo esc_url( $cta_url ); ?>">
                <?php echo esc_html( $cta_label ); ?>
            </a>
        </div>
    <?php endif; ?>
</section>
