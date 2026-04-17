<?php
/**
 * Meadan Homes — index.php
 *
 * Fallback template. WordPress requires this file. Under normal operation,
 * page.php, single.php, or a custom template will be used instead.
 */

get_header(); ?>

<main class="site-main" id="main">
    <div class="container">
        <?php if ( have_posts() ) :
            while ( have_posts() ) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <h1 class="entry-title"><?php the_title(); ?></h1>
                    <div class="entry-content">
                        <?php the_content(); ?>
                    </div>
                </article>
            <?php endwhile;
        else : ?>
            <p><?php esc_html_e( 'No content found.', 'meadan' ); ?></p>
        <?php endif; ?>
    </div>
</main>

<?php get_footer();
