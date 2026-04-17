<?php
/**
 * Meadan — page.php
 * Generic page template.
 */
get_header(); ?>

<main class="site-main" id="main">
    <?php
    while ( have_posts() ) :
        the_post();
        the_content();
    endwhile;
    ?>
</main>

<?php get_footer();
