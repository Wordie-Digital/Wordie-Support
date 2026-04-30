<?php
/**
 * Meadan — templates/single-post.php
 *
 * Single blog post template (Figma 4520:33598).
 * Routed via single_template filter in functions.php.
 *
 * Sections:
 *   1. post-hero     — Stone bg 400px, breadcrumbs, title, excerpt, meta
 *   2. post-content  — 2:1 featured image + article body at 874px
 *   3. post-featured-design — Chalk bg, ACF relationship to design CPT
 *   4. post-related  — Stone bg, 3 related posts from same category
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
    the_post();
    get_template_part( 'template-parts/post-hero' );
    get_template_part( 'template-parts/post-content' );
    get_template_part( 'template-parts/post-featured-design' );
    get_template_part( 'template-parts/post-related' );
endwhile;

get_footer();
