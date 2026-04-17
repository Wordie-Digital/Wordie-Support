<?php
/**
 * Meadan — single.php
 * Single post template (news/blog articles).
 */
get_header(); ?>

<main class="site-main site-main--single" id="main">
    <article class="article">
        <?php
        while ( have_posts() ) :
            the_post(); ?>
            <header class="article__header">
                <p class="article__meta">
                    <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
                        <?php echo esc_html( get_the_date() ); ?>
                    </time>
                </p>
                <h1 class="article__title"><?php the_title(); ?></h1>
            </header>

            <?php if ( has_post_thumbnail() ) : ?>
                <figure class="article__featured-image">
                    <?php the_post_thumbnail( 'large', [ 'loading' => 'lazy' ] ); ?>
                </figure>
            <?php endif; ?>

            <div class="article__content">
                <?php the_content(); ?>
            </div>
        <?php endwhile; ?>
    </article>
</main>

<?php get_footer();
