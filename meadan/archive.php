<?php
/**
 * Meadan — archive.php
 * Generic archive template (blog posts).
 */
get_header(); ?>

<main class="site-main site-main--archive" id="main">
    <header class="archive-header">
        <h1 class="archive-header__title"><?php the_archive_title(); ?></h1>
    </header>

    <div class="archive-grid">
        <?php if ( have_posts() ) :
            while ( have_posts() ) :
                the_post(); ?>
                <article class="article-card">
                    <?php if ( has_post_thumbnail() ) : ?>
                        <a class="article-card__image-link" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
                            <?php the_post_thumbnail( 'medium_large', [ 'loading' => 'lazy', 'class' => 'article-card__image' ] ); ?>
                        </a>
                    <?php endif; ?>
                    <div class="article-card__body">
                        <p class="article-card__date">
                            <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
                                <?php echo esc_html( get_the_date() ); ?>
                            </time>
                        </p>
                        <h2 class="article-card__title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h2>
                        <p class="article-card__excerpt"><?php echo esc_html( get_the_excerpt() ); ?></p>
                    </div>
                </article>
            <?php endwhile;

            the_posts_pagination();
        else : ?>
            <p><?php esc_html_e( 'No posts found.', 'meadan' ); ?></p>
        <?php endif; ?>
    </div>
</main>

<?php get_footer();
