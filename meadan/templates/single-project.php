<?php
/**
 * Meadan — templates/single-project.php
 * Single Project CPT template.
 */
get_header(); ?>

<main class="site-main site-main--single-project" id="main">
    <?php while ( have_posts() ) : the_post(); ?>

    <article class="project-single" itemscope itemtype="https://schema.org/CreativeWork">
        <?php if ( has_post_thumbnail() ) : ?>
            <figure class="project-single__hero">
                <?php the_post_thumbnail( 'full', [ 'loading' => 'eager', 'class' => 'project-single__hero-image', 'itemprop' => 'image' ] ); ?>
            </figure>
        <?php endif; ?>

        <div class="project-single__content">
            <header class="project-single__header">
                <h1 class="project-single__title" itemprop="name"><?php the_title(); ?></h1>
            </header>

            <div class="project-single__body" itemprop="description">
                <?php the_content(); ?>
            </div>
        </div>
    </article>

    <?php endwhile; ?>
</main>

<?php get_footer();
