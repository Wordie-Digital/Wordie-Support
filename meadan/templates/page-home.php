<?php
/**
 * Template Name: Home
 *
 * The page template used for the Meadan Homes homepage.
 * Renders all Gutenberg/ACF blocks added in the page editor.
 *
 * @package MeadanHomes
 */

get_header(); ?>

<main class="site-main site-main--home" id="main" role="main">
    <?php while ( have_posts() ) : the_post(); ?>

        <?php if ( have_blocks() ) :
            // Render each block registered via ACF
            while ( have_blocks() ) :
                $block = next_block();
                echo apply_filters( 'the_content', render_block( $block ) );
            endwhile;
        else :
            // Fallback: render post content directly (handles classic editor / shortcodes)
            the_content();
        endif; ?>

    <?php endwhile; ?>
</main><!-- .site-main -->

<?php get_footer();
