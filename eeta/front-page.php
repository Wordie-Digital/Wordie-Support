<?php
/**
 * EETA — front-page.php
 * Renders ACF Flexible Content sections for the homepage.
 */
get_header();
?>

<main class="eeta-main" id="main">
<?php
if ( have_rows( 'page_sections' ) ) :
    while ( have_rows( 'page_sections' ) ) :
        the_row();

        $layout = get_row_layout();

        get_template_part( 'template-parts/layouts/' . str_replace( '_', '-', $layout ) );

    endwhile;
endif;
?>
</main>

<?php get_footer(); ?>
