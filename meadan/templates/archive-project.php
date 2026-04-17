<?php
/**
 * Meadan — templates/archive-project.php
 * Project CPT archive template.
 */
get_header(); ?>

<main class="site-main site-main--archive-project" id="main">
    <header class="archive-header">
        <p class="archive-header__label"><?php esc_html_e( 'PORTFOLIO', 'meadan' ); ?></p>
        <h1 class="archive-header__title"><?php esc_html_e( 'Our Projects', 'meadan' ); ?></h1>
    </header>

    <div class="project-grid">
        <?php if ( have_posts() ) :
            while ( have_posts() ) : the_post(); ?>
                <article class="project-card">
                    <?php if ( has_post_thumbnail() ) : ?>
                        <a class="project-card__image-link" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
                            <?php the_post_thumbnail( 'medium_large', [ 'loading' => 'lazy', 'class' => 'project-card__image' ] ); ?>
                        </a>
                    <?php endif; ?>
                    <div class="project-card__body">
                        <h2 class="project-card__title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h2>
                        <p class="project-card__excerpt"><?php echo esc_html( get_the_excerpt() ); ?></p>
                    </div>
                </article>
            <?php endwhile;

            the_posts_pagination();
        else : ?>
            <p><?php esc_html_e( 'No projects found.', 'meadan' ); ?></p>
        <?php endif; ?>
    </div>
</main>

<?php get_footer();
