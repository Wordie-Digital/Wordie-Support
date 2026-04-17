<?php
/**
 * Meadan — front-page.php
 * Home page template. Renders ACF Gutenberg blocks via the_content().
 */
get_header(); ?>

<main class="site-main site-main--home" id="main">
    <?php
    while ( have_posts() ) :
        the_post();
        the_content();
    endwhile;
    ?>
</main>

<?php get_footer();
